<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePublicationRequest extends FormRequest
{
    public function authorize()
    {
        // Tout utilisateur connecté peut créer des publications
        return $this->user() !== null;
    }

    public function rules()
    {
        return [
            'title' => 'required|string|max:255',
            'abstract' => 'required|string',
            'keywords' => 'required|array|min:1',
            'keywords.*' => 'string|max:50',
            'publication_date' => 'required|date',
            'type' => 'required|in:article,conference,book,chapter,thesis',
            'journal_name' => 'nullable|string|max:255',
            'conference_name' => 'nullable|string|max:255',
            'publisher' => 'nullable|string|max:255',
            'doi' => 'nullable|string|max:100',
            'external_authors' => 'nullable|array',
            'external_authors.*.name' => 'string|max:100',
            'external_authors.*.institution' => 'string|max:100',
            'professor_ids' => 'required|array|min:1',
            'professor_ids.*' => 'exists:professors,id',
        ];
    }
}