<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserInterest extends Model
{
    use HasFactory;

    protected $fillable = ['interest'];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }
} 