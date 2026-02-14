<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('m_pertanyaan', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('section_id');

            $table->integer('no_urut');
            $table->text('pertanyaan');

            $table->string('jenis_pertanyaan', 50);

            $table->json('opsi_jawaban')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->foreign('section_id')
                ->references('id')
                ->on('m_section')
                ->onDelete('cascade');

            $table->index('section_id');
            $table->index(['section_id', 'no_urut']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('m_pertanyaan');
    }
};
