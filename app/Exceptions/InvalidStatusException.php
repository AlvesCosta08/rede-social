<?php

namespace App\Exceptions;

use Exception;

class InvalidStatusException extends Exception
{
    protected $message = 'Status inválido para esta operação.';
    protected $code = 422;

    public function __construct(?string $status = null, ?string $operation = null)
    {
        if ($status && $operation) {
            $this->message = "Não é possível realizar '{$operation}' com o status '{$status}'.";
        } elseif ($status) {
            $this->message = "Status '{$status}' inválido para esta operação.";
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