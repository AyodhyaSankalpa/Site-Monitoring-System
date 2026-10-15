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
        Schema::create('sites', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('url');
            $table->string('client_name')->nullable();
            $table->string('client_email')->nullable();
            $table->string('client_phone')->nullable();

            // Uptime & Status Check
            $table->boolean('is_online')->default(true);
            $table->integer('last_status_code')->nullable();
            $table->integer('last_response_time_ms')->nullable();
            $table->text('last_error')->nullable();
            $table->timestamp('last_checked_at')->nullable();
            $table->integer('check_interval_minutes')->default(15);
            $table->boolean('is_active')->default(true);

            // SSL Certificate Expiration
            $table->string('ssl_status')->default('unknown'); // valid, expiring_soon, expired, invalid, unknown
            $table->string('ssl_issuer')->nullable();
            $table->timestamp('ssl_expires_at')->nullable();

            // Domain Expiration
            $table->string('domain_registrar')->nullable();
            $table->date('domain_expires_at')->nullable();

            // Hosting Expiration & Server Details
            $table->string('host_provider')->nullable();
            $table->string('host_ip')->nullable();
            $table->date('host_expires_at')->nullable();

            $table->timestamps();

            $table->index('is_active');
            $table->index('is_online');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sites');
    }
};
