<?php

namespace App\Http\Controllers;

use App\Models\Poll;
use App\Models\PollVote;
use App\Events\VoteCasted;
use App\Http\Requests\PollVoteRequest;
use Illuminate\Support\Str;
use Illuminate\Http\Request;

class PollController extends Controller
{
    public function submitVote(PollVoteRequest $request, Poll $poll)
    {
        $userId = auth('sanctum')->id();
        $ip = $request->input('ip_address');

        $cookieData = json_decode($request->cookie(Poll::POLL_COOKIE_KEY, '{}'), true);

        $cookieData = array_merge(Poll::POLL_COOKIE_STRUCTURE, $cookieData);

        $voterIdentity = isset($cookieData['voter_identity']) && !empty($cookieData['voter_identity']) ? $cookieData['voter_identity'] : (string) Str::uuid();
        $cookieData['voter_identity'] = $voterIdentity;

        $pollId = $poll->id;
        $optionId = $request->input('poll_option_id');

        $existingVote = PollVote::query()
            ->where(function($query) use ($userId, $voterIdentity) {
                $query->where('voter_identity', $voterIdentity);

                if($userId) {
                    $query->orWhere('user_id', $userId);
                }
            })
            ->whereHas('pollOption', fn ($query) => $query->where('poll_id', $pollId))
            ->first();

        if ($existingVote) {
            $existingVote->update([
                'poll_option_id' => $optionId,
                'user_id' => $userId ?: $existingVote->user_id,
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

        return response()->json(['message' => 'Vote submitted successfully.'])
            ->cookie(Poll::POLL_COOKIE_KEY, json_encode($cookieData), 60 * 24 * 30);
    }
}
