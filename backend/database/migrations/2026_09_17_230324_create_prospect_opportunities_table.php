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
        Schema::create('prospect_opportunities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('prospect_id')
                ->constrained('prospects')
                ->cascadeOnDelete();

            $table->foreignId('service_id')
                ->constrained('services')
                ->cascadeOnDelete();

            $table->string('priority', 20)
                ->default('medium');

            $table->string('status', 30)
                ->default('detected');

            $table->decimal('estimated_amount', 12, 2)
                ->nullable();

            $table->text('notes')
                ->nullable();

            $table->timestamps();

            $table->unique([
                'prospect_id',
                'service_id',
            ]);

            $table->index('priority');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('prospect_opportunities');
    }
};
