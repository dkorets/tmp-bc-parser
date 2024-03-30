<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Direction;
use App\Repository\DirectionRepository;
use App\Services\Dto\TableRows;
use GuzzleHttp\Exception\GuzzleException;

readonly class Processor implements ProcessorInterface
{
    public function __construct(
        private Scrapper            $scrapper,
        private HtmlParser          $htmlParser,
        private DirectionRepository $repository,
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
        $response = $this->scrapper->fetchRates(
            $direction->from,
            $direction->to,
            $direction->city,
        );

        $direction->enabled = true;
        $direction->save();
        $this->repository->incrementUsage($direction);

        return $this->htmlParser->convertToCollection(
            $direction,
            $response->getBody()->getContents(),
        );
    }
}
