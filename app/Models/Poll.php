<?php

namespace App\Models;

use App\Models\PollOption;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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

    public function getUsersVote($ip, $userId = null)
    {
        return $this->pollVotes()
            ->where(function ($query) use ($ip, $userId) {
                if ($ip) {
                    $query->where('ip_address', $ip);
                }

                if ($userId) {
                    $query->orWhere('user_id', $userId);
                }
            })
            ->first();
    }
}
