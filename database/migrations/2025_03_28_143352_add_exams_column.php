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
        Schema::table('exams', function (Blueprint $table) {
           $table->string('created_by')->after('slug');
           $table->integer('timer')->nullable()->after('created_by');
           $table->dateTime('tanggal_dibuka')->nullable()->after('timer');
           $table->dateTime('tanggal_ditutup')->nullable()->after('tanggal_dibuka');
           $table->foreignId('exam_category_id')->constrained()->nullable()->after('tanggal_ditutup');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('exams', function (Blueprint $table) {
            $table->dropForeign('exams_exam_category_id_foreign');
            $table->dropColumn('exam_category_id');
            $table->dropColumn('exam_category_id');
            $table->dropColumn('tanggal_ditutup');
            $table->dropColumn('tanggal_dibuka');
            $table->dropColumn('timer');
            $table->dropColumn('created_by');
            
        });
    }
};
