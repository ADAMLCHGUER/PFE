<?php

namespace App\Http\Controllers\Prestataire;

use App\Http\Controllers\Controller;
use App\Models\Service;
use App\Models\ServiceView;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class PrestataireStatController extends Controller
{
    /**
     * Récupérer les statistiques de vues
     */
    public function viewsStats(Request $request)
    {
        $user = Auth::user();
        $service = Service::where('user_id', $user->id)->firstOrFail();
        
        // Définir la période
        $period = $request->period ?? 'month'; // day, week, month, year
        
        $startDate = null;
        $groupFormat = '';
        
        switch ($period) {
            case 'day':
                $startDate = Carbon::now()->subDay();
                $groupFormat = '%Y-%m-%d %H:00:00';
                break;
            case 'week':
                $startDate = Carbon::now()->subWeek();
                $groupFormat = '%Y-%m-%d';
                break;
            case 'month':
                $startDate = Carbon::now()->subMonth();
                $groupFormat = '%Y-%m-%d';
                break;
            case 'year':
                $startDate = Carbon::now()->subYear();
                $groupFormat = '%Y-%m';
                break;
            default:
                $startDate = Carbon::now()->subMonth();
                $groupFormat = '%Y-%m-%d';
        }
        
        // Récupérer les vues groupées par période
        $views = ServiceView::where('service_id', $service->id)
            ->where('created_at', '>=', $startDate)
            ->select(DB::raw("DATE_FORMAT(created_at, '$groupFormat') as date"), DB::raw('count(*) as count'))
            ->groupBy('date')
            ->orderBy('date')
            ->get();
        
        // Statistiques globales
        $totalViews = ServiceView::where('service_id', $service->id)->count();
        $viewsToday = ServiceView::where('service_id', $service->id)
            ->whereDate('created_at', Carbon::today())
            ->count();
        $viewsThisWeek = ServiceView::where('service_id', $service->id)
            ->whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])
            ->count();
        $viewsThisMonth = ServiceView::where('service_id', $service->id)
            ->whereMonth('created_at', Carbon::now()->month)
            ->whereYear('created_at', Carbon::now()->year)
            ->count();
        
        return response()->json([
            'views_data' => $views,
            'total_views' => $totalViews,
            'views_today' => $viewsToday,
            'views_this_week' => $viewsThisWeek,
            'views_this_month' => $viewsThisMonth
        ]);
    }

    /**
     * Récupérer les statistiques des avis
     */
    public function reviewsStats(Request $request)
    {
        $user = Auth::user();
        $service = Service::where('user_id', $user->id)->firstOrFail();
        
        // Nombre limite d'avis à récupérer
        $limit = $request->limit ?? null;
        
        // Récupérer les avis
        $query = Review::with('user')
            ->where('service_id', $service->id)
            ->orderBy('created_at', 'desc');
            
        if ($limit) {
            $query->limit($limit);
        }
        
        $reviews = $query->get();
        
        // Statistiques sur les avis
        $averageRating = $service->averageRating();
        $totalReviews = Review::where('service_id', $service->id)->count();
        
        // Répartition des notes
        $ratingDistribution = [];
        for ($i = 1; $i <= 5; $i++) {
            $count = Review::where('service_id', $service->id)
                ->where('rating', $i)
                ->count();
            
            $ratingDistribution[$i] = [
                'count' => $count,
                'percentage' => $totalReviews > 0 ? round(($count / $totalReviews) * 100, 1) : 0
            ];
        }
        
        return response()->json([
            'reviews' => $reviews,
            'average_rating' => $averageRating,
            'total_reviews' => $totalReviews,
            'rating_distribution' => $ratingDistribution
        ]);
    }
}