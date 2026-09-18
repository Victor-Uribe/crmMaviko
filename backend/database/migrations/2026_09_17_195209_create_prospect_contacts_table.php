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
        Schema::create('prospect_contacts', function (Blueprint $table) {
            $table->id();

            $table->foreignId('prospect_id')
                ->constrained('prospects')
                ->cascadeOnDelete();

            $table->string('type', 50);

            $table->string('label')->nullable();

            $table->string('value');

            $table->string('normalized_value')->nullable();

            $table->boolean('is_primary')->default(false);

            $table->boolean('is_verified')->default(false);

            $table->text('source_url')->nullable();

            $table->text('notes')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->index(['type', 'normalized_value']);
            $table->index('prospect_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('prospect_contacts');
    }
};
