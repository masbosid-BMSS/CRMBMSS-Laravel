<x-app-layout>
  <div class="ph" x-data="{ pmModal: false }">
    <div>
      <h1 class="pt">Metode Pembayaran</h1>
      <p class="ps">Kelola pilihan metode pembayaran global untuk Input Transaksi. Hanya Master Admin yang dapat menambah, mengubah, menonaktifkan, atau menghapus metode.</p>
    </div>
    <div>
      <button type="button" @click="pmModal = true" class="btn btn-p">＋ Tambah Metode</button>
    </div>

    <!-- Modal Tambah Metode -->
    <div class="modalbg" x-show="pmModal" x-cloak style="display:flex">
      <div class="modal" @click.outside="pmModal = false">
        <div class="sh">
          <h3>Tambah Metode Pembayaran</h3>
          <button type="button" class="icon" @click="pmModal = false">×</button>
        </div>
        <form action="{{ route('payment-methods.store') }}" method="POST">
          @csrf
          <div class="field">
            <label>Nama Metode *</label>
            <input class="input" name="name" required placeholder="Contoh: BSI Transfer">
          </div>
          <div class="field">
            <label>Kategori *</label>
            <select class="input" name="category">
              <option value="Bank Transfer">Bank Transfer</option>
              <option value="QRIS">QRIS</option>
              <option value="Tunai">Tunai</option>
              <option value="E-Wallet">E-Wallet</option>
              <option value="Virtual Account">Virtual Account</option>
              <option value="Lainnya">Lainnya</option>
            </select>
          </div>
          <div class="field">
            <label>Detail / Rekening</label>
            <input class="input" name="detail" placeholder="Contoh: No. Rek 123456789 a.n. BMSS">
          </div>
          <div class="field">
            <label>Status</label>
            <select class="input" name="is_active">
              <option value="1">Aktif</option>
              <option value="0">Nonaktif</option>
            </select>
          </div>
          <div class="q" style="margin-top:16px">
            <button type="button" @click="pmModal = false" class="btn btn-s">Batal</button>
            <button type="submit" class="btn btn-p">Simpan Metode</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <div class="readonly">ℹ️ Metode yang <b>Aktif</b> otomatis muncul di form Input Transaksi untuk seluruh admin. Menghapus metode tidak mengubah transaksi lama yang sudah tersimpan.</div>

  <div class="grid kpi-grid" style="margin-top:16px">
    <div class="card kpi">
      <div class="l">METODE AKTIF</div>
      <div class="v">{{ $activeCount }}</div>
      <div class="m">Muncul di form transaksi</div>
    </div>
    <div class="card kpi gold">
      <div class="l">TOTAL METODE</div>
      <div class="v">{{ $methods->count() }}</div>
      <div class="m">Aktif + nonaktif</div>
    </div>
    <div class="card kpi green">
      <div class="l">TERPAKAI DI TRANSAKSI</div>
      <div class="v">{{ $usedCount }}</div>
      <div class="m">Termasuk metode historis</div>
    </div>
    <div class="card kpi red">
      <div class="l">HAK KELOLA</div>
      <div class="v">MASTER</div>
      <div class="m">Admin lain hanya memakai pilihan</div>
    </div>
  </div>

  <div class="grid" style="grid-template-columns:repeat(auto-fit,minmax(270px,1fr))">
    @foreach($methods as $pm)
    @php
      $cls = str_contains(strtolower($pm->category), 'bank') ? 'pm-bank' : (str_contains(strtolower($pm->category), 'qris') ? 'pm-qris' : (str_contains(strtolower($pm->category), 'tunai') ? 'pm-cash' : 'pm-other'));
      $icon = str_contains(strtolower($pm->category), 'bank') ? '🏦' : (str_contains(strtolower($pm->category), 'qris') ? '▦' : (str_contains(strtolower($pm->category), 'tunai') ? '💵' : '◈'));
    @endphp
    <div class="card pm-card {{ $cls }} {{ ! $pm->is_active ? 'pm-inactive' : '' }}">
      <div style="display:flex;justify-content:space-between;gap:12px;align-items:flex-start">
        <div class="pm-icon">{{ $icon }}</div>
        <span class="status {{ $pm->is_active ? 's-active' : 's-gray' }}">{{ $pm->is_active ? 'Aktif' : 'Nonaktif' }}</span>
      </div>
      <h3 style="margin:13px 0 4px;font-size:16px;font-weight:800">{{ $pm->name }}</h3>
      <div class="small muted">{{ $pm->category }}</div>
      <div class="pm-detail">{{ $pm->detail ?: 'Tanpa keterangan tambahan.' }}</div>

      <div class="pm-actions">
        <form action="{{ route('payment-methods.toggle', $pm) }}" method="POST" style="flex:1">
          @csrf
          <button type="submit" class="mini {{ ! $pm->is_active ? 'g' : 'y' }} full">
            {{ ! $pm->is_active ? 'Aktifkan' : 'Nonaktifkan' }}
          </button>
        </form>
        <form action="{{ route('payment-methods.destroy', $pm) }}" method="POST" onsubmit="return confirm('Hapus metode pembayaran ini?')">
          @csrf
          @method('DELETE')
          <button type="submit" class="mini" style="color:var(--red)">Hapus</button>
        </form>
      </div>
    </div>
    @endforeach
  </div>
</x-app-layout>
