<x-app-layout>
  <div class="ph" x-data="{ prModal: false }">
    <div>
      <h1 class="pt">Program</h1>
      <p class="ps">Kelola daftar program fundraising yang dapat dipilih pada kontak, transaksi, dan campaign.</p>
    </div>
    @can('is-master')
    <div>
      <button type="button" @click="prModal = true" class="btn btn-p">＋ Tambahkan Program Baru</button>
    </div>
    @endcan

    <!-- Modal Tambah Program -->
    <div class="modalbg" x-show="prModal" x-cloak style="display:flex">
      <div class="modal" @click.outside="prModal = false">
        <div class="sh">
          <h3>Tambahkan Program Baru</h3>
          <button type="button" class="icon" @click="prModal = false">×</button>
        </div>
        <form action="{{ route('programs.store') }}" method="POST">
          @csrf
          <div class="field">
            <label>Nama Program *</label>
            <input class="input" name="name" required placeholder="Contoh: Makan Santri Jumat">
          </div>
          <div class="field">
            <label>Kategori *</label>
            <select class="input" name="category">
              <option value="Sedekah">Sedekah</option>
              <option value="Zakat">Zakat</option>
              <option value="Infak">Infak</option>
              <option value="Wakaf">Wakaf</option>
              <option value="Sosial">Sosial</option>
              <option value="Kemanusiaan">Kemanusiaan</option>
            </select>
          </div>
          <div class="field">
            <label>Deskripsi</label>
            <textarea class="input" name="description" placeholder="Penjelasan program..."></textarea>
          </div>
          <div class="field">
            <label>Status</label>
            <select class="input" name="status">
              <option value="Aktif">Aktif</option>
              <option value="Arsip">Arsip</option>
            </select>
          </div>
          <div class="q" style="margin-top:16px">
            <button type="button" @click="prModal = false" class="btn btn-s">Batal</button>
            <button type="submit" class="btn btn-p">Simpan Program</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  @if(! $isMaster)
    <div class="readonly">👁 Program bersifat global. Admin dapat melihat dan menggunakan program, sedangkan penambahan atau perubahan program hanya dilakukan oleh Master Admin.</div>
  @endif

  <div class="grid" style="grid-template-columns:repeat(auto-fit,minmax(260px,1fr))">
    @forelse($programs as $p)
    <div class="card sec">
      <div class="sh">
        <h3>{{ $p->name }}</h3>
        <span class="status {{ $p->status === 'Aktif' ? 's-active' : 's-gray' }}">{{ $p->status }}</span>
      </div>
      <div class="chip {{ $p->category === 'Zakat' ? 'gold' : ($p->category === 'Sedekah' ? 'red' : 'a') }}">
        {{ $p->category }}
      </div>
      <p class="small muted" style="margin:12px 0;line-height:1.55;min-height:36px">
        {{ $p->description ?: 'Belum ada deskripsi.' }}
      </p>
      <div class="small muted" style="border-top:1px dashed var(--line);padding-top:10px">
        Campaign terkait: <b>{{ $p->campaigns_count }}</b>
      </div>
    </div>
    @empty
    <div class="card sec"><div class="empty">Belum ada program.</div></div>
    @endforelse
  </div>
</x-app-layout>
