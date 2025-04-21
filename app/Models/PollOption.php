<?php

namespace App\Models;

use App\Models\Poll;
use Illuminate\Database\Eloquent\Model;

class PollOption extends Model
{
    protected $fillable = [
        'poll_id',
        'option',
    ];

    public function poll()
    {
        return $this->belongsTo(Poll::class);
    }
}
