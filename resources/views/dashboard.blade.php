@extends('layouts.app')
@section('title', 'Dashboard')
@section('content')
@php
 $rp = fn ($v) => 'Rp ' . number_format($v, 0, ',', '.');
 $kartu = [
  ['Total Produk', number_format($kpi['produk'], 0, ',', '.'), $kpi['d_produk'], 'box'],
  ['Total Stok', number_format($kpi['stok'], 0, ',', '.') . ' pcs', $kpi['d_stok'], 'stack'],
  ['Produksi', number_format($kpi['produksi'], 0, ',', '.') . ' pcs', $kpi['d_produksi'], 'fact'],
  ['Penjualan', $kpi['trx'] . ' transaksi', $kpi['d_trx'], 'bag'],
  ['Omzet', $rp($kpi['omzet']), $kpi['d_omzet'], 'wal'],
 ];
@endphp
<h2>Dashboard</h2>
<p class="sub">Selamat {{ $salam }}, Pak {{ explode(' ', auth()->user()->nama)[0] }}. Berikut ringkasan usaha Anda hari ini.</p>

<div class="kpis">
 @foreach ($kartu as [$judul, $nilai, $d, $ikon])
  <div class="card kpi"><small>{{ $judul }}<span style="color:var(--pri)">{!! \App\Support\Icon::svg($ikon, 18) !!}</span></small><b>{{ $nilai }}</b><span class="{{ $d[1] }}">{{ $d[0] }}</span></div>
 @endforeach
</div>

<div class="row">
 <div class="card">
  <div class="hd"><div><h3>Tren penjualan</h3><p>Omzet seluruh cabang · {{ $rentang }}</p></div>
   <div class="tabs"><button type="button" class="on">Harian</button><button type="button">Mingguan</button><button type="button">Bulanan</button></div></div>
  <div class="big">{{ $rp($tren->sum('nilai')) }}<small>7 hari terakhir</small></div>
  <div class="cw">
   <div class="yax">@for ($v = $batas; $v >= 0; $v -= $langkah)<span>Rp {{ number_format($v / 1000, 0, ',', '.') }} rb</span>@endfor</div>
   <div class="cr">
    <div class="chart">
     @foreach ($tren as $t)
      <div class="col {{ $loop->last ? 't' : '' }}">{{ round($t['nilai'] / 1000) }} rb<i style="height:{{ round($t['nilai'] / $batas * 88) }}%"></i></div>
     @endforeach
    </div>
    <div class="xl">@foreach ($tren as $t)<span>{{ $t['label'] }}</span>@endforeach</div>
   </div>
  </div>
 </div>

 <div class="card">
  <div class="hd"><div><h3>Stok Menipis</h3><p>Segera jadwalkan produksi ulang</p></div></div>
  <div class="low">
   @forelse ($menipis as $m)
    <div class="it"><b>{{ $m->nama_produk }}</b>{!! \App\Support\Icon::svg('warn', 18) !!}<small>{{ $m->lokasi }} · {{ $m->jumlah }} pcs</small><span class="pill">Menipis</span></div>
   @empty
    <p style="color:var(--mut)">Semua stok aman.</p>
   @endforelse
   <div><a class="ghost btn-i" href="{{ route('menu', 'stok') }}">{!! \App\Support\Icon::svg('stack', 18) !!}Lihat stok</a></div>
  </div>
 </div>
</div>

<div class="card">
 <div class="hd"><div><h3>Transaksi Terbaru</h3><p>Data dari Mobile Kasir</p></div>
  <a class="ghost btn-i" href="{{ route('menu', 'penjualan') }}">Semua transaksi</a></div>
 <div class="tw"><table>
  <tr><th>ID Transaksi</th><th>Tanggal</th><th>Cabang</th><th>Kasir</th><th>Total</th><th>Aksi</th></tr>
  @forelse ($terbaru as $r)
   <tr><td>{{ $r->no_transaksi }}</td><td>{{ \Illuminate\Support\Carbon::parse($r->tanggal_transaksi)->translatedFormat('d M Y · H.i') }}</td><td>{{ $r->nama_cabang }}</td><td>{{ $r->kasir }}</td><td>{{ $rp($r->total) }}</td><td><a>Lihat Detail</a></td></tr>
  @empty
   <tr><td colspan="6" style="text-align:center;color:var(--mut)">Belum ada transaksi.</td></tr>
  @endforelse
 </table></div>
</div>
@endsection
