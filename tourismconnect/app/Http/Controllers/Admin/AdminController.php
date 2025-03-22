<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Service;
use App\Models\Review;
use App\Models\Category;
use App\Models\City;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AdminController extends Controller
{
    /**
     * Récupérer les statistiques pour le tableau de bord admin
     */
    public function dashboardStats()
    {
        // Comptage des utilisateurs par type
        $usersByType = User::select('type', DB::raw('count(*) as count'))
            ->groupBy('type')
            ->get()
            ->pluck('count', 'type')
            ->toArray();
        
        // Nombre de services par catégorie
        $servicesByCategory = Service::select('category_id', DB::raw('count(*) as count'))
            ->groupBy('category_id')
            ->get()
            ->map(function ($item) {
                $category = Category::find($item->category_id);
                return [
                    'category_id' => $item->category_id,
                    'category_name' => $category ? $category->name : 'Inconnue',
                    'count' => $item->count
                ];
            });
        
        // Nombre de services par ville
        $servicesByCity = Service::select('city_id', DB::raw('count(*) as count'))
            ->groupBy('city_id')
            ->get()
            ->map(function ($item) {
                $city = City::find($item->city_id);
                return [
                    'city_id' => $item->city_id,
                    'city_name' => $city ? $city->name : 'Inconnue',
                    'count' => $item->count
                ];
            });
        
        // Nombre de nouveaux utilisateurs par jour (30 derniers jours)
        $newUsers = User::where('created_at', '>=', Carbon::now()->subDays(30))
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('count(*) as count'))
            ->groupBy('date')
            ->orderBy('date')
            ->get();
        
        // Nombre d'avis par jour (30 derniers jours)
        $newReviews = Review::where('created_at', '>=', Carbon::now()->subDays(30))
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('count(*) as count'))
            ->groupBy('date')
            ->orderBy('date')
            ->get();
        
        // Statistiques globales
        $totalUsers = User::count();
        $totalServices = Service::count();
        $totalReviews = Review::count();
        $pendingVerifications = User::where('type', 'prestataire')
            ->where('status', 'pending')
            ->count();
        
        return response()->json([
            'users_by_type' => $usersByType,
            'services_by_category' => $servicesByCategory,
            'services_by_city' => $servicesByCity,
            'new_users' => $newUsers,
            'new_reviews' => $newReviews,
            'total_users' => $totalUsers,
            'total_services' => $totalServices,
            'total_reviews' => $totalReviews,
            'pending_verifications' => $pendingVerifications
        ]);
    }
}