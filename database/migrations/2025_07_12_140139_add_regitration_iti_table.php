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
        Schema::create('registration_iti', function (Blueprint $table) {
            $table->id();
            $table->foreignId('result_id')->constrained()->onDelete('cascade');
            $table->string('periode_akademik');
            $table->string('program_studi');
            $table->boolean('agree_to_contact')->default(false);
            $table->enum('status', ['belum dihubungi', 'sudah dihubungi'])->default('belum dihubungi');
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('registration_iti');
    }
};
