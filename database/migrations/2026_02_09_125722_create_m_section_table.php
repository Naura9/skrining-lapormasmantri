<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('m_section', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('kategori_id');
            $table->string('judul_section', 150);
            $table->integer('no_urut');
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('kategori_id')
                ->references('id')
                ->on('m_kategori')
                ->onDelete('cascade');

            $table->index('kategori_id');
            $table->index('no_urut');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('m_section');
    }
};
