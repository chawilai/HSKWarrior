<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('word_guesses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null'); // for authenticated users
            $table->foreignId('dictionary_zh_hans_id')->constrained('dictionary_zh_hans')->onDelete('cascade');
            $table->boolean('is_correct'); // true if guessed correctly
            $table->timestamp('guessed_at')->useCurrent();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('word_guesses');
    }
};
