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
            $table->json("name")->nullable()->change();
            $table->boolean("show_calc")->default(false);
            $table->double("price",10,2)->default(0);
            $table->double("endirim_price",10,2)->default(0);
            $table->unsignedBigInteger("user_id");
            $table->string("user_type")->default('admin');
            $table->integer("time_range_sections")->nullable();
        });
        Schema::table('exam_results', function (Blueprint $table) {
            $table->integer("time_reply")->default(0);
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
