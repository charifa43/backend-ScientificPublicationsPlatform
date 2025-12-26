<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Professor;
use App\Http\Requests\StoreProfessorRequest;
use Illuminate\Http\Request;

class ProfessorController extends Controller
{
    /**
     * Display a listing of professors (PUBLIC)
     */
    public function index()
    {
        try {
            $professors = Professor::select('id', 'first_name', 'last_name', 'email', 'departement', 'grade', 'specialty')
                ->orderBy('first_name')
                ->get()
                ->map(function ($professor) {
                    return [
                        'id' => $professor->id,
                        'full_name' => $professor->full_name,
                        'first_name' => $professor->first_name,
                        'last_name' => $professor->last_name,
                        'email' => $professor->email,
                        'department' => $professor->departement,
                        'specialty' => $professor->specialty,
                        'grade' => $professor->formatted_grade,
                        'title' => $professor->formatted_grade, // Pour compatibilité avec React
                    ];
                });
            
            return response()->json($professors);
            
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Erreur lors de la récupération des professeurs',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store a newly created professor (PROTECTED - Director only)
     */
    public function store(StoreProfessorRequest $request)
    {
        $validated = $request->validated();
        
        if (isset($validated['password'])) {
            $validated['password'] = bcrypt($validated['password']);
        }
        
        $professor = Professor::create($validated);
        
        return response()->json($professor, 201);
    }

    /**
     * Display the specified professor (PROTECTED)
     */
    public function show(Professor $professor)
    {
        return response()->json([
            'id' => $professor->id,
            'full_name' => $professor->full_name,
            'first_name' => $professor->first_name,
            'last_name' => $professor->last_name,
            'email' => $professor->email,
            'phone' => $professor->phone,
            'department' => $professor->departement,
            'specialty' => $professor->specialty,
            'grade' => $professor->formatted_grade,
            'role' => $professor->formatted_role,
            'team_id' => $professor->team_id,
        ]);
    }

    /**
     * Update the specified professor (PROTECTED - Director only)
     */
    public function update(StoreProfessorRequest $request, Professor $professor)
    {
        $validated = $request->validated();
        
        if (isset($validated['password'])) {
            $validated['password'] = bcrypt($validated['password']);
        } else {
            unset($validated['password']);
        }
        
        $professor->update($validated);
        
        return response()->json($professor);
    }

    /**
     * Remove the specified professor (PROTECTED - Director only)
     */
    public function destroy(Professor $professor)
    {
        $professor->delete();
        
        return response()->json([
            'message' => 'Professeur supprimé avec succès'
        ]);
    }
}