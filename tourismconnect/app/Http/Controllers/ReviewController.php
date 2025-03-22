<?php

namespace App\Http\Controllers;

use App\Models\Review;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class ReviewController extends Controller
{
    /**
     * Ajouter un avis sur un service
     */
    public function store(Request $request, $id)
    {
        // Vérifier si le service existe
        $service = Service::findOrFail($id);

        // Vérifier si l'utilisateur a déjà laissé un avis
        $existingReview = Review::where('user_id', Auth::id())
            ->where('service_id', $id)
            ->first();

        if ($existingReview) {
            return response()->json([
                'message' => 'Vous avez déjà laissé un avis pour ce service.'
            ], 422);
        }

        // Valider les données
        $validator = Validator::make($request->all(), [
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'required|string|min:10|max:1000',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Les données fournies ne sont pas valides.',
                'errors' => $validator->errors()
            ], 422);
        }

        // Créer l'avis
        $review = Review::create([
            'user_id' => Auth::id(),
            'service_id' => $id,
            'rating' => $request->rating,
            'comment' => $request->comment,
        ]);

        return response()->json([
            'message' => 'Votre avis a été ajouté avec succès.',
            'review' => $review
        ], 201);
    }

    /**
     * Modifier un avis
     */
    public function update(Request $request, $id)
    {
        // Trouver l'avis
        $review = Review::findOrFail($id);

        // Vérifier si l'utilisateur est le propriétaire de l'avis
        if ($review->user_id !== Auth::id()) {
            return response()->json([
                'message' => 'Vous n\'êtes pas autorisé à modifier cet avis.'
            ], 403);
        }

        // Valider les données
        $validator = Validator::make($request->all(), [
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'required|string|min:10|max:1000',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Les données fournies ne sont pas valides.',
                'errors' => $validator->errors()
            ], 422);
        }

        // Mettre à jour l'avis
        $review->update([
            'rating' => $request->rating,
            'comment' => $request->comment,
        ]);

        return response()->json([
            'message' => 'Votre avis a été mis à jour avec succès.',
            'review' => $review
        ]);
    }

    /**
     * Supprimer un avis
     */
    public function destroy($id)
    {
        // Trouver l'avis
        $review = Review::findOrFail($id);

        // Vérifier si l'utilisateur est le propriétaire de l'avis ou un administrateur
        if ($review->user_id !== Auth::id() && Auth::user()->type !== 'admin') {
            return response()->json([
                'message' => 'Vous n\'êtes pas autorisé à supprimer cet avis.'
            ], 403);
        }

        // Supprimer l'avis
        $review->delete();

        return response()->json([
            'message' => 'L\'avis a été supprimé avec succès.'
        ]);
    }
}