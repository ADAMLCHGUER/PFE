<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Auth\Events\Registered;
use Illuminate\Validation\Rules\Password;
use App\Notifications\PrestataireRegistered;
use App\Notifications\PrestataireApproved;
use App\Notifications\PrestataireRejected;

class AuthController extends Controller
{
    /**
     * Authentification d'un utilisateur
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            
            $user = Auth::user();
            
            // Vérifier si le prestataire a été approuvé
            if ($user->type === 'prestataire' && $user->status === 'pending') {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();
                
                return response()->json([
                    'message' => 'Votre compte prestataire est en attente d\'approbation.'
                ], 403);
            }
            
            return response()->json([
                'user' => $user,
                'message' => 'Authentification réussie'
            ]);
        }

        return response()->json([
            'message' => 'Les identifiants fournis ne correspondent pas à nos enregistrements.'
        ], 401);
    }

    /**
     * Inscription d'un utilisateur régulier
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Les données fournies ne sont pas valides.',
                'errors' => $validator->errors()
            ], 422);
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'type' => 'touriste',
            'status' => 'active'
        ]);

        event(new Registered($user));

        Auth::login($user);

        return response()->json([
            'user' => $user,
            'message' => 'Inscription réussie'
        ], 201);
    }

    /**
     * Inscription d'un prestataire (première étape)
     */
    public function registerPrestataire(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Password::defaults()],
            'service_type' => ['required', 'exists:categories,id'],
            'contact' => ['required', 'string', 'max:255'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Les données fournies ne sont pas valides.',
                'errors' => $validator->errors()
            ], 422);
        }

        // Créer l'utilisateur avec statut 'pending'
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'type' => 'prestataire',
            'status' => 'pending'
        ]);

        // Créer un service de base associé
        $service = Service::create([
            'user_id' => $user->id,
            'category_id' => $request->service_type,
            'name' => $request->name,
            'contact' => $request->contact,
            'description' => $request->description ?? 'Description à compléter',
            'city_id' => $request->city_id ?? 1,  // Ville par défaut
            'subscription_type' => 'basic',
            'address' => $request->address ?? 'Adresse à compléter',
        ]);

        event(new Registered($user));

        // Notifier les administrateurs de la nouvelle inscription
        $admins = User::where('type', 'admin')->get();
        foreach ($admins as $admin) {
            $admin->notify(new PrestataireRegistered($user));
        }

        return response()->json([
            'message' => 'Votre demande d\'inscription a été enregistrée. Un administrateur examinera votre profil et vous recevrez un email dès que votre compte sera activé.',
        ], 201);
    }

    /**
     * Approbation d'un compte prestataire
     */
    public function approvePrestataire(Request $request, $id)
    {
        $user = User::findOrFail($id);
        
        if ($user->type !== 'prestataire' || $user->status !== 'pending') {
            return response()->json([
                'message' => 'Cet utilisateur n\'est pas un prestataire en attente d\'approbation'
            ], 400);
        }
        
        $user->status = 'active';
        $user->save();
        
        // Notifier le prestataire que son compte a été approuvé
        $user->notify(new PrestataireApproved());
        
        return response()->json([
            'message' => 'Le prestataire a été approuvé avec succès'
        ]);
    }

    /**
     * Rejet d'un compte prestataire
     */
    public function rejectPrestataire(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'reason' => ['required', 'string', 'max:1000'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Veuillez fournir une raison pour le rejet',
                'errors' => $validator->errors()
            ], 422);
        }

        $user = User::findOrFail($id);
        
        if ($user->type !== 'prestataire' || $user->status !== 'pending') {
            return response()->json([
                'message' => 'Cet utilisateur n\'est pas un prestataire en attente d\'approbation'
            ], 400);
        }
        
        $user->status = 'rejected';
        $user->save();
        
        // Notifier le prestataire que son compte a été rejeté
        $user->notify(new PrestataireRejected($request->reason));
        
        return response()->json([
            'message' => 'Le prestataire a été rejeté avec succès'
        ]);
    }

    /**
     * Récupération des informations de l'utilisateur connecté
     */
    public function user(Request $request)
    {
        $user = $request->user();
        
        // Charger le service si l'utilisateur est un prestataire
        if ($user && $user->type === 'prestataire') {
            $user->load('service');
        }
        
        return response()->json($user);
    }

    /**
     * Déconnexion
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return response()->json(['message' => 'Déconnexion réussie']);
    }
}