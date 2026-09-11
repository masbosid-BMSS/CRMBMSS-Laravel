<x-app-layout>
  <div class="ph" x-data="{ txModal: false }">
    <div>
      <h1 class="pt">Transaksi Dana</h1>
      <p class="ps">Zakat, infak, sedekah, wakaf, dan dana lain dalam satu ledger internal.</p>
    </div>
    <div>
      <button type="button" @click="txModal = true" class="btn btn-p">＋ Input Transaksi</button>
    </div>

    <!-- Modal Input Transaksi -->
    <div class="modalbg" x-show="txModal" x-cloak style="display:flex">
      <div class="modal" @click.outside="txModal = false">
        <div class="sh">
          <h3>Input Transaksi Dana</h3>
          <button type="button" class="icon" @click="txModal = false">×</button>
        </div>
        <form action="{{ route('transactions.store') }}" method="POST">
          @csrf
          <div class="field">
            <label>Kontak Donatur *</label>
            <select class="input" name="contact_id" required>
              <option value="">-- Pilih Donatur --</option>
              @foreach(\App\Models\Contact::active()->orderBy('name')->get() as $c)
                <option value="{{ $c->id }}">{{ $c->name }} ({{ $c->niss }})</option>
              @endforeach
            </select>
          </div>
          <div class="fg">
            <div class="field">
              <label>Jenis Dana</label>
              <select class="input" name="type">
                <option value="Infak">Infak</option>
                <option value="Zakat">Zakat</option>
                <option value="Sedekah">Sedekah</option>
                <option value="Wakaf">Wakaf</option>
              </select>
            </div>
            <div class="field">
              <label>Nominal</label>
              <div class="money"><span>Rp</span><input name="amount" type="number" min="1000" step="1000" required placeholder="0"></div>
            </div>
          </div>
          <div class="field">
            <label>Metode Pembayaran</label>
            <select class="input" name="payment_method">
              @foreach($paymentMethods as $pm)
                <option value="{{ $pm->name }}">{{ $pm->name }}</option>
              @endforeach
            </select>
          </div>
          <div class="field">
            <label>Program</label>
            <select class="input" name="program">
              <option value="">-- Pilih Program --</option>
              @foreach($programs as $pr)
                <option value="{{ $pr->name }}">{{ $pr->name }} ({{ $pr->category }})</option>
              @endforeach
            </select>
          </div>
          <div class="field">
            <label>Campaign (Opsional)</label>
            <select class="input" name="campaign_id">
              <option value="">Tanpa campaign</option>
              @foreach($campaigns as $cp)
                <option value="{{ $cp->id }}">{{ $cp->name }}</option>
              @endforeach
            </select>
          </div>
          <div class="q" style="margin-top:16px">
            <button type="button" @click="txModal = false" class="btn btn-s">Batal</button>
            <button type="submit" class="btn btn-p">Simpan Transaksi</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <div class="toolbar">
    <form action="{{ route('transactions.index') }}" method="GET" style="display:flex;gap:10px;flex-wrap:wrap">
      <select class="input" name="owner" style="width:180px" onchange="this.form.submit()">
        <option value="all">Semua Admin</option>
        @if(!auth()->user()->isMaster())
          <option value="mine" {{ request('owner') === 'mine' ? 'selected' : '' }}>Milik Saya</option>
        @endif
        @foreach($admins as $adm)
          <option value="{{ $adm->id }}" {{ request('owner') === $adm->id ? 'selected' : '' }}>{{ $adm->name }}</option>
        @endforeach
      </select>

      <select class="input" name="type" style="width:180px" onchange="this.form.submit()">
        <option value="all">Semua Jenis</option>
        <option value="Zakat" {{ request('type') === 'Zakat' ? 'selected' : '' }}>Zakat</option>
        <option value="Infak" {{ request('type') === 'Infak' ? 'selected' : '' }}>Infak</option>
        <option value="Sedekah" {{ request('type') === 'Sedekah' ? 'selected' : '' }}>Sedekah</option>
        <option value="Wakaf" {{ request('type') === 'Wakaf' ? 'selected' : '' }}>Wakaf</option>
      </select>
    </form>
  </div>

  <div class="tw">
    <table>
      <thead>
        <tr>
          <th>ID</th>
          <th>Nama</th>
          <th>Owner</th>
          <th>Jenis</th>
          <th>Program</th>
          <th>Nominal</th>
          <th>Status</th>
          <th>Akses</th>
        </tr>
      </thead>
      <tbody>
        @forelse($transactions as $t)
        @php
          $canEdit = auth()->user()->can('update', $t);
        @endphp
        <tr>
          <td><b>{{ $t->id }}</b></td>
          <td>
            <a href="{{ route('contacts.show', $t->contact) }}" style="color:var(--navy);text-decoration:none;font-weight:bold">
              {{ $t->contact?->name ?: '-' }}
            </a>
          </td>
          <td>{{ $t->owner?->name ?: '-' }}</td>
          <td>
            <span class="status {{ $t->type === 'Zakat' ? 's-gold' : 's-red' }}">{{ $t->type }}</span>
          </td>
          <td>
            {{ $t->program }}
            @if($t->campaign)
              <div class="small muted">◎ {{ $t->campaign->name }}</div>
            @endif
          </td>
          <td><b>Rp {{ number_format($t->amount, 0, ',', '.') }}</b></td>
          <td><span class="status s-active">{{ $t->status }}</span></td>
          <td><span class="status {{ $canEdit ? 's-active' : 's-gray' }}">{{ $canEdit ? 'Edit' : 'View Only' }}</span></td>
        </tr>
        @empty
        <tr>
          <td colspan="8"><div class="empty">Belum ada data transaksi.</div></td>
        </tr>
        @endforelse
      </tbody>
    </table>
  </div>

  <div style="margin-top:16px">
    {{ $transactions->links() }}
  </div>
</x-app-layout>
