<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('welcome_invitation_logs', function (Blueprint $table) {
            $table->text('user_agent')->nullable()->after('ip_address');
            $table->string('device_type', 50)->nullable()->after('user_agent');
            $table->string('browser', 100)->nullable()->after('device_type');
        });
    }

    public function down(): void
    {
        Schema::table('welcome_invitation_logs', function (Blueprint $table) {
            $table->dropColumn(['user_agent', 'device_type', 'browser']);
        });
    }
};
