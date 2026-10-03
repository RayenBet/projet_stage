<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('class_id')->constrained('classes')->onDelete('cascade');
            $table->string('matricule')->unique();
            $table->string('first_name');
            $table->string('last_name');
            $table->integer('score')->default(0);
            $table->integer('total_presence')->default(0);
            $table->integer('total_absence')->default(0);
            $table->integer('total_late')->default(0);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('students');
    }
};