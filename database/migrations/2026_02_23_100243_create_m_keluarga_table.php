<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('m_keluarga', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->uuid('unit_rumah_id');

            $table->string('no_kk', 16)->unique();
            $table->string('kepala_keluarga', 150);

            $table->timestamps();
            $table->softDeletes();

            $table->foreign('unit_rumah_id')
                ->references('id')
                ->on('m_unit_rumah')
                ->onDelete('cascade');

            $table->index('unit_rumah_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('m_keluarga');
    }
};