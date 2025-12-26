<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\Professor;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{

    /**
     * Register a new professor
     */
    public function register(Request $request): JsonResponse
    {
        \Log::info('Register attempt', $request->all());

        try {
            $request->validate([
                'first_name' => 'required|string|max:100',
                'last_name' => 'required|string|max:100',
                'email' => 'required|email|unique:professors',
                'password' => 'required|string|min:8',
                'grade' => 'required|in:DOCTORANT,DOCTOR',
                'role' => 'sometimes|in:professor,director',
            ]);

            $professor = Professor::create([
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'grade' => $request->grade,
                'role' => $request->role ?? 'professor',
            ]);

            $token = $professor->createToken('auth_token')->plainTextToken;

            \Log::info('Register success', ['professor_id' => $professor->id]);

            // Même structure que login
            $professorData = [
                'id' => $professor->id,
                'first_name' => $professor->first_name,
                'last_name' => $professor->last_name,
                'email' => $professor->email,
                'grade' => $professor->grade,
                'role' => $professor->role,
                'is_director' => $professor->role === 'director',
                'is_team_leader' => false, // Nouveau, pas encore chef d'équipe
                'is_professor' => $professor->role === 'professor',
                'full_name' => $professor->full_name,
                'formatted_role' => $professor->formatted_role,
            ];

            return response()->json([
                'professor' => $professorData,
                'token' => $token,
            ], 201);
        } catch (\Exception $e) {
            \Log::error('Register error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }

    /**
    * Login professor
     */
    public function login(Request $request): JsonResponse
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        \Log::info('Login attempt', ['email' => $request->email]);

        // 1. Trouver le professeur
        $professor = Professor::where('email', $request->email)->first();

        if (!$professor) {
            \Log::warning('Professor not found', ['email' => $request->email]);
            throw ValidationException::withMessages([
                'email' => ['Les identifiants sont incorrects.'],
            ]);
        }

        // 2. Vérifier le mot de passe
        if (!Hash::check($request->password, $professor->password)) {
            \Log::warning('Password mismatch', [
                'input' => $request->password,
                'stored_hash' => $professor->password
            ]);
            throw ValidationException::withMessages([
                'email' => ['Les identifiants sont incorrects.'],
            ]);
        }

        // 3. Créer le token
        $token = $professor->createToken('auth_token')->plainTextToken;

        \Log::info('Login success', ['professor_id' => $professor->id]);

        // 4. Préparer la réponse avec TOUTES les informations
        $professorData = [
            'id' => $professor->id,
            'first_name' => $professor->first_name,
            'last_name' => $professor->last_name,
            'email' => $professor->email,
            'grade' => $professor->grade,
            'role' => $professor->role, // ← C'EST ICI LE PROBLÈME !
            'is_director' => $professor->role === 'director', // ← AJOUTEZ CECI
            'is_team_leader' => $professor->isTeamLeader(), // ← AJOUTEZ CECI
            'is_professor' => $professor->role === 'professor', // ← AJOUTEZ CECI
            'full_name' => $professor->full_name, // ← AJOUTEZ CECI
            'formatted_role' => $professor->formatted_role, // ← AJOUTEZ CECI
        ];

        return response()->json([
            'token' => $token,
            'professor' => $professorData // ← Utilisez $professorData au lieu de $professor
        ]);
    }
    

    /**
     * Logout professor (revoke token)
     */
    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Logged out successfully']);
    }

    /**
     * Get authenticated professor data
     */
    public function professor(Request $request): JsonResponse
    {
        return response()->json($request->user());
    }

    /**
     * Check if user is authenticated
     */
    public function checkAuth(Request $request): JsonResponse
    {
        return response()->json(['authenticated' => $request->user() !== null]);
    }

    /**
     * Update professor profile
     */
    public function updateProfile(Request $request): JsonResponse
    {
        $professor = $request->user();
        
        $request->validate([
            'first_name' => 'sometimes|string|max:100',
            'last_name' => 'sometimes|string|max:100',
            'email' => 'sometimes|email|unique:professors,email,' . $professor->id,
            'phone' => 'nullable|string|max:20',
            'departement' => 'nullable|string|max:100',
            'specialty' => 'nullable|string|max:100',
            'grade' => 'sometimes|in:DOCTORANT,DOCTOR',
        ]);

        $professor->update($request->only([
            'first_name', 'last_name', 'email', 'phone', 
            'departement', 'specialty', 'grade'
        ]));

        return response()->json($professor);
    }
}