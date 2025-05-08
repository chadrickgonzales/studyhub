<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('resource_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->timestamps();
        });

        Schema::table('study_resources', function (Blueprint $table) {
            $table->string('version')->default('1.0')->after('file_path');
            $table->text('preview_data')->nullable()->after('version');
            $table->json('metadata')->nullable()->after('preview_data');
            $table->string('access_level')->default('public')->after('metadata');
            $table->integer('download_count')->default(0)->after('access_level');
        });

        Schema::create('resource_category_study_resource', function (Blueprint $table) {
            $table->id();
            $table->foreignId('study_resource_id')->constrained()->onDelete('cascade');
            $table->foreignId('resource_category_id')->constrained()->onDelete('cascade');
            $table->timestamps();
        });

        Schema::create('resource_versions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('study_resource_id')->constrained()->onDelete('cascade');
            $table->string('version');
            $table->string('file_path');
            $table->string('file_name');
            $table->string('mime_type');
            $table->integer('file_size');
            $table->text('changes')->nullable();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->timestamps();
        });

        Schema::create('resource_downloads', function (Blueprint $table) {
            $table->id();
            $table->foreignId('study_resource_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('version');
            $table->timestamp('downloaded_at');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('resource_downloads');
        Schema::dropIfExists('resource_versions');
        Schema::dropIfExists('resource_category_study_resource');
        Schema::dropIfExists('resource_categories');

        Schema::table('study_resources', function (Blueprint $table) {
            $table->dropColumn([
                'version',
                'preview_data',
                'metadata',
                'access_level',
                'download_count'
            ]);
        });
    }
}; 