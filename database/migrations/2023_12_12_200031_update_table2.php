<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('exams', function (Blueprint $table) {
            $table->boolean("repeat_sound")->default(false);
            $table->boolean("show_result_user")->default(true);
            $table->timestamp('start_time')->nullable(); // Imtahan baslama vaxdi
        });
        Schema::table('exam_questions', function (Blueprint $table) {
            $table->string("layout")->default('standart'); // standart (sol ve sag),onepage (yuxarida sual asagida cavab)
        });
        Schema::table('categories', function (Blueprint $table) {
            $table->string("icon")->nullable();
        });
        Schema::table('users', function (Blueprint $table) {
            $table->integer("user_type")->default(1);
        });
        Schema::table('exam_results', function (Blueprint $table) {
            $table->integer("user_type")->default(1);
            $table->boolean("payed")->default(false);
        });
        Schema::table('sections', function (Blueprint $table) {
            $table->integer("time_range_sections")->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
};
