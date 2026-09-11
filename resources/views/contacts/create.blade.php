<x-app-layout>
  <div class="ph">
    <div>
      <h1 class="pt">Tambah Kontak Donatur</h1>
      <p class="ps">Satu orang, satu Contact ID & NISS. NISS digenerate otomatis dengan atomic sequence lock.</p>
    </div>
    <div>
      <a href="{{ route('contacts.index') }}" class="btn btn-s">← Kembali</a>
    </div>
  </div>

  <div class="card sec" style="max-width:760px;margin:auto">
    <form action="{{ route('contacts.store') }}" method="POST">
      @csrf
      <div class="fg">
        <div class="field">
          <label>Nama Lengkap Donatur *</label>
          <input class="input" name="name" value="{{ old('name') }}" required placeholder="Contoh: Bpk. Ahmad Fauzi">
        </div>
        <div class="field">
          <label>WhatsApp / No. HP *</label>
          <input class="input" name="phone" value="{{ old('phone') }}" required placeholder="Contoh: 081234567890">
        </div>
      </div>

      <div class="fg">
        <div class="field">
          <label>Kota Domisili</label>
          <input class="input" name="city" value="{{ old('city') }}" placeholder="Contoh: Bandung">
        </div>
        <div class="field">
          <label>Sumber Kontak</label>
          <input class="input" name="source" value="{{ old('source') }}" placeholder="Ngalamat / Meta Ads / Referral">
        </div>
      </div>

      <div class="fg">
        <div class="field">
          <label>Status Donatur</label>
          <select class="input" name="status">
            <option value="Baru">Baru</option>
            <option value="Aktif" selected>Aktif</option>
            <option value="Loyal">Loyal</option>
            <option value="At Risk">At Risk</option>
            <option value="Dormant">Dormant</option>
          </select>
        </div>
        <div class="field">
          <label>Status Relasi</label>
          <select class="input" name="relation_status">
            <option value="Normal" selected>Normal</option>
            <option value="Blokir">Blokir (Do Not Contact)</option>
            <option value="Untrust">Untrust (Kepercayaan Menurun)</option>
            <option value="Bosan">Bosan (Komunikasi Berulang)</option>
          </select>
        </div>
      </div>

      <div class="fg">
        <div class="field">
          <label>Program Favorit</label>
          <select class="input" name="program">
            <option value="">Pilih Program</option>
            @foreach(\App\Models\Program::where('status', 'Aktif')->get() as $pr)
              <option value="{{ $pr->name }}" {{ old('program') === $pr->name ? 'selected' : '' }}>{{ $pr->name }} ({{ $pr->category }})</option>
            @endforeach
          </select>
        </div>
        <div class="field">
          <label>NISS Kustom (Opsional)</label>
          <input class="input" name="niss" value="{{ old('niss') }}" placeholder="Kosongkan untuk NISS otomatis">
        </div>
      </div>

      @can('is-master')
      <div class="fg" style="background:#fafbfe;padding:14px;border-radius:14px;border:1px solid var(--line);margin:14px 0">
        <div class="field">
          <label>Owner CS</label>
          <select class="input" name="owner_id">
            @foreach(\App\Models\User::where('role', 'admin')->where('status', 'active')->get() as $cs)
              <option value="{{ $cs->id }}" {{ old('owner_id', auth()->id()) === $cs->id ? 'selected' : '' }}>{{ $cs->name }}</option>
            @endforeach
          </select>
        </div>
        <div class="field">
          <label>Nomor WhatsApp CS</label>
          <select class="input" name="wa_account_id">
            <option value="">Pilih nomor WA</option>
            @foreach(\App\Models\WaAccount::where('status', 'Aktif')->with('user')->get() as $wa)
              <option value="{{ $wa->id }}" {{ old('wa_account_id') === $wa->id ? 'selected' : '' }}>
                {{ $wa->user?->name }} · {{ $wa->label }} ({{ $wa->phone ?: 'belum diisi' }})
              </option>
            @endforeach
          </select>
        </div>
      </div>
      @endcan

      <div class="field">
        <label>Catatan Donatur</label>
        <textarea class="input" name="relationship_note" placeholder="Catatan preferensi, waktu hubungi, kondisi donatur..."></textarea>
      </div>

      <div class="followup-create-box" x-data="{ makeFu: true }">
        <label class="checkline">
          <input type="checkbox" name="make_followup" value="1" x-model="makeFu">
          Buat pengingat follow-up otomatis setelah kontak disimpan
        </label>
        <div x-show="makeFu" style="margin-top:12px">
          <div class="fg">
            <div class="field">
              <label>Jadwal Follow-up</label>
              <input class="input" type="datetime-local" name="followup_date" value="{{ now()->addDay()->setTime(9, 0)->format('Y-m-d\TH:i') }}">
            </div>
            <div class="field">
              <label>Catatan Follow-up</label>
              <input class="input" name="followup_title" value="Sapa kontak baru & perkenalkan program BMSS">
            </div>
          </div>
        </div>
      </div>

      <div class="q" style="margin-top:18px">
        <a href="{{ route('contacts.index') }}" class="btn btn-s">Batal</a>
        <button type="submit" class="btn btn-p">Simpan Kontak</button>
      </div>
    </form>
  </div>
</x-app-layout>
