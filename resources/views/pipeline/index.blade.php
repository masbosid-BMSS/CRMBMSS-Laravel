<x-app-layout>
  <div class="ph" x-data="{ leadModal: false }">
    <div>
      <h1 class="pt">Lead & Pipeline</h1>
      <p class="ps">Pantau perjalanan lead dari pertama masuk sampai menjadi donor.</p>
    </div>
    <div>
      <button type="button" @click="leadModal = true" class="btn btn-p">＋ Lead Baru</button>
    </div>

    <!-- Modal Tambah Lead -->
    <div class="modalbg" x-show="leadModal" x-cloak style="display:flex">
      <div class="modal" @click.outside="leadModal = false">
        <div class="sh">
          <h3>Tambah Lead Baru</h3>
          <button type="button" class="icon" @click="leadModal = false">×</button>
        </div>
        <form action="{{ route('leads.store') }}" method="POST">
          @csrf
          <div class="field">
            <label>Nama Calon Donatur *</label>
            <input class="input" name="name" required placeholder="Contoh: Bpk. Fajar">
          </div>
          <div class="fg">
            <div class="field">
              <label>WhatsApp</label>
              <input class="input" name="phone" placeholder="08xxxxxxxxxx">
            </div>
            <div class="field">
              <label>Kota</label>
              <input class="input" name="city" placeholder="Contoh: Solo">
            </div>
          </div>
          <div class="fg">
            <div class="field">
              <label>Sumber Lead</label>
              <input class="input" name="source" placeholder="Contoh: Meta Ads">
            </div>
            <div class="field">
              <label>Minat Program</label>
              <input class="input" name="interest" placeholder="Contoh: Guru Ngaji">
            </div>
          </div>
          <div class="fg">
            <div class="field">
              <label>Potensi (Rp)</label>
              <div class="money"><span>Rp</span><input name="potential_amount" type="number" placeholder="0"></div>
            </div>
            <div class="field">
              <label>Tahap / Stage</label>
              <select class="input" name="stage">
                <option value="Lead Baru">Lead Baru</option>
                <option value="Contacted">Contacted</option>
                <option value="Interested">Interested</option>
                <option value="Follow-up">Follow-up</option>
                <option value="Donasi">Donasi</option>
              </select>
            </div>
          </div>
          @can('is-master')
          <div class="field">
            <label>Owner CS</label>
            <select class="input" name="owner_id">
              @foreach($admins as $adm)
                <option value="{{ $adm->id }}" {{ $adm->id === auth()->id() ? 'selected' : '' }}>{{ $adm->name }}</option>
              @endforeach
            </select>
          </div>
          @endcan
          <div class="q" style="margin-top:16px">
            <button type="button" @click="leadModal = false" class="btn btn-s">Batal</button>
            <button type="submit" class="btn btn-p">Simpan Lead</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <div class="toolbar">
    <form action="{{ route('leads.index') }}" method="GET">
      <select class="input" name="owner" style="width:180px" onchange="this.form.submit()">
        <option value="all">Semua Admin</option>
        @if(!auth()->user()->isMaster())
          <option value="mine" {{ request('owner') === 'mine' ? 'selected' : '' }}>Milik Saya</option>
        @endif
        @foreach($admins as $adm)
          <option value="{{ $adm->id }}" {{ request('owner') === $adm->id ? 'selected' : '' }}>{{ $adm->name }}</option>
        @endforeach
      </select>
    </form>
    <div class="small muted">Lead milik admin lain tetap terlihat dalam mode view only.</div>
  </div>

  <div class="kanban">
    @foreach($stages as $st)
    @php
      $stageLeads = $leads->where('stage', $st);
    @endphp
    <div class="col">
      <div class="coh">
        <b>{{ $st }}</b>
        <span class="count">{{ $stageLeads->count() }}</span>
      </div>
      @forelse($stageLeads as $l)
      <div class="lead" x-data="{ moveOpen: false }">
        <div style="display:flex;justify-content:space-between;gap:10px">
          <b>{{ $l->name }}</b>
          <span class="small muted">{{ $l->owner?->name ?: '-' }}</span>
        </div>
        <div class="small muted" style="margin-top:6px">{{ $l->source ?: 'Organik' }} · {{ $l->city ?: '-' }}</div>
        <div class="lt">
          <span>{{ $l->interest ?: '-' }}</span>
          <span>Rp {{ number_format($l->potential_amount, 0, ',', '.') }}</span>
          <span>{{ auth()->user()->can('update', $l) ? 'Editable' : 'View only' }}</span>
        </div>

        @if(auth()->user()->can('update', $l))
        <div style="margin-top:8px;padding-top:8px;border-top:1px dashed var(--line);display:flex;justify-content:space-between;align-items:center">
          <span class="small muted">Pindah stage:</span>
          <div style="position:relative">
            <button type="button" @click="moveOpen = !moveOpen" class="mini" style="height:26px;font-size:10px">
              {{ $l->stage }} ▾
            </button>
            <div x-show="moveOpen" @click.outside="moveOpen = false" x-cloak style="position:absolute;right:0;bottom:100%;margin-bottom:4px;width:140px;background:#fff;border:1px solid var(--line);border-radius:12px;box-shadow:var(--sh);padding:6px;z-index:40">
              @foreach($stages as $nextStage)
                @if($nextStage !== $l->stage)
                <form action="{{ route('leads.update-stage', $l) }}" method="POST">
                  @csrf
                  <input type="hidden" name="stage" value="{{ $nextStage }}">
                  <button type="submit" class="nav" style="color:var(--text);padding:6px 8px;font-size:11px;width:100%;text-align:left">
                    {{ $nextStage }}
                  </button>
                </form>
                @endif
              @endforeach
            </div>
          </div>
        </div>
        @endif
      </div>
      @empty
      <div class="empty small">Belum ada data</div>
      @endforelse
    </div>
    @endforeach
  </div>
</x-app-layout>
