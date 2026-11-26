<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pengeluarans', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                ->constrained('users')
                ->restrictOnDelete();

            $table->string('kategori');
            $table->text('keterangan')->nullable();
            $table->decimal('jumlah', 12, 2);
            $table->date('tanggal');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pengeluarans');
    }
};