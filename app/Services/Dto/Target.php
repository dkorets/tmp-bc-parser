<?php

declare(strict_types=1);

namespace App\Services\Dto;

use App\Models\Direction;

readonly class Target
{
    public function __construct(
        public int $from,
        public int $to,
        public int $city,
    ) {
    }

    public function uid(): string
    {
        return Direction::buildUid($this->from, $this->to, $this->city);
    }

    public static function fromArray(array $data): self
    {
        return new self(
            (int) $data['from'],
            (int) $data['to'],
            (int) $data['city'],
        );
    }
}
