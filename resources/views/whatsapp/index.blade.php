<x-app-layout>
  <div class="ph">
    <div>
      <h1 class="pt">CS & Nomor WhatsApp</h1>
      <p class="ps">Setiap CS mengelola maksimal 5 nomor WA. Kapasitas per nomor dapat disesuaikan sekitar 3.000–5.000 database.</p>
    </div>
  </div>

  <div class="grid kpi-grid">
    <div class="card kpi">
      <div class="l">CS AKTIF</div>
      <div class="v">{{ $admins->count() }}</div>
      <div class="m">Maksimal 5 nomor / CS</div>
    </div>
    <div class="card kpi gold">
      <div class="l">NOMOR TERDAFTAR</div>
      <div class="v">{{ $registeredNumbers }}</div>
      <div class="m">Dari {{ $admins->count() * 5 }} slot</div>
    </div>
    <div class="card kpi">
      <div class="l">DATABASE AKTIF</div>
      <div class="v">{{ number_format($totalContacts, 0, ',', '.') }}</div>
      <div class="m">Semua kontak</div>
    </div>
    <div class="card kpi green">
      <div class="l">TOTAL KAPASITAS</div>
      <div class="v">{{ number_format($totalCapacity, 0, ',', '.') }}</div>
      <div class="m">Berdasarkan setting nomor</div>
    </div>
  </div>

  <div class="space-y-4">
    @foreach($admins as $adm)
    <div class="card sec" style="margin-bottom:16px">
      <div class="sh">
        <div>
          <h3>{{ $adm->name }}</h3>
          <span>{{ $adm->contacts->count() }} database · {{ $adm->waAccounts->whereNotNull('phone')->count() }}/5 nomor terisi</span>
        </div>
      </div>
      <div class="wa-grid">
        @foreach($adm->waAccounts as $wa)
        @php
          $assigned = $wa->assigned_count;
          $pct = $wa->capacity > 0 ? min(100, round(($assigned / $wa->capacity) * 100)) : 0;
          $cls = $pct >= 100 ? 'full' : ($pct >= 80 ? 'warn' : '');
        @endphp
        <div class="card wa-card" x-data="{ editModal: false }">
          <div class="wa-head">
            <div>
              <span class="slot-pill">Slot {{ $wa->slot }}</span>
              <h4 style="margin:9px 0 4px;font-size:14px;font-weight:800">{{ $wa->label }}</h4>
              <div class="small muted">{{ $wa->phone ?: 'Nomor belum diisi' }}</div>
            </div>
            <span class="status {{ $wa->status === 'Aktif' ? 's-active' : 's-gray' }}">{{ $wa->status }}</span>
          </div>
          <div style="display:flex;justify-content:space-between;margin-top:14px;font-size:12px">
            <span class="small muted">Database</span>
            <b>{{ number_format($assigned, 0, ',', '.') }} / {{ number_format($wa->capacity, 0, ',', '.') }}</b>
          </div>
          <div class="capbar">
            <div class="capfill {{ $cls }}" style="width:{{ $pct }}%"></div>
          </div>
          <div class="small muted" style="margin-top:7px">Sisa {{ number_format($wa->remaining_capacity, 0, ',', '.') }} slot</div>
          <button type="button" @click="editModal = true" class="btn btn-s full" style="margin-top:12px;min-height:36px;font-size:12px">
            Atur Nomor & Kapasitas
          </button>

          <!-- Edit Slot Modal -->
          <div class="modalbg" x-show="editModal" x-cloak style="display:flex">
            <div class="modal" @click.outside="editModal = false">
              <div class="sh">
                <h3>Atur {{ $adm->name }} · {{ $wa->label }}</h3>
                <button type="button" class="icon" @click="editModal = false">×</button>
              </div>
              <form action="{{ route('whatsapp.update-account', $wa) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="field">
                  <label>Label Nomor</label>
                  <input class="input" name="label" value="{{ $wa->label }}" required>
                </div>
                <div class="field">
                  <label>Nomor WhatsApp</label>
                  <input class="input" name="phone" value="{{ $wa->phone }}" placeholder="08xxxxxxxxxx">
                </div>
                <div class="field">
                  <label>Kapasitas Database</label>
                  <input class="input" type="number" name="capacity" min="{{ $assigned }}" value="{{ $wa->capacity }}" required>
                  <div class="small muted" style="margin-top:4px">Minimal {{ $assigned }} (kontak yang sudah terdaftar)</div>
                </div>
                <div class="field">
                  <label>Status</label>
                  <select class="input" name="status">
                    <option value="Aktif" {{ $wa->status === 'Aktif' ? 'selected' : '' }}>Aktif</option>
                    <option value="Nonaktif" {{ $wa->status === 'Nonaktif' ? 'selected' : '' }}>Nonaktif</option>
                  </select>
                </div>
                <div class="q" style="margin-top:16px">
                  <button type="button" @click="editModal = false" class="btn btn-s">Batal</button>
                  <button type="submit" class="btn btn-p">Simpan</button>
                </div>
              </form>
            </div>
          </div>
        </div>
        @endforeach
      </div>
    </div>
    @endforeach
  </div>
</x-app-layout>
