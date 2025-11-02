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
        Schema::table('orders', function (Blueprint $table) {
            $table->integer('shipping_province_id')->nullable()->after('shipping_state');
            $table->string('shipping_province_name')->nullable()->after('shipping_province_id');
            $table->integer('shipping_city_id')->nullable()->after('shipping_province_name');
            $table->string('shipping_city_name')->nullable()->after('shipping_city_id');
            $table->string('shipping_courier')->nullable()->after('shipping_amount'); // jne, pos, tiki, etc
            $table->string('shipping_service')->nullable()->after('shipping_courier'); // REG, OKE, YES, etc
            $table->string('shipping_etd')->nullable()->after('shipping_service'); // 2-3 hari
            $table->integer('shipping_weight')->default(0)->after('shipping_etd'); // gram
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn([
                'shipping_province_id',
                'shipping_province_name',
                'shipping_city_id',
                'shipping_city_name',
                'shipping_courier',
                'shipping_service',
                'shipping_etd',
                'shipping_weight'
            ]);
        });
    }
};
