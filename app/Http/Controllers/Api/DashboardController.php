<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Publication;
use App\Models\Team;
use App\Models\Professor;  // CHANGÉ: Professor au lieu de User
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function stats(Request $request)
    {
        $professor = $request->user(); // C'est déjà un Professor grâce à Sanctum
        
        $stats = [
            'totalPublications' => Publication::count(),
            'currentYearPublications' => Publication::whereYear('publication_year', date('Y'))->count(),
            'totalResearchers' => Professor::count(), // CHANGÉ: Professor::count() au lieu de User
            'totalTeams' => Team::count(),
        ];

        // Stats spécifiques pour le professeur connecté
        if ($professor) {
            $stats['myPublications'] = $professor->publications()->count();
            $stats['teamPublications'] = $professor->team ? 
                $professor->team->publications()->count() : 0;
        }

        return response()->json($stats);
    }

    public function recentPublications(Request $request)
    {
        $professor = $request->user(); // C'est un Professor
        $publications = [];

        if ($professor && $professor->team) {
            $teamId = $professor->team_id;
            
            // Récupérer les publications de l'équipe
            $publications = Publication::whereHas('professors', function($query) use ($teamId) {
                $query->where('team_id', $teamId);
            })
            ->with(['professors']) // CHANGÉ: 'professors' au lieu de 'professors.user'
            ->orderBy('publication_year', 'desc')
            ->limit(10)
            ->get()
            ->map(function($pub) {
                return [
                    'id' => $pub->id,
                    'title' => $pub->title,
                    'type' => $pub->type,
                    'publication_year' => $pub->publication_year,
                    'authors' => $pub->professors->map(function($prof) {
                        return $prof->full_name; // CHANGÉ: $prof->full_name au lieu de $prof->user->name
                    })->implode(', ')
                ];
            });
        }

        return response()->json($publications);
    }
}