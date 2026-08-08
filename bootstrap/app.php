<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Session\TokenMismatchException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use App\Http\Middleware\AdminMiddleware;
use App\Http\Middleware\CheckPermission;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // ============================================================
        // ALIAS DE MIDDLEWARES
        // ============================================================
        $middleware->alias([
            'auth.membro' => \App\Http\Middleware\AutenticacaoMembro::class,
            'admin' => AdminMiddleware::class,
            'permissao' => CheckPermission::class,
            'guest' => \App\Http\Middleware\RedirectIfAuthenticated::class,
            'auth' => \App\Http\Middleware\Authenticate::class,
        ]);

        // ============================================================
        // MIDDLEWARES GLOBAIS (WEB)
        // ============================================================
        $middleware->appendToGroup('web', [
            \Illuminate\Cookie\Middleware\EncryptCookies::class,
            \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
            \Illuminate\Session\Middleware\StartSession::class,
            \Illuminate\View\Middleware\ShareErrorsFromSession::class,
            \Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class,
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
            \App\Http\Middleware\CompatibilidadeMembro::class,
        ]);

        // ============================================================
        // PRIORIDADE DOS MIDDLEWARES
        // ============================================================
        $middleware->priority([
            \Illuminate\Foundation\Http\Middleware\HandlePrecognitiveRequests::class,
            \Illuminate\Cookie\Middleware\EncryptCookies::class,
            \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
            \Illuminate\Session\Middleware\StartSession::class,
            \Illuminate\View\Middleware\ShareErrorsFromSession::class,
            \Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class,
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
            \App\Http\Middleware\AutenticacaoMembro::class,
            AdminMiddleware::class,
            CheckPermission::class,
        ]);

        // ============================================================
        // EXCEÇÕES DE CSRF (ROTAS AJAX)
        // ============================================================
        $middleware->validateCsrfTokens(except: [
            'feed/publicar',
            'feed/*/curtir',
            'feed/*/comentar',
            'feed/*',
            'perfil/foto',
            'perfil/*/foto',
            'perfil/foto/remover',
            'upload-foto',
            'remover-foto',
            'buscar-membros',
            'membros/buscar',
            'membros/buscar-avancado',
            'logout',
            'admin/membros/bulk',
            'buscar-membro-antigo/*',
            'verificar-matricula/*',
            'login',
            'register',
        ]);

        // ============================================================
        // REDIRECIONAR PARA LOGIN QUANDO NÃO AUTENTICADO
        // ============================================================
        $middleware->redirectGuestsTo(fn () => route('login'));
    })
    ->withCommands([
        // Comandos removidos ou vazio se não houver nenhum
    ])
    ->withExceptions(function (Exceptions $exceptions): void {
        // ============================================================
        // RESPONDER COM JSON PARA ROTAS API
        // ============================================================
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*') || $request->ajax()
        );

        // ============================================================
        // TRATAMENTO DE EXCEÇÕES PARA AJAX
        // ============================================================
        $exceptions->render(function (Throwable $e, Request $request) {
            if (!$request->ajax()) {
                return null;
            }

            if ($e instanceof TokenMismatchException) {
                return response()->json([
                    'success' => false,
                    'message' => 'Sessão expirada. Recarregue a página.',
                    'error' => 'token_mismatch',
                    'reload' => true
                ], 403);
            }

            if ($e instanceof AuthenticationException) {
                return response()->json([
                    'success' => false,
                    'message' => 'Sessão expirada. Faça login novamente.',
                    'error' => 'unauthenticated',
                    'redirect' => route('login')
                ], 401);
            }

            if ($e instanceof AccessDeniedHttpException) {
                return response()->json([
                    'success' => false,
                    'message' => 'Você não tem permissão.',
                    'error' => 'forbidden'
                ], 403);
            }

            if ($e instanceof NotFoundHttpException) {
                return response()->json([
                    'success' => false,
                    'message' => 'Recurso não encontrado.',
                    'error' => 'not_found'
                ], 404);
            }

            if ($e instanceof ValidationException) {
                return response()->json([
                    'success' => false,
                    'message' => 'Erro de validação.',
                    'errors' => $e->errors()
                ], 422);
            }

            $message = app()->environment('production') 
                ? 'Erro interno do servidor.' 
                : $e->getMessage();

            return response()->json([
                'success' => false,
                'message' => $message,
                'error' => 'server_error',
                ...(app()->environment('local') ? [
                    'exception' => get_class($e),
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                    'trace' => $e->getTrace()
                ] : [])
            ], 500);
        });

        // ============================================================
        // TRATAMENTO DE ERRO 403 (PERMISSÃO NEGADA) PARA WEB
        // ============================================================
        $exceptions->render(function (AccessDeniedHttpException $e, Request $request) {
            if ($request->ajax() || $request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Você não tem permissão para realizar esta ação.',
                    'error' => 'forbidden'
                ], 403);
            }

            return redirect()->back()
                ->with('error', 'Você não tem permissão para realizar esta ação.');
        });
    })->create();