<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Team;
use App\Models\Professor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TeamController extends Controller
{
    /**
     * Display a listing of all teams.
     * GET /api/teams
     */
    public function index()
    {
        try {
            $teams = Team::with(['leader', 'members'])->get();
            
            return response()->json([
                'success' => true,
                'data' => $teams,
                'message' => 'Teams retrieved successfully',
                'count' => $teams->count()
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve teams',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store a newly created team.
     * POST /api/teams
     */
    public function store(Request $request)
    {
        // Validation des données
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:teams,name',
            'field' => 'nullable|in:Maths,Informatique,IA,Physique,Génie Civil',
            'domain' => 'nullable|string|max:100',
            'creation_date' => 'nullable|date',
            'description' => 'nullable|string',
            'team_leader_id' => 'nullable|exists:professors,id'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation errors',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $team = Team::create($request->all());
            
            // Charger les relations
            $team->load(['leader', 'members']);
            
            return response()->json([
                'success' => true,
                'data' => $team,
                'message' => 'Team created successfully'
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create team',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified team.
     * GET /api/teams/{id}
     */
    public function show($id)
    {
        try {
            $team = Team::with(['leader', 'members'])->find($id);
            
            if (!$team) {
                return response()->json([
                    'success' => false,
                    'message' => 'Team not found'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => $team,
                'message' => 'Team retrieved successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve team',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update the specified team.
     * PUT/PATCH /api/teams/{id}
     */
    public function update(Request $request, $id)
    {
        try {
            $team = Team::find($id);
            
            if (!$team) {
                return response()->json([
                    'success' => false,
                    'message' => 'Team not found'
                ], 404);
            }

            // Validation
            $validator = Validator::make($request->all(), [
                'name' => 'sometimes|required|string|max:255|unique:teams,name,' . $id,
                'field' => 'nullable|in:Maths,Informatique,IA,Physique,Génie Civil',
                'domain' => 'nullable|string|max:100',
                'creation_date' => 'nullable|date',
                'description' => 'nullable|string',
                'team_leader_id' => 'nullable|exists:professors,id'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation errors',
                    'errors' => $validator->errors()
                ], 422);
            }

            $team->update($request->all());
            $team->load(['leader', 'members']);

            return response()->json([
                'success' => true,
                'data' => $team,
                'message' => 'Team updated successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update team',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified team.
     * DELETE /api/teams/{id}
     */
    public function destroy($id)
    {
        try {
            $team = Team::find($id);
            
            if (!$team) {
                return response()->json([
                    'success' => false,
                    'message' => 'Team not found'
                ], 404);
            }

            // Vérifier si l'équipe a des membres
            if ($team->members()->count() > 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot delete team that has members. Remove members first.'
                ], 400);
            }

            $team->delete();

            return response()->json([
                'success' => true,
                'message' => 'Team deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete team',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Add a professor to a team (as member)
     * POST /api/teams/{teamId}/members/{professorId}
     */
    public function addMember($teamId, $professorId)
    {
        try {
            $team = Team::find($teamId);
            $professor = Professor::find($professorId);

            if (!$team) {
                return response()->json([
                    'success' => false,
                    'message' => 'Team not found'
                ], 404);
            }

            if (!$professor) {
                return response()->json([
                    'success' => false,
                    'message' => 'Professor not found'
                ], 404);
            }

            // Ajouter le professeur à l'équipe
            $professor->team_id = $teamId;
            $professor->save();

            return response()->json([
                'success' => true,
                'message' => 'Professor added to team successfully',
                'data' => [
                    'team' => $team->load('members'),
                    'professor' => $professor
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to add member',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove a professor from a team
     * DELETE /api/teams/{teamId}/members/{professorId}
     */
    public function removeMember($teamId, $professorId)
    {
        try {
            $professor = Professor::where('id', $professorId)
                                 ->where('team_id', $teamId)
                                 ->first();

            if (!$professor) {
                return response()->json([
                    'success' => false,
                    'message' => 'Professor not found in this team'
                ], 404);
            }

            $professor->team_id = null;
            $professor->save();

            return response()->json([
                'success' => true,
                'message' => 'Professor removed from team successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to remove member',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get all members of a specific team
     * GET /api/teams/{id}/members
     */
    public function getMembers($id)
    {
        try {
            $team = Team::with('members')->find($id);
            
            if (!$team) {
                return response()->json([
                    'success' => false,
                    'message' => 'Team not found'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => $team->members,
                'message' => 'Team members retrieved successfully',
                'count' => $team->members->count()
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve team members',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}