<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('translations', function (Blueprint $table) {
            $table->text('value')->nullable()->change(); // dev: allow null for seeding
        });
    }

    public function down(): void
    {
        Schema::table('translations', function (Blueprint $table) {
            $table->text('value')->nullable(false)->change();
        });
    }
};
