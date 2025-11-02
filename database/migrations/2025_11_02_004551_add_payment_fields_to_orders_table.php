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
            $table->string('xendit_invoice_id')->nullable()->after('payment_method');
            $table->string('xendit_invoice_url')->nullable()->after('xendit_invoice_id');
            $table->timestamp('xendit_paid_at')->nullable()->after('xendit_invoice_url');
            $table->timestamp('xendit_expired_at')->nullable()->after('xendit_paid_at');
            $table->text('payment_channel')->nullable()->after('xendit_expired_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn([
                'xendit_invoice_id',
                'xendit_invoice_url',
                'xendit_paid_at',
                'xendit_expired_at',
                'payment_channel'
            ]);
        });
    }
};
