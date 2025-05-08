<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudyResource extends Model
{
    use HasFactory;

    protected $fillable = [
        'study_room_id',
        'uploaded_by',
        'name',
        'file_path',
        'file_type',
        'file_size',
        'description',
    ];

    public function studyRoom()
    {
        return $this->belongsTo(StudyRoom::class);
    }

    public function uploader()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }
} 