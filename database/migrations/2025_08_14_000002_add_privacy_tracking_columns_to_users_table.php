<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'user_ap_version')) {
                $table->string('user_ap_version', 50)->nullable()->after('user_ap_accepted_date');
            }
            if (!Schema::hasColumn('users', 'user_ap_ip')) {
                $table->string('user_ap_ip', 45)->nullable()->after('user_ap_version');
            }
            if (!Schema::hasColumn('users', 'user_ap_user_agent')) {
                $table->text('user_ap_user_agent')->nullable()->after('user_ap_ip');
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'user_ap_user_agent')) {
                $table->dropColumn('user_ap_user_agent');
            }
            if (Schema::hasColumn('users', 'user_ap_ip')) {
                $table->dropColumn('user_ap_ip');
            }
            if (Schema::hasColumn('users', 'user_ap_version')) {
                $table->dropColumn('user_ap_version');
            }
        });
    }
};


