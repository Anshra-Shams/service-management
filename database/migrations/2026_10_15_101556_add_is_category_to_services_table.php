<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('services', function (Blueprint $table) {
            $table->boolean('is_category')->default(true)->after('details');
        });

        // Set is_category = false for services that were auto-created as invoice line items
        DB::table('services')
            ->whereNull('details')
            ->where(function ($q) {
                $q->whereNull('amount')->orWhere('amount', 0);
            })
            ->update(['is_category' => false]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('services', function (Blueprint $table) {
            $table->dropColumn('is_category');
        });
    }
};
