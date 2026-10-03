<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Session extends Model
{
    use HasFactory;

    protected $fillable = [
        'class_id',
        'subject_id',
        'professor_id',
        'room',
        'campus',
        'date',
        'start_time',
        'end_time',
        'duration',
        'status', // pending, active, completed, cancelled
        'cancellation_reason'
    ];

    protected $casts = [
        'date' => 'date',
        'start_time' => 'datetime',
        'end_time' => 'datetime'
    ];

    public function classe()
    {
        return $this->belongsTo(Classe::class, 'class_id');
    }

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    public function professor()
    {
        return $this->belongsTo(Professor::class);
    }

    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }

    public function getPresenceRateAttribute()
    {
        $total = $this->attendances()->count();
        if ($total === 0) return 0;
        $present = $this->attendances()->where('status', 'present')->count();
        return round(($present / $total) * 100, 1);
    }

    public function isActive()
    {
        return $this->status === 'active';
    }

    public function isCompleted()
    {
        return $this->status === 'completed';
    }

    public function isCancelled()
    {
        return $this->status === 'cancelled';
    }

    /**
     * Statut "réel" tenant compte du temps écoulé, sans modifier la colonne
     * status en base (qui ne change que par action explicite du professeur) :
     * - une séance jamais démarrée dont le créneau est déjà passé -> "manquée"
     * - une séance restée active après l'heure de fin -> "à clôturer"
     */
    public function getEffectiveStatusAttribute(): string
    {
        if ($this->status === 'pending' && $this->end_time && now()->greaterThan($this->end_time)) {
            return 'missed';
        }

        if ($this->status === 'active' && $this->end_time && now()->greaterThan($this->end_time)) {
            return 'overdue';
        }

        return $this->status;
    }
}