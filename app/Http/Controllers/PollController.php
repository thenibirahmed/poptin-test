<?php

namespace App\Http\Controllers;

use App\Events\VoteCasted;
use App\Models\Poll;
use App\Models\PollVote;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PollController extends Controller
{
    public function submitVote(Request $request, Poll $poll)
    {
        $request->validate([
            'poll_option_id' => 'required|exists:poll_options,id',
        ]);

        $userId = auth('sanctum')->id();
        $ip = $request->ip();

        $vote = $poll->getUsersVote($ip);

        if ($vote) {
            if (!$vote->user_id && $userId) {
                $vote->update([
                    'user_id' => $userId,
                ]);
            }

            return response()->json(['message' => 'You have already voted.'], 403);
        }

        PollVote::create([
            'poll_option_id' => $request->poll_option_id,
            'user_id' => $userId,
            'ip_address' => $ip,
        ]);

        VoteCasted::dispatch($poll);

        return response()->json(['message' => 'Vote submitted successfully.']);
    }

}
