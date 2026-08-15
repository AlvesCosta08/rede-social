<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\FeedController;
use App\Http\Controllers\PerfilController;
use App\Http\Controllers\MembroController;
use App\Http\Controllers\ImagemController;
use App\Http\Controllers\SeguidorController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\SecretarioController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Log;

// ============================================================
// REDIRECIONAMENTO INICIAL
// ============================================================
Route::redirect('/', '/login')->name('home.redirect');

// ============================================================
// ROTAS PÚBLICAS (SEM AUTENTICAÇÃO)
// ============================================================

Route::middleware(['throttle:60,1'])->group(function () {
    // ⭐ VERIFICAÇÃO DE MATRÍCULA PARA CADASTRO (AJAX)
    Route::get('/verificar-matricula/{matricula}', [RegisterController::class, 'verificarMatricula'])
        ->name('verificar.matricula')
        ->where('matricula', '[0-9]+');

    // ⭐ BUSCAR MEMBRO PARA CADASTRO (AJAX)
    Route::get('/buscar-membro/{matricula}', [RegisterController::class, 'buscarMembro'])
        ->name('buscar.membro')
        ->where('matricula', '[0-9]+');
    
    // ⭐ NOVA ROTA: VERIFICAR EMAIL PARA CADASTRO (AJAX)
    Route::post('/verificar-email', [RegisterController::class, 'verificarEmail'])
        ->name('verificar.email');
});

// ============================================================
// ROTAS DE AUTENTICAÇÃO
// ============================================================

Route::middleware(['guest', 'throttle:10,1'])->group(function () {
    // LOGIN
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login'])->name('login.post');
    
    // REGISTRO
    Route::get('/register', [RegisterController::class, 'showRegisterForm'])->name('register');
    Route::post('/register', [RegisterController::class, 'register'])->name('register.post');
});

// ============================================================
// LOGOUT
// ============================================================

Route::middleware(['auth'])->post('/logout', [LogoutController::class, 'logout'])
    ->name('logout');

// ============================================================
// ROTAS PROTEGIDAS (AUTENTICADAS)
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
    Route::prefix('cartao')->name('cartao.')->group(function () {
        Route::get('/meu-cartao', [MembroController::class, 'meuCartao'])->name('meu-cartao')
            ->middleware(['throttle:20,1']);
            
        Route::get('/{matricula}', [MembroController::class, 'cartao'])
            ->name('show')
            ->where('matricula', '[0-9]+')
            ->middleware(['throttle:50,1']);
    });

    // ============================================================
    // MEMBROS (Membro Comum)
    // ============================================================
    Route::prefix('membros')->name('membros.')->group(function () {
        Route::get('/', [MembroController::class, 'index'])->name('index')
            ->middleware(['throttle:100,1']);
        
        Route::get('/buscar', [MembroController::class, 'buscar'])->name('buscar')
            ->middleware(['throttle:60,1']);
        
        Route::get('/autocomplete', [MembroController::class, 'autocomplete'])
            ->name('autocomplete')
            ->middleware(['throttle:200,1']);
        
        Route::get('/proximidade', [MembroController::class, 'buscarPorProximidade'])
            ->name('proximidade')
            ->middleware(['throttle:20,1']);
        
        Route::get('/sugestoes', [MembroController::class, 'sugestoes'])
            ->name('sugestoes')
            ->middleware(['throttle:50,1']);
        
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
        
        Route::get('/verificar/{seguidor}/{seguido}', [SeguidorController::class, 'verificarSegue'])
            ->name('verificar')
            ->where('seguidor', '[0-9]+')
            ->where('seguido', '[0-9]+')
            ->middleware(['throttle:100,1']);
    });

    // ============================================================
    // ADMIN - ROTAS COM PERMISSÃO
    // ============================================================
    Route::prefix('admin')->name('admin.')->middleware(['auth'])->group(function () {
        
        // ⭐ DASHBOARD DO ADMIN
        Route::get('/dashboard', [AdminController::class, 'dashboard'])
            ->name('dashboard')
            ->middleware(['permissao:dashboard', 'throttle:60,1']);

        // ⭐ ROTAS DE MEMBROS (ADMIN)
        Route::prefix('membros')->name('membros.')->group(function () {
            
            // Listar membros
            Route::get('/', [AdminController::class, 'index'])
                ->name('index')
                ->middleware(['permissao:dashboard', 'throttle:300,1']);
            
            // Visualizar membro
            Route::get('/{matricula}', [AdminController::class, 'show'])
                ->name('show')
                ->where('matricula', '[0-9]+')
                ->middleware(['permissao:dashboard', 'throttle:100,1']);
            
            // Criar membro
            Route::get('/create', [AdminController::class, 'create'])
                ->name('create')
                ->middleware(['permissao:criar_membro', 'throttle:50,1']);
            
            Route::post('/', [AdminController::class, 'store'])
                ->name('store')
                ->middleware(['permissao:criar_membro', 'throttle:20,1']);
            
            // Editar membro
            Route::get('/{matricula}/edit', [AdminController::class, 'edit'])
                ->name('edit')
                ->where('matricula', '[0-9]+')
                ->middleware(['permissao:editar_membro', 'throttle:50,1']);
            
            Route::put('/{matricula}', [AdminController::class, 'update'])
                ->name('update')
                ->where('matricula', '[0-9]+')
                ->middleware(['permissao:editar_membro', 'throttle:20,1']);
            
            // Deletar membro
            Route::delete('/{matricula}', [AdminController::class, 'destroy'])
                ->name('destroy')
                ->where('matricula', '[0-9]+')
                ->middleware(['permissao:excluir_membro', 'throttle:10,1']);
            
            // Ações em massa
            Route::post('/bulk', [AdminController::class, 'bulkAction'])
                ->name('bulk')
                ->middleware(['permissao:dashboard', 'throttle:20,1']);
            
            // Exportar
            Route::get('/exportar', [AdminController::class, 'exportar'])
                ->name('exportar')
                ->middleware(['permissao:dashboard', 'throttle:10,1']);
            
            // Posts de membros
            Route::get('/{matricula}/posts', [AdminController::class, 'posts'])
                ->name('posts')
                ->where('matricula', '[0-9]+')
                ->middleware(['permissao:dashboard', 'throttle:100,1']);

            // ⭐ UPLOAD DE FOTO (ADMIN)
            Route::post('/{matricula}/foto', [AdminController::class, 'uploadFoto'])
                ->name('upload-foto')
                ->middleware(['permissao:editar_membro', 'throttle:10,1']);
        });

        // ⭐ ESTATÍSTICAS
        Route::get('/estatisticas', [AdminController::class, 'estatisticas'])
            ->name('estatisticas')
            ->middleware(['permissao:dashboard', 'throttle:30,1']);

        // ⭐ DELETAR POST (via admin)
        Route::delete('/posts/{id}', [AdminController::class, 'deletePost'])
            ->name('posts.delete')
            ->where('id', '[0-9]+')
            ->middleware(['permissao:dashboard', 'throttle:10,1']);

        // ⭐ GERENCIAR SECRETÁRIOS - APENAS ADMIN
        Route::prefix('secretarios')->name('secretarios.')->group(function () {
            Route::get('/', [SecretarioController::class, 'index'])
                ->name('index')
                ->middleware(['permissao:gerenciar_secretarios', 'throttle:60,1']);
            
            Route::put('/{matricula}', [SecretarioController::class, 'update'])
                ->name('update')
                ->where('matricula', '[0-9]+')
                ->middleware(['permissao:gerenciar_secretarios', 'throttle:20,1']);
            
            Route::delete('/{matricula}', [SecretarioController::class, 'destroy'])
                ->name('destroy')
                ->where('matricula', '[0-9]+')
                ->middleware(['permissao:gerenciar_secretarios', 'throttle:10,1']);
            
            Route::get('/estatisticas', [SecretarioController::class, 'estatisticas'])
                ->name('estatisticas')
                ->middleware(['permissao:gerenciar_secretarios', 'throttle:30,1']);
            
            Route::get('/buscar-membros', [SecretarioController::class, 'buscarMembros'])
                ->name('buscar-membros')
                ->middleware(['permissao:gerenciar_secretarios', 'throttle:60,1']);

            // ⭐ UPLOAD DE FOTO (SECRETÁRIO)
            Route::post('/{matricula}/foto', [SecretarioController::class, 'uploadFoto'])
                ->name('upload-foto')
                ->middleware(['permissao:editar_membro', 'throttle:10,1']);
        });
    });

    // ============================================================
    // SAIR DA IGREJA
    // ============================================================
    Route::get('/sair-igreja', [LogoutController::class, 'showSairForm'])->name('sair.igreja')
        ->middleware(['throttle:20,1']);
        
    Route::post('/sair-igreja', [LogoutController::class, 'sair'])->name('sair.igreja.confirmar')
        ->middleware(['throttle:5,1']);
});

// ============================================================
// ROTAS PÚBLICAS PARA IMAGENS
// ============================================================

Route::get('/imagens/fotos/{filename}', [ImagemController::class, 'show'])
    ->name('imagem.foto')
    ->where('filename', '^[a-zA-Z0-9_\-\.]+$')
    ->middleware(['throttle:100,1']);

Route::post('/imagens/upload', [ImagemController::class, 'upload'])
    ->name('imagem.upload')
    ->middleware(['auth', 'throttle:10,1']);

Route::delete('/imagens/fotos', [ImagemController::class, 'destroy'])
    ->name('imagem.destroy')
    ->middleware(['auth', 'throttle:10,1']);

// ============================================================
// FALLBACK - 404
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