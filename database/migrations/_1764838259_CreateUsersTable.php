<?php
namespace Database\Migrations;
use Config\Schema;
use Config\Blueprint;

class _1764838259_CreateUsersTable
{
    public function up()
    {
        Schema::create("users", function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('phone')->unique();
            $table->string('email')->nullable()->unique();
            $table->string('password');
            $table->string('avatar')->nullable();
            $table->date('date_of_birth')->nullable();
            $table->enum('status', [0,1])->default('1')->comments('1=> active, 0=> inactive');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists("users");
    }
}
