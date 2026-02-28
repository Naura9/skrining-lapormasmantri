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
        Schema::create('t_skrining', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('keluarga_id');
            $table->uuid('user_id');
            $table->date('tanggal_skrining');
            
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('keluarga_id')
                ->references('id')
                ->on('m_keluarga')
                ->onDelete('cascade');

            $table->foreign('user_id')
                ->references('id')
                ->on('m_user')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('t_skrining');
    }
};
