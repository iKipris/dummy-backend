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
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string('first_name')->nullable();
            $table->unsignedBigInteger('user_id');
            $table->string('last_name')->nullable();
            $table->string('phone_number')->nullable();
            $table->string('avatar_link')->nullable();
            $table->string('address')->nullable();
            $table->string('zip_code')->nullable();
            $table->string('city')->nullable();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('settings');
    }
};
