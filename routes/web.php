<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\FeedController;
use App\Http\Controllers\PerfilController;
use App\Http\Controllers\MembroController;
use App\Http\Controllers\ImagemController;
use App\Http\Controllers\SeguidorController;
use App\Http\Controllers\Admin\MembroController as AdminMembroController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Log;

// ===== REDIRECIONAMENTO INICIAL =====
Route::redirect('/', '/login')->name('home.redirect');

// ============================================================
// ROTAS PÚBLICAS (SEM AUTENTICAÇÃO)
// ============================================================

Route::middleware(['throttle:60,1'])->group(function () {
    Route::get('/verificar-matricula/{matricula}', [AuthController::class, 'verificarMatricula'])
        ->name('verificar.matricula')
        ->where('matricula', '[0-9]+');

    Route::get('/buscar-membro-antigo/{matricula}', [AuthController::class, 'buscarMembroAntigo'])
        ->name('buscar.membro.antigo')
        ->where('matricula', '[0-9]+');
});

// ============================================================
// ROTAS DE AUTENTICAÇÃO
// ============================================================

Route::middleware(['guest', 'throttle:10,1'])->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');
    
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.post');
});

// ============================================================
// LOGOUT
// ============================================================

Route::middleware(['auth'])->post('/logout', [AuthController::class, 'logout'])
    ->name('logout');

// ============================================================
// ROTAS PROTEGIDAS
// ============================================================

Route::middleware(['auth'])->group(function () {
    
    Route::redirect('/home', '/feed')->name('home');

    // ============================================================
    // FEED
    // ============================================================
    Route::prefix('feed')->name('feed.')->group(function () {
        Route::get('/', [FeedController::class, 'index'])->name('index')
            ->middleware(['throttle:300,1']);
        
        Route::get('/global', [FeedController::class, 'global'])->name('global')
            ->middleware(['throttle:200,1']);
        
        Route::post('/publicar', [FeedController::class, 'store'])
            ->name('publicar')
            ->middleware(['throttle:20,1']);
        
        Route::post('/{id}/curtir', [FeedController::class, 'curtir'])
            ->name('curtir')
            ->where('id', '[0-9]+')
            ->middleware(['throttle:100,1']);
        
        Route::post('/{id}/comentar', [FeedController::class, 'comentar'])
            ->name('comentar')
            ->where('id', '[0-9]+')
            ->middleware(['throttle:30,1']);
        
        Route::delete('/{id}', [FeedController::class, 'delete'])
            ->name('delete')
            ->where('id', '[0-9]+')
            ->middleware(['throttle:10,1']);
            
        Route::put('/{id}', [FeedController::class, 'update'])
            ->name('update')
            ->where('id', '[0-9]+')
            ->middleware(['throttle:15,1']);
        
        // ⭐ NOVA ROTA: Deletar comentário
        Route::delete('/comentario/{id}', [FeedController::class, 'deleteComentario'])
            ->name('delete-comentario')
            ->where('id', '[0-9]+')
            ->middleware(['throttle:10,1']);
    });

    // ============================================================
    // PERFIL
    // ============================================================
    Route::prefix('perfil')->name('perfil.')->group(function () {
        Route::get('/editar', [PerfilController::class, 'edit'])->name('edit')
            ->middleware(['throttle:50,1']);
            
        Route::put('/editar', [PerfilController::class, 'update'])->name('update')
            ->middleware(['throttle:10,1']);
        
        // ⭐ ROTAS DE FOTO
        Route::post('/foto', [PerfilController::class, 'uploadFoto'])
            ->name('foto.upload');
        
        Route::delete('/foto', [PerfilController::class, 'removerFoto'])
            ->name('foto.remover');
        
        Route::get('/{matricula}/posts', [PerfilController::class, 'posts'])
            ->name('posts')
            ->where('matricula', '[0-9]+')
            ->middleware(['throttle:100,1']);
        
        Route::get('/{matricula}', [PerfilController::class, 'show'])
            ->name('show')
            ->where('matricula', '[0-9]+')
            ->middleware(['throttle:150,1']);
    });

    // ============================================================
    // CARTÃO DIGITAL
    // ============================================================
    Route::prefix('cartao')->name('membro.')->group(function () {
        Route::get('/meu-cartao', [MembroController::class, 'meuCartao'])->name('meu-cartao')
            ->middleware(['throttle:20,1']);
            
        Route::get('/{matricula}', [MembroController::class, 'cartao'])
            ->name('cartao')
            ->where('matricula', '[0-9]+')
            ->middleware(['throttle:50,1']);
    });

    // ============================================================
    // MEMBROS
    // ============================================================
    Route::prefix('membros')->name('membros.')->group(function () {
        Route::get('/', [MembroController::class, 'index'])->name('index')
            ->middleware(['throttle:100,1']);
        
        Route::get('/buscar', [MembroController::class, 'buscar'])->name('buscar')
            ->middleware(['throttle:60,1']);
        
        Route::get('/autocomplete', [MembroController::class, 'autocomplete'])
            ->name('autocomplete')
            ->middleware(['throttle:200,1']);
        
        // ⭐ ROTA: Busca avançada
        Route::get('/buscar-avancado', [MembroController::class, 'buscarAvancado'])
            ->name('buscar.avancado')
            ->middleware(['throttle:30,1']);
        
        // ⭐ ROTA: Busca com highlight
        Route::get('/buscar-highlight', [MembroController::class, 'buscarComHighlight'])
            ->name('buscar.highlight')
            ->middleware(['throttle:20,1']);
        
        Route::get('/proximidade', [MembroController::class, 'buscarPorProximidade'])
            ->name('proximidade')
            ->middleware(['throttle:20,1']);
        
        Route::get('/sugestoes', [MembroController::class, 'sugestoes'])
            ->name('sugestoes')
            ->middleware(['throttle:50,1']);
        
        Route::get('/estatisticas', [MembroController::class, 'estatisticas'])
            ->name('estatisticas')
            ->middleware(['throttle:30,1']);
        
        Route::get('/exportar', [MembroController::class, 'exportar'])
            ->name('exportar')
            ->middleware(['throttle:10,1']);
        
        Route::post('/localizacao', [MembroController::class, 'atualizarLocalizacao'])
            ->name('localizacao')
            ->middleware(['throttle:10,1']);
        
        Route::get('/{matricula}', [MembroController::class, 'show'])
            ->name('show')
            ->where('matricula', '[0-9]+')
            ->middleware(['throttle:100,1']);
    });

    // ============================================================
    // SEGUIDORES
    // ============================================================
    Route::prefix('seguidores')->name('seguidores.')->group(function () {
        Route::post('/seguir/{matricula}', [SeguidorController::class, 'seguir'])
            ->name('seguir')
            ->where('matricula', '[0-9]+')
            ->middleware(['throttle:30,1']);
            
        Route::post('/deixar-seguir/{matricula}', [SeguidorController::class, 'deixarSeguir'])
            ->name('deixar')
            ->where('matricula', '[0-9]+')
            ->middleware(['throttle:30,1']);
        
        Route::get('/sugerir', [SeguidorController::class, 'sugerir'])->name('sugerir')
            ->middleware(['throttle:50,1']);
            
        Route::get('/seguindo/{matricula}', [SeguidorController::class, 'seguindo'])
            ->name('seguindo')
            ->where('matricula', '[0-9]+')
            ->middleware(['throttle:100,1']);
            
        Route::get('/seguidores/{matricula}', [SeguidorController::class, 'seguidores'])
            ->name('seguidores')
            ->where('matricula', '[0-9]+')
            ->middleware(['throttle:100,1']);
        
        // ⭐ NOVA ROTA: Verificar se segue
        Route::get('/verificar/{seguidor}/{seguido}', [SeguidorController::class, 'verificarSegue'])
            ->name('verificar')
            ->where('seguidor', '[0-9]+')
            ->where('seguido', '[0-9]+')
            ->middleware(['throttle:100,1']);
    });

    // ============================================================
    // ADMIN
    // ============================================================
    Route::prefix('admin')->name('admin.')->middleware(['admin'])->group(function () {
        Route::middleware(['throttle:300,1'])->group(function () {
            // CRUD de membros
            Route::resource('membros', AdminMembroController::class);
            
            // Ações em massa
            Route::post('membros/bulk', [AdminMembroController::class, 'bulkAction'])
                ->name('membros.bulk');
            
            // Exportar
            Route::get('membros/exportar', [AdminMembroController::class, 'exportar'])
                ->name('membros.exportar');
            
            // Estatísticas
            Route::get('estatisticas', [AdminMembroController::class, 'estatisticas'])
                ->name('estatisticas');
            
            // ⭐ CORRIGIDO: Posts de membros (com matrícula)
            Route::get('membros/{matricula}/posts', [AdminMembroController::class, 'posts'])
                ->name('membros.posts')
                ->where('matricula', '[0-9]+');
            
            // ⭐ CORRIGIDO: Deletar post (com ID)
            Route::delete('posts/{id}', [AdminMembroController::class, 'deletePost'])
                ->name('posts.delete')
                ->where('id', '[0-9]+');
        });
    });

    // ============================================================
    // SAIR DA IGREJA
    // ============================================================
    Route::get('/sair-igreja', [AuthController::class, 'showSair'])->name('sair.igreja')
        ->middleware(['throttle:20,1']);
        
    Route::post('/sair-igreja', [AuthController::class, 'sair'])->name('sair.igreja.confirmar')
        ->middleware(['throttle:5,1']);
});

// ============================================================
// ROTAS PÚBLICAS PARA IMAGENS
// ============================================================

Route::get('/imagens/fotos/{filename}', [ImagemController::class, 'show'])
    ->name('imagem.foto')
    ->where('filename', '^[a-zA-Z0-9_\-\.]+$')
    ->middleware(['throttle:100,1']);

// ⭐ NOVA ROTA: Upload de imagem via ImagemController
Route::post('/imagens/upload', [ImagemController::class, 'upload'])
    ->name('imagem.upload')
    ->middleware(['auth', 'throttle:10,1']);

// ============================================================
// FALLBACK
// ============================================================

Route::fallback(function () {
    if (app()->environment('local', 'production')) {
        Log::warning('404 - Rota não encontrada', [
            'url' => request()->fullUrl(),
            'method' => request()->method(),
            'ip' => request()->ip(),
            'user_agent' => request()->userAgent()
        ]);
    }
    
    if (request()->expectsJson()) {
        return response()->json(['error' => 'Rota não encontrada'], 404);
    }
    
    abort(404);
});