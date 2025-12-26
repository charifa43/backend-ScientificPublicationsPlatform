<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Publication extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'publication_year',
        'type',
        'doi',
        'publication_url',
        'abstract',
        'external_authors',
    ];

    protected $casts = [
        'publication_year' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    // Constantes pour les types de publication
    public const TYPES = [
        'research' => 'Recherche',
        'conference' => 'Conférence',
        'chapter' => 'Chapitre',
        'thesis' => 'Thèse',
        'other' => 'Autre'
    ];

    // Accessors
    public function getFormattedTypeAttribute()
    {
        return self::TYPES[$this->type] ?? $this->type;
    }

    // Relations
    public function internalAuthors()
    {
        return $this->belongsToMany(Professor::class, 'professor_publication')
                    ->withPivot('author_order')
                    ->withTimestamps();
    }
}