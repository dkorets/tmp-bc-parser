<?php

declare(strict_types=1);

namespace App\Services\Dto;

use App\Models\Direction;

class TableRows
{
    /** @var TableRow[] */
    public array $rows;

    public function __construct(
        public readonly Direction $direction
    ) {
    }

    public function addRow(TableRow $row): self
    {
        $this->rows[] = $row;

        return $this;
    }
}
