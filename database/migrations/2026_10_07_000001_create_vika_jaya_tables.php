<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('produk', function (Blueprint $t) {
            $t->id('id_produk');
            $t->string('kode_produk', 30)->unique();
            $t->string('nama_produk', 100)->index();
            $t->text('deskripsi')->nullable();
            $t->string('satuan', 20)->default('pcs');
            $t->decimal('harga_jual', 12, 2)->default(0);
            $t->timestamps();
        });

        Schema::create('bahan', function (Blueprint $t) {
            $t->id('id_bahan');
            $t->string('nama_bahan', 100)->index();
            $t->string('satuan', 20);
            $t->decimal('stok_bahan', 12, 2)->default(0);
            $t->decimal('stok_minimum', 12, 2)->default(0);
            $t->timestamps();
        });

        Schema::create('produksi', function (Blueprint $t) {
            $t->id('id_produksi');
            $t->foreignId('id_user')->constrained('users', 'id_user')->cascadeOnUpdate()->restrictOnDelete();
            $t->foreignId('id_produk')->constrained('produk', 'id_produk')->cascadeOnUpdate()->restrictOnDelete();
            $t->date('tanggal_produksi')->index();
            $t->unsignedInteger('jumlah_hasil');
            $t->string('catatan')->nullable();
            $t->timestamps();
        });

        Schema::create('detail_produksi', function (Blueprint $t) {
            $t->id('id_detail_produksi');
            $t->foreignId('id_produksi')->constrained('produksi', 'id_produksi')->cascadeOnUpdate()->cascadeOnDelete();
            $t->foreignId('id_bahan')->constrained('bahan', 'id_bahan')->cascadeOnUpdate()->restrictOnDelete();
            $t->decimal('jumlah_digunakan', 12, 2);
        });

        Schema::create('stok_pusat', function (Blueprint $t) {
            $t->id('id_stok_pusat');
            $t->foreignId('id_produk')->unique()->constrained('produk', 'id_produk')->cascadeOnUpdate()->cascadeOnDelete();
            $t->integer('jumlah')->default(0);
            $t->integer('stok_minimum')->default(0);
            $t->timestamps();
        });

        Schema::create('stok_cabang', function (Blueprint $t) {
            $t->id('id_stok_cabang');
            $t->foreignId('id_cabang')->constrained('cabang', 'id_cabang')->cascadeOnUpdate()->cascadeOnDelete();
            $t->foreignId('id_produk')->constrained('produk', 'id_produk')->cascadeOnUpdate()->cascadeOnDelete();
            $t->integer('jumlah')->default(0);
            $t->timestamps();
            $t->unique(['id_cabang', 'id_produk']);
        });

        Schema::create('distribusi', function (Blueprint $t) {
            $t->id('id_distribusi');
            $t->foreignId('id_cabang')->constrained('cabang', 'id_cabang')->cascadeOnUpdate()->restrictOnDelete();
            $t->foreignId('id_user')->constrained('users', 'id_user')->cascadeOnUpdate()->restrictOnDelete();
            $t->date('tanggal_distribusi')->index();
            $t->string('catatan')->nullable();
            $t->timestamps();
        });

        Schema::create('detail_distribusi', function (Blueprint $t) {
            $t->id('id_detail_distribusi');
            $t->foreignId('id_distribusi')->constrained('distribusi', 'id_distribusi')->cascadeOnUpdate()->cascadeOnDelete();
            $t->foreignId('id_produk')->constrained('produk', 'id_produk')->cascadeOnUpdate()->restrictOnDelete();
            $t->unsignedInteger('jumlah');
        });

        Schema::create('transaksi', function (Blueprint $t) {
            $t->id('id_transaksi');
            $t->string('no_transaksi', 30)->unique();
            $t->foreignId('id_cabang')->constrained('cabang', 'id_cabang')->cascadeOnUpdate()->restrictOnDelete();
            $t->foreignId('id_user')->constrained('users', 'id_user')->cascadeOnUpdate()->restrictOnDelete();
            $t->dateTime('tanggal_transaksi')->index();
            $t->decimal('total', 14, 2);
            $t->enum('status', ['selesai', 'batal'])->default('selesai');
            $t->timestamps();
            $t->index(['id_cabang', 'tanggal_transaksi']);
        });

        Schema::create('detail_transaksi', function (Blueprint $t) {
            $t->id('id_detail_transaksi');
            $t->foreignId('id_transaksi')->constrained('transaksi', 'id_transaksi')->cascadeOnUpdate()->cascadeOnDelete();
            $t->foreignId('id_produk')->constrained('produk', 'id_produk')->cascadeOnUpdate()->restrictOnDelete();
            $t->unsignedInteger('jumlah');
            $t->decimal('harga_satuan', 12, 2);
            $t->decimal('subtotal', 14, 2);
        });

        Schema::create('pembayaran', function (Blueprint $t) {
            $t->id('id_pembayaran');
            $t->foreignId('id_transaksi')->unique()->constrained('transaksi', 'id_transaksi')->cascadeOnUpdate()->cascadeOnDelete();
            $t->enum('metode', ['tunai', 'transfer', 'qris']);
            $t->decimal('uang_dibayar', 14, 2);
            $t->decimal('kembalian', 14, 2)->default(0);
            $t->timestamps();
        });
    }

    public function down(): void
    {
        foreach (['pembayaran', 'detail_transaksi', 'transaksi', 'detail_distribusi', 'distribusi',
                  'stok_cabang', 'stok_pusat', 'detail_produksi', 'produksi', 'bahan', 'produk'] as $tbl) {
            Schema::dropIfExists($tbl);
        }
    }
};
