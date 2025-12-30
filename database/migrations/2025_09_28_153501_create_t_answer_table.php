<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('t_answer', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('question_id');
            $table->uuid('family_id');
            $table->uuid('family_member_id')->nullable();
            $table->uuid('screening_id');
            $table->text('answer_value')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->foreign('question_id')->references('id')->on('m_question')->onDelete('cascade');
            $table->foreign('family_id')->references('id')->on('m_family')->onDelete('cascade');
            $table->foreign('family_member_id')->references('id')->on('m_family_member')->onDelete('set null');
            $table->foreign('screening_id')->references('id')->on('t_screening')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('t_answer');
    }
};
