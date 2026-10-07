<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
<title>Login · Vika Jaya</title>
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap">
<link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body>
<section class="login">
 <div class="l-left">
  <div class="brand"><div class="logo">vj</div><div><b>Vika Jaya</b><small>Produksi &amp; penjualan</small></div></div>
  <h1>Dari dapur produksi,<br>ke setiap pelanggan.</h1>
  <p>Pantau produksi, distribusi, dan penjualan Vika Jaya dalam satu ruang kerja.</p>
  <img alt="Ilustrasi pabrik, toko, dan truk pengiriman" src="{{ asset('img/login.jpg') }}">
  <div class="stats"><div><b>1</b><span>Pusat produksi</span></div><div><b>2</b><span>Cabang penjualan</span></div><div><b>1</b><span>Sistem terintegrasi</span></div></div>
  <small style="opacity:.7">© {{ date('Y') }} Vika Jaya. Seluruh hak dilindungi.</small>
 </div>
 <div class="l-right">
  <form class="form" method="POST" action="{{ route('login.attempt') }}">
   @csrf
   <div><div class="eyebrow">PORTAL OWNER</div><h2>Selamat datang kembali</h2></div>
   <p class="sub">Masuk untuk melihat perkembangan usaha Anda.</p>
   <div><label for="u">Username</label>
    <div class="inp"><input id="u" name="username" value="{{ old('username') }}" autocomplete="username" placeholder="Masukkan username" autofocus><i>{!! \App\Support\Icon::svg('user') !!}</i></div></div>
   <div><label for="p">Password</label>
    <div class="inp"><input id="p" name="password" type="password" autocomplete="current-password" placeholder="Masukkan password"><button type="button" id="eye" aria-label="Tampilkan password">{!! \App\Support\Icon::svg('off') !!}</button></div></div>
   @if ($errors->any())
    <div class="err" role="alert">{!! \App\Support\Icon::svg('alert', 18) !!}{{ $errors->first() }}</div>
   @endif
   <button class="btn" type="submit">Login</button>
   <p class="note">Akses khusus Owner. Data penjualan kasir tersinkron dari aplikasi mobile.</p>
  </form>
 </div>
</section>
<script>
(function(){
 var p=document.getElementById('p'),b=document.getElementById('eye'),on=@json(\App\Support\Icon::svg('eye')),off=@json(\App\Support\Icon::svg('off')),s=false;
 b.addEventListener('click',function(){s=!s;p.type=s?'text':'password';b.innerHTML=s?on:off;});
})();
</script>
</body>
</html>
