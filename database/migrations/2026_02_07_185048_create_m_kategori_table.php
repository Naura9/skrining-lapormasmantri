<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('m_kategori', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('nama_kategori', 100);
            $table->enum('target_skrining', ['nik', 'kk']);
            $table->timestamps();
            $table->softDeletes();

            $table->index('nama_kategori');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('m_kategori');
    }
};
