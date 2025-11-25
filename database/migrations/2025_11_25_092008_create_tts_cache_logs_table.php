<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('tts_cache_logs', function (Blueprint $table) {
            $table->id();
            $table->text('text_content'); // The original text
            $table->string('filename_hash')->unique(); // The MD5 filename
            $table->string('voice')->nullable();
            $table->float('rate')->default(1.0);
            $table->float('volume')->default(100);
            $table->float('pitch')->default(1.0);
            $table->timestamps();
            
            // Index for faster searching
            $table->index('filename_hash');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tts_cache_logs');
    }
};
