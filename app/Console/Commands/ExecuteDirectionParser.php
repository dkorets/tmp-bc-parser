<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\Direction;
use App\Repository\DirectionRepository;
use App\Services\ProcessorCacheDecorator;
use App\Services\Scrapper;
use Illuminate\Console\Command;
use Illuminate\Contracts\Cache\Repository;
use Psr\Log\LoggerInterface;

class ExecuteDirectionParser extends Command
{
    protected $signature = 'bestchange:parse';

    protected $description = 'Execute bestchange parser for each direction';

    private array $exceptionsBag = [];

    public function __construct(
        private readonly LoggerInterface $logger,
        private readonly DirectionRepository $repository,
        private readonly Scrapper $scrapper,
        private readonly Repository $cache,
    ) {
        parent::__construct();
    }

    public function handle()
    {
        $this->logger->info('ExecuteDirectionParser: started');

        // TODO: notify subscribers by callback/websockets
        /** @var Direction $direction */
        foreach ($this->repository->getEnabled() as $direction) {
            $this->processDirection($direction);
        }

        $this->logger->info('ExecuteDirectionParser: finished', [
            'errors_count' => count($this->exceptionsBag),
        ]);

        if ([] !== $this->exceptionsBag) {
            throw array_pop($this->exceptionsBag);
        }
    }

    private function processDirection(Direction $direction): void
    {
        try {
            $data = $this->scrapper->fetchRates(
                $direction->from,
                $direction->to,
                $direction->city,
            );

            $this->cache->set(
                ProcessorCacheDecorator::buildCacheKey($direction),
                $data,
                ProcessorCacheDecorator::CACHE_TTL_SECONDS,
            );
        } catch (\Throwable $exception) {
            $this->logger->error('ExecuteDirectionParser external HTTP call failed', [
                'direction_id' => $direction->id,
                'exception' => $exception->getMessage(),
            ]);

            $this->exceptionsBag[] = $exception;
        }
    }
}
