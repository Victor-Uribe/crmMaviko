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
        Schema::create('prospect_import_items', function (Blueprint $table) {
            $table->id();

            $table->foreignId('batch_id')
                ->constrained(
                    'prospect_import_batches'
                )
                ->cascadeOnDelete();

            $table->string('business_name');

            $table->unsignedTinyInteger(
                'quality_score'
            )->default(0);

            $table->string('status', 30)
                ->default('pending');

            $table->foreignId(
                'duplicate_prospect_id'
            )
                ->nullable()
                ->constrained('prospects')
                ->nullOnDelete();

            $table->foreignId(
                'imported_prospect_id'
            )
                ->nullable()
                ->constrained('prospects')
                ->nullOnDelete();

            $table->json('payload');

            $table->text('error_message')
                ->nullable();

            $table->timestamp('reviewed_at')
                ->nullable();

            $table->timestamps();

            $table->index([
                'status',
                'quality_score',
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('prospect_import_items');
    }
};
