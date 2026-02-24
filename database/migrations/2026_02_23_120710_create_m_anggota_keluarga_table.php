<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('m_anggota_keluarga', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->uuid('keluarga_id');

            $table->string('nama', 150);
            $table->string('nik', 16)->unique();
            $table->string('tempat_lahir', 100);
            $table->date('tanggal_lahir');

            $table->enum('jenis_kelamin', ['L', 'P']);

            $table->string('no_kk_asal', 16)->nullable();

            $table->string('hubungan_keluarga', 50); 
            $table->string('status_perkawinan', 30); 
            $table->string('pendidikan_terakhir', 50);
            $table->string('pekerjaan', 100)->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->foreign('keluarga_id')
                ->references('id')
                ->on('m_keluarga')
                ->onDelete('cascade');

            $table->index('keluarga_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('m_anggota_keluarga');
    }
};