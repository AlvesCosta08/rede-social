<?php

namespace App\Exceptions;

use Exception;

class MembroNotFoundException extends Exception
{
    protected $message = 'Membro não encontrado.';
    protected $code = 404;

    public function __construct(?string $matricula = null)
    {
        if ($matricula) {
            $this->message = "Membro com matrícula {$matricula} não foi encontrado.";
        }
        parent::__construct($this->message, $this->code);
    }
}