<?php

namespace App\Http\Controllers;

use App\Models\Poll;
use Illuminate\Http\Request;

class PollController extends Controller
{
    public function viewPoll(Poll $poll)
    {
        return view('poll', [
            'poll' => $poll,
        ]);
    }
}
