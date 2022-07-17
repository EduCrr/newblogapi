<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAllTables extends Migration
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
            $table->string('name');
            $table->string('avatar')->default('1.jpg');
            $table->string('email')->unique();
            $table->string('password');
        });

        Schema::create('posts', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('banner');
            $table->text('description');
           
            $table->tinyInteger('visivel')->default(1);
            $table->string('slug')->unique();
            $table->dateTime('created_at');
        });

        Schema::create('imagens', function (Blueprint $table) {
            $table->id();
            $table->string('imagem');
            $table->unsignedBigInteger('post_id')->nullable();
        });

        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->tinyInteger('visivel')->default(1);

        });

        Schema::table('posts', function (Blueprint $table) {
            $table->unsignedBigInteger('category_id')->nullable();
        });

        Schema::table('posts', function (Blueprint $table) {
            $table->foreign('category_id')
            ->references('id')
            ->on('categories')
            ->onDelete('set null');
        });

        Schema::table('imagens', function (Blueprint $table) {
            $table->foreign('post_id')
            ->references('id')
            ->on('posts')
            ->onDelete('set null');
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
        Schema::dropIfExists('posts');
        Schema::dropIfExists('imagens');
        Schema::dropIfExists('categories');
        Schema::table('posts', function (Blueprint $table) {
            $table->dropColumn('category_id');
        });
        Schema::table('imagens', function (Blueprint $table) {
            $table->dropColumn('post_id');
        });
    }
}
