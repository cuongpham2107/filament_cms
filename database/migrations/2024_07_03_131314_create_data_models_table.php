<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('data_models', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('schema');
            $table->text('relations')->nullable();
            $table->text('resource')->nullable();
            $table->string('trait_ralation_path')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('data_models');
    }
};
