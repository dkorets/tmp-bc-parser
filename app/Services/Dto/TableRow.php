<?php

declare(strict_types=1);

namespace App\Services\Dto;

use App\Services\Decimal\Decimal;

class TableRow
{
    public function __construct(
        public readonly string $exchangeName,
        public readonly Decimal $giveAmount,
        public readonly Decimal $getAmount,
        public readonly int $position,
    ) {
    }

    public function isFirstPosition(): bool
    {
        return 1 === $this->position;
    }

    public function isSecondPosition(): bool
    {
        return 2 === $this->position;
    }

    public function isThirdPosition(): bool
    {
        return 3 === $this->position;
    }

    public function isOnPosition(int $position): bool
    {
        return $position === $this->position;
    }
}
