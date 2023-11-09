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
        Schema::create('shared_recipes', function (Blueprint $table) {
            $table->id();


            $table->unsignedBigInteger('recipe_id');
            $table
                ->foreign('recipe_id')
                ->references('id')
                ->on('recipes')
                ->onDelete('CASCADE')
                ->onUpdate('CASCADE');

            $table->unsignedBigInteger('user_shared_to');
            $table
                ->foreign('user_shared_to')
                ->references('id')
                ->on('users')
                ->onDelete('CASCADE')
                ->onUpdate('CASCADE');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shared_recipes');
    }
};
