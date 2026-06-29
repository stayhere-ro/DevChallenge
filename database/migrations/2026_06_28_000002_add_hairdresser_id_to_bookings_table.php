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
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropUnique('bookings_scheduled_at_unique');
            $table->foreignId('hairdresser_id')
                ->nullable()
                ->after('email')
                ->constrained()
                ->nullOnDelete();
            $table->unique(['hairdresser_id', 'scheduled_at'], 'bookings_hairdresser_scheduled_at_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropUnique('bookings_hairdresser_scheduled_at_unique');
            $table->dropConstrainedForeignId('hairdresser_id');
            $table->unique('scheduled_at', 'bookings_scheduled_at_unique');
        });
    }
};
