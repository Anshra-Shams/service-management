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
        Schema::table('customers', function (Blueprint $table) {
            $table->string('plot')->nullable()->after('plot_size');
            $table->string('street')->nullable()->after('plot');
            $table->string('sector')->nullable()->after('street');
            $table->string('phase')->nullable()->after('sector');
        });

        // Migrate existing customer addresses into new columns
        $customers = \App\Models\Customer::all();
        foreach ($customers as $customer) {
            $rawAddress = trim($customer->address ?? '');
            if (!$rawAddress) continue;

            $updated = false;
            if (preg_match('/(plot|house|h\.no|p\.no)\s*[:#\-]?\s*([a-zA-Z0-9\-\/]+)/i', $rawAddress, $m)) {
                $customer->plot = $m[2];
                $updated = true;
            }
            if (preg_match('/street\s*[:#\-]?\s*([a-zA-Z0-9\-\/]+)/i', $rawAddress, $m)) {
                $customer->street = $m[1];
                $updated = true;
            }
            if (preg_match('/sector\s*[:#\-]?\s*([a-zA-Z0-9\-\/]+)/i', $rawAddress, $m)) {
                $customer->sector = $m[1];
                $updated = true;
            }
            if (preg_match('/phase\s*[:#\-]?\s*([a-zA-Z0-9\-\/]+)/i', $rawAddress, $m)) {
                $customer->phase = $m[1];
                $updated = true;
            }

            if ($updated) {
                $customer->save();
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->dropColumn(['plot', 'street', 'sector', 'phase']);
        });
    }
};
