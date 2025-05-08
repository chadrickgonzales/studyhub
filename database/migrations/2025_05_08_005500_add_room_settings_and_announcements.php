<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('study_rooms', function (Blueprint $table) {
            $table->string('privacy')->default('public')->after('description'); // public, private, invite-only
            $table->text('rules')->nullable()->after('privacy');
            $table->json('settings')->nullable()->after('rules'); // Store additional settings as JSON
            $table->integer('message_count')->default(0)->after('settings');
            $table->integer('active_members_count')->default(0)->after('message_count');
            $table->timestamp('last_activity_at')->nullable()->after('active_members_count');
        });

        Schema::create('room_announcements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('study_room_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('title');
            $table->text('content');
            $table->boolean('is_pinned')->default(false);
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('room_announcements');

        Schema::table('study_rooms', function (Blueprint $table) {
            $table->dropColumn([
                'privacy',
                'rules',
                'settings',
                'message_count',
                'active_members_count',
                'last_activity_at'
            ]);
        });
    }
}; 