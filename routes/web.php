<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\FeedController;
use App\Http\Controllers\PerfilController;
use App\Http\Controllers\MembroController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

// ===== AUTENTICAÇÃO =====
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register']);
Route::get('/logout', [AuthController::class, 'logout'])->name('logout');

// ===== SAIR DA IGREJA =====
Route::get('/sair-igreja', [AuthController::class, 'showSair'])->name('sair.igreja');
Route::post('/sair-igreja', [AuthController::class, 'sair'])->name('sair.igreja.confirmar');

// ===== ROTAS PROTEGIDAS =====
Route::middleware(['auth.membro'])->group(function () {
    // Feed
    Route::get('/feed', [FeedController::class, 'index'])->name('feed');
    Route::post('/publicacao', [FeedController::class, 'store'])->name('publicacao.store');
    Route::post('/publicacao/{id}/curtir', [FeedController::class, 'curtir'])->name('publicacao.curtir');
    Route::post('/publicacao/{id}/comentar', [FeedController::class, 'comentar'])->name('publicacao.comentar');
    Route::delete('/publicacao/{id}', [FeedController::class, 'delete'])->name('publicacao.delete');

    // Perfil
    Route::get('/perfil/{matricula}', [PerfilController::class, 'show'])->name('perfil.show');
    Route::get('/perfil/editar', [PerfilController::class, 'edit'])->name('perfil.edit');
    Route::put('/perfil/editar', [PerfilController::class, 'update'])->name('perfil.update');
    Route::post('/perfil/foto', [PerfilController::class, 'uploadFoto'])->name('perfil.foto');

    // Cartão
    Route::get('/meu-cartao', [MembroController::class, 'meuCartao'])->name('membro.meu-cartao');
    Route::get('/cartao/{matricula}', [MembroController::class, 'cartao'])->name('membro.cartao');

    // Buscar membros
    Route::get('/buscar-membros', [MembroController::class, 'buscar'])->name('membros.buscar');
});

// Rota principal
Route::get('/', function () {
    return view('welcome');
});