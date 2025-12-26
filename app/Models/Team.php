<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Team extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'field',        // Ajouté
        'domain',       // Ajouté
        'creation_date', // Ajouté
        'description', 
        'team_leader_id'
    ];

    protected $casts = [
        'creation_date' => 'date',
    ];

    // Relation avec le responsable
    public function leader()
    {
        return $this->belongsTo(Professor::class, 'team_leader_id');
    }

    // Relation avec les membres
    public function members()
    {
        return $this->hasMany(Professor::class, 'team_id');
    }
}