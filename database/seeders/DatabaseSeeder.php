<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Data contoh agar dashboard tidak kosong. Ganti atau hapus sesuai kebutuhan.
     * Login Owner: owner / owner123
     */
    public function run(): void
    {
        mt_srand(7);
        $now = now();
        $ts = fn (array $r) => $r + ['created_at' => $now, 'updated_at' => $now];

        DB::table('cabang')->insert(array_map($ts, [
            ['nama_cabang' => 'Cabang 1', 'alamat' => '[alamat cabang 1]', 'telepon' => null],
            ['nama_cabang' => 'Cabang 2', 'alamat' => '[alamat cabang 2]', 'telepon' => null],
        ]));

        DB::table('users')->insert(array_map($ts, [
            ['id_cabang' => null, 'nama' => 'Sandy Eka Wahyu', 'username' => 'owner',  'password' => Hash::make('owner123'), 'role' => 'owner', 'status' => 'aktif'],
            ['id_cabang' => 1,    'nama' => 'Noval Bagus',     'username' => 'kasir1', 'password' => Hash::make('kasir123'), 'role' => 'kasir', 'status' => 'aktif'],
            ['id_cabang' => 2,    'nama' => 'Daniel Suryana',  'username' => 'kasir2', 'password' => Hash::make('kasir123'), 'role' => 'kasir', 'status' => 'aktif'],
        ]));

        $produk = [
            ['PRD-001', 'Keripik Singkong Original', 12000, 20],
            ['PRD-002', 'Keripik Singkong Balado',   13000, 180],
            ['PRD-003', 'Talas',                     15000, 200],
            ['PRD-004', 'Keripik Pisang',            12000, 150],
            ['PRD-005', 'Keripik Tempe',             11000, 230],
            ['PRD-006', 'Keripik Bayam',             10000, 220],
        ];
        foreach ($produk as $i => [$kode, $nama, $harga, $stok]) {
            $id = $i + 1;
            DB::table('produk')->insert($ts(['id_produk' => $id, 'kode_produk' => $kode, 'nama_produk' => $nama, 'satuan' => 'pcs', 'harga_jual' => $harga]));
            DB::table('stok_pusat')->insert($ts(['id_produk' => $id, 'jumlah' => $stok, 'stok_minimum' => 30]));
            foreach ([1, 2] as $cab) {
                $jml = ($id === 3 && $cab === 1) || ($id === 2 && $cab === 2) ? 5 : 40;
                DB::table('stok_cabang')->insert($ts(['id_cabang' => $cab, 'id_produk' => $id, 'jumlah' => $jml]));
            }
        }

        DB::table('bahan')->insert($ts(['id_bahan' => 1, 'nama_bahan' => 'Singkong', 'satuan' => 'kg', 'stok_bahan' => 500, 'stok_minimum' => 50]));
        $prod = DB::table('produksi')->insertGetId($ts(['id_user' => 1, 'id_produk' => 1, 'tanggal_produksi' => today()->toDateString(), 'jumlah_hasil' => 500, 'catatan' => 'Produksi contoh']));
        DB::table('detail_produksi')->insert(['id_produksi' => $prod, 'id_bahan' => 1, 'jumlah_digunakan' => 250]);

        DB::table('produksi')->insert($ts(['id_user' => 1, 'id_produk' => 2, 'tanggal_produksi' => today()->subDay()->toDateString(), 'jumlah_hasil' => 400, 'catatan' => 'Produksi contoh']));

        // Transaksi 7 hari terakhir
        for ($d = 6; $d >= 0; $d--) {
            $seq = 0;
            $jumlahTrx = mt_rand(2, 5);
            for ($i = 0; $i < $jumlahTrx; $i++) {
                $cab = 1 + ($i % 2);
                $waktu = now()->subDays($d)->setTime(8 + $i, mt_rand(0, 59), 0);
                $ids = array_rand($produk, mt_rand(1, 3));
                $ids = (array) $ids;

                $baris = [];
                $total = 0;
                foreach ($ids as $k) {
                    $qty = mt_rand(1, 4);
                    $sub = $qty * $produk[$k][2];
                    $total += $sub;
                    $baris[] = ['id_produk' => $k + 1, 'jumlah' => $qty, 'harga_satuan' => $produk[$k][2], 'subtotal' => $sub];
                }

                $idTrx = DB::table('transaksi')->insertGetId($ts([
                    'no_transaksi'      => 'TRX-' . $waktu->format('dmy') . '-' . str_pad(++$seq, 3, '0', STR_PAD_LEFT),
                    'id_cabang'         => $cab,
                    'id_user'           => $cab + 1,
                    'tanggal_transaksi' => $waktu,
                    'total'             => $total,
                    'status'            => 'selesai',
                ]));
                DB::table('detail_transaksi')->insert(array_map(fn ($b) => $b + ['id_transaksi' => $idTrx], $baris));

                $tunai = $i % 2 === 0;
                $bayar = $tunai ? (int) (ceil($total / 10000) * 10000) : $total;
                DB::table('pembayaran')->insert($ts([
                    'id_transaksi' => $idTrx,
                    'metode'       => $tunai ? 'tunai' : 'qris',
                    'uang_dibayar' => $bayar,
                    'kembalian'    => $bayar - $total,
                ]));
            }
        }
    }
}
