<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->foreignId('hairdresser_id')
                ->nullable()
                ->after('email')
                ->constrained()
                ->cascadeOnDelete();
        });

        $defaultHairdresserId = DB::table('hairdressers')->insertGetId([
            'name' => 'Main Salon',
            'email' => 'hairdresser@example.com',
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('bookings')->update(['hairdresser_id' => $defaultHairdresserId]);

        Schema::table('bookings', function (Blueprint $table) {
            $table->dropUnique('bookings_scheduled_at_unique');
        });

        Schema::table('bookings', function (Blueprint $table) {
            $table->unique(['hairdresser_id', 'scheduled_at'], 'bookings_hairdresser_scheduled_unique');
            $table->index(['email', 'scheduled_at'], 'bookings_email_scheduled_index');
        });

        if (Schema::getConnection()->getDriverName() === 'mysql') {
            DB::statement('ALTER TABLE bookings MODIFY hairdresser_id BIGINT UNSIGNED NOT NULL');
        }
    }

    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropUnique('bookings_hairdresser_scheduled_unique');
            $table->dropIndex('bookings_email_scheduled_index');
            $table->dropForeign(['hairdresser_id']);
            $table->dropColumn('hairdresser_id');
        });

        Schema::table('bookings', function (Blueprint $table) {
            $table->unique('scheduled_at', 'bookings_scheduled_at_unique');
        });
    }
};
