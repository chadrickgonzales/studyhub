<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ResourceComment extends Model
{
    use HasFactory;

    protected $fillable = [
        'content',
        'resource_id',
        'user_id',
        'parent_id',
    ];

    public function resource(): BelongsTo
    {
        return $this->belongsTo(Resource::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(ResourceComment::class, 'parent_id');
    }

    public function replies()
    {
        return $this->hasMany(ResourceComment::class, 'parent_id');
    }
} 