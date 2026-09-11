<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'CRM BMSS v1.2 — DEMO Tim' }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
<div class="app" style="display:block">
  <!-- Sidebar -->
  <aside class="sidebar">
    <div class="side-brand">
      <div style="width:40px;height:40px;border-radius:12px;background:rgba(255,255,255,.15);display:grid;place-items:center;font-weight:900;font-size:16px">BM</div>
      <div>
        <strong>CRM BMSS</strong>
        <small>Baitulmaal Sejuta Santri · v.1</small>
      </div>
    </div>

    <a href="{{ route('dashboard') }}" class="nav {{ request()->routeIs('dashboard') ? 'a' : '' }}">⌂ <span class="nl">Dashboard</span></a>

    <div class="nt">CRM</div>
    <a href="{{ route('contacts.index') }}" class="nav {{ request()->routeIs('contacts.*') ? 'a' : '' }}">♙ <span class="nl">Semua Kontak</span></a>
    <a href="{{ route('leads.index') }}" class="nav {{ request()->routeIs('leads.*') ? 'a' : '' }}">◇ <span class="nl">Lead & Pipeline</span></a>
    <a href="{{ route('followups.index') }}" class="nav {{ request()->routeIs('followups.*') ? 'a' : '' }}">✓ <span class="nl">Follow-up</span></a>

    <div class="nt">Zakat</div>
    <a href="{{ route('zakat.index') }}" class="nav {{ request()->routeIs('zakat.index') ? 'a' : '' }}">∑ <span class="nl">Zakat Center</span></a>
    <a href="{{ route('zakat.calculator') }}" class="nav {{ request()->routeIs('zakat.calculator') ? 'a' : '' }}">＋ <span class="nl">Hitung Zakat</span></a>
    <a href="{{ route('zakat.history', 'zakat.show') }}" class="nav {{ request()->routeIs('zakat.history', 'zakat.show') ? 'a' : '' }}">↺ <span class="nl">Riwayat Perhitungan</span></a>

    <div class="nt">Fundraising</div>
    <a href="{{ route('transactions.index') }}" class="nav {{ request()->routeIs('transactions.*') ? 'a' : '' }}">Rp <span class="nl">Transaksi Dana</span></a>
    <a href="{{ route('campaigns.index') }}" class="nav {{ request()->routeIs('campaigns.*') ? 'a' : '' }}">◎ <span class="nl">Campaign</span></a>
    <a href="{{ route('programs.index') }}" class="nav {{ request()->routeIs('programs.*') ? 'a' : '' }}">♡ <span class="nl">Program</span></a>

    @can('is-master')
    <div class="nt">Management</div>
    <a href="{{ route('payment-methods.index') }}" class="nav {{ request()->routeIs('payment-methods.*') ? 'a' : '' }}">▣ <span class="nl">Metode Pembayaran</span></a>
    <a href="{{ route('whatsapp.index') }}" class="nav {{ request()->routeIs('whatsapp.*') ? 'a' : '' }}">☎ <span class="nl">CS & Nomor WA</span></a>
    <a href="{{ route('users.index') }}" class="nav {{ request()->routeIs('users.*') ? 'a' : '' }}">👥 <span class="nl">Admin & User</span></a>
    <a href="{{ route('kpi-targets.index') }}" class="nav {{ request()->routeIs('kpi-targets.*') ? 'a' : '' }}">🎯 <span class="nl">Target & KPI CS</span></a>
    <a href="{{ route('settings.index') }}" class="nav {{ request()->routeIs('settings.*') ? 'a' : '' }}">⚙ <span class="nl">Pengaturan</span></a>
    @endcan

    <div class="userbox">
      <div class="ava">{{ auth()->user()->initials }}</div>
      <div class="uc">
        <b>{{ auth()->user()->name }}</b>
        <div class="small" style="color:rgba(255,255,255,.7)">{{ auth()->user()->isMaster() ? 'Master Admin' : 'Admin / CS' }}</div>
      </div>
    </div>
  </aside>

  <!-- Main Content -->
  <main class="main">
    <div class="top">
      <form action="{{ route('contacts.index') }}" method="GET" class="search">
        ⌕ <input name="search" value="{{ request('search') }}" placeholder="Cari nama, WA, no hitung, ID transaksi...">
      </form>
      <div class="top-actions">
        <a href="{{ route('contacts.create') }}" class="btn btn-p">＋ Tambah</a>
        <div class="relative" x-data="{ userMenuOpen: false }">
          <button type="button" @click="userMenuOpen = !userMenuOpen" class="icon" style="font-weight:800">
            {{ auth()->user()->initials }}
          </button>
          <div x-show="userMenuOpen" @click.outside="userMenuOpen = false" x-cloak style="position:absolute;right:0;top:48px;width:200px;background:#fff;border:1px solid var(--line);border-radius:16px;box-shadow:var(--sh);padding:10px;z-index:50">
            <div style="padding:6px 10px 10px;border-bottom:1px solid var(--line)">
              <b style="font-size:12px;color:var(--navy)">{{ auth()->user()->name }}</b>
              <div class="small muted">{{ auth()->user()->isMaster() ? 'Master Admin' : 'Admin / CS' }}</div>
            </div>
            <form action="{{ route('logout') }}" method="POST" style="margin-top:6px">
              @csrf
              <button type="submit" class="btn btn-r full" style="min-height:36px;font-size:12px">Logout</button>
            </form>
          </div>
        </div>
      </div>
    </div>

    <div class="content">
      @if(session('success'))
        <div class="alert s">{{ session('success') }}</div>
      @endif

      @if(session('error'))
        <div class="alert e">{{ session('error') }}</div>
      @endif

      @if($errors->any())
        <div class="alert e">
          <ul style="margin:0;padding-left:18px">
            @foreach($errors->all() as $err)
              <li>{{ $err }}</li>
            @endforeach
          </ul>
        </div>
      @endif

      {{ $slot }}
    </div>
  </main>

  <!-- Bottom Mobile Nav -->
  <nav class="bottom">
    <a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') ? 'a' : '' }}"><span>⌂</span><span>Home</span></a>
    <a href="{{ route('contacts.index') }}" class="{{ request()->routeIs('contacts.*') ? 'a' : '' }}"><span>♙</span><span>Kontak</span></a>
    <a href="{{ route('zakat.index') }}" class="{{ request()->routeIs('zakat.*') ? 'a' : '' }}"><span>∑</span><span>Zakat</span></a>
    <a href="{{ route('followups.index') }}" class="{{ request()->routeIs('followups.*') ? 'a' : '' }}"><span>✓</span><span>Follow-up</span></a>
    <a href="{{ route('transactions.index') }}" class="{{ request()->routeIs('transactions.*') ? 'a' : '' }}"><span>Rp</span><span>Dana</span></a>
  </nav>
</div>

@stack('scripts')
</body>
</html>
