<?php

// declare(strict_types=1);

namespace App\Exception;

use RuntimeException;
use Symfony\Component\Serializer\Serializer;

class InvalidJsonRequest extends RuntimeException
{
    protected Serializer $serializer;

    public function __construct(
        protected readonly array $errors = []
        )
    {
        parent::__construct();
        // $this->errors = $errors;
    }

    public function getErrors(): array
    {
        return $this->errors;
    }
}