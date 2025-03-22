<?php

namespace App\Http\Controllers\Prestataire;

use App\Http\Controllers\Controller;
use App\Models\Offer;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class PrestataireOfferController extends Controller
{
    /**
     * Afficher la liste des offres du prestataire
     */
    public function index()
    {
        $user = Auth::user();
        $service = Service::where('user_id', $user->id)->firstOrFail();
        
        $offers = Offer::where('service_id', $service->id)
            ->orderBy('created_at', 'desc')
            ->get();
            
        return response()->json($offers);
    }

    /**
     * Afficher les détails d'une offre
     */
    public function show($id)
    {
        $user = Auth::user();
        $service = Service::where('user_id', $user->id)->firstOrFail();
        
        $offer = Offer::where('service_id', $service->id)
            ->where('id', $id)
            ->firstOrFail();
            
        return response()->json($offer);
    }

    /**
     * Créer une nouvelle offre
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        $service = Service::where('user_id', $user->id)->firstOrFail();
        
        // Vérifier le nombre maximum d'offres selon le type d'abonnement
        $offersCount = Offer::where('service_id', $service->id)->count();
        $maxOffers = 3; // Par défaut (basic)
        
        if ($service->subscription_type === 'premium' || $service->subscription_type === 'pro') {
            $maxOffers = 100; // Illimité pour premium et pro
        }
        
        if ($offersCount >= $maxOffers) {
            return response()->json([
                'message' => "Vous avez atteint le nombre maximum d'offres autorisé pour votre abonnement ({$maxOffers})."
            ], 422);
        }
        
        // Valider les données
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'nullable|numeric|min:0',
            'start_date' => 'nullable|date|after_or_equal:today',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'is_active' => 'boolean',
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'message' => 'Les données fournies ne sont pas valides.',
                'errors' => $validator->errors()
            ], 422);
        }
        
        // Créer l'offre
        $offer = Offer::create([
            'service_id' => $service->id,
            'title' => $request->title,
            'description' => $request->description,
            'price' => $request->price,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'is_active' => $request->is_active ?? true,
        ]);
        
        return response()->json([
            'message' => 'Offre créée avec succès',
            'offer' => $offer
        ], 201);
    }

    /**
     * Mettre à jour une offre
     */
    public function update(Request $request, $id)
    {
        $user = Auth::user();
        $service = Service::where('user_id', $user->id)->firstOrFail();
        
        $offer = Offer::where('service_id', $service->id)
            ->where('id', $id)
            ->firstOrFail();
        
        // Valider les données
        $validator = Validator::make($request->all(), [
        'title' => 'required|string|max:255',
           'description' => 'required|string',
           'price' => 'nullable|numeric|min:0',
           'start_date' => 'nullable|date',
           'end_date' => 'nullable|date|after_or_equal:start_date',
           'is_active' => 'boolean',
       ]);
       
       if ($validator->fails()) {
           return response()->json([
               'message' => 'Les données fournies ne sont pas valides.',
               'errors' => $validator->errors()
           ], 422);
       }
       
       // Mettre à jour l'offre
       $offer->update([
           'title' => $request->title,
           'description' => $request->description,
           'price' => $request->price,
           'start_date' => $request->start_date,
           'end_date' => $request->end_date,
           'is_active' => $request->is_active ?? $offer->is_active,
       ]);
       
       return response()->json([
           'message' => 'Offre mise à jour avec succès',
           'offer' => $offer
       ]);
   }

   /**
    * Supprimer une offre
    */
   public function destroy($id)
   {
       $user = Auth::user();
       $service = Service::where('user_id', $user->id)->firstOrFail();
       
       $offer = Offer::where('service_id', $service->id)
           ->where('id', $id)
           ->firstOrFail();
       
       $offer->delete();
       
       return response()->json([
           'message' => 'Offre supprimée avec succès'
       ]);
   }
}