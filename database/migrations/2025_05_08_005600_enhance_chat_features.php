<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('study_room_messages', function (Blueprint $table) {
            $table->foreignId('parent_id')->nullable()->after('id')->constrained('study_room_messages')->onDelete('cascade');
            $table->text('content_html')->nullable()->after('content'); // For formatted content
            $table->json('reactions')->nullable()->after('content_html'); // Store reactions as JSON
            $table->json('read_by')->nullable()->after('reactions'); // Store read receipts
            $table->boolean('is_edited')->default(false)->after('read_by');
            $table->timestamp('edited_at')->nullable()->after('is_edited');
        });

        Schema::create('message_attachments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('message_id')->constrained('study_room_messages')->onDelete('cascade');
            $table->string('file_name');
            $table->string('file_path');
            $table->string('mime_type');
            $table->integer('file_size');
            $table->timestamps();
        });

        Schema::create('typing_indicators', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('study_room_id')->constrained()->onDelete('cascade');
            $table->timestamp('last_typed_at');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('typing_indicators');
        Schema::dropIfExists('message_attachments');

        Schema::table('study_room_messages', function (Blueprint $table) {
            $table->dropForeign(['parent_id']);
            $table->dropColumn([
                'parent_id',
                'content_html',
                'reactions',
                'read_by',
                'is_edited',
                'edited_at'
            ]);
        });
    }
}; 