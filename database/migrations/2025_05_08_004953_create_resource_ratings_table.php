<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateResourceRatingsTable extends Migration
{
    public function up()
    {
        Schema::create('resource_ratings', function (Blueprint $table) {
            $table->id();
            $table->integer('rating');
            $table->text('comment')->nullable();
            $table->foreignId('resource_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->timestamps();

            $table->unique(['resource_id', 'user_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('resource_ratings');
    }
} 