<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class AdminVerificationController extends Controller
{
    /**
     * Afficher la liste des prestataires en attente de validation
     */
    public function index(Request $request)
    {
        $query = User::with(['service.category', 'service.city'])
            ->where('type', 'prestataire');
        
        // Filtrer par statut si spécifié
        if ($request->has('status') && in_array($request->status, ['pending', 'active', 'rejected'])) {
            $query->where('status', $request->status);
        } else {
            // Par défaut, afficher les comptes en attente
            $query->where('status', 'pending');
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
        $perPage = $request->per_page ?? 10;
        $users = $query->paginate($perPage);
        
        return response()->json($users);
    }

    /**
     * Compter le nombre de prestataires en attente de validation
     */
    public function pendingCount()
    {
        $count = User::where('type', 'prestataire')
            ->where('status', 'pending')
            ->count();
            
        return response()->json([
            'count' => $count
        ]);
    }
}