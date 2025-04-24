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
        'user_id',
        'uuid'
    ];

    public const CREATE_SKELETON = [
        'name' => '',
        'question' => '',
    ];

    public const POLL_COOKIE_KEY = 'poll_votes';

    public const POLL_COOKIE_STRUCTURE = [
        'voter_identity' => '',
        'poll_votes' => [],
    ];

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
}
