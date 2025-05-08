<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('study_sessions', function (Blueprint $table) {
            $table->timestamp('end_time')->nullable();
            $table->string('recording_path')->nullable();
            $table->text('notes')->nullable();
            $table->json('whiteboard_data')->nullable();
            $table->boolean('is_recurring')->default(false);
            $table->string('recurrence_pattern')->nullable();
            $table->string('template_name')->nullable();
        });

        Schema::create('session_attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('study_session_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->timestamp('joined_at');
            $table->timestamp('left_at')->nullable();
            $table->integer('duration_minutes')->default(0);
            $table->timestamps();
        });

        Schema::create('session_feedbacks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('study_session_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->integer('rating');
            $table->text('feedback')->nullable();
            $table->timestamps();
        });

        Schema::create('room_polls', function (Blueprint $table) {
            $table->id();
            $table->foreignId('study_room_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('question');
            $table->json('options');
            $table->boolean('is_multiple_choice')->default(false);
            $table->boolean('is_anonymous')->default(false);
            $table->timestamp('ends_at')->nullable();
            $table->timestamps();
        });

        Schema::create('poll_votes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('poll_id')->constrained('room_polls')->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->json('selected_options');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('poll_votes');
        Schema::dropIfExists('room_polls');
        Schema::dropIfExists('session_feedbacks');
        Schema::dropIfExists('session_attendances');

        Schema::table('study_sessions', function (Blueprint $table) {
            $table->dropColumn([
                'end_time',
                'recording_path',
                'notes',
                'whiteboard_data',
                'is_recurring',
                'recurrence_pattern',
                'template_name'
            ]);
        });
    }
}; 