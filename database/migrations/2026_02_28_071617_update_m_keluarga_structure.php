<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('m_keluarga', function (Blueprint $table) {
            $table->dropUnique(['no_kk']);

            $table->dropColumn('kepala_keluarga');

            $table->boolean('is_luar_wilayah')
                ->default(false)
                ->after('no_kk');

            $table->text('alamat_ktp')
                ->nullable()
                ->after('is_luar_wilayah');

            $table->string('rt_ktp', 3)
                ->nullable()
                ->after('alamat_ktp');

            $table->string('rw_ktp', 3)
                ->nullable()
                ->after('rt_ktp');

            $table->index('no_kk');
        });
    }

    public function down(): void
    {
        Schema::table('m_keluarga', function (Blueprint $table) {

            $table->dropIndex(['no_kk']);

            $table->dropColumn([
                'is_luar_wilayah',
                'alamat_ktp',
                'rt_ktp',
                'rw_ktp'
            ]);

            $table->string('kepala_keluarga', 150);

            $table->unique('no_kk');
        });
    }
};