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
            $table->foreignId('rekening_bank_id')
                ->nullable()
                ->after('metode_pembayaran')
                ->constrained('rekening_banks')
                ->restrictOnDelete();

            $table->string('nama_bank', 50)
                ->nullable()
                ->after('rekening_bank_id');

            $table->string('nomor_rekening', 50)
                ->nullable()
                ->after('nama_bank');

            $table->string('atas_nama', 100)
                ->nullable()
                ->after('nomor_rekening');

            $table->string('status_pembayaran', 30)
                ->default('terkonfirmasi')
                ->after('status');

            $table->foreignId('dikonfirmasi_oleh')
                ->nullable()
                ->after('status_pembayaran')
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamp('dikonfirmasi_pada')
                ->nullable()
                ->after('dikonfirmasi_oleh');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transaksis', function (Blueprint $table) {
            $table->dropForeign(['rekening_bank_id']);
            $table->dropForeign(['dikonfirmasi_oleh']);

            $table->dropColumn([
                'rekening_bank_id',
                'nama_bank',
                'nomor_rekening',
                'atas_nama',
                'status_pembayaran',
                'dikonfirmasi_oleh',
                'dikonfirmasi_pada',
            ]);
        });
    }
};
