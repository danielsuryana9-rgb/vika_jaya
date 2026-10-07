<?php

namespace App\Http\Controllers;

use App\Models\Produk;
use Illuminate\Http\Request;

class ProdukController extends Controller
{
    public function create()
    {
        try {
            $next = (Produk::max('id') ?? 0) + 1;
        } catch (\Throwable $e) {
            $next = 7; // tabel belum ada, pakai nomor contoh
        }

        $kode = 'PRD-' . str_pad($next, 3, '0', STR_PAD_LEFT);

        return view('pages.produk.create', [
            'kode' => $kode,
            'active' => 'produk',
        ]);
    }

    public function store(Request $request)
    {
        // "Rp 15.000" -> 15000
        $request->merge([
            'harga_jual' => preg_replace('/\D/', '', (string) $request->harga_jual),
        ]);

        $data = $request->validate([
            'kode'       => 'required|string|max:20|unique:produk,kode', // sesuaikan nama tabel
            'nama'       => 'required|string|max:120',
            'kategori'   => 'required|string|max:60',
            'satuan'     => 'required|string|max:20',
            'harga_jual' => 'required|integer|min:0',
            'foto'       => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ], [
            'kode.unique' => 'Kode produk sudah dipakai produk lain.',
            'foto.max'    => 'Ukuran foto maksimal 2 MB.',
            'foto.mimes'  => 'Foto harus berformat JPG atau PNG.',
        ]);

        if ($request->hasFile('foto')) {
            $data['foto'] = $request->file('foto')->store('produk', 'public');
        }

        Produk::create($data);

        return redirect('/produk')->with('ok', 'Produk berhasil disimpan.');
    }
}