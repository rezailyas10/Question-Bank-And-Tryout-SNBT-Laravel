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
            $table->foreignId('sub_category_id')->constrained()->onDelete('cascade');
            $table->boolean('is_published')->default(false)->after('slug');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('exams', function (Blueprint $table) {
            $table->dropForeign('exams_sub_category_id_foreign');
            $table->dropColumn('sub_category_id');
            $table->dropColumn('is_published');
        });
    }
};
