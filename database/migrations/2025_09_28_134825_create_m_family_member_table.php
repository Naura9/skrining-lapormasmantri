<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('m_family_member', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('family_id'); 

            $table->string('full_name', 100);
            $table->string('national_id_number', 16)->unique();
            $table->string('place_of_birth', 100);
            $table->date('date_of_birth');
            $table->enum('gender', ['male', 'female']);
            $table->string('relationship', 50);
            $table->string('marital_status', 50);
            $table->string('last_education', 50)->nullable();
            $table->string('occupation', 100)->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->index(['family_id','full_name','national_id_number']);
            
            $table->foreign('family_id')->references('id')->on('m_family')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('m_family_member');
    }
};
