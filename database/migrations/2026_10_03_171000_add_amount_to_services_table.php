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
        Schema::table('services', function (Blueprint $table) {
            if (!Schema::hasColumn('services', 'amount')) {
                $table->decimal('amount', 10, 2)->default(0)->after('name');
            }
        });

        if (Schema::hasTable('invoice_service') && !Schema::hasColumn('invoice_service', 'amount')) {
            Schema::table('invoice_service', function (Blueprint $table) {
                $table->decimal('amount', 10, 2)->nullable()->after('service_id');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('services', 'amount')) {
            Schema::table('services', function (Blueprint $table) {
                $table->dropColumn('amount');
            });
        }

        if (Schema::hasTable('invoice_service') && Schema::hasColumn('invoice_service', 'amount')) {
            Schema::table('invoice_service', function (Blueprint $table) {
                $table->dropColumn('amount');
            });
        }
    }
};
