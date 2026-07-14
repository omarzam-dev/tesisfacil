<?php
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware(['auth', 'licencia'])->group(function () {
    Route::get('/apa', [App\Http\Controllers\RevisionApaController::class, 'index'])->name('apa.index');
    Route::get('/apa/nueva', [App\Http\Controllers\RevisionApaController::class, 'create'])->name('apa.create');
    Route::post('/apa', [App\Http\Controllers\RevisionApaController::class, 'store'])->name('apa.store');
    Route::get('/apa/{revision}', [App\Http\Controllers\RevisionApaController::class, 'show'])->name('apa.show');
});

require __DIR__.'/auth.php';