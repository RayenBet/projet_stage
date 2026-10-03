<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Classe extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'department',
        'level',
        'campus' // ons, mharza
    ];

    public function students()
    {
        return $this->hasMany(Student::class, 'class_id');
    }

    public function sessions()
    {
        return $this->hasMany(Session::class, 'class_id');
    }

    public function getStudentCountAttribute()
    {
        return $this->students()->count();
    }
}