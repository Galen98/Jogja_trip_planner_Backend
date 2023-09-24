<?php

// database/migrations/YYYY_MM_DD_create_user_likes_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserLikesTable extends Migration
{
    public function up()
    {
        Schema::create('user_likes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('users_id');
            $table->unsignedBigInteger('wisata_id');
            $table->string('nama', 255)->nullable();
            $table->integer('rating', 11)->nullable();
            $table->string('kategori', 255)->nullable();
            $table->string('image', 255)->nullable();
            $table->string('url_maps', 255)->nullable();
            $table->string('jenis_wisata', 255)->nullable();
            $table->string('deskripsi', 255)->nullable();
            $table->string('isnight', 255)->nullable();
            $table->string('anak', 255)->nullable();
            $table->string('lansia', 255)->nullable();
            $table->string('descitinerary', 255)->nullable();
            $table->string('htm_weekend', 255)->nullable();
            $table->string('htm_weekday', 255)->nullable();
            $table->string('durasi', 255)->nullable();
            $table->string('things', 255)->nullable();
            $table->decimal('latitude', 9, 7)->nullable();
            $table->decimal('longitude', 10, 6)->nullable();
            $table->timestamps();
            $table->foreign('users_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('user_likes');
    }
}

