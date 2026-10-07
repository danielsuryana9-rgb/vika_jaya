<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Produk extends Model
{
    protected $table = 'produk'; // ganti sesuai nama tabel Anda
    protected $fillable = ['kode', 'nama', 'kategori', 'satuan', 'harga_jual', 'foto'];
}