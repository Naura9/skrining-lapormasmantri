<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('m_user_nakes', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('user_id')->index();
            $table->uuid('kelurahan_id')->index();

            $table->string('nik', 20)->unique();
            $table->string('no_telepon', 15);
            $table->enum('jenis_kelamin', ['L', 'P']);

            $table->timestamps();
            $table->softDeletes();

            $table->foreign('user_id')
                ->references('id')
                ->on('m_user')
                ->onDelete('cascade');

            $table->foreign('kelurahan_id')
                ->references('id')
                ->on('m_kelurahan')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('m_user_nakes');
    }
};
