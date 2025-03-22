<?php

namespace App\Http\Controllers;

use App\Models\Service;
use App\Models\Category;
use App\Models\City;
use App\Models\ServiceView;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class ServiceController extends Controller
{
    /**
     * Afficher la liste des services avec filtres
     */
    public function index(Request $request)
    {
        $query = Service::query()
            ->with(['category', 'city', 'mainImage'])
            ->whereHas('user', function($q) {
                $q->where('status', 'active');
            });

        // Filtrer par mot-clé
        if ($request->has('keyword') && $request->keyword) {
            $keyword = $request->keyword;
            $query->where(function($q) use ($keyword) {
                $q->where('name', 'like', "%{$keyword}%")
                  ->orWhere('description', 'like', "%{$keyword}%");
            });
        }

        // Filtrer par catégorie
        if ($request->has('category_id') && $request->category_id) {
            $query->where('category_id', $request->category_id);
        }

        // Filtrer par ville
        if ($request->has('city_id') && $request->city_id) {
            $query->where('city_id', $request->city_id);
        }

        // Ordonner les résultats
        if ($request->has('sort') && in_array($request->sort, ['name', 'created_at'])) {
            $direction = ($request->has('direction') && $request->direction === 'desc') ? 'desc' : 'asc';
            $query->orderBy($request->sort, $direction);
        } else {
            // Mettre en premier les services mis en avant, puis par date de création
            $query->orderBy('is_featured', 'desc')
                  ->orderBy('created_at', 'desc');
        }

        // Pagination
        $perPage = $request->has('per_page') ? (int)$request->per_page : 12;
        $services = $query->paginate($perPage);

        // Ajouter des informations supplémentaires pour chaque service
        $services->getCollection()->transform(function ($service) {
            $service->avg_rating = $service->averageRating();
            $service->reviews_count = $service->reviews()->count();
            return $service;
        });

        return response()->json($services);
    }

    /**
     * Afficher les services mis en avant
     */
    public function featured()
    {
        $services = Service::with(['category', 'city', 'mainImage'])
            ->whereHas('user', function($q) {
                $q->where('status', 'active');
            })
            ->where('is_featured', true)
            ->limit(6)
            ->get();

        // Ajouter des informations supplémentaires pour chaque service
        $services->transform(function ($service) {
            $service->avg_rating = $service->averageRating();
            $service->reviews_count = $service->reviews()->count();
            return $service;
        });

        return response()->json($services);
    }

    /**
     * Afficher les détails d'un service
     */
    public function show(Request $request, $id)
    {
        $service = Service::with(['category', 'city', 'images', 'user', 'offers', 'reviews.user'])
            ->whereHas('user', function($q) {
                $q->where('status', 'active');
            })
            ->findOrFail($id);

        // Enregistrer la vue (si ce n'est pas le propriétaire du service)
        if (!Auth::check() || Auth::id() !== $service->user_id) {
            ServiceView::create([
                'service_id' => $service->id,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'user_id' => Auth::id()
            ]);
        }

        // Ajouter des informations supplémentaires
        $service->avg_rating = $service->averageRating();
        $service->reviews_count = $service->reviews()->count();

        // Trouver des services similaires
        $similarServices = Service::with(['category', 'city', 'mainImage'])
            ->whereHas('user', function($q) {
                $q->where('status', 'active');
            })
            ->where('id', '!=', $service->id)
            ->where(function($q) use ($service) {
                $q->where('category_id', $service->category_id)
                  ->orWhere('city_id', $service->city_id);
            })
            ->limit(3)
            ->get();

        // Ajouter des informations pour les services similaires
        $similarServices->transform(function ($similar) {
            $similar->avg_rating = $similar->averageRating();
            return $similar;
        });

        $service->similar_services = $similarServices;

        return response()->json($service);
    }
}