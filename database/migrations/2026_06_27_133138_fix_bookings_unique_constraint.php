<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        try {
            Schema::table('bookings', function (Blueprint $table) {
                $table->dropUnique('bookings_scheduled_at_unique');
                $table->unique(['scheduled_at', 'hairdresser_id'], 'bookings_scheduled_at_hairdresser_id_unique');
            });
        } catch (\Throwable $e) {
            // Ignore if the unique constraint doesn't exist
        }
    }

    public function down(): void
    {
        
    
        try {
            $table->dropUnique('bookings_hairdresser_scheduled_unique');
        } catch (\Throwable $e) {}

        $hasDuplicates = \DB::table('bookings')
            ->select('scheduled_at')
            ->groupBy('scheduled_at')
            ->havingRaw('COUNT(*) > 1')
            ->exists();

        if (! $hasDuplicates) {
            try {
                $table->unique('scheduled_at', 'bookings_scheduled_at_unique');
            } catch (\Throwable $e) {}
        }

    }
};
