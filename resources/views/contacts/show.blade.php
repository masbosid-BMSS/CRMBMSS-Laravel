<x-app-layout>
  <div class="ph">
    <div>
      <h1 class="pt">Profil Kontak</h1>
      <p class="ps">Overview, zakat, transaksi, komunikasi, dan follow-up dalam satu layar.</p>
    </div>
  </div>

  @php
    $canEdit = auth()->user()->can('update', $contact);
    $relCls = $contact->relation_status === 'Blokir' ? 's-block' : ($contact->relation_status === 'Untrust' ? 's-untrust' : ($contact->relation_status === 'Bosan' ? 's-bored' : 's-active'));
    $statusCls = $contact->status === 'Loyal' ? 's-loyal' : ($contact->status === 'Aktif' ? 's-active' : ($contact->status === 'At Risk' ? 's-risk' : ($contact->status === 'Dormant' ? 's-dorm' : 's-gray')));
    $zCls = $contact->zakat_status === 'Lunas' ? 's-active' : ($contact->zakat_status === 'Outstanding' ? 's-red' : ($contact->zakat_status === 'Sebagian' ? 's-risk' : 's-gray'));
  @endphp

  <div class="card profile-top" x-data="{ tab: 'ov', editModal: false, txModal: false, fuModal: false }">
    <div class="phead">
      <div style="flex:1;min-width:260px">
        @if(! $canEdit)
          <div class="readonly">👁 Mode lihat. Database ini dikelola oleh <b>{{ $contact->owner?->name ?: '-' }}</b>. Anda dapat melihat informasi, tetapi tidak dapat mengubah data.</div>
        @endif

        @if($contact->relation_status === 'Blokir')
          <div class="readonly" style="background:#242842;color:#fff;border-color:#242842">
            ⛔ BLOKIR / DO NOT CONTACT — jangan lakukan WhatsApp, broadcast, atau follow-up. {{ $contact->relationship_note }}
          </div>
        @elseif($contact->relation_status === 'Untrust')
          <div class="readonly" style="background:var(--redSoft);color:#9b2630;border-color:#ffd1d5">
            ⚠ UNTRUST — kepercayaan menurun. {{ $contact->relationship_note }}
          </div>
        @elseif($contact->relation_status === 'Bosan')
          <div class="readonly" style="background:#f2f1ff;color:#5e4d91;border-color:#ddd8f7">
            ◌ BOSAN — program/komunikasi terasa berulang. {{ $contact->relationship_note }}
          </div>
        @endif

        <div class="pid">
          <div class="ava">{{ $contact->initials }}</div>
          <div>
            <h2 style="margin:0 0 4px;font-size:24px">{{ $contact->name }}</h2>
            <div class="muted">{{ $contact->city ?: '-' }} · {{ $contact->phone }} · <b>{{ $contact->niss }}</b></div>
            <div style="display:flex;gap:8px;flex-wrap:wrap;margin-top:10px">
              <span class="status {{ $statusCls }}">{{ $contact->status }}</span>
              <span class="status {{ $relCls }}">{{ $contact->relation_status }}</span>
              <span class="status {{ $zCls }}">{{ $contact->zakat_status }}</span>
              <span class="status s-gray">Owner: {{ $contact->owner?->name ?: '-' }}</span>
            </div>
          </div>
        </div>

        <div class="pkpi">
          <div class="pk"><small>Lifetime</small><b>Rp {{ number_format($contact->ltv, 0, ',', '.') }}</b></div>
          <div class="pk"><small>Transaksi</small><b>{{ $contact->transactions->count() }}</b></div>
          <div class="pk"><small>Zakat Terhitung</small><b>Rp {{ number_format($latestCalc?->zakat_amount ?? 0, 0, ',', '.') }}</b></div>
          <div class="pk"><small>Last Activity</small><b>{{ $contact->last_activity_at?->diffForHumans() ?: '-' }}</b></div>
        </div>
      </div>

      <div class="q">
        @if($contact->relation_status !== 'Blokir')
          <a href="https://wa.me/{{ preg_replace('/\D/', '', $contact->phone) }}" target="_blank" rel="noopener" class="btn btn-s">WhatsApp</a>
        @endif
        @if($canEdit)
          <button type="button" @click="editModal = true" class="btn btn-s">Edit</button>
          <a href="{{ route('zakat.calculator', ['contact_id' => $contact->id]) }}" class="btn btn-r">Hitung Zakat</a>
          <button type="button" @click="txModal = true" class="btn btn-p">+ Dana</button>
          @if($contact->relation_status !== 'Blokir')
            <button type="button" @click="fuModal = true" class="btn btn-s">Follow-up</button>
          @endif
        @endif
      </div>
    </div>

    <!-- Tabs Navigation -->
    <div class="tabs">
      <button class="tab" :class="tab === 'ov' ? 'a' : ''" @click="tab = 'ov'">Overview</button>
      <button class="tab" :class="tab === 'zk' ? 'a' : ''" @click="tab = 'zk'">Zakat</button>
      <button class="tab" :class="tab === 'tr' ? 'a' : ''" @click="tab = 'tr'">Transaksi</button>
      <button class="tab" :class="tab === 'fo' ? 'a' : ''" @click="tab = 'fo'">Follow-up</button>
    </div>

    <!-- Tab Overview -->
    <div class="tabp" :class="tab === 'ov' ? 'a' : ''">
      <div class="sub">
        <div class="card sec">
          <div class="sh"><h3>Data Kontak</h3></div>
          <div class="kv"><span>NISS</span><b>{{ $contact->niss }}</b></div>
          <div class="kv"><span>WhatsApp</span><b>{{ $contact->phone }}</b></div>
          <div class="kv"><span>Kota</span><b>{{ $contact->city ?: '-' }}</b></div>
          <div class="kv"><span>Source</span><b>{{ $contact->source ?: '-' }}</b></div>
          <div class="kv"><span>PIC / Owner</span><b>{{ $contact->owner?->name ?: '-' }}</b></div>
        </div>
        <div class="card sec">
          <div class="sh"><h3>Insight</h3></div>
          <div class="kv"><span>Program Favorit</span><b>{{ $contact->program ?: '-' }}</b></div>
          <div class="kv"><span>Segment</span><b>{{ $contact->status }} Donor</b></div>
          <div class="kv"><span>Next Action</span><b>{{ $contact->followups->firstWhere('status', '!=', 'Completed')?->scheduled_at?->format('d M H:i') ?: 'Belum ada' }}</b></div>
          <div style="margin-top:12px">
            @forelse($contact->tags ?: [] as $t)
              <span class="tag">{{ $t }}</span>
            @empty
              <span class="small muted">Tidak ada tag</span>
            @endforelse
          </div>
        </div>
      </div>
    </div>

    <!-- Tab Zakat -->
    <div class="tabp" :class="tab === 'zk' ? 'a' : ''">
      <div class="sub">
        <div class="card sec">
          <div class="sh"><h3>Status Zakat</h3></div>
          @if($latestCalc)
            <div style="padding:18px;border-radius:18px;background:linear-gradient(180deg,var(--gold),#ffd24d);color:#3b2c00">
              <div class="small" style="opacity:.75">Kewajiban Zakat</div>
              <div style="font-size:28px;font-weight:800;margin:4px 0 3px">Rp {{ number_format($latestCalc->zakat_amount, 0, ',', '.') }}</div>
              <div class="small">{{ $latestCalc->type }} · {{ $latestCalc->status }}</div>
            </div>
            <div class="kv" style="margin-top:14px"><span>Dibayar</span><b>Rp {{ number_format($latestCalc->paid_amount, 0, ',', '.') }}</b></div>
            <div class="kv"><span>Sisa</span><b>Rp {{ number_format($latestCalc->remaining_amount, 0, ',', '.') }}</b></div>
            <div class="kv"><span>Tanggal Hitung</span><b>{{ $latestCalc->calculation_date?->format('d M Y') }}</b></div>
          @else
            <div class="empty">Belum ada perhitungan zakat.</div>
          @endif
        </div>
        <div class="card sec">
          <div class="sh"><h3>Detail Harta</h3></div>
          @if(! $canEdit && ! auth()->user()->isMaster())
            <div class="mask">🔒 Detail harta hanya terlihat oleh owner dan Master Admin</div>
          @elseif($latestCalc)
            <div class="kv"><span>Total Harta</span><b>Rp {{ number_format($latestCalc->assets_total, 0, ',', '.') }}</b></div>
            <div class="kv"><span>Pengurang</span><b>Rp {{ number_format($latestCalc->deductions_total, 0, ',', '.') }}</b></div>
            <div class="kv"><span>Harta Bersih</span><b>Rp {{ number_format($latestCalc->net_amount, 0, ',', '.') }}</b></div>
            <div class="kv"><span>Nisab</span><b>Rp {{ number_format($latestCalc->nisab_amount, 0, ',', '.') }}</b></div>
          @else
            <div class="empty">Belum ada detail perhitungan.</div>
          @endif
        </div>
      </div>
    </div>

    <!-- Tab Transaksi -->
    <div class="tabp" :class="tab === 'tr' ? 'a' : ''">
      <div class="card sec">
        <div class="sh"><h3>Riwayat Transaksi</h3></div>
        @forelse($contact->transactions as $t)
          <div class="item">
            <span class="dot" style="background:{{ $t->type === 'Zakat' ? 'var(--gold)' : 'var(--red)' }}"></span>
            <div style="flex:1">
              <b>{{ $t->type }} · {{ $t->program }}</b>
              <div class="small muted">{{ $t->id }} · dicatat oleh {{ $t->recordedBy?->name ?: '-' }} · {{ $t->transaction_date?->format('d M Y H:i') }}</div>
            </div>
            <b>Rp {{ number_format($t->amount, 0, ',', '.') }}</b>
          </div>
        @empty
          <div class="empty">Belum ada transaksi dana.</div>
        @endforelse
      </div>
    </div>

    <!-- Tab Follow-up -->
    <div class="tabp" :class="tab === 'fo' ? 'a' : ''">
      <div class="card sec">
        <div class="sh"><h3>Follow-up Terkait</h3></div>
        @forelse($contact->followups as $f)
          <div class="item">
            <span class="dot" style="background:{{ $f->reason === 'Zakat' ? 'var(--gold)' : 'var(--navy)' }}"></span>
            <div style="flex:1">
              <b>{{ $f->title }}</b>
              <div class="small muted">{{ $f->reason }} · {{ $f->scheduled_at?->format('d M Y H:i') }}</div>
            </div>
            <span class="status {{ $f->status === 'Overdue' ? 's-dorm' : ($f->status === 'Completed' ? 's-gray' : 's-active') }}">
              {{ $f->status }}
            </span>
          </div>
        @empty
          <div class="empty">Belum ada follow-up.</div>
        @endforelse
      </div>
    </div>

    <!-- Modal Edit Kontak -->
    <div class="modalbg" x-show="editModal" x-cloak style="display:flex">
      <div class="modal" @click.outside="editModal = false">
        <div class="sh">
          <h3>Edit Kontak Donatur</h3>
          <button class="icon" @click="editModal = false">×</button>
        </div>
        <form action="{{ route('contacts.update', $contact) }}" method="POST">
          @csrf
          @method('PUT')
          <div class="field">
            <label>Nama Lengkap</label>
            <input class="input" name="name" value="{{ $contact->name }}" required>
          </div>
          <div class="fg">
            <div class="field">
              <label>WhatsApp</label>
              <input class="input" name="phone" value="{{ $contact->phone }}" required>
            </div>
            <div class="field">
              <label>Kota</label>
              <input class="input" name="city" value="{{ $contact->city }}">
            </div>
          </div>
          <div class="fg">
            <div class="field">
              <label>Status Donor</label>
              <select class="input" name="status">
                @foreach(['Baru', 'Aktif', 'Loyal', 'At Risk', 'Dormant'] as $st)
                  <option value="{{ $st }}" {{ $contact->status === $st ? 'selected' : '' }}>{{ $st }}</option>
                @endforeach
              </select>
            </div>
            <div class="field">
              <label>Status Relasi</label>
              <select class="input" name="relation_status">
                @foreach(['Normal', 'Blokir', 'Untrust', 'Bosan'] as $rs)
                  <option value="{{ $rs }}" {{ $contact->relation_status === $rs ? 'selected' : '' }}>{{ $rs }}</option>
                @endforeach
              </select>
            </div>
          </div>
          <div class="field">
            <label>Catatan Relasi</label>
            <textarea class="input" name="relationship_note">{{ $contact->relationship_note }}</textarea>
          </div>
          <div class="q" style="margin-top:16px">
            <button type="button" @click="editModal = false" class="btn btn-s">Batal</button>
            <button type="submit" class="btn btn-p">Simpan Perubahan</button>
          </div>
        </form>
      </div>
    </div>

    <!-- Modal Input Transaksi -->
    <div class="modalbg" x-show="txModal" x-cloak style="display:flex">
      <div class="modal" @click.outside="txModal = false">
        <div class="sh">
          <h3>Input Transaksi Dana</h3>
          <button class="icon" @click="txModal = false">×</button>
        </div>
        <form action="{{ route('transactions.store') }}" method="POST">
          @csrf
          <input type="hidden" name="contact_id" value="{{ $contact->id }}">
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
            <div class="money">
              <span>Rp</span>
              <input name="amount" type="number" min="1000" step="1000" required placeholder="0">
            </div>
          </div>
          <div class="field">
            <label>Metode Pembayaran</label>
            <select class="input" name="payment_method">
              @foreach(\App\Models\PaymentMethod::where('is_active', true)->get() as $pm)
                <option value="{{ $pm->name }}">{{ $pm->name }}</option>
              @endforeach
            </select>
          </div>
          <div class="field">
            <label>Program / Keterangan</label>
            <input class="input" name="program" value="{{ $contact->program }}">
          </div>
          <div class="q" style="margin-top:16px">
            <button type="button" @click="txModal = false" class="btn btn-s">Batal</button>
            <button type="submit" class="btn btn-p">Simpan Transaksi</button>
          </div>
        </form>
      </div>
    </div>

    <!-- Modal Tambah Followup -->
    <div class="modalbg" x-show="fuModal" x-cloak style="display:flex">
      <div class="modal" @click.outside="fuModal = false">
        <div class="sh">
          <h3>Tambah Follow-up</h3>
          <button class="icon" @click="fuModal = false">×</button>
        </div>
        <form action="{{ route('followups.store') }}" method="POST">
          @csrf
          <input type="hidden" name="contact_id" value="{{ $contact->id }}">
          <div class="field">
            <label>Alasan</label>
            <select class="input" name="reason">
              <option value="Repeat Donation">Repeat Donation</option>
              <option value="Program Update">Program Update</option>
              <option value="Zakat">Reminder Zakat</option>
              <option value="Retention">Retention</option>
              <option value="Relationship">Relationship</option>
            </select>
          </div>
          <div class="field">
            <label>Judul / Catatan</label>
            <input class="input" name="title" required placeholder="Contoh: Sapa via WhatsApp">
          </div>
          <div class="fg">
            <div class="field">
              <label>Prioritas</label>
              <select class="input" name="priority">
                <option value="Normal">Normal</option>
                <option value="Tinggi">Tinggi</option>
                <option value="Rendah">Rendah</option>
              </select>
            </div>
            <div class="field">
              <label>Jadwal</label>
              <input class="input" type="datetime-local" name="scheduled_at" required value="{{ now()->addHours(2)->format('Y-m-d\TH:i') }}">
            </div>
          </div>
          <div class="q" style="margin-top:16px">
            <button type="button" @click="fuModal = false" class="btn btn-s">Batal</button>
            <button type="submit" class="btn btn-p">Simpan Follow-up</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</x-app-layout>
