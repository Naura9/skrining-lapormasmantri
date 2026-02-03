<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('m_posyandu', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->uuid('kelurahan_id');
            $table->string('nama_posyandu', 100);

            $table->timestamps();
            $table->softDeletes();

            $table->index('kelurahan_id');
            $table->index('nama_posyandu');

            $table->foreign('kelurahan_id')
                  ->references('id')
                  ->on('m_kelurahan')
                  ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('m_posyandu');
    }
};
