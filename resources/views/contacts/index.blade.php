<x-app-layout>
  <div class="ph">
    <div>
      <h1 class="pt">Semua Kontak</h1>
      <p class="ps">Satu orang, satu Contact ID. Setiap kontak terasosiasi dengan NISS unik, status relasi, dan PIC CS.</p>
    </div>
    <div class="q">
      <a href="{{ route('contacts.export-xlsx') }}" class="btn btn-s">⇩ Export XLSX</a>
      <a href="{{ route('contacts.create') }}" class="btn btn-p">＋ Tambah Kontak</a>
    </div>
  </div>

  <div class="toolbar">
    <form action="{{ route('contacts.index') }}" method="GET" class="search" style="max-width:360px">
      ⌕ <input name="search" value="{{ request('search') }}" placeholder="Cari nama, WA, kota, NISS...">
    </form>
    <form id="filterForm" action="{{ route('contacts.index') }}" method="GET" style="display:flex;gap:10px;flex-wrap:wrap">
      <input type="hidden" name="search" value="{{ request('search') }}">
      <select class="input" name="owner" style="width:170px" onchange="this.form.submit()">
        <option value="all">Semua Admin</option>
        @if(!auth()->user()->isMaster())
          <option value="mine" {{ request('owner') === 'mine' ? 'selected' : '' }}>Milik Saya</option>
        @endif
        @foreach($admins as $adm)
          <option value="{{ $adm->id }}" {{ request('owner') === $adm->id ? 'selected' : '' }}>{{ $adm->name }}</option>
        @endforeach
      </select>

      <select class="input" name="relation" style="width:160px" onchange="this.form.submit()">
        <option value="all">Status Relasi</option>
        <option value="Normal" {{ request('relation') === 'Normal' ? 'selected' : '' }}>Normal</option>
        <option value="Blokir" {{ request('relation') === 'Blokir' ? 'selected' : '' }}>Blokir</option>
        <option value="Untrust" {{ request('relation') === 'Untrust' ? 'selected' : '' }}>Untrust</option>
        <option value="Bosan" {{ request('relation') === 'Bosan' ? 'selected' : '' }}>Bosan</option>
      </select>

      <select class="input" name="zakat" style="width:160px" onchange="this.form.submit()">
        <option value="all">Status Zakat</option>
        <option value="Belum Dihitung" {{ request('zakat') === 'Belum Dihitung' ? 'selected' : '' }}>Belum Dihitung</option>
        <option value="Prospek" {{ request('zakat') === 'Prospek' ? 'selected' : '' }}>Prospek</option>
        <option value="Outstanding" {{ request('zakat') === 'Outstanding' ? 'selected' : '' }}>Outstanding</option>
        <option value="Sebagian" {{ request('zakat') === 'Sebagian' ? 'selected' : '' }}>Sebagian</option>
        <option value="Lunas" {{ request('zakat') === 'Lunas' ? 'selected' : '' }}>Lunas</option>
      </select>

      @if(request()->hasAny(['search', 'owner', 'relation', 'zakat', 'issue']))
        <a href="{{ route('contacts.index') }}" class="btn btn-s" style="min-height:46px">× Reset</a>
      @endif
    </form>
  </div>

  @if($issue)
    <div class="issue-filter">
      <strong>Filter aktif:</strong>
      <span class="chip red" style="height:28px">{{ ucfirst($issue) }}</span>
      <a href="{{ route('contacts.index') }}" class="mini" style="margin-left:auto">× Reset</a>
    </div>
  @endif

  <div class="tw">
    <table>
      <thead>
        <tr>
          <th>Kontak</th>
          <th>NISS</th>
          <th>Owner</th>
          <th>Status Donor</th>
          <th>Relasi</th>
          <th>Zakat</th>
          <th>Last Activity</th>
          <th>LTV</th>
          <th>Akses</th>
          <th>Aksi</th>
        </tr>
      </thead>
      <tbody>
        @forelse($contacts as $c)
        @php
          $canEdit = auth()->user()->can('update', $c);
          $relCls = $c->relation_status === 'Blokir' ? 's-block' : ($c->relation_status === 'Untrust' ? 's-untrust' : ($c->relation_status === 'Bosan' ? 's-bored' : 's-active'));
          $statusCls = $c->status === 'Loyal' ? 's-loyal' : ($c->status === 'Aktif' ? 's-active' : ($c->status === 'At Risk' ? 's-risk' : ($c->status === 'Dormant' ? 's-dorm' : 's-gray')));
          $zCls = $c->zakat_status === 'Lunas' ? 's-active' : ($c->zakat_status === 'Outstanding' ? 's-red' : ($c->zakat_status === 'Sebagian' ? 's-risk' : 's-gray'));
        @endphp
        <tr>
          <td>
            <a href="{{ route('contacts.show', $c) }}" class="person" style="text-decoration:none;color:inherit">
              <div class="ava">{{ $c->initials }}</div>
              <div>
                <b>{{ $c->name }}</b>
                <div class="small muted">{{ $c->city ?: '-' }} · {{ $c->phone }}</div>
              </div>
            </a>
          </td>
          <td><b>{{ $c->niss }}</b></td>
          <td>{{ $c->owner?->name ?: '-' }}</td>
          <td><span class="status {{ $statusCls }}">{{ $c->status }}</span></td>
          <td><span class="status {{ $relCls }}">{{ $c->relation_status }}</span></td>
          <td><span class="status {{ $zCls }}">{{ $c->zakat_status }}</span></td>
          <td>{{ $c->last_activity_at?->diffForHumans() ?: '-' }}</td>
          <td>Rp {{ number_format($c->ltv, 0, ',', '.') }}</td>
          <td><span class="status {{ $canEdit ? 's-active' : 's-gray' }}">{{ $canEdit ? 'Edit' : 'View Only' }}</span></td>
          <td>
            <div class="q">
              @if($c->relation_status === 'Blokir')
                <button class="mini wa-blocked" title="Kontak Blokir">🔒 WA</button>
              @else
                <a href="https://wa.me/{{ preg_replace('/\D/', '', $c->phone) }}" target="_blank" rel="noopener" class="mini wa-direct">WA</a>
              @endif
              <a href="{{ route('contacts.show', $c) }}" class="mini p">Detail</a>
            </div>
          </td>
        </tr>
        @empty
        <tr>
          <td colspan="10"><div class="empty">Belum ada data kontak yang cocok.</div></td>
        </tr>
        @endforelse
      </tbody>
    </table>
  </div>

  <div style="margin-top:16px">
    {{ $contacts->links() }}
  </div>
</x-app-layout>
