<?php

namespace App\Models;

use App\Models\PollOption;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Poll extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'question',
    ];

    public const CREATE_SKELETON = [
        'name' => '',
        'question' => '',
    ];

    public const POLL_COOKIE_KEY = 'poll_votes';

    public function pollOptions()
    {
        return $this->hasMany(PollOption::class);
    }

    public function pollVotes()
    {
        return $this->hasManyThrough(
            PollVote::class,
            PollOption::class,
            'poll_id',
            'poll_option_id',
            'id',
            'id'
        );
    }

    public function getUsersVote($userId = null)
    {
        $cookieVotes = json_decode(request()->cookie(Poll::POLL_COOKIE_KEY, '{}'), true);
        
        if (isset($cookieVotes[$this->id])) {
            return $this->pollOptions()->find($cookieVotes[$this->id]);
        }

        if ($userId) {
            $vote = $this->pollVotes()->with('pollOption')->where('user_id', $userId)->first();
            return $vote?->pollOption;
        }

        return null;
    }
}
