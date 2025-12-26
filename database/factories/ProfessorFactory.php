<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

class ProfessorFactory extends Factory
{
    public function definition()
    {
        return [
            'first_name' => $this->faker->firstName(),
            'last_name' => $this->faker->lastName(),
            'email' => $this->faker->unique()->safeEmail(),
            'phone' => $this->faker->phoneNumber(),
            'password' => Hash::make('password'),
            'departement' => $this->faker->randomElement(['Computer Science', 'Mathematics', 'Physics', 'Engineering']),
            'specialty' => $this->faker->jobTitle(),
            'grade' => $this->faker->randomElement(['DOCTORANT', 'DOCTOR']), // ← SEULEMENT 2 VALEURS
            'role' => 'professor', // ← Par défaut simple professeur
            'team_id' => null,
            'email_verified_at' => now(),
        ];
    }
    
    
    // Le directeur (unique admin)
    public function director()
    {
        return $this->state(function (array $attributes) {
            return [
                'first_name' => 'Directeur',
                'last_name' => 'Labo',
                'email' => 'directeur@lab.com',
                'password' => Hash::make('directeur123'),
                'grade' => 'DOCTOR', // Le directeur est un DOCTOR
                'role' => 'director', // ← Rôle directeur = admin
                'departement' => 'Direction',
                'specialty' => 'Management',
            ];
        });
    }
   
}
