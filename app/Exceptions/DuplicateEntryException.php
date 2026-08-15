<?php

namespace App\Exceptions;

use Exception;

class DuplicateEntryException extends Exception
{
    protected $message = 'Registro duplicado.';
    protected $code = 409;

    public function __construct(?string $field = null, ?string $value = null)
    {
        if ($field && $value) {
            $this->message = "O valor '{$value}' para o campo '{$field}' já está em uso.";
        } elseif ($field) {
            $this->message = "O campo '{$field}' já está em uso.";
        }
        parent::__construct($this->message, $this->code);
    }

    /**
     * Renderiza a exceção
     */
    public function render($request)
    {
        if ($request->expectsJson()) {
            return response()->json([
                'success' => false,
                'message' => $this->message,
                'code' => $this->code
            ], $this->code);
        }

        return redirect()->back()
            ->with('error', $this->message);
    }
}