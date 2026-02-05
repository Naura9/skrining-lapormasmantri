<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('m_user_kader', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('user_id')->index();
            $table->uuid('posyandu_id')->index();

            $table->string('no_telepon', 15);
            $table->enum('jenis_kelamin', ['L', 'P']);
            $table->enum('status', ['aktif', 'nonaktif']);

            $table->timestamps();
            $table->softDeletes();

            $table->foreign('user_id')
                ->references('id')
                ->on('m_user')
                ->onDelete('cascade');

            $table->foreign('posyandu_id')
                ->references('id')
                ->on('m_posyandu')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('m_user_kader');
    }
};
