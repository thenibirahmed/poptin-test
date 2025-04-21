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

    public function getUsersVote($ip = null)
    {
        $ip = $ip ?: request()->ip();
        
        return $this->pollVotes()
            ->when($ip, fn ($query) => $query->where('ip_address', $ip))
            ->first();
    }
}
