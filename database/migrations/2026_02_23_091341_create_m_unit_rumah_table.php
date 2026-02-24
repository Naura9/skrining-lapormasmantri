<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('m_unit_rumah', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->uuid('kelurahan_id');
            $table->uuid('posyandu_id');

            $table->text('alamat');
            $table->string('rt', 3);
            $table->string('rw', 3);

            $table->timestamps();
            $table->softDeletes();

            $table->foreign('kelurahan_id')
                ->references('id')
                ->on('m_kelurahan')
                ->onDelete('cascade');

            $table->foreign('posyandu_id')
                ->references('id')
                ->on('m_posyandu')
                ->onDelete('cascade');

            $table->index('kelurahan_id');
            $table->index('posyandu_id');
            $table->index(['kelurahan_id', 'posyandu_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('m_unit_rumah');
    }
};