<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Resource extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'type',
        'url',
        'file_path',
        'study_room_id',
        'user_id',
    ];

    public function studyRoom(): BelongsTo
    {
        return $this->belongsTo(StudyRoom::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function ratings(): HasMany
    {
        return $this->hasMany(ResourceRating::class);
    }

    public function comments(): HasMany
    {
        return $this->hasMany(ResourceComment::class);
    }
} 