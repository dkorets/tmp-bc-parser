<?php

declare(strict_types=1);

namespace App\Services\Decimal;

class DecimalFactory
{
    public function create($value, int $precision = 8): Decimal
    {
        if (is_array($value)) {
            return $this->createFromData($value);
        }

        return new Decimal((string) $value, $precision);
    }

    public function createZero(): Decimal
    {
        return new Decimal('0');
    }

    private function createFromData(array $data): Decimal
    {
        return new Decimal((string) $data[Decimal::VALUE], (int) $data[Decimal::PRECISION]);
    }
}
