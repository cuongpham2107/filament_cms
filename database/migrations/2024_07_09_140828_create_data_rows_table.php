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
        Schema::create('data_rows', function (Blueprint $table) {
            $table->id();
            $table->foreignId('data_type_id')->constrained('data_types')->onDelete('cascade');
            $table->string('field');
            $table->string('type');
            $table->string('type_filament')->nullable();
            $table->integer('length')->nullable();
            $table->boolean('notnull')->default(false);
            $table->boolean('unsigned')->default(false);
            $table->boolean('autoincrement')->default(false);
            $table->string('index')->default('none');
            $table->string('default')->nullable();
            $table->string('display_name')->nullable();
            $table->boolean('required')->default(false);
            $table->string('show')->nullable();
            $table->string('details')->nullable();
            $table->integer('order')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('data_rows');
    }
};
