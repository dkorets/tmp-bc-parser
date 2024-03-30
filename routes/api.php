<?php

declare(strict_types=1);

use App\Http\Controllers\ListExecutionHistoryAction;
use App\Http\Controllers\ParseAction;
use Illuminate\Support\Facades\Route;

// TODO: add authentication
Route::get('/directions', [ParseAction::class, 'index'])->name('directions.index');
Route::get('/directions/{uid}/history', ListExecutionHistoryAction::class)->name('directions.history');
