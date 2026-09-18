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
        Schema::create('prospect_import_batches', function (Blueprint $table) {
            $table->id();
            $table->string('external_id')
                ->unique();

            $table->string('source', 100);

            $table->string('status', 30)
                ->default('received');

            $table->unsignedInteger('total')
                ->default(0);

            $table->unsignedInteger('ready')
                ->default(0);

            $table->unsignedInteger('duplicates')
                ->default(0);

            $table->unsignedInteger('errors')
                ->default(0);

            $table->json('metadata')
                ->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('prospect_import_batches');
    }
};
