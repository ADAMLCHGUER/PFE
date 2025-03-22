<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\City;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AdminCityController extends Controller
{
    /**
     * Afficher la liste des villes
     */
    public function index()
    {
        $cities = City::withCount('services')->get();
        return response()->json($cities);
    }

    /**
     * Stocker une nouvelle ville
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:cities',
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'message' => 'Les données fournies ne sont pas valides.',
                'errors' => $validator->errors()
            ], 422);
        }
        
        $city = City::create([
            'name' => $request->name,
        ]);
        
        return response()->json([
            'message' => 'Ville créée avec succès',
            'city' => $city
        ], 201);
    }

    /**
     * Afficher les détails d'une ville
     */
    public function show($id)
    {
        $city = City::withCount('services')->findOrFail($id);
        return response()->json($city);
    }

    /**
     * Mettre à jour une ville
     */
    public function update(Request $request, $id)
    {
        $city = City::findOrFail($id);
        
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:cities,name,' . $id,
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'message' => 'Les données fournies ne sont pas valides.',
                'errors' => $validator->errors()
            ], 422);
        }
        
        $city->update([
            'name' => $request->name,
        ]);
        
        return response()->json([
            'message' => 'Ville mise à jour avec succès',
            'city' => $city
        ]);
    }

    /**
     * Supprimer une ville
     */
    public function destroy($id)
    {
        $city = City::findOrFail($id);
        
        // Vérifier si des services sont associés à cette ville
        $servicesCount = $city->services()->count();
        if ($servicesCount > 0) {
            return response()->json([
                'message' => 'Impossible de supprimer cette ville car elle est associée à ' . $servicesCount . ' service(s).'
            ], 422);
        }
        
        $city->delete();
        
        return response()->json([
            'message' => 'Ville supprimée avec succès'
        ]);
    }
}