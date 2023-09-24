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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name', 255);
            $table->string('email', 255)->unique();
            $table->timestamps('email_verified_at')->nullable();
            $table->string('password', 255);
            $table->string('job', 255)->nullable();
            $table->string('usia', 255)->nullable();
            $table->string('motivation', 255)->nullable();
            $table->string('hometown', 255)->nullable();
            $table->string('image', 255)->nullable();
            $table->string('tipe', 255)->nullable();
            $table->string('remember_token', 255)->nullable();
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
        Schema::dropIfExists('users');
    }
};
