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
        Schema::create('transaksis', function (Blueprint $table) {
$table->id();
        $table->string('order_id')->unique(); 
        $table->foreignId('barang_id')->constrained('barangs')->onDelete('cascade');
        $table->integer('jumlah');
        $table->bigInteger('total_harga');
        $table->enum('metode_pembayaran', ['Cash', 'E-Money']);
        $table->enum('status', ['Pending', 'Success', 'Cancelled'])->default('Pending');
        $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transaksis');
    }
};
