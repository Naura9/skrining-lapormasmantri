<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('m_kelurahan', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('nama_kelurahan', 100);

            $table->timestamps();
            $table->softDeletes();

            $table->index('nama_kelurahan');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('m_kelurahan');
    }
};
