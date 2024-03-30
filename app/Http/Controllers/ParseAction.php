<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\ParseBestChangeRequest;
use App\Models\Direction;
use App\Repository\DirectionRepository;
use App\Services\ProcessorInterface;
use Illuminate\Http\JsonResponse;

class ParseAction extends Controller
{
    public function index(
        ParseBestChangeRequest $request,
        ProcessorInterface $processor,
        DirectionRepository $repository,
    ): JsonResponse {
        $targets = $request->targets();

        $directions = $repository->foundByTargets($targets);

        /** @var Direction $direction */
        foreach ($directions as $direction) {
            if (true === array_key_exists($direction->uid(), $targets)) {
                unset($targets[$direction->uid()]);
            }
        }

        // now we have only unknown directions in $targets
        foreach ($targets as $target) {
            $newDirection = new Direction();
            $newDirection->from = $target->from;
            $newDirection->to = $target->to;
            $newDirection->city = $target->city;
            $newDirection->usage = 0;
            $newDirection->save();

            $directions->push($newDirection);
        }

        $result = $processor->handleMultiple(...$directions);

        // TODO: add metric http response time(by client)
        return new JsonResponse([
            'data' => $result,
        ]);
    }
}
