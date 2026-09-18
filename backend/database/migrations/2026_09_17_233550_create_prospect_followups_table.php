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
        Schema::create('prospect_followups', function (Blueprint $table) {
            $table->id();

            $table->foreignId('prospect_id')
                ->constrained('prospects')
                ->cascadeOnDelete();

            $table->foreignId('prospect_contact_id')
                ->nullable()
                ->constrained('prospect_contacts')
                ->nullOnDelete();

            $table->foreignId('prospect_opportunity_id')
                ->nullable()
                ->constrained('prospect_opportunities')
                ->nullOnDelete();

            $table->string('type', 30);

            $table->string('title');

            $table->text('notes')
                ->nullable();

            $table->string('priority', 20)
                ->default('medium');

            $table->string('status', 20)
                ->default('pending');

            $table->dateTime('scheduled_at');

            $table->dateTime('completed_at')
                ->nullable();

            $table->string('outcome', 30)
                ->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->index([
                'status',
                'scheduled_at',
            ]);

            $table->index([
                'prospect_id',
                'scheduled_at',
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('prospect_followups');
    }
};
