<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Publication;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class PublicationController extends Controller
{
    /**
     * ------------------------------------------------------------
     * LISTE DES PUBLICATIONS (PUBLIQUE - PAGINÉE)
     * ------------------------------------------------------------
     */
    public function index(Request $request)
    {
        try {
            $perPage = $request->get('per_page', 10);

            $publications = Publication::with('internalAuthors')
                ->orderBy('publication_year', 'desc')
                ->paginate($perPage);

            return response()->json([
                'data' => $publications->items(),
                'meta' => [
                    'current_page' => $publications->currentPage(),
                    'last_page'    => $publications->lastPage(),
                    'per_page'     => $publications->perPage(),
                    'total'        => $publications->total(),
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Erreur récupération publications',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * ------------------------------------------------------------
     * MES PUBLICATIONS (PROFESSEUR CONNECTÉ)
     * ------------------------------------------------------------
     */
    public function myPublications()
    {
        try {
            $professor = Auth::user();

            $publications = Publication::with('internalAuthors')
                ->whereHas('internalAuthors', function ($query) use ($professor) {
                    $query->where('professor_id', $professor->id);
                })
                ->orderBy('publication_year', 'desc')
                ->get();

            return response()->json($publications);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Erreur récupération mes publications',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * ------------------------------------------------------------
     * CRÉER UNE PUBLICATION (PROTÉGÉ)
     * ------------------------------------------------------------
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title'             => 'required|string|max:500',
            'publication_year'  => 'required|integer',
            'type'              => 'required|string',
            'doi'               => 'nullable|string',
            'publication_url'   => 'nullable|url',
            'abstract'          => 'nullable|string',
            'external_authors'  => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $publication = Publication::create([
                'title'             => $request->title,
                'publication_year'  => $request->publication_year,
                'type'              => $request->type,
                'doi'               => $request->doi,
                'publication_url'   => $request->publication_url,
                'abstract'          => $request->abstract,
                'external_authors'  => $request->external_authors,
            ]);

            // Associer automatiquement le professeur connecté comme auteur
            $publication->internalAuthors()->attach(
                Auth::id(),
                ['author_order' => 1]
            );

            return response()->json([
                'message' => 'Publication créée avec succès',
                'publication' => $publication->load('internalAuthors')
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Erreur création publication',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * ------------------------------------------------------------
     * (OPTIONNEL) AFFICHER UNE PUBLICATION
     * ------------------------------------------------------------
     */
    public function show($id)
    {
        $publication = Publication::with('internalAuthors')->findOrFail($id);
        return response()->json($publication);
    }

    /**
     * ------------------------------------------------------------
     * (OPTIONNEL) SUPPRIMER UNE PUBLICATION
     * ------------------------------------------------------------
     */
    public function destroy($id)
    {
        $publication = Publication::findOrFail($id);
        $publication->delete();

        return response()->json([
            'message' => 'Publication supprimée avec succès'
        ]);
    }
}
