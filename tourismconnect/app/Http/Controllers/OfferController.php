<?php

namespace App\Http\Controllers;

use App\Models\Offer;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class OfferController extends Controller
{
    /**
     * Afficher la liste des offres
     */
    public function index()
    {
        $offers = Offer::with(['service.category', 'service.city'])
            ->whereHas('service.user', function($q) {
                $q->where('status', 'active');
            })
            ->where('is_active', true)
            ->whereDate('start_date', '<=', now())
            ->where(function($q) {
                $q->whereNull('end_date')
                  ->orWhereDate('end_date', '>=', now());
            })
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($offers);
    }

    /**
     * Afficher les détails d'une offre
     */
    public function show($id)
    {
        $offer = Offer::with(['service.category', 'service.city', 'service.images'])
            ->findOrFail($id);

        return response()->json($offer);
    }
}