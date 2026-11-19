<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Game extends Model
{
    protected $fillable = ['result', 'win_type', 'points', 'moves', 'duration_seconds'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
