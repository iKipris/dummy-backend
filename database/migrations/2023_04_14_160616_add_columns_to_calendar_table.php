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
        Schema::table('calendar_events', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id');
            $table->string('url')->nullable();
            $table->string('title')->nullable();
            $table->string('start')->nullable();
            $table->string('end')->nullable();
            $table->string('calendar')->nullable();
            $table->string('location')->nullable();
            $table->string('guests')->nullable();
            $table->string('description')->nullable();
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
        Schema::table('calendar_events', function (Blueprint $table) {
            //
        });
    }
};
