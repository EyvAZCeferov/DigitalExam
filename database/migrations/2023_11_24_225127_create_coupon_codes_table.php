<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('coupon_codes', function (Blueprint $table) {
            $table->id();
            $table->json("name")->nullable();
            $table->string("code")->default("edudistance");
            $table->integer("discount")->default(0);
            $table->boolean("status")->default(true);
            $table->string("type")->default('value'); //value,percent
            $table->unsignedBigInteger('user_id');
            $table->string("user_type")->default("admin");
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
        Schema::dropIfExists('coupon_codes');
    }
};
