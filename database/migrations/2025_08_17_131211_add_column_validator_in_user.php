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
             $table->boolean('is_validator')
                  ->default(false)
                  ->after('roles'); // letakkan setelah kolom roles
             $table->foreignId('sub_category_id')
                  ->nullable()
                  ->constrained('sub_categories')
                  ->onDelete('set null');
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
             $table->dropColumn('is_validator');
              $table->dropForeign(['sub_category_id']);
            $table->dropColumn('sub_category_id');
        });
    }
};
