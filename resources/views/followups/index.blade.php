<x-app-layout>
  <div class="ph" x-data="{ fuModal: false }">
    <div>
      <h1 class="pt">{{ $isMaster ? 'Follow-up Semua Admin' : 'Follow-up Saya' }}</h1>
      <p class="ps">{{ $isMaster ? 'Master dapat melihat tindak lanjut dari seluruh admin.' : 'Prioritaskan percakapan yang memang butuh tindakan.' }}</p>
    </div>
    <div>
      <button type="button" @click="fuModal = true" class="btn btn-p">＋ Follow-up</button>
    </div>

    <!-- Modal Tambah Followup -->
    <div class="modalbg" x-show="fuModal" x-cloak style="display:flex">
      <div class="modal" @click.outside="fuModal = false">
        <div class="sh">
          <h3>Tambah Follow-up</h3>
          <button type="button" class="icon" @click="fuModal = false">×</button>
        </div>
        <form action="{{ route('followups.store') }}" method="POST">
          @csrf
          <div class="field">
            <label>Kontak *</label>
            <select class="input" name="contact_id" required>
              <option value="">-- Pilih Kontak --</option>
              @foreach($contacts as $c)
                <option value="{{ $c->id }}">{{ $c->name }} · {{ $c->phone }}</option>
              @endforeach
            </select>
          </div>
          <div class="fg">
            <div class="field">
              <label>Alasan</label>
              <select class="input" name="reason">
                <option value="Zakat">Reminder Zakat</option>
                <option value="Program Update">Program Update</option>
                <option value="Repeat Donation">Repeat Donation</option>
                <option value="Retention">Retention</option>
                <option value="Relationship">Relationship</option>
                <option value="Kontak Baru">Kontak Baru</option>
              </select>
            </div>
            <div class="field">
              <label>Jadwal</label>
              <input type="datetime-local" class="input" name="scheduled_at" required value="{{ now()->addHours(2)->format('Y-m-d\TH:i') }}">
            </div>
          </div>
          <div class="field">
            <label>Judul / Catatan Pesan *</label>
            <input class="input" name="title" required placeholder="Contoh: Sapa via WA dan kirim update program">
          </div>
          <div class="field">
            <label>Prioritas</label>
            <select class="input" name="priority">
              <option value="Normal">Normal</option>
              <option value="Tinggi">Tinggi</option>
              <option value="Rendah">Rendah</option>
            </select>
          </div>
          <div class="q" style="margin-top:16px">
            <button type="button" @click="fuModal = false" class="btn btn-s">Batal</button>
            <button type="submit" class="btn btn-p">Simpan Follow-up</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <div class="grid kpi-grid">
    <div class="card kpi red">
      <div class="l">OVERDUE</div>
      <div class="v">{{ $overdueCount }}</div>
      <div class="m">Perlu diselesaikan lebih dulu</div>
    </div>
    <div class="card kpi">
      <div class="l">HARI INI</div>
      <div class="v">{{ $todayCount }}</div>
      <div class="m">Siap dikerjakan</div>
    </div>
    <div class="card kpi gold">
      <div class="l">TERJADWAL</div>
      <div class="v">{{ $scheduledCount }}</div>
      <div class="m">Besok & minggu ini</div>
    </div>
    <div class="card kpi green">
      <div class="l">PORTFOLIO</div>
      <div class="v">{{ $isMaster ? 'Semua' : 'Saya' }}</div>
      <div class="m">{{ $isMaster ? 'Lembaga' : auth()->user()->name }}</div>
    </div>
  </div>

  <div class="card sec">
    <div class="sh">
      <h3>Daftar Follow-up</h3>
      <span>Hari ini & terjadwal</span>
    </div>
    <div class="schedule">
      @forelse($activeList as $f)
      <div class="sch">
        <div class="time">
          {{ $f->scheduled_at?->format('d M') }}<br>
          <span class="small muted">{{ $f->scheduled_at?->format('H:i') }}</span>
        </div>
        <div class="person">
          <div class="ava">{{ $f->contact?->initials ?? '?' }}</div>
          <div>
            <b>{{ $f->contact?->name ?? 'Kontak' }}</b>
            <div class="small muted">{{ $f->reason }} · {{ $f->title }} · Owner: {{ $f->owner?->name ?: '-' }}</div>
          </div>
        </div>
        <div class="q">
          @if($f->contact && $f->contact->relation_status !== 'Blokir')
            <a href="https://wa.me/{{ preg_replace('/\D/', '', $f->contact->phone) }}" target="_blank" rel="noopener" class="mini p">WA</a>
          @endif
          @if(auth()->user()->can('update', $f))
            <form action="{{ route('followups.complete', $f) }}" method="POST">
              @csrf
              <button type="submit" class="mini g">Selesai</button>
            </form>
          @endif
        </div>
      </div>
      @empty
      <div class="empty">Tidak ada follow-up aktif.</div>
      @endforelse
    </div>
  </div>
</x-app-layout>
