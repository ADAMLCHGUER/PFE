<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AdminCategoryController extends Controller
{
    /**
     * Afficher la liste des catégories
     */
    public function index()
    {
        $categories = Category::withCount('services')->get();
        return response()->json($categories);
    }

    /**
     * Stocker une nouvelle catégorie
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:categories',
            'icon' => 'nullable|string|max:255',
            'description' => 'nullable|string',
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'message' => 'Les données fournies ne sont pas valides.',
                'errors' => $validator->errors()
            ], 422);
        }
        
        $category = Category::create([
            'name' => $request->name,
            'icon' => $request->icon,
            'description' => $request->description,
        ]);
        
        return response()->json([
            'message' => 'Catégorie créée avec succès',
            'category' => $category
        ], 201);
    }

    /**
     * Afficher les détails d'une catégorie
     */
    public function show($id)
    {
        $category = Category::withCount('services')->findOrFail($id);
        return response()->json($category);
    }

    /**
     * Mettre à jour une catégorie
     */
    public function update(Request $request, $id)
    {
        $category = Category::findOrFail($id);
        
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:categories,name,' . $id,
            'icon' => 'nullable|string|max:255',
            'description' => 'nullable|string',
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'message' => 'Les données fournies ne sont pas valides.',
                'errors' => $validator->errors()
            ], 422);
        }
        
        $category->update([
            'name' => $request->name,
            'icon' => $request->icon,
            'description' => $request->description,
        ]);
        
        return response()->json([
            'message' => 'Catégorie mise à jour avec succès',
            'category' => $category
        ]);
    }

    /**
     * Supprimer une catégorie
     */
    public function destroy($id)
    {
        $category = Category::findOrFail($id);
        
        // Vérifier si des services sont associés à cette catégorie
        $servicesCount = $category->services()->count();
        if ($servicesCount > 0) {
            return response()->json([
                'message' => 'Impossible de supprimer cette catégorie car elle est associée à ' . $servicesCount . ' service(s).'
            ], 422);
        }
        
        $category->delete();
        
        return response()->json([
            'message' => 'Catégorie supprimée avec succès'
        ]);
    }
}