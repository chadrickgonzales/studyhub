<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('study_resources', function (Blueprint $table) {
            $table->id();
            $table->foreignId('study_room_id')->constrained()->onDelete('cascade');
            $table->foreignId('uploaded_by')->constrained('users')->onDelete('cascade');
            $table->string('name');
            $table->string('file_path');
            $table->string('file_type');
            $table->bigInteger('file_size')->comment('Size in bytes');
            $table->text('description')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('study_resources');
    }
}; 