{{-- Simpan di: resources/views/pages/produk/create.blade.php (timpa file lama) --}}
@extends('layouts.app')
@section('title', 'Tambah Produk')
@section('crumb', 'Beranda / Produk / Tambah Produk')

@section('content')
<style>
.row.top-start{align-items:start}
.fgrid{display:grid;grid-template-columns:1fr 1fr;gap:20px}
.fld.full{grid-column:1/-1}
.fld label{display:block;font-weight:600;font-size:14px;margin-bottom:8px}
.fld input[type=text],.fld select{width:100%;height:44px;border:1px solid var(--line);border-radius:10px;padding:0 14px;font:inherit;font-size:15px;background:#fff;color:var(--ink)}
.fld input::placeholder{color:#94a3b8}
.fld input:focus,.fld select:focus{outline:2px solid var(--pri);outline-offset:1px}
.fld select{appearance:none;-webkit-appearance:none;padding-right:40px;background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' viewBox='0 0 24 24' fill='none' stroke='%2364748b' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpath d='m6 9 6 6 6-6'/%3E%3C/svg%3E");background-repeat:no-repeat;background-position:right 14px center}
.ferr{display:block;color:var(--bad);font-size:13px;margin-top:6px}

.drop{border:1.5px dashed #cbd5e1;border-radius:12px;background:var(--bg);padding:28px 16px;display:flex;flex-direction:column;align-items:center;gap:12px;text-align:center;color:var(--mut)}
.drop svg{color:var(--pri)}
.drop p{font-size:14px}
.drop.over{border-color:var(--pri);background:var(--pri-soft)}
.drop small{font-size:12px}
.drop img{max-height:120px;max-width:100%;border-radius:10px;object-fit:cover}

.info{display:flex;gap:12px;align-items:flex-start;background:var(--pri-soft);color:var(--ink);border-radius:10px;padding:14px 16px;margin:16px 0;font-size:14px;line-height:1.5}
.info svg{color:var(--pri);margin-top:2px;flex:none}

.actions{display:flex;justify-content:flex-end;gap:12px;margin-top:28px}
.actions .ghost{height:44px;text-decoration:none;display:inline-flex;align-items:center}
.btn-save{padding:0 20px;cursor:pointer;display:inline-flex;align-items:center;gap:8px}

@media(max-width:860px){.fgrid{grid-template-columns:1fr}}
</style>

<h2>Tambah Produk</h2>
<p class="sub">Tambahkan produk baru ke katalog seluruh lokasi.</p>

<div class="row top-start">
  <form class="card" method="POST" action="{{ route('produk.store') }}" enctype="multipart/form-data" id="formProduk">
    @csrf

    <div class="hd" style="margin-bottom:20px">
      <div>
        <h3>Informasi produk</h3>
        <p>Lengkapi identitas dan harga jual produk.</p>
      </div>
    </div>

    <div class="fgrid">
      <div class="fld">
        <label for="kode">Kode produk</label>
        <input id="kode" name="kode" type="text" value="{{ old('kode', $kode) }}" required>
        @error('kode')<span class="ferr">{{ $message }}</span>@enderror
      </div>

      <div class="fld">
        <label for="nama">Nama produk</label>
        <input id="nama" name="nama" type="text" placeholder="Masukkan nama produk" value="{{ old('nama') }}" required>
        @error('nama')<span class="ferr">{{ $message }}</span>@enderror
      </div>

      <div class="fld">
        <label for="kategori">Kategori</label>
        <select id="kategori" name="kategori" required>
          <option value="" disabled {{ old('kategori') ? '' : 'selected' }}>Pilih kategori</option>
          @foreach (['Keripik', 'Roti', 'Kue', 'Minuman', 'Lainnya'] as $k)
            <option value="{{ $k }}" {{ old('kategori') === $k ? 'selected' : '' }}>{{ $k }}</option>
          @endforeach
        </select>
        @error('kategori')<span class="ferr">{{ $message }}</span>@enderror
      </div>

      <div class="fld">
        <label for="satuan">Satuan</label>
        <select id="satuan" name="satuan" required>
          @foreach (['pcs', 'pack', 'box', 'kg'] as $s)
            <option value="{{ $s }}" {{ old('satuan', 'pcs') === $s ? 'selected' : '' }}>{{ $s }}</option>
          @endforeach
        </select>
        @error('satuan')<span class="ferr">{{ $message }}</span>@enderror
      </div>

      <div class="fld full">
        <label for="harga">Harga jual</label>
        <input id="harga" name="harga_jual" type="text" inputmode="numeric" placeholder="Rp 0" value="{{ old('harga_jual') }}" required>
        @error('harga_jual')<span class="ferr">{{ $message }}</span>@enderror
      </div>

      <div class="fld full">
        <label>Upload foto produk</label>
        <div class="drop" id="drop">
          <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h7"/><path d="M16 5h6M19 2v6"/><circle cx="9" cy="9" r="1.5"/><path d="m21 15-4.5-4.5L5 21"/></svg>
          <p id="dropText">Tarik foto ke sini atau pilih dari perangkat</p>
          <img id="preview" class="hide" alt="Pratinjau foto">
          <button type="button" class="ghost" id="pilihFoto">Pilih foto</button>
          <small>JPG atau PNG · Maksimal 2 MB</small>
          <input type="file" name="foto" id="foto" accept="image/png,image/jpeg" class="hide">
        </div>
        @error('foto')<span class="ferr">{{ $message }}</span>@enderror
      </div>
    </div>

    <div class="actions">
      <a href="{{ url('/produk') }}" class="ghost">Batal</a>
      <button type="submit" class="btn btn-save">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><path d="M17 21v-8H7v8M7 3v5h8"/></svg>
        Simpan
      </button>
    </div>
  </form>

  <aside class="card">
    <h3 style="font-size:18px;margin-bottom:16px">Panduan produk</h3>
    <p class="mut" style="line-height:1.5">Gunakan nama yang mudah dikenali kasir dan pelanggan.</p>
    <div class="info">
      <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="9"/><path d="M12 16v-4M12 8h.01"/></svg>
      <span>Kode produk harus unik dan tidak boleh sama dengan produk lainnya.</span>
    </div>
    <p class="mut" style="font-size:14px;line-height:1.5">Harga jual berlaku untuk Pusat, Cabang 1, dan Cabang 2. Stok awal dicatat melalui produksi.</p>
  </aside>
</div>

<script>
(function () {
  var harga = document.getElementById('harga');
  function fmt() {
    var d = harga.value.replace(/\D/g, '');
    harga.value = d ? 'Rp ' + d.replace(/\B(?=(\d{3})+(?!\d))/g, '.') : '';
  }
  harga.addEventListener('input', fmt);
  if (harga.value) fmt();

  var drop = document.getElementById('drop'),
      foto = document.getElementById('foto'),
      preview = document.getElementById('preview'),
      text = document.getElementById('dropText');

  document.getElementById('pilihFoto').addEventListener('click', function () { foto.click(); });

  function tampil(file) {
    if (!file) return;
    if (!/^image\/(png|jpeg)$/.test(file.type)) { alert('Format harus JPG atau PNG.'); foto.value = ''; return; }
    if (file.size > 2 * 1024 * 1024) { alert('Ukuran foto maksimal 2 MB.'); foto.value = ''; return; }
    preview.src = URL.createObjectURL(file);
    preview.classList.remove('hide');
    text.textContent = file.name;
  }
  foto.addEventListener('change', function () { tampil(foto.files[0]); });

  ['dragenter', 'dragover'].forEach(function (e) {
    drop.addEventListener(e, function (ev) { ev.preventDefault(); drop.classList.add('over'); });
  });
  ['dragleave', 'drop'].forEach(function (e) {
    drop.addEventListener(e, function (ev) { ev.preventDefault(); drop.classList.remove('over'); });
  });
  drop.addEventListener('drop', function (ev) {
    if (ev.dataTransfer.files.length) { foto.files = ev.dataTransfer.files; tampil(foto.files[0]); }
  });
})();
</script>
@endsection