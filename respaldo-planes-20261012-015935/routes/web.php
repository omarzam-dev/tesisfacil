<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\CapituloController;
use App\Http\Controllers\CapituloVersionController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RevisionApaController;
use App\Http\Controllers\SubscriptionController;
use App\Http\Controllers\TesisController;
use App\Models\Plan;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    $plans = Plan::visibles()->get();

    return view('welcome', compact('plans'));
})->name('home');

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified', 'check.subscription'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Rutas de la app (requieren login + suscripción activa o trial vigente)
Route::middleware(['auth', 'check.subscription'])->group(function () {
    Route::get('/apa', [RevisionApaController::class, 'index'])->name('apa.index');
    Route::get('/apa/nueva', [RevisionApaController::class, 'create'])->name('apa.create');
    Route::post('/apa', [RevisionApaController::class, 'store'])->name('apa.store');
    Route::get('/apa/{revision}', [RevisionApaController::class, 'show'])->name('apa.show');

    Route::get('/tesis', [TesisController::class, 'index'])->name('tesis.index');
    Route::get('/tesis/nueva', [TesisController::class, 'create'])->name('tesis.create');
    Route::post('/tesis', [TesisController::class, 'store'])->name('tesis.store');
    Route::get('/tesis/{tesis}', [TesisController::class, 'show'])->name('tesis.show');
    Route::get('/tesis/{tesis}/descargar-completa', [TesisController::class, 'descargarCompleta'])->name('tesis.descargar-completa');

    Route::post('/tesis/{tesis}/capitulos', [CapituloController::class, 'store'])->name('capitulos.store');
    Route::patch('/capitulos/{capitulo}/estado', [CapituloController::class, 'actualizarEstado'])->name('capitulos.estado');
    Route::patch('/capitulos/{capitulo}/nombre', [CapituloController::class, 'actualizarNombre'])->name('capitulos.nombre');
    Route::delete('/capitulos/{capitulo}', [CapituloController::class, 'destroy'])->name('capitulos.destroy');

    Route::post('/capitulos/{capitulo}/versiones', [CapituloVersionController::class, 'store'])->name('capitulos.version.store');
    Route::get('/capitulos/{capitulo}/versiones/{version}/descargar', [CapituloVersionController::class, 'download'])->name('capitulos.version.download');
    Route::delete('/capitulos/{capitulo}/versiones/{version}', [CapituloVersionController::class, 'destroy'])->name('capitulos.version.destroy');
});

// Rutas de suscripción (requieren login pero NO bloquean por suscripción vencida)
Route::middleware('auth')->group(function () {
    Route::get('/subscription/expired', [SubscriptionController::class, 'expired'])->name('subscription.expired');
    Route::get('/subscription/plans', [SubscriptionController::class, 'plans'])->name('subscription.plans');
    Route::get('/subscription/checkout/{plan}', [SubscriptionController::class, 'checkout'])->name('subscription.checkout');
    Route::post('/subscription/upload-proof/{plan}', [SubscriptionController::class, 'uploadProof'])->name('subscription.upload-proof');
    Route::get('/subscription/pending', [SubscriptionController::class, 'pending'])->name('subscription.pending');
    Route::get('/subscription/manage', [SubscriptionController::class, 'manage'])->name('subscription.manage');
    Route::post('/subscription/cancel', [SubscriptionController::class, 'cancel'])->name('subscription.cancel');
});

// Panel de administrador (checkAdmin() adentro de cada método del controlador)
Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [AdminController::class, 'index'])->name('index');
    Route::get('/payments', [AdminController::class, 'payments'])->name('payments');
    Route::post('/payments/{proof}/approve', [AdminController::class, 'approve'])->name('payments.approve');
    Route::post('/payments/{proof}/reject', [AdminController::class, 'reject'])->name('payments.reject');

    Route::get('/plans', [AdminController::class, 'plans'])->name('plans');
    Route::post('/plans', [AdminController::class, 'storePlan'])->name('plans.store');
    Route::put('/plans/{plan}', [AdminController::class, 'updatePlan'])->name('plans.update');

    Route::get('/payment-methods', [AdminController::class, 'paymentMethods'])->name('payment-methods');
    Route::post('/payment-methods', [AdminController::class, 'storePaymentMethod'])->name('payment-methods.store');
    Route::put('/payment-methods/{method}', [AdminController::class, 'updatePaymentMethod'])->name('payment-methods.update');

    Route::get('/subscriptions', [AdminController::class, 'subscriptions'])->name('subscriptions');
    Route::patch('/subscriptions/{subscription}/cancel', [AdminController::class, 'cancelSubscription'])->name('subscriptions.cancel');
    Route::delete('/subscriptions/{subscription}', [AdminController::class, 'destroySubscription'])->name('subscriptions.destroy');

    Route::get('/users', [AdminController::class, 'users'])->name('users');
    Route::patch('/users/{user}/toggle-admin', [AdminController::class, 'toggleAdmin'])->name('users.toggle-admin');
});

require __DIR__.'/auth.php';
