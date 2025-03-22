<?php

namespace App\Http\Controllers\Prestataire;

use App\Http\Controllers\Controller;
use App\Models\Service;
use App\Models\ServiceImage;
use App\Models\Category;
use App\Models\City;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;

class PrestataireProfileController extends Controller
{
    /**
     * Afficher les détails du profil du service
     */
    public function show()
    {
        $user = Auth::user();
        $service = Service::with(['category', 'city', 'images'])
            ->where('user_id', $user->id)
            ->firstOrFail();

        // Récupérer les catégories et villes pour les menus déroulants
        $categories = Category::all();
        $cities = City::all();

        return response()->json([
            'service' => $service,
            'categories' => $categories,
            'cities' => $cities
        ]);
    }

    /**
     * Mettre à jour le profil du service
     */
    public function update(Request $request)
    {
        $user = Auth::user();
        $service = Service::where('user_id', $user->id)->firstOrFail();

        // Valider les données
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'category_id' => 'required|exists:categories,id',
            'city_id' => 'required|exists:cities,id',
            'address' => 'required|string|max:255',
            'contact' => 'required|string|max:255',
            'website' => 'nullable|url|max:255',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Les données fournies ne sont pas valides.',
                'errors' => $validator->errors()
            ], 422);
        }

        // Mettre à jour le service
        $service->update([
            'name' => $request->name,
            'description' => $request->description,
            'category_id' => $request->category_id,
            'city_id' => $request->city_id,
            'address' => $request->address,
            'contact' => $request->contact,
            'website' => $request->website,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
        ]);

        return response()->json([
            'message' => 'Profil mis à jour avec succès',
            'service' => $service
        ]);
    }

    /**
     * Télécharger une image pour le service
     */
    public function uploadImage(Request $request)
    {
        $user = Auth::user();
        $service = Service::where('user_id', $user->id)->firstOrFail();

        // Valider l'image
        $validator = Validator::make($request->all(), [
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'L\'image fournie n\'est pas valide.',
                'errors' => $validator->errors()
            ], 422);
        }

        // Vérifier le nombre maximum d'images selon le type d'abonnement
        $imagesCount = ServiceImage::where('service_id', $service->id)->count();
        $maxImages = 5; // Par défaut
        
        if ($service->subscription_type === 'premium') {
            $maxImages = 10;
        } elseif ($service->subscription_type === 'pro') {
            $maxImages = 20;
        }

        if ($imagesCount >= $maxImages) {
            return response()->json([
                'message' => "Vous avez atteint le nombre maximum d'images autorisé pour votre abonnement ({$maxImages})."
            ], 422);
        }

        // Traiter et enregistrer l'image
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = time() . '_' . $service->id . '.' . $image->getClientOriginalExtension();
            
            // Redimensionner l'image si nécessaire
            $img = Image::make($image->getRealPath());
            $img->resize(800, null, function ($constraint) {
                $constraint->aspectRatio();
                $constraint->upsize();
            });
            
            // Enregistrer l'image
            $path = 'public/services/' . $service->id;
            if (!Storage::exists($path)) {
                Storage::makeDirectory($path);
            }
            
            $img->save(storage_path('app/' . $path . '/' . $imageName));
            
            // Créer un enregistrement dans la base de données
            $isMain = $imagesCount === 0; // Première image = image principale
            $serviceImage = ServiceImage::create([
                'service_id' => $service->id,
                'image_path' => Storage::url($path . '/' . $imageName),
                'is_main' => $isMain
            ]);

            return response()->json([
                'message' => 'Image téléchargée avec succès',
                'image' => $serviceImage
            ], 201);
        }

        return response()->json([
            'message' => 'Aucune image n\'a été fournie'
        ], 422);
    }

    /**
     * Supprimer une image du service
     */
    public function deleteImage($id)
    {
        $user = Auth::user();
        $service = Service::where('user_id', $user->id)->firstOrFail();
        
        $image = ServiceImage::where('service_id', $service->id)
            ->where('id', $id)
            ->firstOrFail();
        
        // Supprimer le fichier
        $filePath = str_replace('/storage', 'public', $image->image_path);
        if (Storage::exists($filePath)) {
            Storage::delete($filePath);
        }
        
        // Si c'était l'image principale, définir une autre image comme principale
        if ($image->is_main) {
            $nextImage = ServiceImage::where('service_id', $service->id)
                ->where('id', '!=', $id)
                ->first();
                
            if ($nextImage) {
                $nextImage->update(['is_main' => true]);
            }
        }
        
        $image->delete();
        
        return response()->json([
            'message' => 'Image supprimée avec succès'
        ]);
    }

    /**
     * Définir une image comme image principale
     */
    public function setMainImage($id)
    {
        $user = Auth::user();
        $service = Service::where('user_id', $user->id)->firstOrFail();
        
        // Réinitialiser toutes les images
        ServiceImage::where('service_id', $service->id)
            ->update(['is_main' => false]);
        
        // Définir la nouvelle image principale
        $image = ServiceImage::where('service_id', $service->id)
            ->where('id', $id)
            ->firstOrFail();
            
        $image->update(['is_main' => true]);
        
        return response()->json([
            'message' => 'Image principale définie avec succès',
            'image' => $image
        ]);
    }
}