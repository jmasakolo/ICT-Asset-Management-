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
            $table->string('model')->nullable()->after('category');
            $table->string('serial_number')->nullable()->unique()->after('model');
            $table->string('asset_tag')->nullable()->unique()->after('serial_number');
            $table->string('condition')->default('new')->after('status');
            $table->date('received_at')->nullable()->after('condition');
            $table->date('warranty_expires_at')->nullable()->after('received_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('assets', function (Blueprint $table) {
            $table->dropColumn([
                'model',
                'serial_number',
                'asset_tag',
                'condition',
                'received_at',
                'warranty_expires_at',
            ]);
        });
    }
};
