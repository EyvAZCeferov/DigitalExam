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
        Schema::create('mark_questions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger("exam_id");
            $table->foreign('exam_id')->references("id")->on("exams")->onDelete('cascade');
            $table->unsignedBigInteger("exam_result_id");
            $table->foreign('exam_result_id')->references("id")->on("exam_results")->onDelete('cascade');
            $table->unsignedBigInteger("question_id");
            $table->foreign('question_id')->references("id")->on("exam_questions")->onDelete('cascade');
            $table->unsignedBigInteger("user_id");
            $table->foreign('user_id')->references("id")->on("users")->onDelete('cascade');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('mark_questions');
    }
};
