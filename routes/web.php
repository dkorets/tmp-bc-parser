<?php

declare(strict_types=1);

use App\Http\Controllers\ParseAction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Psr\Log\LoggerInterface;

Route::get('/', function (LoggerInterface $logger, Request $request) {
    $logger->info('homepage opened', ['ip' => $request->ip()]);

    // TODO render openAPI spec
    return 'bestchange-parser';
});
