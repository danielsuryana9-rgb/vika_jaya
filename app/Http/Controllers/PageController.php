<?php

namespace App\Http\Controllers;

class PageController extends Controller
{
    public const MENU = [
        'produk' => 'Produk', 'bahan' => 'Bahan', 'produksi' => 'Produksi', 'stok' => 'Stok',
        'distribusi' => 'Distribusi', 'penjualan' => 'Penjualan', 'cabang' => 'Cabang',
        'laporan' => 'Laporan', 'pengguna' => 'Pengguna', 'profil' => 'Profil',
    ];

    public function show(string $menu)
    {
        abort_unless(isset(self::MENU[$menu]), 404);

        return view('pages.placeholder', ['title' => self::MENU[$menu], 'active' => $menu]);
    }
}
