<?php
namespace Database\Migrations;
use Config\Schema;
use Config\Blueprint;

class _1765100509_CreateStudentsTable
{
    public function up()
    {
        Schema::create("students", function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists("students");
    }
}
