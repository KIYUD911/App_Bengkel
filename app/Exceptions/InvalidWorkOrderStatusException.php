<?php

namespace App\Exceptions;

use RuntimeException;

class InvalidWorkOrderStatusException extends RuntimeException
{
    public function __construct(
        public readonly string $currentStatus,
        public readonly string $attemptedStatus = '',
        string $message = '',
    ) {
        parent::__construct(
            $message ?: "Tidak dapat mengubah status Work Order dari \"{$currentStatus}\""
                . ($attemptedStatus ? " ke \"{$attemptedStatus}\"." : '.')
        );
    }
}
