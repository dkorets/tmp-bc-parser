<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Direction;
use App\Services\Dto\TableRows;
use Illuminate\Contracts\Cache\Repository as Cache;

class ProcessorCacheDecorator implements ProcessorInterface
{
    public const CACHE_KEY_PREFIX = 'direction:';
    public const CACHE_TTL_SECONDS = 300;

    public function __construct(
        private readonly Cache $cache,
        private readonly ProcessorInterface $processor,
    ) {
    }

    public static function buildCacheKey(Direction $direction): string
    {
        return self::CACHE_KEY_PREFIX . $direction->id;
    }

    public function handleMultiple(Direction ...$directions): array
    {
        $directionsData = [];

        foreach ($directions as $direction) {
            $directionsData[] = $this->handleSingle($direction);
        }

        return $directionsData;
    }

    public function handleSingle(Direction $direction): TableRows
    {
        // TODO: add cache hitrate metric, if $direction->usage > 0 or $direction->enabled = false
//        $cacheData = $this->cache->get($this->buildCacheKey($direction));
//
//        if (null !== $cacheData) {
//            return $cacheData;
//        }

        return $this->processor->handleSingle($direction);
    }
}
