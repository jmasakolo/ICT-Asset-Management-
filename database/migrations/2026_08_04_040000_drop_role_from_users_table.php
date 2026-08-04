<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Reverses 2026_08_04_010000_add_role_to_users_table — the login model is
// being simplified back down to just two kinds of account: Admin (the
// separate `admins` table/guard, unaffected) and Regular user (everyone in
// `users`, no further sub-roles).
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('role');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('role')->default('ict_asset_team')->after('email');
        });
    }
};
