<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Make name nullable (so API route can omit it)
        DB::statement('ALTER TABLE bookings MODIFY name VARCHAR(255) NULL');
        // Add hairdresser_id (FK to users)
        Schema::table('bookings', function (Blueprint $table) {
            $table->foreignId('hairdresser_id')
                ->nullable()
                ->after('id')
                ->constrained('users')
                ->cascadeOnDelete();
        });
        // Replace unique(scheduled_at) with unique(hairdresser_id, scheduled_at)
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropUnique('bookings_scheduled_at_unique');
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
            $table->unique('scheduled_at', 'bookings_scheduled_at_unique');
            $table->dropConstrainedForeignId('hairdresser_id');
        });

        DB::statement('ALTER TABLE bookings MODIFY name VARCHAR(255) NOT NULL');
    }
};
