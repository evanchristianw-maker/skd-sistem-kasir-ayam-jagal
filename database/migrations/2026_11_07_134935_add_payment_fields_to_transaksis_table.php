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
        Schema::table('transaksis', function (Blueprint $table) {
            $table->string('metode_pembayaran', 20)
                ->nullable()
                ->after('total_harga');

            $table->decimal('uang_dibayarkan', 12, 2)
                ->nullable()
                ->after('metode_pembayaran');

            $table->decimal('kembalian', 12, 2)
                ->nullable()
                ->after('uang_dibayarkan');

            $table->string('status', 20)
                ->default('selesai')
                ->after('kembalian');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transaksis', function (Blueprint $table) {
            $table->dropColumn([
                'metode_pembayaran',
                'uang_dibayarkan',
                'kembalian',
                'status',
            ]);
        });
    }
};
