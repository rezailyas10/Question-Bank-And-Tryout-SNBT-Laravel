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
        Schema::create('results_evaluations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('result_id')->constrained()->onDelete('cascade');
            $table->longText('evaluation')->nullable();     // hasil analisis
            $table->longText('recommendation')->nullable();
            $table->integer('correct')->nullable();
            $table->integer('wrong')->nullable();
            $table->float('score')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('results_evaluations');
    }
};
