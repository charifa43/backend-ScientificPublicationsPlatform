<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreProfessorRequest extends FormRequest
{
    public function authorize()
    {
        // Seul un directeur peut créer/modifier des professeurs
        return $this->user() && $this->user()->role === 'director';
    }

    public function rules()
    {
        $professorId = $this->route('professor') ? $this->route('professor')->id : null;

        return [
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'email' => 'required|email|unique:professors,email,' . $professorId,
            'password' => $professorId ? 'sometimes|string|min:8' : 'required|string|min:8',
            'phone' => 'nullable|string|max:20',
            'departement' => 'nullable|string|max:100',
            'specialty' => 'nullable|string|max:100',
            'grade' => 'required|in:DOCTORANT,DOCTOR',
            'role' => 'required|in:professor,director',
            'team_id' => 'nullable|exists:teams,id',
        ];
    }

    public function messages()
    {
        return [
            'email.unique' => 'Cet email est déjà utilisé.',
            'password.min' => 'Le mot de passe doit contenir au moins 8 caractères.',
            'grade.in' => 'Le grade doit être DOCTORANT ou DOCTOR.',
            'role.in' => 'Le rôle doit être professor ou director.',
            'team_id.exists' => "L'équipe sélectionnée n'existe pas.",
        ];
    }
}