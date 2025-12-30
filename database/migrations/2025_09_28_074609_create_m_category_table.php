<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('m_category', function (Blueprint $table) {
            $table->uuid('id')->primary();                   
            $table->string('category_name', 100);            
            $table->timestamps();                            
            $table->softDeletes();  
                                     
            $table->index('category_name');                  
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('m_category');
    }
};
