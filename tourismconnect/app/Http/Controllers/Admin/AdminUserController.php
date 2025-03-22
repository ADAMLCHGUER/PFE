<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;

class AdminUserController extends Controller
{
    /**
     * Afficher la liste des utilisateurs
     */
    public function index(Request $request)
    {
        $query = User::query();
        
        // Filtrage par type
        if ($request->has('type') && in_array($request->type, ['admin', 'prestataire', 'touriste'])) {
            $query->where('type', $request->type);
        }
        
        // Filtrage par statut
        if ($request->has('status') && in_array($request->status, ['active', 'pending', 'rejected'])) {
            $query->where('status', $request->status);
        }
        
        // Recherche par nom ou email
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }
        
        // Tri
        $sortField = $request->sort ?? 'created_at';
        $sortDirection = $request->direction ?? 'desc';
        $query->orderBy($sortField, $sortDirection);
        
        // Pagination
        $perPage = $request->per_page ?? 15;
        $users = $query->paginate($perPage);
        
        return response()->json($users);
    }

    /**
     * Afficher les détails d'un utilisateur
     */
    public function show($id)
    {
        $user = User::with(['service.category', 'service.city'])->findOrFail($id);
        return response()->json($user);
    }

    /**
     * Mettre à jour un utilisateur
     */
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);
        
        // Valider les données
        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|required|string|max:255',
            'email' => 'sometimes|required|email|max:255|unique:users,email,' . $id,
            'password' => 'sometimes|nullable|string|min:8',
            'type' => 'sometimes|required|in:admin,prestataire,touriste',
            'status' => 'sometimes|required|in:active,pending,rejected',
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'message' => 'Les données fournies ne sont pas valides.',
                'errors' => $validator->errors()
            ], 422);
        }
        
        // Mettre à jour les champs
        if ($request->has('name')) {
            $user->name = $request->name;
        }
        
        if ($request->has('email')) {
            $user->email = $request->email;
        }
        
        if ($request->has('password') && $request->password) {
            $user->password = Hash::make($request->password);
        }
        
        if ($request->has('type')) {
            $user->type = $request->type;
        }
        
        if ($request->has('status')) {
            $user->status = $request->status;
        }
        
        $user->save();
        
        return response()->json([
            'message' => 'Utilisateur mis à jour avec succès',
            'user' => $user
        ]);
    }

    /**
     * Supprimer un utilisateur
     */
    public function destroy($id)
    {
        $user = User::findOrFail($id);
        
        // Empêcher la suppression du compte administrateur principal
        if ($user->id === 1 && $user->type === 'admin') {
            return response()->json([
                'message' => 'Impossible de supprimer le compte administrateur principal'
            ], 403);
        }
        
        $user->delete();
        
        return response()->json([
            'message' => 'Utilisateur supprimé avec succès'
        ]);
    }
}