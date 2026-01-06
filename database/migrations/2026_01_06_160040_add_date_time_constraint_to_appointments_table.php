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
            DB::statement('ALTER TABLE bookings
            ADD CONSTRAINT check_date_time
            CHECK (
                TIME(`time`) BETWEEN TIME("08:00:00") AND TIME("18:00:00")
                AND DAYOFWEEK(`date`) NOT IN (1,7)
            )
        ');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('appointments', function (Blueprint $table) {



        });
    }
};
