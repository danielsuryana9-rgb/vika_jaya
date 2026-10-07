<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
<title>@yield('title', 'Dashboard') · Vika Jaya</title>
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap">
<link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body>
@php
 $menu = [
  ['dashboard', 'Dashboard', 'dash'], ['produk', 'Produk', 'box'], ['bahan', 'Bahan', 'leaf'],
  ['produksi', 'Produksi', 'fact'], ['stok', 'Stok', 'stack'], ['distribusi', 'Distribusi', 'truck'],
  ['penjualan', 'Penjualan', 'bag'], ['cabang', 'Cabang', 'shop'], ['laporan', 'Laporan', 'chart'],
  ['pengguna', 'Pengguna', 'users'], ['profil', 'Profil', 'prof'],
 ];
 $aktif = $active ?? 'dashboard';
 $user = auth()->user();
@endphp
<div class="app">
 <aside class="side">
  <div class="brand"><div class="logo">vj</div><div><b>Vika Jaya</b><small>Produksi &amp; penjualan</small></div></div>
  <div class="cap">RUANG KERJA OWNER</div>
  <nav>
   @foreach ($menu as [$key, $label, $icon])
    <a href="{{ $key === 'dashboard' ? route('dashboard') : route('menu', $key) }}" class="{{ $aktif === $key ? 'on' : '' }}">{!! \App\Support\Icon::svg($icon) !!}{{ $label }}</a>
   @endforeach
   <form method="POST" action="{{ route('logout') }}">@csrf
    <button type="submit" class="out">{!! \App\Support\Icon::svg('out') !!}Logout</button>
   </form>
  </nav>
  <small>Pusat · Cabang 1 · Cabang 2</small>
  <small class="copy">© {{ date('Y') }} Vika Jaya</small>
 </aside>
 <div class="main">
  <header class="top">
   <div><h3>@yield('title', 'Dashboard')</h3><small>Beranda / @yield('title', 'Dashboard')</small></div>
   <div class="me"><span class="date">{{ now()->translatedFormat('d M Y') }}</span>
    <span class="bell" title="Notifikasi">{!! \App\Support\Icon::svg('bell') !!}</span>
    <div class="usr">
     <div class="av">@if (file_exists(public_path('img/avatar/' . $user->id_user . '.jpg')))<img src="{{ asset('img/avatar/' . $user->id_user . '.jpg') }}" alt="{{ $user->nama }}">@else{{ strtoupper(substr($user->nama, 0, 1)) }}@endif</div>
     <div><b>{{ $user->nama }}</b><small>Owner</small></div>
     <span class="chev">{!! \App\Support\Icon::svg('chev', 18) !!}</span>
    </div></div>
  </header>
  <div class="page">@yield('content')</div>
  <footer class="foot">Vika Jaya · Pengelolaan usaha terpusat</footer>
 </div>
</div>
</body>
</html>
