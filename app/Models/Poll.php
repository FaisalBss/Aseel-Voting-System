<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Enums\PollStatus;

class Poll extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'start_time',
        'end_time',
        'created_by',
        'status',
    ];

    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'status' => PollStatus::class,
    ];

    public function options(): HasMany
    {
        return $this->hasMany(PollOption::class);
    }

    public function votes(): HasMany
    {
        return $this->hasMany(UserVote::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

}
