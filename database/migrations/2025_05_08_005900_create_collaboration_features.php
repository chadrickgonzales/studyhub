<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('todo_lists', function (Blueprint $table) {
            $table->id();
            $table->foreignId('study_room_id')->constrained()->onDelete('cascade');
            $table->string('title');
            $table->text('description')->nullable();
            $table->timestamps();
        });

        Schema::create('todo_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('todo_list_id')->constrained()->onDelete('cascade');
            $table->string('title');
            $table->text('description')->nullable();
            $table->boolean('is_completed')->default(false);
            $table->timestamp('due_date')->nullable();
            $table->foreignId('assigned_to')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
        });

        Schema::create('group_assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('study_room_id')->constrained()->onDelete('cascade');
            $table->string('title');
            $table->text('description');
            $table->timestamp('due_date');
            $table->json('assigned_to')->nullable(); // Array of user IDs
            $table->timestamps();
        });

        Schema::create('study_goals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('study_room_id')->constrained()->onDelete('cascade');
            $table->string('title');
            $table->text('description');
            $table->timestamp('target_date');
            $table->boolean('is_completed')->default(false);
            $table->timestamps();
        });

        Schema::create('goal_milestones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('study_goal_id')->constrained()->onDelete('cascade');
            $table->string('title');
            $table->text('description')->nullable();
            $table->boolean('is_completed')->default(false);
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
        });

        Schema::create('shared_calendars', function (Blueprint $table) {
            $table->id();
            $table->foreignId('study_room_id')->constrained()->onDelete('cascade');
            $table->string('title');
            $table->text('description')->nullable();
            $table->json('events')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('shared_calendars');
        Schema::dropIfExists('goal_milestones');
        Schema::dropIfExists('study_goals');
        Schema::dropIfExists('group_assignments');
        Schema::dropIfExists('todo_items');
        Schema::dropIfExists('todo_lists');
    }
}; 