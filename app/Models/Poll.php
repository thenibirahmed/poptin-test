<?php

namespace App\Models;

use App\Models\PollOption;
use Illuminate\Database\Eloquent\Model;

class Poll extends Model
{
    protected $fillable = [
        'name',
        'question',
    ];

    public const CREATE_SKELETON = [
        'name' => '',
        'question' => '',
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
