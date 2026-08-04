<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Postgres does not auto-index a column just because it carries a FK
// constraint (unlike MySQL/InnoDB) — these were flagged as missing during
// the production-readiness review: dashboard GROUP BYs and admin list
// ORDER BYs on these columns would degrade to sequential scans as the
// tables grow.
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('assets', function (Blueprint $table) {
            $table->index('assigned_user_id');
            $table->index('status');
            $table->index('category');
        });

        Schema::table('maintenance_records', function (Blueprint $table) {
            $table->index('asset_id');
            $table->index('performed_at');
        });

        Schema::table('audit_logs', function (Blueprint $table) {
            $table->index(['subject_type', 'subject_id']);
            $table->index('created_at');
        });

        Schema::table('tasks', function (Blueprint $table) {
            $table->index('created_at');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->index('name');
        });
    }

    public function down(): void
    {
        Schema::table('assets', function (Blueprint $table) {
            $table->dropIndex(['assigned_user_id']);
            $table->dropIndex(['status']);
            $table->dropIndex(['category']);
        });

        Schema::table('maintenance_records', function (Blueprint $table) {
            $table->dropIndex(['asset_id']);
            $table->dropIndex(['performed_at']);
        });

        Schema::table('audit_logs', function (Blueprint $table) {
            $table->dropIndex(['subject_type', 'subject_id']);
            $table->dropIndex(['created_at']);
        });

        Schema::table('tasks', function (Blueprint $table) {
            $table->dropIndex(['created_at']);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['name']);
        });
    }
};
