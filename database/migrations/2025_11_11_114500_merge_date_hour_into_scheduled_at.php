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
        Schema::table('bookings', function (Blueprint $table) {
            $table->dateTime('scheduled_at')->nullable()->after('email');
        });

        // Backfill scheduled_at from existing date + hour
        DB::statement("UPDATE bookings SET scheduled_at = CONCAT(date, ' ', hour)");

        // Make scheduled_at NOT NULL
        DB::statement('ALTER TABLE bookings MODIFY scheduled_at DATETIME NOT NULL');

        // Add unique index to prevent double-booking for the same slot
        Schema::table('bookings', function (Blueprint $table) {
            $table->unique('scheduled_at', 'bookings_scheduled_at_unique');
        });

        // Drop old columns
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn(['date', 'hour']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Recreate old columns
        Schema::table('bookings', function (Blueprint $table) {
            $table->date('date')->nullable()->after('email');
            $table->time('hour')->nullable()->after('date');
        });

        // Backfill date and hour from scheduled_at
        DB::statement('UPDATE bookings SET date = DATE(scheduled_at), hour = TIME(scheduled_at)');

        // Drop unique index
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropUnique('bookings_scheduled_at_unique');
        });

        // Allow NULLs temporarily to avoid DBAL requirement, then drop the column
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn('scheduled_at');
        });

        // Make old columns NOT NULL again
        DB::statement('ALTER TABLE bookings MODIFY date DATE NOT NULL');
        DB::statement('ALTER TABLE bookings MODIFY hour TIME NOT NULL');
    }
};
