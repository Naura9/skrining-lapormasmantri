<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('m_anggota_keluarga', function (Blueprint $table) {
            $table->string('tempat_lahir', 100)->nullable()->change();
            $table->date('tanggal_lahir')->nullable()->change();
            $table->enum('jenis_kelamin', ['L', 'P'])->nullable()->change();
            $table->string('status_perkawinan', 30)->nullable()->change();
            $table->string('pendidikan_terakhir', 50)->nullable()->change();

            $table->index(['keluarga_id', 'hubungan_keluarga']);
        });
    }

    public function down(): void
    {
        Schema::table('m_anggota_keluarga', function (Blueprint $table) {
            $table->string('tempat_lahir', 100)->nullable(false)->change();
            $table->date('tanggal_lahir')->nullable(false)->change();
            $table->enum('jenis_kelamin', ['L', 'P'])->nullable(false)->change();
            $table->string('status_perkawinan', 30)->nullable(false)->change();
            $table->string('pendidikan_terakhir', 50)->nullable(false)->change();
        });
    }
};