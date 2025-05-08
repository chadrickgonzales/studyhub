<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIsSharedToStudyRoomNotesTable extends Migration
{
    public function up()
    {
        Schema::table('study_room_notes', function (Blueprint $table) {
            $table->boolean('is_shared')->default(false)->after('is_pinned');
        });
    }

    public function down()
    {
        Schema::table('study_room_notes', function (Blueprint $table) {
            $table->dropColumn('is_shared');
        });
    }
} 