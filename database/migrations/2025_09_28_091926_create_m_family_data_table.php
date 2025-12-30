<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('m_family', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('family_card_number', 20);
            $table->string('head_of_family', 100);
            $table->string('address', 255);
            $table->string('neighborhood_rt', 5);
            $table->string('neighborhood_rw', 5);
            $table->string('urban_village', 100);
            $table->string('posyandu', 100);

            $table->timestamps();
            $table->softDeletes();

            $table->index('family_card_number');                  
            $table->index('neighborhood_rt');                  
            $table->index('neighborhood_rw');                  
            $table->index('urban_village');                  
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('m_family');
    }
};
