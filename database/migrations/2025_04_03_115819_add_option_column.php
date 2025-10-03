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
        Schema::table('multiple_options', function (Blueprint $table) {
            $table->string('multiple1')->after('multiple_text');
            $table->enum('yes/no1',['yes','no'])->after('multiple1');
            $table->string('multiple2')->after('yes/no1');
            $table->enum('yes/no2',['yes','no'])->after('multiple2');
            $table->string('multiple3')->after('yes/no2');
            $table->enum('yes/no3',['yes','no'])->after('multiple3');
            $table->string('multiple4')->after('yes/no3');
            $table->enum('yes/no4',['yes','no'])->after('multiple4')->nullable();
            $table->string('multiple5')->after('yes/no4');
            $table->enum('yes/no5',['yes','no'])->after('multiple5')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('multiple_options', function (Blueprint $table) {

            $table->dropColumn('multiple1');
            $table->dropColumn('yes/no1');
            $table->dropColumn('multiple2');
            $table->dropColumn('yes/no2');
            $table->dropColumn('multiple3');
            $table->dropColumn('yes/no3');
            $table->dropColumn('multiple4');
            $table->dropColumn('yes/no4');
            $table->dropColumn('multiple5');
            $table->dropColumn('yes/no5');
        });
    }
};
