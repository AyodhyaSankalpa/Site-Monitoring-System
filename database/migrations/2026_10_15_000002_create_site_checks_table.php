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
        Schema::create('site_checks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('site_id')->constrained('sites')->cascadeOnDelete();
            $table->integer('status_code')->nullable();
            $table->integer('response_time_ms')->nullable();
            $table->boolean('is_online');
            $table->text('error_message')->nullable();
            $table->timestamp('checked_at')->useCurrent();

            $table->index(['site_id', 'checked_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('site_checks');
    }
};
