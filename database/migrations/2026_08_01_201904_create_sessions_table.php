<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('class_id')->constrained()->onDelete('cascade');
            $table->foreignId('subject_id')->constrained()->onDelete('cascade');
            $table->foreignId('professor_id')->constrained()->onDelete('cascade');
            $table->string('room');
            $table->enum('campus', ['ons', 'mharza']);
            $table->date('date');
            $table->datetime('start_time');
            $table->datetime('end_time');
            $table->integer('duration');
            $table->enum('status', ['pending', 'active', 'completed', 'cancelled'])->default('pending');
            $table->string('cancellation_reason')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('sessions');
    }
};