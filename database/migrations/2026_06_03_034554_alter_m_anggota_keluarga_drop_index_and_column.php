<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('m_anggota_keluarga', function (Blueprint $table) {
            $table->dropUnique('m_anggota_keluarga_nik_unique');

            $table->dropColumn('no_kk_asal');
        });
    }

    public function down(): void
    {
        Schema::table('m_anggota_keluarga', function (Blueprint $table) {
            $table->string('no_kk_asal', 20)->nullable();

            $table->unique('nik', 'm_anggota_keluarga_nik_unique');
        });
    }
};
