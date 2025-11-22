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
        Schema::table('user_files', function (Blueprint $table) {
            if (!Schema::hasColumn('user_files', 'status')) {
                $table->string('status')->nullable()->after('path');
            }
            if (!Schema::hasColumn('user_files', 'output_path')) {
                $table->string('output_path')->nullable()->after('status');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_files', function (Blueprint $table) {
            $table->dropColumn(['status', 'output_path']);
        });
    }
};
