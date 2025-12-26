<?php

use Illuminate\Support\Facades\Route;

// Controllers
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Api\ProfessorController;
use App\Http\Controllers\Api\PublicationController;
use App\Http\Controllers\Api\TeamController;
use App\Http\Controllers\Api\DashboardController;

/*
|--------------------------------------------------------------------------
| PUBLIC ROUTES (NO AUTH REQUIRED)
|--------------------------------------------------------------------------
*/

// Auth
Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);

// Grades
Route::get('/grades', function () {
    return response()->json([
        'DOCTORANT' => 'Doctorant',
        'DOCTOR' => 'Doctor',
    ]);
});

// Professors (public)
Route::get('/professors', [ProfessorController::class, 'index']);

// Teams (public)
Route::get('/teams', [TeamController::class, 'index']);
Route::get('/teams/{id}', [TeamController::class, 'show']);

/*
|--------------------------------------------------------------------------
| PUBLICATIONS (PUBLIC READ)
|--------------------------------------------------------------------------
| IMPORTANT : index & show sont publics
*/

Route::get('/publications', [PublicationController::class, 'index']);   // ✅ LISTE PAGINÉE
Route::get('/publications/{id}', [PublicationController::class, 'show']);

/*
|--------------------------------------------------------------------------
| PROTECTED ROUTES (AUTH REQUIRED)
|--------------------------------------------------------------------------
*/

Route::middleware('auth:professor-api')->group(function () {

    /*
    |--------------------------------------------------------------------------
    | AUTH / PROFILE
    |--------------------------------------------------------------------------
    */
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/professor', [AuthController::class, 'professor']);
    Route::get('/check', [AuthController::class, 'checkAuth']);
    Route::put('/profile', [AuthController::class, 'updateProfile']);

    /*
    |--------------------------------------------------------------------------
    | PROFESSORS (ADMIN)
    |--------------------------------------------------------------------------
    */
    Route::apiResource('professors', ProfessorController::class)
        ->except(['index'])
        ->middleware('role:director');

    /*
    |--------------------------------------------------------------------------
    | TEAMS (PROTECTED)
    |--------------------------------------------------------------------------
    */
    Route::prefix('teams')->group(function () {
        Route::post('/', [TeamController::class, 'store']);
        Route::put('/{id}', [TeamController::class, 'update']);
        Route::delete('/{id}', [TeamController::class, 'destroy']);
        Route::get('/{id}/members', [TeamController::class, 'getMembers']);
        Route::post('/{id}/members', [TeamController::class, 'addMember']);
        Route::delete('/{id}/members/{professorId}', [TeamController::class, 'removeMember']);
    });

    /*
    |--------------------------------------------------------------------------
    | PUBLICATIONS (PROTECTED ACTIONS)
    |--------------------------------------------------------------------------
    */
    Route::post('/publications', [PublicationController::class, 'store']);
    Route::put('/publications/{id}', [PublicationController::class, 'update']);
    Route::delete('/publications/{id}', [PublicationController::class, 'destroy']);

    Route::get('/publications/search', [PublicationController::class, 'search']);
    Route::get('/my-publications', [PublicationController::class, 'myPublications']);
    Route::get('/professor/domain-publications', [PublicationController::class, 'domainPublications']);
    Route::get('/team-publications', [PublicationController::class, 'teamPublications']);

    /*
    |--------------------------------------------------------------------------
    | DASHBOARD
    |--------------------------------------------------------------------------
    */
    Route::get('/dashboard/stats', [DashboardController::class, 'stats']);
    Route::get('/dashboard/recent-publications', [DashboardController::class, 'recentPublications']);
});
