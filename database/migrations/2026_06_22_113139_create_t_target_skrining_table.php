<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('t_target_skrining', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->uuid('kelurahan_id');
            $table->uuid('kategori_id');

            $table->integer('target')->default(0);

            $table->timestamps();
            $table->softDeletes();

            $table->foreign('kelurahan_id')
                ->references('id')
                ->on('m_kelurahan')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->foreign('kategori_id')
                ->references('id')
                ->on('m_kategori')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('t_target_skrining');
    }
};