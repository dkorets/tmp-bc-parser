<?php

namespace App\Services;

use App\Models\Direction;
use App\Services\Dto\TableRows;

interface ProcessorInterface
{
    /**
     * @return TableRows[]
     */
    public function handleMultiple(Direction ...$directions): array;

    public function handleSingle(Direction $direction): TableRows;
}
