<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropUnique(['scheduled_at']);
        });

        Schema::table('bookings', function (Blueprint $table) {
            $table->unique(['hairdresser_id', 'scheduled_at']);
        });
    }

    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->unique(['scheduled_at']);
        });

        Schema::table('bookings', function (Blueprint $table) {
            $table->dropUnique(['hairdresser_id', 'scheduled_at']);
        });
    }
};
