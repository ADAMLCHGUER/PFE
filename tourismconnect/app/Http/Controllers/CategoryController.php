<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    /**
     * Afficher la liste des catégories
     */
    public function index()
    {
        $categories = Category::all();
        return response()->json($categories);
    }

    /**
     * Afficher les détails d'une catégorie
     */
    public function show($id)
    {
        $category = Category::findOrFail($id);
        return response()->json($category);
    }
}