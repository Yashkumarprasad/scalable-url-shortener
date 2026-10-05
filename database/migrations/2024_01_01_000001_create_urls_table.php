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
        Schema::create('urls', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->text('original_url');
            $table->string('short_code', 32)->unique();
            $table->string('custom_alias', 64)->nullable()->unique();
            $table->string('title', 255)->nullable();
            $table->timestamp('expires_at')->nullable()->index();
            $table->boolean('is_active')->default(true)->index();
            $table->unsignedBigInteger('click_count')->default(0);
            $table->timestamps();
            $table->softDeletes();

            // Compound index for user URL listing and active lookup performance
            $table->index(['user_id', 'created_at']);
            $table->index(['is_active', 'expires_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('urls');
    }
};
