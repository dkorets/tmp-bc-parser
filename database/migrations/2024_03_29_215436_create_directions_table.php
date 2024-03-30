<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('directions', function (Blueprint $table) {
            $table->id();
            $table->unsignedSmallInteger('from');
            $table->unsignedSmallInteger('to');
            $table->unsignedSmallInteger('city');
            $table->unsignedBigInteger('usage');
            $table->boolean('enabled')->default(true);
            $table->timestamps();

            $table->unique(['from', 'to', 'city']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('directions');
    }
};
