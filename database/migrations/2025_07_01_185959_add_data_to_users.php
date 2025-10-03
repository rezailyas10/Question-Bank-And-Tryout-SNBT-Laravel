<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->enum('jenjang', ['SD', 'SMP', 'SMA', 'Kuliah'])->nullable()->after('phone_number');
            $table->enum('kelas', ['7','8','9','10','11','12'])->nullable()->after('jenjang');

            // Tambah nama sekolah
            $table->string('sekolah')->nullable()->after('kelas');
            $table->string('instansi')->nullable()->after('sekolah');

            // Tambah social media (username saja, tanpa @ atau URL lengkap)
            $table->string('instagram')->nullable()->after('instansi');
            $table->string('facebook')->nullable()->after('instagram');
            $table->string('twitter')->nullable()->after('facebook');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
             $table->dropColumn(['twitter', 'facebook', 'instagram', 'sekolah','instansi','jenjang','kelas']);
        });
    }
};
