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
        Schema::table('questions', function (Blueprint $table) {
            $table->foreignId('user_id')->nullable()->after('exam_id')->constrained()->onDelete('cascade');
               $table->enum('status',['Ditinjau', 'Diterima', 'Ditolak'])->default('Ditinjau')->after('user_id');
                  $table->enum('difficulty',['Mudah', 'Sedang', 'Sulit'])->nullable()->after('explanation'); 

            
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('questions', function (Blueprint $table) {
            $table->dropForeign(['user_id']); // hapus foreign key constraint dulu
        $table->dropColumn(['user_id', 'status', 'difficulty']); // hapus kolom
        });
    }
};
