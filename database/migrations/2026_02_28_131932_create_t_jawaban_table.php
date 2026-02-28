<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('t_jawaban', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->uuid('skrining_id');
            $table->uuid('pertanyaan_id');
            $table->uuid('anggota_keluarga_id')->nullable();

            $table->text('value_jawaban')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->foreign('skrining_id')
                ->references('id')
                ->on('t_skrining')
                ->onDelete('cascade');

            $table->foreign('pertanyaan_id')
                ->references('id')
                ->on('m_pertanyaan')
                ->onDelete('cascade');

            $table->foreign('anggota_keluarga_id')
                ->references('id')
                ->on('m_anggota_keluarga')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('t_jawaban');
    }
};
