<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE aset_mes MODIFY kategori ENUM('putra', 'putri', 'koordinator', 'johen_streamer') NOT NULL DEFAULT 'putra'");
        } else {
            Schema::table('aset_mes', function (Blueprint $table) {
                $table->string('kategori')->default('putra')->change();
            });
        }
    }

    public function down(): void
    {
        DB::table('aset_mes')->whereIn('kategori', ['koordinator', 'johen_streamer'])->update(['kategori' => 'putra']);

        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE aset_mes MODIFY kategori ENUM('putra', 'putri') NOT NULL DEFAULT 'putra'");
        } else {
            Schema::table('aset_mes', function (Blueprint $table) {
                $table->string('kategori')->default('putra')->change();
            });
        }
    }
};
