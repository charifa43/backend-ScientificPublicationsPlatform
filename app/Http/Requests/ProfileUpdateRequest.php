<?php

namespace App\Http\Requests;

use App\Models\Professor;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProfileUpdateRequest extends FormRequest
{
    public function rules(): array
    {
        $professorId = $this->user()->id;
        
        return [
            'first_name' => ['sometimes', 'string', 'max:100'],
            'last_name' => ['sometimes', 'string', 'max:100'],
            'email' => [
                'sometimes', 
                'email', 
                'max:255', 
                Rule::unique('professors')->ignore($professorId) // ← Table professors
            ],
            'phone' => ['nullable', 'string', 'max:20'],
            'departement' => ['nullable', 'string', 'max:100'],
            'specialty' => ['nullable', 'string', 'max:200'],
            'grade' => ['nullable', 'in:DOCTORANT,DOCTOR'],
        ];
    }
    
    /**
     * Messages de validation personnalisés
     */
    public function messages(): array
    {
        return [
            'first_name.required' => 'Le prénom est obligatoire',
            'last_name.required' => 'Le nom est obligatoire',
            'email.required' => 'L\'email est obligatoire',
            'email.unique' => 'Cet email est déjà utilisé',
            'team_id.exists' => 'L\'équipe sélectionnée n\'existe pas',
        ];
    }
}