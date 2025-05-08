<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('profile_picture')->nullable()->after('email');
            $table->text('bio')->nullable()->after('profile_picture');
            $table->string('education_level')->nullable()->after('bio');
            $table->string('institution')->nullable()->after('education_level');
            $table->string('major')->nullable()->after('institution');
            $table->integer('reputation_score')->default(0)->after('major');
            $table->timestamp('last_active_at')->nullable()->after('reputation_score');
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'profile_picture',
                'bio',
                'education_level',
                'institution',
                'major',
                'reputation_score',
                'last_active_at'
            ]);
        });
    }
}; 