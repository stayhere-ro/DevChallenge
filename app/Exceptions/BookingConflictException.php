<?php

namespace App\Exceptions;

use Exception;

class BookingConflictException extends Exception
{
    public function __construct(string $message = 'This time slot is already booked for the selected hairdresser.')
    {
        parent::__construct($message);
    }
}
