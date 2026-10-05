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
        if (!Schema::hasTable('invoice_service')) {
            Schema::create('invoice_service', function (Blueprint $table) {
                $table->id();
                $table->foreignId('invoice_id')->constrained()->onDelete('cascade');
                $table->foreignId('service_id')->constrained()->onDelete('cascade');
                $table->timestamps();
            });

            // Populate existing relationships
            $existingInvoices = DB::table('invoices')->whereNotNull('service_id')->get();
            foreach ($existingInvoices as $invoice) {
                DB::table('invoice_service')->insertOrIgnore([
                    'invoice_id' => $invoice->id,
                    'service_id' => $invoice->service_id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoice_service');
    }
};
