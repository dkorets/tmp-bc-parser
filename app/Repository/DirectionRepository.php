<?php

declare(strict_types=1);

namespace App\Repository;
use App\Models\Direction;
use App\Services\Dto\Target;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class DirectionRepository
{
    public function getBuilder(): Builder
    {
        return (new Direction())->newQuery();
    }

    public function getEnabled(): Collection
    {
        return $this->getBuilder()->where('enabled', true)->get();
    }

    public function incrementUsage(Direction $direction): int
    {
        return $this->getBuilder()
            ->where('id', $direction->id)
            ->increment('usage');
    }

    public function foundByTargets(array $targetsList): Collection
    {
        return $this->getBuilder()->where(function ($query) use ($targetsList) {
            /** @var Target $target */
            foreach ($targetsList as $target) {
                $query->orWhere(function($subQuery) use ($target) {
                    $subQuery->where('from', $target->from)
                        ->where('to', $target->to)
                        ->where('city', $target->city);
                });
            }
        })->get();
    }
}
