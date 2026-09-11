<x-app-layout>
  <div class="ph">
    <div>
      <h1 class="pt">Pengaturan</h1>
      <p class="ps">Konfigurasi organisasi dan database management.</p>
    </div>
  </div>

  <div class="sub">
    <div class="card sec">
      <div class="sh">
        <h3>Profil Organisasi</h3>
      </div>
      <form action="{{ route('settings.update') }}" method="POST">
        @csrf
        <div class="field">
          <label>Nama Organisasi</label>
          <input class="input" name="org_name" value="{{ $org }}" required>
        </div>
        <div class="field">
          <label>Target Bulanan (Rp)</label>
          <input class="input" type="number" name="monthly_target" value="{{ $target }}" required>
        </div>
        <div class="field">
          <label>Email Internal</label>
          <input class="input" type="email" name="org_email" value="{{ $email }}" required>
        </div>
        <button type="submit" class="btn btn-p full" style="margin-top:8px">Simpan Perubahan</button>
      </form>
    </div>

    <div class="card sec">
      <div class="sh">
        <h3>Database Management</h3>
        <span>Master only</span>
      </div>
      <div class="list">
        <div class="item">
          <span class="dot" style="background:var(--navy)"></span>
          <div style="flex:1">
            <b>Kontak Donatur</b>
            <div class="small muted">{{ $contactsCount }} record aktif</div>
          </div>
          <a href="{{ route('contacts.export-xlsx') }}" class="mini p">Export</a>
        </div>
      </div>
      <div class="note" style="margin-top:16px">
        Database tersimpan secara aman di database relasional backend dengan nomor identitas NISS yang terlindungi dari race condition.
      </div>
    </div>
  </div>
</x-app-layout>
