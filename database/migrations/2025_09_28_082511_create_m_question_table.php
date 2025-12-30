<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('m_question', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('category_id');
            $table->integer('order_no')->nullable();             
            $table->string('question_text', 255);             
            $table->string('question_type'); 
            
            $table->timestamps();
            $table->softDeletes();

            $table->index('order_no');                  
            $table->index('question_text');                  
            
            $table->foreign('category_id')->references('id')->on('m_category')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('m_question');
    }
};
