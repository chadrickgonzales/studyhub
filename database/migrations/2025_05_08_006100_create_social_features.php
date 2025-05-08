<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('friendships', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('friend_id')->constrained('users')->onDelete('cascade');
            $table->string('status')->default('pending'); // pending, accepted, rejected
            $table->timestamps();
        });

        Schema::create('direct_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sender_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('receiver_id')->constrained('users')->onDelete('cascade');
            $table->text('content');
            $table->timestamp('read_at')->nullable();
            $table->timestamps();
        });

        Schema::create('achievement_badges', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('description');
            $table->string('icon');
            $table->string('criteria');
            $table->timestamps();
        });

        Schema::create('user_achievements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('achievement_badge_id')->constrained()->onDelete('cascade');
            $table->timestamp('earned_at');
            $table->timestamps();
        });

        Schema::create('activity_feeds', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('type');
            $table->text('description');
            $table->morphs('subject');
            $table->timestamps();
        });

        Schema::create('study_groups', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description');
            $table->string('category');
            $table->string('privacy')->default('public');
            $table->integer('member_count')->default(0);
            $table->timestamps();
        });

        Schema::create('study_group_members', function (Blueprint $table) {
            $table->id();
            $table->foreignId('study_group_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('role')->default('member');
            $table->timestamps();
        });

        Schema::create('user_recommendations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('recommended_user_id')->constrained('users')->onDelete('cascade');
            $table->string('reason');
            $table->float('match_score');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('user_recommendations');
        Schema::dropIfExists('study_group_members');
        Schema::dropIfExists('study_groups');
        Schema::dropIfExists('activity_feeds');
        Schema::dropIfExists('user_achievements');
        Schema::dropIfExists('achievement_badges');
        Schema::dropIfExists('direct_messages');
        Schema::dropIfExists('friendships');
    }
}; 