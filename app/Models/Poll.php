<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Carbon\Carbon;

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

    // public function setStartTimeAttribute($value)
    // {
    //     $this->attributes['start_time'] = Carbon::createFromFormat('Y/m/d/H:i', $value)->toDateTimeString();
    // }

    // public function setEndTimeAttribute($value)
    // {
    //     $this->attributes['end_time'] = Carbon::createFromFormat('Y/m/d/H:i', $value)->toDateTimeString();
    // }
}
