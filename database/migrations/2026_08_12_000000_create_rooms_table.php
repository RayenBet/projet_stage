<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('rooms', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->enum('campus', ['ons', 'mharza']);
            $table->unsignedInteger('capacity')->nullable();
            $table->string('type')->nullable(); // ex: salle, amphithéâtre, laboratoire
            $table->timestamps();

            $table->unique(['name', 'campus']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('rooms');
    }
};