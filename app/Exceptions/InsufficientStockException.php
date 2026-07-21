<?php

namespace App\Exceptions;

use RuntimeException;

class InsufficientStockException extends RuntimeException
{
    public function __construct(
        public readonly string $partName,
        public readonly int $availableQty,
        public readonly int $requestedQty,
        string $message = '',
    ) {
        parent::__construct(
            $message ?: "Stok tidak mencukupi untuk \"{$partName}\". "
                . "Tersedia: {$availableQty}, Diminta: {$requestedQty}."
        );
    }
}
