<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Direction;
use App\Repository\DirectionRepository;
use App\Services\Dto\TableRows;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Http\Response;
use Psr\Log\LoggerInterface;

readonly class Processor implements ProcessorInterface
{
    public function __construct(
        private Scrapper            $scrapper,
        private HtmlParser          $htmlParser,
        private DirectionRepository $repository,
        private LoggerInterface $logger,
    ) {
    }

    /**
     * @param Direction ...$directions
     * @return TableRows[]
     * @throws GuzzleException
     */
    public function handleMultiple(Direction ...$directions): array
    {
        $directionsData = [];

        foreach ($directions as $direction) {
            $directionsData[] = $this->handleSingle($direction);
        }

        return $directionsData;
    }

    /**
     * @throws GuzzleException
     */
    public function handleSingle(Direction $direction): TableRows
    {
        $direction->enabled = true;
        $direction->save();
        $this->repository->incrementUsage($direction);

        try {
            $response = $this->scrapper->fetchRates(
                $direction->from,
                $direction->to,
                $direction->city,
            );
        } catch (\Throwable $throwable) {
            $this->logger->error('Bestchange scrapper request failed', [
                'direction' => $direction->toArray(),
                'error_message' => $throwable->getMessage(),
                'exception' => $throwable->getTraceAsString(),
            ]);

            return new TableRows($direction);
        }

        if ($response->getStatusCode() > Response::HTTP_OK) {
            $this->logger->error('Bestchange scrapper error response', [
                'direction' => $direction->toArray(),
                'response_status' => $response->getStatusCode(),
                'response_body' => $response->getBody()->getContents(),
            ]);

            return new TableRows($direction);
        }

        return $this->htmlParser->convertToCollection(
            $direction,
            $response->getBody()->getContents(),
        );
    }
}
