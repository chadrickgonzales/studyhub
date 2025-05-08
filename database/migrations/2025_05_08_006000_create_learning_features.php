<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('quizzes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('study_room_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('title');
            $table->text('description')->nullable();
            $table->integer('time_limit')->nullable(); // in minutes
            $table->boolean('is_published')->default(false);
            $table->timestamps();
        });

        Schema::create('quiz_questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('quiz_id')->constrained()->onDelete('cascade');
            $table->string('question');
            $table->string('type'); // multiple_choice, true_false, short_answer
            $table->json('options')->nullable();
            $table->json('correct_answers');
            $table->integer('points')->default(1);
            $table->timestamps();
        });

        Schema::create('quiz_attempts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('quiz_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->integer('score');
            $table->timestamp('started_at');
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
        });

        Schema::create('flashcard_sets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('study_room_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('title');
            $table->text('description')->nullable();
            $table->timestamps();
        });

        Schema::create('flashcards', function (Blueprint $table) {
            $table->id();
            $table->foreignId('flashcard_set_id')->constrained()->onDelete('cascade');
            $table->text('front');
            $table->text('back');
            $table->integer('difficulty')->default(1);
            $table->timestamps();
        });

        Schema::create('study_timers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->integer('work_duration'); // in minutes
            $table->integer('break_duration'); // in minutes
            $table->integer('long_break_duration')->nullable(); // in minutes
            $table->integer('sessions_before_long_break')->nullable();
            $table->timestamps();
        });

        Schema::create('timer_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('study_timer_id')->constrained()->onDelete('cascade');
            $table->timestamp('started_at');
            $table->timestamp('ended_at')->nullable();
            $table->string('type'); // work, break, long_break
            $table->timestamps();
        });

        Schema::create('note_templates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('study_room_id')->constrained()->onDelete('cascade');
            $table->string('title');
            $table->text('content');
            $table->json('structure')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('note_templates');
        Schema::dropIfExists('timer_sessions');
        Schema::dropIfExists('study_timers');
        Schema::dropIfExists('flashcards');
        Schema::dropIfExists('flashcard_sets');
        Schema::dropIfExists('quiz_attempts');
        Schema::dropIfExists('quiz_questions');
        Schema::dropIfExists('quizzes');
    }
}; 