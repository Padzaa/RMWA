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
        Schema::create('collection_recipes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('collection_id');
            $table->foreign('collection_id')
                ->references('id')
                ->on('collections')
                ->onUpdate('cascade')
                ->onDelete('cascade');
            $table->unsignedBigInteger('recipe_id');
            $table->foreign('recipe_id')
                ->references('id')
                ->on('recipes')
                ->onUpdate('cascade')
                ->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('collection_recipes');
    }
};
