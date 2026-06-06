<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Migrate existing string values ke JSON array sebelum ubah kolom
        DB::table('daily_report_details')->get()->each(function ($row) {
            DB::table('daily_report_details')->where('id', $row->id)->update([
                'photo_physical' => $row->photo_physical ? json_encode([$row->photo_physical]) : json_encode([]),
                'photo_activity' => $row->photo_activity ? json_encode([$row->photo_activity]) : json_encode([]),
                'photo_other'    => $row->photo_other    ? json_encode([$row->photo_other])    : json_encode([]),
            ]);
        });

        Schema::table('daily_report_details', function (Blueprint $table) {
            $table->json('photo_physical')->nullable()->change();
            $table->json('photo_activity')->nullable()->change();
            $table->json('photo_other')->nullable()->change();
        });
    }

    public function down(): void
    {
        // Kembalikan ke string (ambil foto pertama saja)
        DB::table('daily_report_details')->get()->each(function ($row) {
            $physical = json_decode($row->photo_physical, true);
            $activity = json_decode($row->photo_activity, true);
            $other    = json_decode($row->photo_other, true);

            DB::table('daily_report_details')->where('id', $row->id)->update([
                'photo_physical' => $physical[0] ?? null,
                'photo_activity' => $activity[0] ?? null,
                'photo_other'    => $other[0]    ?? null,
            ]);
        });

        Schema::table('daily_report_details', function (Blueprint $table) {
            $table->string('photo_physical')->nullable()->change();
            $table->string('photo_activity')->nullable()->change();
            $table->string('photo_other')->nullable()->change();
        });
    }
};