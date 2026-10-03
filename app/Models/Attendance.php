<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'session_id',
        'status', // present, late, absent, cancelled
        'arrival_time',
        'points_earned'
    ];

    protected $casts = [
        'arrival_time' => 'datetime',
        'points_earned' => 'integer'
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function session()
    {
        return $this->belongsTo(Session::class);
    }

    public function isPresent()
    {
        return $this->status === 'present' || $this->status === 'late';
    }

    public function isLate()
    {
        return $this->status === 'late';
    }

    public function isAbsent()
    {
        return $this->status === 'absent';
    }
}