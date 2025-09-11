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
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'user_ap_accepted')) {
                $table->boolean('user_ap_accepted')->default(false)->after('remember_token');
            }
            if (!Schema::hasColumn('users', 'user_ap_accepted_date')) {
                $table->dateTime('user_ap_accepted_date')->nullable()->after('user_ap_accepted');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'user_ap_accepted_date')) {
                $table->dropColumn('user_ap_accepted_date');
            }
            if (Schema::hasColumn('users', 'user_ap_accepted')) {
                $table->dropColumn('user_ap_accepted');
            }
        });
    }
};


