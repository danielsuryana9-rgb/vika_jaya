<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    private const BATAS_STOK_CABANG = 10;

    public function __invoke()
    {
        $today = today();

        $selesaiHariIni = DB::table('transaksi')
            ->whereDate('tanggal_transaksi', $today)
            ->where('status', 'selesai');

        $kemarin = today()->subDay();
        $selesaiKemarin = DB::table('transaksi')
            ->whereDate('tanggal_transaksi', $kemarin)
            ->where('status', 'selesai');
        $produksiKemarin = (int) DB::table('produksi')->whereDate('tanggal_produksi', $kemarin)->sum('jumlah_hasil');
        $baru = DB::table('produk')->where('created_at', '>=', now()->startOfMonth())->count();

        $kpi = [
            'produk'   => DB::table('produk')->count(),
            'stok'     => (int) DB::table('stok_pusat')->sum('jumlah') + (int) DB::table('stok_cabang')->sum('jumlah'),
            'produksi' => (int) DB::table('produksi')->whereDate('tanggal_produksi', $today)->sum('jumlah_hasil'),
            'trx'      => (clone $selesaiHariIni)->count(),
            'omzet'    => (float) (clone $selesaiHariIni)->sum('total'),
        ];

        $kpi['d_produk']   = $baru > 0 ? ['↑ ' . $baru . ' produk bulan ini', 'up'] : ['Belum ada produk baru bulan ini', 'mut'];
        $kpi['d_stok']     = ['Pusat dan seluruh cabang', 'mut'];
        $kpi['d_produksi'] = $this->delta($kpi['produksi'], $produksiKemarin);
        $kpi['d_trx']      = $this->delta($kpi['trx'], (clone $selesaiKemarin)->count());
        $kpi['d_omzet']    = $this->delta($kpi['omzet'], (float) (clone $selesaiKemarin)->sum('total'));

        $per_hari = DB::table('transaksi')
            ->selectRaw('DATE(tanggal_transaksi) as tgl, SUM(total) as total')
            ->where('status', 'selesai')
            ->whereDate('tanggal_transaksi', '>=', today()->subDays(6))
            ->groupByRaw('DATE(tanggal_transaksi)')
            ->pluck('total', 'tgl');

        $tren = collect(range(6, 0))->map(function ($i) use ($per_hari) {
            $d = today()->subDays($i);

            return ['label' => $d->translatedFormat('d M'), 'nilai' => (float) ($per_hari[$d->toDateString()] ?? 0)];
        });

        $maks = max(1, $tren->max('nilai'));
        $langkah = max(100000, (int) (ceil($maks / 4 / 100000) * 100000));
        $batas = $langkah * 4;
        $rentang = today()->subDays(6)->translatedFormat('d') . '–' . today()->translatedFormat('d M Y');

        $pusat = DB::table('stok_pusat as s')
            ->join('produk as p', 'p.id_produk', '=', 's.id_produk')
            ->whereColumn('s.jumlah', '<=', 's.stok_minimum')
            ->select('p.nama_produk', DB::raw("'Pusat' as lokasi"), 's.jumlah');

        $menipis = DB::table('stok_cabang as s')
            ->join('produk as p', 'p.id_produk', '=', 's.id_produk')
            ->join('cabang as c', 'c.id_cabang', '=', 's.id_cabang')
            ->where('s.jumlah', '<=', self::BATAS_STOK_CABANG)
            ->select('p.nama_produk', 'c.nama_cabang as lokasi', 's.jumlah')
            ->unionAll($pusat)
            ->get()
            ->take(5);

        $terbaru = DB::table('transaksi as t')
            ->join('cabang as c', 'c.id_cabang', '=', 't.id_cabang')
            ->join('users as u', 'u.id_user', '=', 't.id_user')
            ->orderByDesc('t.tanggal_transaksi')
            ->limit(5)
            ->get(['t.no_transaksi', 't.tanggal_transaksi', 'c.nama_cabang', 'u.nama as kasir', 't.total']);

        $jam = now()->hour;
        $salam = $jam < 11 ? 'pagi' : ($jam < 15 ? 'siang' : ($jam < 18 ? 'sore' : 'malam'));

        return view('dashboard', compact('kpi', 'tren', 'menipis', 'terbaru', 'salam', 'batas', 'langkah', 'rentang'));
    }

    /** Perubahan dibanding kemarin: [teks, kelas css]. */
    private function delta(float|int $sekarang, float|int $kemarin): array
    {
        if ($kemarin <= 0) {
            return ['Belum ada data kemarin', 'mut'];
        }

        $persen = round(($sekarang - $kemarin) / $kemarin * 100, 1);
        $angka = rtrim(rtrim(number_format(abs($persen), 1, ',', '.'), '0'), ',');
        $panah = $persen > 0 ? '↑ ' : ($persen < 0 ? '↓ ' : '');

        return [$panah . $angka . '% dari kemarin', $persen > 0 ? 'up' : ($persen < 0 ? 'dn' : 'mut')];
    }
}
