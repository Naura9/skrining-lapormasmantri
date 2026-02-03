<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('m_user', function (Blueprint $table) {
            $table->renameColumn('email', 'username');
        });

        Schema::table('m_user', function (Blueprint $table) {
            $table->string('username', 50)->unique()->change();
        });
    }

    public function down(): void
    {
        Schema::table('m_user', function (Blueprint $table) {
            $table->renameColumn('username', 'email');
        });

        Schema::table('m_user', function (Blueprint $table) {
            $table->string('email', 50)->change();
        });
    }
};
