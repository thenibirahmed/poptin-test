<?php

namespace App\Services;

use App\Models\Poll;
use App\Models\PollVote;
use App\Events\VoteCasted;
use App\Models\PollOption;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Pagination\LengthAwarePaginator;

class PollService
{
    public function getPollByUuid(string $uuid): ?Poll
    {
        return Poll::with(['pollOptions.votes', 'pollVotes'])->where('uuid', $uuid)->first();
    }

    public function getPollById(int $id): ?Poll
    {
        return Poll::with(['pollOptions.votes', 'pollVotes'])->find($id);
    }

    public function getUsersVote(Poll $poll, ?int $userId = null): ?PollVote
    {
        $cookieData = json_decode(request()->cookie(Poll::POLL_COOKIE_KEY, '{}'), true);
        $cookieData = array_merge(Poll::POLL_COOKIE_STRUCTURE, $cookieData);

        $cookieVotes = $cookieData['poll_votes'] ?? [];
        $voterIdentity = $cookieData['voter_identity'] ?? null;

        return $poll->pollVotes()
            ->with('pollOption')
            ->where(function ($query) use ($userId, $voterIdentity) {
                $query->where('voter_identity', $voterIdentity);

                if ($userId) {
                    $query->orWhere('user_id', $userId);
                }
            })
            ->first();
    }

    public function getPollsForUser(): LengthAwarePaginator
    {
        $user = auth()->user();

        if ($user->hasRole(['admin'])) {
            return Poll::withCount('pollVotes')->paginate(10);
        }

        return Poll::where('user_id', $user->id)->withCount('pollVotes')->paginate(10);
    }

    public function getPollWinners(Poll $poll)
    {
        $maxVotes = $poll->pollOptions->map(fn ($option) => $option->votes->count())->max();

        return $poll->pollOptions->filter(fn ($option) => $option->votes->count() === $maxVotes);
    }

    public function getFormattedWinners(Poll $poll): string
    {
        return $this->getPollWinners($poll)
            ->pluck('option')
            ->implode(', ');
    }

    public function makeVoteApiCall(Poll $poll, int $optionId): ?string
    {
        $http = Http::acceptJson();

        if (Auth::check()) {
            $user = Auth::user();
            $token = $user->tokens()->first()?->plainTextToken ?? $user->createToken('poll-token')->plainTextToken;
            $http = $http->withToken($token);
        }

        $pollVotesCookie = Cookie::get(Poll::POLL_COOKIE_KEY, '{}');
        $cookies = [Poll::POLL_COOKIE_KEY => $pollVotesCookie];
        $domain = parse_url(config('app.url'), PHP_URL_HOST);

        $url = route('polls.vote', $poll);

        $response = $http->withCookies($cookies, $domain)->post($url, [
            'poll_option_id' => $optionId,
            'ip_address' => request()->ip(),
        ]);

        if ($response->successful()) {
            $cookie = collect($response->cookies()->toArray())->firstWhere('Name', Poll::POLL_COOKIE_KEY);
            return $cookie ? urldecode($cookie['Value']) : null;
        }

        throw new \Exception($response->json('message', 'An error occurred while submitting your vote.'));
    }

    public function storeVote(Request $request, Poll $poll): array
    {
        try {
            $userId = auth('sanctum')->id();
            $ip = $request->input('ip_address');

            $cookieData = json_decode($request->cookie(Poll::POLL_COOKIE_KEY, '{}'), true);
            $cookieData = array_merge(Poll::POLL_COOKIE_STRUCTURE, $cookieData);

            $voterIdentity = $cookieData['voter_identity'] ?: (string) Str::uuid();
            $cookieData['voter_identity'] = $voterIdentity;

            $pollId = $poll->id;
            $optionId = $request->poll_option_id;

            $existingVote = PollVote::query()
                ->where(function ($query) use ($userId, $voterIdentity) {
                    $query->where('voter_identity', $voterIdentity);

                    if ($userId) {
                        $query->orWhere('user_id', $userId);
                    }
                })
                ->whereHas('pollOption', fn ($query) => $query->where('poll_id', $pollId))
                ->first();

            if ($existingVote) {
                $existingVote->update([
                    'poll_option_id' => $optionId,
                    'user_id' => $userId ?: $existingVote->user_id,
                    'ip_address' => $ip,
                ]);
            } else {
                PollVote::create([
                    'poll_option_id' => $optionId,
                    'user_id' => $userId,
                    'voter_identity' => $voterIdentity,
                    'ip_address' => $ip,
                ]);
            }

            $cookieData['poll_votes'][$pollId] = $optionId;

            VoteCasted::dispatch($poll);

            return [
                'message' => 'Vote submitted successfully.',
                'cookie' => json_encode($cookieData),
            ];
        } catch (\Exception $e) {
            Log::error('Vote submission failed.', [
                'error' => $e->getMessage(),
            ]);

            throw new \Exception('Unable to submit vote, internal error. Please try again later.', 500);
        }
    }

    public function createPoll(array $pollData, array $options): Poll
    {
        $pollData['uuid'] = (string) Str::uuid();

        try{
            $poll = DB::transaction(function () use ($pollData, $options) {
                $poll = Poll::create($pollData);
        
                $timeStamp = now();
                $pollOptionToInsert = array_map(fn ($option) => [
                    'poll_id' => $poll->id,
                    'option' => $option,
                    'created_at' => $timeStamp,
                    'updated_at' => $timeStamp,
                ], $options);
        
                PollOption::insert($pollOptionToInsert);
    
                return $poll;
            });
        }catch (\Exception $e) {
            Log::error('Poll creation failed.', [
                'error' => $e->getMessage(),
            ]);

            throw new \Exception('Failed to create poll');
        }

        return $poll;
    }

    public function updatePoll(Poll $poll, array $pollData, array $newOptions): void
    {
        try{

            DB::transaction(function () use ($poll, $pollData, $newOptions) {
                $poll->update($pollData);
        
                $existingOptions = $poll->pollOptions->pluck('option', 'id')->toArray();
                $toInsert = [];
                $toKeep = [];
                $timeStamp = now();
        
                foreach ($newOptions as $option) {
                    $matchedId = array_search($option, $existingOptions);
        
                    if ($matchedId !== false) {
                        $toKeep[] = $matchedId;
                    } else {
                        $toInsert[] = [
                            'poll_id' => $poll->id,
                            'option' => $option,
                            'created_at' => $timeStamp,
                            'updated_at' => $timeStamp,
                        ];
                    }
                }
        
                $poll->pollOptions()->whereNotIn('id', $toKeep)->delete();
        
                if (!empty($toInsert)) {
                    PollOption::insert($toInsert);
                }
            });
        } catch (\Exception $e) {

            Log::error('Poll update failed.', [
                'error' => $e->getMessage(),
            ]);

            throw new \Exception('Failed to update poll');
        }
    }

    public function deletePoll(int $pollId): void
    {
        Poll::where('id', $pollId)->delete();
    }
}
