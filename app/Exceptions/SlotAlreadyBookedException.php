<?php

namespace App\Exceptions;

use RuntimeException;

class SlotAlreadyBookedException extends RuntimeException
{
    public function __construct(
        string $message = 'The selected time slot is already booked.'
    ) {
        parent::__construct($message);
    }
}
