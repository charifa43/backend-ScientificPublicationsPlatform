<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Professor;
use App\Models\Team;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        // 1. Créer le DIRECTEUR (admin unique)
        $director = Professor::factory()->director()->create();
        
        // 2. Créer des équipes
        $team1 = Team::create([
            'name' => 'Équipe IA et Big Data',
            'field' => 'IA',
            'domain' => 'Intelligence Artificielle',
            'creation_date' => '2020-01-15',
            'team_leader_id' => null,
        ]);
        
        $team2 = Team::create([
            'name' => 'Équipe Maths',
            'field' => 'Maths',
            'domain' => 'Robotique Autonome',
            'creation_date' => '2019-03-20',
            'team_leader_id' => null,
        ]);
        
        // 3. Créer des DOCTORS (notez la majuscule - c'est la valeur de 'grade')
        $doctor1 = Professor::create([
            'first_name' => 'Karim',
            'last_name' => 'Benali',
            'email' => 'k.benali@lab.com',
            'password' => 'password123', // En clair - sera hashé par le mutateur
            'grade' => 'DOCTOR',
            'role' => 'professor',
            'team_id' => $team1->id,
            'specialty' => 'Intelligence Artificielle',
        ]);
        
        $doctor2 = Professor::create([
            'first_name' => 'Fatima',
            'last_name' => 'Zahra',
            'email' => 'f.zahra@lab.com',
            'password' => 'password123',
            'grade' => 'DOCTOR',
            'role' => 'professor',
            'team_id' => $team2->id,
            'specialty' => 'Mathématiques Appliquées',
        ]);
        
        // 4. Assigner des chefs d'équipe
        $team1->update(['team_leader_id' => $doctor1->id]);
        $team2->update(['team_leader_id' => $doctor2->id]);
        
        // 5. Créer des DOCTORANTS (membres)
        Professor::factory()->count(4)->create([
            'team_id' => $team1->id,
            'grade' => 'DOCTORANT',
        ]);
        
        Professor::factory()->count(3)->create([
            'team_id' => $team2->id,
            'grade' => 'DOCTORANT',
        ]);
        
        // 6. Créer des professors sans équipe
        Professor::factory()->count(2)->create([
            'grade' => 'DOCTOR',
            'team_id' => null,
        ]);
        
        Professor::factory()->count(3)->create([
            'grade' => 'DOCTORANT',
            'team_id' => null,
        ]);
    }
}