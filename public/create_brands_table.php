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
        Schema::create('brands', function (Blueprint $table) {
            $table->integer('id')->unsigned()->autoIncrement()->nullable()->primary();
            $table->string('name', 255)->nullable();
            $table->string('slug', 255)->nullable()->unique();
            $table->integer('sort');
            $table->timestamp('created_at')->index();
            $table->timestamp('updated_at')->index();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('brands');
    }
};
