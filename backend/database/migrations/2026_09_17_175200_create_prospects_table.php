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
        Schema::create('prospects', function (Blueprint $table) {
             $table->id();

            $table->string('business_name');

            $table->string('category')->nullable();

            $table->text('description')->nullable();

            $table->string('country')->default('México');

            $table->string('state')->nullable();

            $table->string('city')->nullable();

            $table->text('address')->nullable();

            $table->string('source')->nullable();

            $table->text('source_url')->nullable();

            $table->text('opportunity')->nullable();

            $table->unsignedTinyInteger('quality_score')->default(0);

            $table->string('stage')->default('new');

            $table->timestamps();

            $table->softDeletes();

            $table->index('stage');
            $table->index('city');
            $table->index('quality_score');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('prospects');
    }
};
