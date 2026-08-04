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
        Schema::table('assets', function (Blueprint $table) {
            // Postgres, unlike MySQL/InnoDB, does not auto-index a column just
            // because it carries a FK constraint — index() is added explicitly.
            $table->foreignId('department_id')->nullable()->after('assigned_user_id')
                ->constrained('departments')->nullOnDelete();
            $table->foreignId('location_id')->nullable()->after('department_id')
                ->constrained('locations')->nullOnDelete();
            $table->index('department_id');
            $table->index('location_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('assets', function (Blueprint $table) {
            $table->dropConstrainedForeignId('department_id');
            $table->dropConstrainedForeignId('location_id');
        });
    }
};
