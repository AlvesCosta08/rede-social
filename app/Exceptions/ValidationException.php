<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Support\Facades\Log;

class ValidationException extends Exception
{
    protected $errors = [];
    protected $message = 'Erro de validação.';
    protected $code = 422;

    public function __construct(array $errors, ?string $message = null)
    {
        $this->errors = $errors;
        if ($message) {
            $this->message = $message;
        }
        parent::__construct($this->message, $this->code);
    }

    /**
     * Retorna os erros
     */
    public function getErrors(): array
    {
        return $this->errors;
    }

    /**
     * Renderiza a exceção
     */
    public function render($request)
    {
        Log::warning('⚠️ Erro de validação', [
            'errors' => $this->errors,
            'message' => $this->message,
            'ip' => $request->ip()
        ]);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => false,
                'message' => $this->message,
                'errors' => $this->errors,
                'code' => $this->code
            ], $this->code);
        }

        return back()
            ->withErrors($this->errors)
            ->withInput($request->except('password', 'password_confirmation'));
    }
}