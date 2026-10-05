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
        Schema::create('url_clicks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('url_id')->constrained()->cascadeOnDelete();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->text('referer')->nullable();
            $table->string('device_type', 32)->default('UNKNOWN')->index();
            $table->string('country_code', 3)->nullable()->index();
            $table->string('country_name', 100)->nullable();
            $table->string('city', 100)->nullable();
            $table->timestamp('clicked_at')->index();
            $table->timestamp('created_at')->nullable();

            // Aggregation performance indexes for analytics
            $table->index(['url_id', 'clicked_at']);
            $table->index(['url_id', 'device_type']);
            $table->index(['url_id', 'country_code']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('url_clicks');
    }
};
