<?php

namespace App\Http\Controllers;

use App\Events\VoteCasted;
use App\Models\Poll;
use App\Models\PollVote;
use Illuminate\Http\Request;

class PollController extends Controller
{
    public function submitVote(Request $request, Poll $poll)
    {
        $request->validate([
            'poll_option_id' => 'required|exists:poll_options,id',
        ]);

        $userId = auth('sanctum')->id();
        $cookieVotes = json_decode($request->cookie(Poll::POLL_COOKIE_KEY, '{}'), true);

        if (isset($cookieVotes[$poll->id])) {
            VoteCasted::dispatch($poll);
            return response()->json(['message' => 'You have already voted.'], 403);
        }

        PollVote::create([
            'poll_option_id' => $request->poll_option_id,
            'user_id' => $userId,
        ]);

        $cookieVotes[$poll->id] = $request->poll_option_id;

        VoteCasted::dispatch($poll);
        return response()->json(['message' => 'Vote submitted successfully.'])
            ->cookie(Poll::POLL_COOKIE_KEY, json_encode($cookieVotes), 60 * 24 * 30);
    }
}
