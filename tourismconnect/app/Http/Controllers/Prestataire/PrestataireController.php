<?php

namespace App\Http\Controllers\Prestataire;

use App\Http\Controllers\Controller;
use App\Models\Service;
use App\Models\ServiceView;
use App\Models\Review;
use App\Models\Offer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class PrestataireController extends Controller
{
    /**
     * Récupérer les statistiques pour le tableau de bord
     */
    public function dashboardStats()
    {
        $user = Auth::user();
        $service = $user->service;

        if (!$service) {
            return response()->json([
                'message' => 'Aucun service trouvé pour ce prestataire'
            ], 404);
        }

        // Nombre de vues des 30 derniers jours
        $viewsCount30Days = ServiceView::where('service_id', $service->id)
            ->where('created_at', '>=', Carbon::now()->subDays(30))
            ->count();

        // Nombre d'offres actives
        $activeOffersCount = Offer::where('service_id', $service->id)
            ->where('is_active', true)
            ->where(function($q) {
                $q->whereNull('end_date')
                  ->orWhereDate('end_date', '>=', now());
            })
            ->count();

        // Nombre total d'avis
        $reviewsCount = Review::where('service_id', $service->id)->count();

        // Note moyenne
        $averageRating = $service->averageRating();

        // Générer un graphique simple des vues (placeholder - à implémenter selon besoins)
        $viewsChart = null;

        return response()->json([
            'views_count_30days' => $viewsCount30Days,
            'active_offers_count' => $activeOffersCount,
            'reviews_count' => $reviewsCount,
            'average_rating' => $averageRating,
            'views_chart' => $viewsChart
        ]);
    }

    /**
     * Récupérer les différents plans d'abonnement
     */
    public function subscriptionPlans()
    {
        // Dans une implémentation réelle, ces plans seraient probablement stockés en base de données
        $plans = [
            [
                'id' => 'basic',
                'name' => 'Basic',
                'price' => 19.99,
                'currency' => 'EUR',
                'interval' => 'month',
                'description' => 'Plan de base pour les petits prestataires',
                'features' => [
                    'Profil de service visible',
                    'Gestion des offres (max. 3)',
                    'Statistiques de base'
                ]
            ],
            [
                'id' => 'premium',
                'name' => 'Premium',
                'price' => 49.99,
                'currency' => 'EUR',
                'interval' => 'month',
                'description' => 'Plan avancé pour une meilleure visibilité',
                'features' => [
                    'Profil de service visible',
                    'Gestion des offres (illimitées)',
                    'Mise en avant dans les résultats de recherche',
                    'Statistiques avancées',
                    'Badge Premium'
                ]
            ],
            [
                'id' => 'pro',
                'name' => 'Pro',
                'price' => 99.99,
                'currency' => 'EUR',
                'interval' => 'month',
                'description' => 'Plan complet pour une visibilité maximale',
                'features' => [
                    'Profil de service visible',
                    'Gestion des offres (illimitées)',
                    'Mise en avant dans les résultats de recherche',
                    'Statistiques avancées',
                    'Badge Pro',
                    'Présentation sur la page d\'accueil',
                    'Rapports analytiques personnalisés',
                    'Support prioritaire'
                ]
            ]
        ];

        return response()->json($plans);
    }
}