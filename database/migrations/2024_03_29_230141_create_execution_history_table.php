<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('execution_history', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('direction_id')->index();
            $table->unsignedInteger('response_time_ms')->index();
            $table->smallInteger('response_status')->index();
            $table->json('table_rows');
            $table->string('used_proxy');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('execution_history');
    }
};
