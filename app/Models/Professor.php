<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Professor extends Authenticatable
{
    use HasFactory, Notifiable, HasApiTokens;

    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'phone',
        'password',
        'departement',
        'specialty',
        'grade', // ← DOCTORANT ou DOCTOR
        'role',  // ← professor, admin
        'team_id'
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'created_at',
        'updated_at'
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];


    // Constantes pour les grades
    public const GRADES = [
        'DOCTORANT' => 'Doctorant',
        'DOCTOR' => 'Doctor',
    ];

    // Constantes pour les rôles
    public const ROLES = [
        'professor' => 'Professeur',
        'director' => 'Directeur de Labo',
    ];

    // Accessors
    public function getFormattedGradeAttribute()
    {
        return self::GRADES[$this->grade] ?? $this->grade;
    }

    public function getFormattedRoleAttribute()
    {
        return self::ROLES[$this->role] ?? $this->role;
    }

    public function getFullNameAttribute()
    {
        return "{$this->first_name} {$this->last_name}";
    }

    // Scopes
    public function scopeDoctorants($query)
    {
        return $query->where('grade', 'DOCTORANT');
    }

    public function scopeDoctors($query)
    {
        return $query->where('grade', 'DOCTOR');
    }

    public function scopeAdmins($query)
    {
        return $query->where('role', 'director');
    }

    // Vérifications
    public function isDirector()
    {
        return $this->role === 'director';
    }

    public function isProfessor()
    {
        return $this->role === 'professor';
    }

    // Un professor peut être chef d'équipe
    public function isTeamLeader()
    {
        return $this->teamsLeading()->exists();
    }

    // Le directeur a tous les accès
    public function isAdmin()
    {
        return $this->isDirector(); // ← Directeur = Admin
    }

    // Relations (gardez les vôtres)
    public function team()
    {
        return $this->belongsTo(Team::class, 'team_id');
    }

    public function teamsLeading()
    {
        return $this->hasMany(Team::class, 'team_leader_id');
    }

    public function publications()
    {
        return $this->belongsToMany(Publication::class, 'professor_publication')
                    ->withPivot('author_order')
                    ->withTimestamps();
    }

    public function getAuthIdentifierName()
    {
        return 'id'; // Retourne le nom de la colonne ID
    }
}