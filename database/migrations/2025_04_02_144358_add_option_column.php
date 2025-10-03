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
        Schema::table('multiple_choices', function (Blueprint $table) {
            $table->string('option1')->after('option_text');
            $table->string('option2')->after('option1');
            $table->string('option3')->after('option2');
            $table->string('option4')->after('option3');
            $table->string('option5')->after('option4');
            $table->string('correct_answer')->after('option5');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('multiple_choices', function (Blueprint $table) {
            $table->dropColumn('option1');
            $table->dropColumn('option2');
            $table->dropColumn('option3');
            $table->dropColumn('option4');
            $table->dropColumn('option5');
            $table->dropColumn('correct_answer');
        });
    }
};
