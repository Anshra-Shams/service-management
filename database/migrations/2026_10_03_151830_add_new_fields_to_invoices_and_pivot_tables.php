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
        Schema::table('invoices', function (Blueprint $table) {
            $table->string('category')->nullable();
            $table->string('type')->nullable();
        });

        Schema::table('invoice_service', function (Blueprint $table) {
            $table->decimal('monthly_charges', 10, 2)->default(0);
            $table->decimal('arrears', 10, 2)->default(0);
            $table->decimal('late_surcharge', 10, 2)->default(0);
            $table->decimal('total_bills', 10, 2)->default(0);
            $table->decimal('total_payable', 10, 2)->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropColumn(['category', 'type']);
        });

        Schema::table('invoice_service', function (Blueprint $table) {
            $table->dropColumn(['monthly_charges', 'arrears', 'late_surcharge', 'total_bills', 'total_payable']);
        });
    }
};
