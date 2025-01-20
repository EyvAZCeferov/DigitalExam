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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->string("token"); // token creating
            $table->double('amount',10,2)->default(0.00);
            $table->string("transaction_id",255)->nullable();
            $table->boolean("payment_status")->default(false);
            $table->json("data")->nullable();
            $table->json("frompayment")->nullable();
            $table->unsignedBigInteger("exam_id");
            $table->foreign('exam_id')->references("id")->on("exams")->onDelete('cascade');
            $table->unsignedBigInteger("user_id");
            $table->foreign('user_id')->references("id")->on("users")->onDelete('cascade');
            $table->unsignedBigInteger("coupon_id");
            $table->foreign('coupon_id')->references("id")->on("coupon_codes")->onDelete('cascade');
            $table->unsignedBigInteger("exam_result_id");
            $table->foreign('exam_result_id')->references("id")->on("exam_results")->onDelete('cascade');
            $table->json("exam_data")->nullable();
            $table->json("user_data")->nullable();
            $table->json("coupon_data")->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('payments');
    }
};
