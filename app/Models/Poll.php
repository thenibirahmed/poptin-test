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

    public function getUsersVote($userId = null)
    {        
        $cookieData = json_decode(request()->cookie(Poll::POLL_COOKIE_KEY, '{}'), true);
        $cookieData = array_merge(Poll::POLL_COOKIE_STRUCTURE, $cookieData);

        $cookieVotes = $cookieData['poll_votes'] ?? [];
        $voterIdentity = $cookieData['voter_identity'] ?? null;

        return $this->pollVotes()
            ->with('pollOption')
            ->where(function($query) use ($userId, $voterIdentity) {
                $query->where('voter_identity', $voterIdentity);

                if ($userId) {
                    $query->orWhere('user_id', $userId);
                }
            })
            ->whereIn('poll_option_id', array_values($cookieVotes))
            ->first();
    }
}
