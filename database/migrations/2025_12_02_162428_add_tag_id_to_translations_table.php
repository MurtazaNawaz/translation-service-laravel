<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('translations', function (Blueprint $table) {
            $table->foreignId('tag_id')->nullable()->after('locale_id')->constrained('tags')->nullOnDelete(); // dev: minimal fix
        });
    }

    public function down(): void
    {
        Schema::table('translations', function (Blueprint $table) {
            $table->dropForeign(['tag_id']);
            $table->dropColumn('tag_id');
        });
    }
};
