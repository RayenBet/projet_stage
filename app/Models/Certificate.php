<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Certificate extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'min_score',
        'type',
        'category',
        'provider',
        'level',
        'duration',
        'color',
        'skills',
        'utility',
        'career_benefits',
        'external_url',
        'icon',
        'is_active'
    ];

    protected $casts = [
        'min_score' => 'integer',
        'is_active' => 'boolean'
    ];

    public function students()
    {
        return $this->belongsToMany(Student::class, 'student_certificates')
                    ->withTimestamps();
    }

    /**
     * Tous les certificats (internes ET externes) se débloquent
     * de la même façon : selon le score cumulé de l'étudiant.
     */
    public function isUnlockedFor($student)
    {
        return $student->score >= $this->min_score;
    }

    public function isInternal(): bool
    {
        return $this->type === 'interne';
    }

    public function isExternal(): bool
    {
        return $this->type === 'externe';
    }

    /**
     * Retourne la liste des compétences sous forme de tableau
     * (stockées en base sous forme de chaîne séparée par des virgules).
     */
    public function getSkillsListAttribute(): array
    {
        if (empty($this->skills)) {
            return [];
        }

        return array_map('trim', explode(',', $this->skills));
    }
}