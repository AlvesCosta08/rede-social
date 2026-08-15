<?php

namespace App\Exceptions;

use Exception;

class PermissionDeniedException extends Exception
{
    protected $message = 'Você não tem permissão para realizar esta ação.';
    protected $code = 403;

    public function __construct(?string $message = null)
    {
        if ($message) {
            $this->message = $message;
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