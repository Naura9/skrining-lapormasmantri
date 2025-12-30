<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('t_screening', function (Blueprint $table) {
            $table->uuid('id')->primary();                               
            $table->uuid('family_id');                                   
            $table->uuid('user_id');                                     
            $table->date('screening_date');                              
            $table->timestamps();                                       
            $table->softDeletes();                                       

            $table->foreign('family_id')->references('id')->on('m_family')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('m_user')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('t_screening');
    }
};
