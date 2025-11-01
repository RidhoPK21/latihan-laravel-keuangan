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
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();

            // Foreign Key ke tabel users
            $table->bigInteger('user_id')->unsigned();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

            // Tipe Transaksi: 'income' (Pemasukan) atau 'expense' (Pengeluaran)
            $table->enum('type', ['income', 'expense']);

            // Jumlah Uang
            $table->bigInteger('amount');

            // Tanggal Transaksi
            $table->date('date');

            $table->string('title');
            // Keterangan
            $table->text('description');

            // Bukti Gambar (Opsional)
            $table->string('cover')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};