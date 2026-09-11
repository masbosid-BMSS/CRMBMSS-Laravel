<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>CRM BMSS v1.2 — DEMO Tim</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
<div class="login" id="login">
  <div class="hero">
    <div class="hero-in">
      <div class="brand">
        <div style="width:100px;height:100px;border-radius:24px;background:var(--navy);color:#fff;display:flex;flex-direction:column;align-items:center;justify-content:center;font-weight:900;box-shadow:var(--sh)">
          <span style="font-size:32px;line-height:1">BM</span>
          <span style="font-size:14px;color:var(--gold);letter-spacing:2px">SS</span>
        </div>
        <div>
          <h1>CRM <span>BMSS</span></h1>
          <div style="margin-top:10px;font-size:16px;color:var(--muted);max-width:560px">
            Baitulmaal Sejuta Santri · Sistem CRM Production-Ready untuk kontak, follow-up, transaksi, campaign, konsultasi zakat, dan laporan.
          </div>
        </div>
      </div>
      <div class="hero-grid">
        <div class="hero-card"><b>Dashboard role-based</b><div class="small muted" style="margin-top:6px">Master melihat semua data. Admin/CS fokus ke portfolio dan data miliknya.</div></div>
        <div class="hero-card"><b>Penghitung Zakat Terintegrasi</b><div class="small muted" style="margin-top:6px">Hitung zakat dari profil muzakki, simpan riwayat, lalu catat pembayarannya.</div></div>
        <div class="hero-card"><b>Ownership data</b><div class="small muted" style="margin-top:6px">CS hanya bisa mengotak-atik database miliknya. Data CS lain tetap bisa dilihat (view only).</div></div>
        <div class="hero-card"><b>Warna selaras BMSS</b><div class="small muted" style="margin-top:6px">Palet navy, red, dan gold mengikuti identitas visual logo BMSS.</div></div>
      </div>
    </div>
  </div>

  <div class="panel">
    <div class="login-card card">
      <div class="mini-brand">
        <div style="width:48px;height:48px;border-radius:14px;background:var(--navy);color:#fff;display:grid;place-items:center;font-weight:900;font-size:18px">
          BM
        </div>
        <div>
          <b style="display:block;color:var(--navy)">Internal BMSS</b>
          <span class="small muted">CRM BMSS Laravel</span>
        </div>
      </div>

      @if($errors->any())
        <div class="alert e">{{ $errors->first() }}</div>
      @endif

      @if(session('success'))
        <div class="alert s">{{ session('success') }}</div>
      @endif

      <form action="{{ route('login') }}" method="POST">
        @csrf
        <div class="field">
          <label>Username</label>
          <input class="input" name="username" value="{{ old('username') }}" required autofocus placeholder="Masukkan username">
        </div>
        <div class="field">
          <label>Password</label>
          <input class="input" name="password" type="password" required placeholder="Masukkan password">
        </div>
        <button type="submit" class="btn btn-p full" style="margin-top:4px">Masuk</button>
      </form>

      <div class="note">
        <div class="footnote">
          <b>Kredensial Demo:</b><br>
          • Master Admin: <b style="color:var(--navy)">bmssmanfaat</b> / <b style="color:var(--navy)">bismillah100</b><br>
          • CS Nisa: <b style="color:var(--navy)">nisa.cs</b> / <b style="color:var(--navy)">admin12345</b>
        </div>
      </div>
    </div>
  </div>
</div>
</body>
</html>
