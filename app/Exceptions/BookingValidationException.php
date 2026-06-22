<?php

namespace App\Exceptions;

use RuntimeException;

class BookingValidationException extends RuntimeException
{
    public function __construct(
        public readonly array $errors,
        string $message = 'Validation failed.',
        int $code = 0
    ) {
        parent::__construct($message, $code);
    }

    public static function forField(string $field, string $message): self
    {
        return new self([$field => [$message]]);
    }
}
