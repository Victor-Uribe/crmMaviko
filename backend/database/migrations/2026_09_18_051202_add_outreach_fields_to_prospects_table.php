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
        Schema::table('prospects', function (Blueprint $table) {
            //
             $table->text('whatsapp_message')
                ->nullable();

            $table->string('email_subject')
                ->nullable();

            $table->text('email_message')
                ->nullable();

            $table->text('phone_script')
                ->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('prospects', function (Blueprint $table) {
            //
             $table->dropColumn([
                'whatsapp_message',
                'email_subject',
                'email_message',
                'phone_script',
            ]);
        });
    }
};
