<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('m_keluarga', function (Blueprint $table) {

            $table->string('no_telepon', 20)
                ->nullable()
                ->after('rw_ktp');

            $table->index('no_telepon');
        });
    }

    public function down(): void
    {
        Schema::table('m_keluarga', function (Blueprint $table) {

            $table->dropIndex(['no_telepon']);
            $table->dropColumn('no_telepon');
        });
    }
};
