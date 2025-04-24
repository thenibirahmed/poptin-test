<?php

namespace App\Http\Controllers;

use App\Models\Poll;
use App\Services\PollService;
use App\Http\Requests\PollVoteRequest;

class PollController extends Controller
{
    public function submitVote(PollVoteRequest $request, Poll $poll)
    {
        $result = app(PollService::class)->storeVote($request, $poll);

        return response()->json(['message' => $result['message']])
            ->cookie(Poll::POLL_COOKIE_KEY, $result['cookie'], 60 * 24 * 30);
    }
}
