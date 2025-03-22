<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PrestaireRequest extends FormRequest
{
    public function authorize()
    {
        // Seuls les utilisateurs connectés avec backpack peuvent accéder à cette action
        return backpack_auth()->check();
    }

    public function rules()
    {
        return [
            'nom' => 'required|min:2|max:255',
            'email' => 'required|email|unique:prestataires,email,' . $this->id,
            'telephone' => 'nullable|string|max:20',
            'description' => 'nullable|string',
            'adresse' => 'nullable|string|max:255',
            'ville' => 'nullable|string|max:100',
            'pays' => 'nullable|string|max:100',
            'code_postal' => 'nullable|string|max:20',
            'site_web' => 'nullable|url|max:255',
            'actif' => 'boolean',
            'image' => 'nullable|image|max:2048',
        ];
    }

    public function attributes()
    {
        return [
            'nom' => 'Nom',
            'email' => 'Adresse e-mail',
            'telephone' => 'Téléphone',
            'description' => 'Description',
            'adresse' => 'Adresse',
            'ville' => 'Ville',
            'pays' => 'Pays',
            'code_postal' => 'Code postal',
            'site_web' => 'Site web',
            'actif' => 'Actif',
            'image' => 'Image',
        ];
    }
}