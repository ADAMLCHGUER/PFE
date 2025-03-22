<?php

namespace App\Http\Controllers;

use App\Models\City;
use Illuminate\Http\Request;

class CityController extends Controller
{
    /**
     * Afficher la liste des villes
     */
    public function index()
    {
        $cities = City::all();
        return response()->json($cities);
    }

    /**
     * Afficher les détails d'une ville
     */
    public function show($id)
    {
        $city = City::findOrFail($id);
        return response()->json($city);
    }
}