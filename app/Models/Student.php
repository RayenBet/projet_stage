<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'class_id',
        'matricule',
        'first_name',
        'last_name',
        'score',
        'total_presence',
        'total_absence',
        'total_late'
    ];

    protected $casts = [
        'score' => 'integer',
        'total_presence' => 'integer',
        'total_absence' => 'integer',
        'total_late' => 'integer'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function classe()
    {
        return $this->belongsTo(Classe::class, 'class_id');
    }

    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }

    public function certificates()
    {
        return $this->belongsToMany(Certificate::class, 'student_certificates')
                    ->withTimestamps();
    }

    public function getFullNameAttribute()
    {
        return $this->first_name . ' ' . $this->last_name;
    }

    public function getInitialsAttribute()
    {
        return substr($this->first_name, 0, 1) . substr($this->last_name, 0, 1);
    }

    public function getPresenceRateAttribute()
    {
        $total = $this->total_presence + $this->total_absence + $this->total_late;
        if ($total === 0) return 0;
        return round(($this->total_presence / $total) * 100, 1);
    }
}