<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CityController;
use App\Http\Controllers\OfferController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\AdminUserController;
use App\Http\Controllers\Admin\AdminCategoryController;
use App\Http\Controllers\Admin\AdminCityController;
use App\Http\Controllers\Admin\AdminVerificationController;
use App\Http\Controllers\Prestataire\PrestataireController;
use App\Http\Controllers\Prestataire\PrestataireProfileController;
use App\Http\Controllers\Prestataire\PrestataireOfferController;
use App\Http\Controllers\Prestataire\PrestataireStatController;

// Routes pour l'authentification
Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);
Route::post('/register/prestataire', [AuthController::class, 'registerPrestataire']);
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth');
Route::get('/api/user', [AuthController::class, 'user'])->middleware('auth');

// Routes API publiques
Route::prefix('api')->group(function () {
    // Catégories
    Route::get('/categories', [CategoryController::class, 'index']);
    Route::get('/categories/{id}', [CategoryController::class, 'show']);
    
    // Villes
    Route::get('/cities', [CityController::class, 'index']);
    Route::get('/cities/{id}', [CityController::class, 'show']);
    
    // Services
    Route::get('/services', [ServiceController::class, 'index']);
    Route::get('/services/featured', [ServiceController::class, 'featured']);
    Route::get('/services/{id}', [ServiceController::class, 'show']);
    
    // Avis - uniquement pour les utilisateurs authentifiés
    Route::middleware('auth')->group(function () {
        Route::post('/services/{id}/reviews', [ReviewController::class, 'store']);
        Route::put('/reviews/{id}', [ReviewController::class, 'update']);
        Route::delete('/reviews/{id}', [ReviewController::class, 'destroy']);
    });
});

// Routes API Admin
Route::prefix('api/admin')->middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/dashboard-stats', [AdminController::class, 'dashboardStats']);
    Route::get('/pending-verifications-count', [AdminVerificationController::class, 'pendingCount']);
    
    // Gestion des utilisateurs
    Route::get('/users', [AdminUserController::class, 'index']);
    Route::get('/users/{id}', [AdminUserController::class, 'show']);
    Route::put('/users/{id}', [AdminUserController::class, 'update']);
    Route::delete('/users/{id}', [AdminUserController::class, 'destroy']);
    
    // Gestion des catégories
    Route::apiResource('/categories', AdminCategoryController::class);
    
    // Gestion des villes
    Route::apiResource('/cities', AdminCityController::class);
    
    // Vérification des prestataires
    Route::get('/verifications', [AdminVerificationController::class, 'index']);
    Route::post('/verify/{id}/approve', [AuthController::class, 'approvePrestataire']);
    Route::post('/verify/{id}/reject', [AuthController::class, 'rejectPrestataire']);
});

// Routes API Prestataire
Route::prefix('api/prestataire')->middleware(['auth', 'role:prestataire'])->group(function () {
    // Tableau de bord
    Route::get('/dashboard-stats', [PrestataireController::class, 'dashboardStats']);
    
    // Profil du service
    Route::get('/profile', [PrestataireProfileController::class, 'show']);
    Route::put('/profile', [PrestataireProfileController::class, 'update']);
    Route::post('/profile/images', [PrestataireProfileController::class, 'uploadImage']);
    Route::delete('/profile/images/{id}', [PrestataireProfileController::class, 'deleteImage']);
    Route::post('/profile/images/{id}/main', [PrestataireProfileController::class, 'setMainImage']);
    
    // Gestion des offres
    Route::get('/offers', [PrestataireOfferController::class, 'index']);
    Route::post('/offers', [PrestataireOfferController::class, 'store']);
    Route::get('/offers/{id}', [PrestataireOfferController::class, 'show']);
    Route::put('/offers/{id}', [PrestataireOfferController::class, 'update']);
    Route::delete('/offers/{id}', [PrestataireOfferController::class, 'destroy']);
    
    // Statistiques
    Route::get('/stats/views', [PrestataireStatController::class, 'viewsStats']);
    Route::get('/stats/reviews', [PrestataireStatController::class, 'reviewsStats']);
    
    // Abonnements
    Route::get('/subscriptions', [PrestataireController::class, 'subscriptionPlans']);
});

// Route par défaut pour React - doit être en dernier
Route::get('/{any}', function () {
    return view('app');
})->where('any', '.*');

