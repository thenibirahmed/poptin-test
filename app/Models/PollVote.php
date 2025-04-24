<?php

namespace App\Models;

use App\Models\User;
use App\Models\PollOption;
use Illuminate\Database\Eloquent\Model;

class PollVote extends Model
{
    protected $fillable = [
        'poll_option_id',
        'user_id',
        'voter_identity',
        'ip_address',
    ];

    public function pollOption()
    {
        return $this->belongsTo(PollOption::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
