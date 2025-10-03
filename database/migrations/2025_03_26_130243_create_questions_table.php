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
        Schema::create('questions', function (Blueprint $table) {
            $table->id();
            $table->longText('text');
            $table->string('photos')->nullable();
            $table->enum('question_type', ['pilihan_ganda', 'pilihan_majemuk', 'isian']);
            $table->longtext('explanation')->nullable();
            $table->string('materi');
            $table->foreignId('exam_id')->constrained()->onDelete('cascade');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('questions');
    }
};
