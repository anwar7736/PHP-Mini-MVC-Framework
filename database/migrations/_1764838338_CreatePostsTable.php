<?php
namespace Database\Migrations;
use Config\Schema;
use Config\Blueprint;

class _1764838338_CreatePostsTable
{
    public function up()
    {
        Schema::create("posts", function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->text('body');
            $table->string('image')->nullable();
            $table->bigInteger('view')->default(0);
            $table->enum('status', [0,1])->default('1')->comments('1=> active, 0=> inactive');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists("posts");
    }
}
