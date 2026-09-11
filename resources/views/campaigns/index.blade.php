<x-app-layout>
  <div class="ph" x-data="{ cpModal: false }">
    <div>
      <h1 class="pt">Campaign</h1>
      <p class="ps">Campaign fundraising aktif dan kinerjanya. Berlaku global untuk seluruh CS.</p>
    </div>
    <div>
      <button type="button" @click="cpModal = true" class="btn btn-p">＋ Buat Campaign</button>
    </div>

    <!-- Modal Buat Campaign -->
    <div class="modalbg" x-show="cpModal" x-cloak style="display:flex">
      <div class="modal" @click.outside="cpModal = false">
        <div class="sh">
          <h3>Buat Campaign Global</h3>
          <button type="button" class="icon" @click="cpModal = false">×</button>
        </div>
        <form action="{{ route('campaigns.store') }}" method="POST">
          @csrf
          <div class="field">
            <label>Nama Campaign *</label>
            <input class="input" name="name" required placeholder="Contoh: Sedekah Guru Ngaji">
          </div>
          <div class="field">
            <label>Program Terkait</label>
            <select class="input" name="program_id">
              <option value="">-- Pilih Program --</option>
              @foreach($programs as $p)
                <option value="{{ $p->id }}">{{ $p->name }}</option>
              @endforeach
            </select>
          </div>
          <div class="fg">
            <div class="field">
              <label>Target Dana (Rp) *</label>
              <div class="money"><span>Rp</span><input name="target_amount" type="number" required min="1000" placeholder="0"></div>
            </div>
            <div class="field">
              <label>Sisa Hari</label>
              <input class="input" type="number" name="days_remaining" value="30">
            </div>
          </div>
          <div class="field">
            <label>Deskripsi</label>
            <textarea class="input" name="description" placeholder="Penjelasan campaign..."></textarea>
          </div>
          <div class="field">
            <label>Status</label>
            <select class="input" name="status">
              <option value="Aktif">Aktif</option>
              <option value="Selesai">Selesai</option>
              <option value="Arsip">Arsip</option>
            </select>
          </div>
          <div class="q" style="margin-top:16px">
            <button type="button" @click="cpModal = false" class="btn btn-s">Batal</button>
            <button type="submit" class="btn btn-p">Simpan Campaign</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <div class="grid" style="grid-template-columns:repeat(auto-fit,minmax(260px,1fr))">
    @forelse($campaigns as $c)
    @php
      $pct = $c->target_amount > 0 ? min(100, round(($c->achieved_amount / $c->target_amount) * 100, 1)) : 0;
      $bg = $c->accent_color === 'red' ? 'linear-gradient(90deg,var(--red),#ff6a72)' : ($c->accent_color === 'gold' ? 'linear-gradient(90deg,var(--gold),#ffd766)' : 'linear-gradient(90deg,var(--navy),#5b63a4)');
    @endphp
    <div class="card sec">
      <div class="sh">
        <h3>{{ $c->name }}</h3>
        <span>{{ $c->days_remaining }} hari lagi</span>
      </div>
      <div class="small muted" style="margin-bottom:8px">{{ $c->program?->name ?: 'Umum' }}</div>
      <div style="font-size:23px;font-weight:800">
        Rp {{ number_format($c->achieved_amount, 0, ',', '.') }}
        <span class="small muted" style="font-weight:normal">/ Rp {{ number_format($c->target_amount, 0, ',', '.') }}</span>
      </div>
      <div class="prog" style="margin-top:12px">
        <div class="bar" style="width:{{ $pct }}%;background:{{ $bg }}"></div>
      </div>
      <div class="sub" style="margin-top:14px">
        <div>
          <small class="muted">Donatur</small>
          <div style="font-weight:800;margin-top:3px">{{ $c->donors_count }}</div>
        </div>
        <div>
          <small class="muted">Pencapaian</small>
          <div style="font-weight:800;margin-top:3px;color:var(--green)">{{ $pct }}%</div>
        </div>
      </div>
    </div>
    @empty
    <div class="card sec"><div class="empty">Belum ada campaign.</div></div>
    @endforelse
  </div>
</x-app-layout>
