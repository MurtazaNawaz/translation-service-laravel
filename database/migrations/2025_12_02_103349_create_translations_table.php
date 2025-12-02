<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('translations', function (Blueprint $table) {
            $table->id();
            $table->string('key', 255);
            $table->foreignId('locale_id')->constrained('locales')->cascadeOnDelete();
            $table->text('value');
            $table->json('meta')->nullable(); // dev extra info
            $table->timestamps();

            $table->unique(['key', 'locale_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('translations');
    }
};
