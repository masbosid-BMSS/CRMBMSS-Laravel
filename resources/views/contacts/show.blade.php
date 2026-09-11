<x-app-layout>
    <!-- Readonly Banner if User cannot edit -->
    @if(! $canEdit)
    <div class="mb-4 p-4 rounded-2xl bg-[#FAFBFE] border border-[#DCE1F1] text-xs text-[#616A8B] flex items-center gap-2">
        <span class="text-base">👁</span>
        <span>Mode lihat. Database ini dikelola oleh <b>{{ $contact->owner?->name ?: '-' }}</b>. Anda dapat melihat informasi profil donatur, tetapi tidak dapat mengubah data.</span>
    </div>
    @endif

    <!-- Relation Banner Alert -->
    @if($contact->relation_status === 'Blokir')
    <div class="mb-4 p-4 rounded-2xl bg-[#242842] text-white text-xs font-medium flex items-center justify-between">
        <div>
            <b class="text-red-400">⛔ BLOKIR / DO NOT CONTACT</b> — Kontak meminta untuk tidak dihubungi lagi. Dilarang mengirimkan pesan WhatsApp, broadcast, atau reminder.
            @if($contact->relationship_note)
            <div class="mt-1 text-gray-300 text-[11px]">{{ $contact->relationship_note }}</div>
            @endif
        </div>
    </div>
    @elseif($contact->relation_status === 'Untrust')
    <div class="mb-4 p-4 rounded-2xl bg-[#FFF0F2] border border-[#ffd1d5] text-[#9b2630] text-xs font-medium">
        <b>⚠ STATUS RELASI: UNTRUST</b> — Kepercayaan donatur terhadap program/pengelolaan menurun. Gunakan pendekatan transparan dan berikan laporan berkala.
        @if($contact->relationship_note)
        <div class="mt-1 text-[11px]">{{ $contact->relationship_note }}</div>
        @endif
    </div>
    @elseif($contact->relation_status === 'Bosan')
    <div class="mb-4 p-4 rounded-2xl bg-[#F2F1FF] border border-[#ddd8f7] text-[#5e4d91] text-xs font-medium">
        <b>◌ STATUS RELASI: BOSAN</b> — Komunikasi terasa berulang. Disarankan menawarkan variasi program baru atau mengurangi intensitas follow-up.
        @if($contact->relationship_note)
        <div class="mt-1 text-[11px]">{{ $contact->relationship_note }}</div>
        @endif
    </div>
    @endif

    <!-- Profile Header Card -->
    <div class="bg-white border border-[#E1E3EC] rounded-[24px] p-6 shadow-bm-sm mb-6" x-data="{ tab: 'overview', editModal: false, txModal: false, fuModal: false }">
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6 pb-6 border-b border-gray-100">
            <!-- Left Info -->
            <div class="flex items-start gap-4">
                <div class="w-16 h-16 rounded-full bg-[#EEF0F8] text-[#343A72] font-black text-xl grid place-items-center flex-shrink-0 shadow-inner">
                    {{ $contact->initials }}
                </div>
                <div>
                    <div class="flex flex-wrap items-center gap-2 mb-1">
                        <h1 class="text-2xl font-black text-[#252B5B]">{{ $contact->name }}</h1>
                        <span class="px-2.5 py-0.5 rounded-full bg-gray-100 font-mono text-xs font-bold text-gray-700">{{ $contact->niss }}</span>
                    </div>
                    <div class="text-xs text-gray-500 font-medium flex flex-wrap items-center gap-2">
                        <span>{{ $contact->city ?: 'Kota belum diisi' }}</span>
                        <span>•</span>
                        <span>{{ $contact->phone }}</span>
                        <span>•</span>
                        <span>Owner CS: <b>{{ $contact->owner?->name ?: '-' }}</b></span>
                    </div>
                    <div class="flex flex-wrap items-center gap-2 mt-3">
                        <span class="px-2.5 py-1 rounded-full text-[10px] font-extrabold
                            {{ $contact->status === 'Loyal' ? 's-loyal' : ($contact->status === 'Aktif' ? 's-active' : ($contact->status === 'At Risk' ? 's-risk' : ($contact->status === 'Dormant' ? 's-dorm' : 's-gray'))) }}">
                            {{ $contact->status }}
                        </span>
                        <span class="px-2.5 py-1 rounded-full text-[10px] font-extrabold
                            {{ $contact->relation_status === 'Blokir' ? 's-block' : ($contact->relation_status === 'Untrust' ? 's-untrust' : ($contact->relation_status === 'Bosan' ? 's-bored' : 's-active')) }}">
                            Relasi: {{ $contact->relation_status }}
                        </span>
                        <span class="px-2.5 py-1 rounded-full text-[10px] font-extrabold
                            {{ $contact->zakat_status === 'Lunas' ? 's-active' : ($contact->zakat_status === 'Outstanding' ? 's-red' : ($contact->zakat_status === 'Sebagian' ? 's-risk' : ($contact->zakat_status === 'Prospek' ? 's-gold' : 's-gray'))) }}">
                            Zakat: {{ $contact->zakat_status }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- Right Actions -->
            <div class="flex flex-wrap items-center gap-2">
                @if($contact->relation_status !== 'Blokir')
                    <a href="https://wa.me/{{ preg_replace('/\D/', '', $contact->phone) }}" target="_blank" rel="noopener" class="h-10 px-4 rounded-xl bg-[#EAF8F1] text-[#166848] text-xs font-extrabold flex items-center gap-1.5 hover:bg-[#caead8] transition">
                        <span>☎</span>
                        <span>WhatsApp</span>
                    </a>
                @endif

                @if($canEdit)
                    <button type="button" @click="editModal = true" class="h-10 px-4 rounded-xl bg-white border border-[#E1E3EC] text-[#343A72] text-xs font-extrabold hover:bg-gray-50 transition">
                        Edit
                    </button>
                    <a href="{{ route('zakat.calculator', ['contact_id' => $contact->id]) }}" class="h-10 px-4 rounded-xl bg-[#FF303B] text-white text-xs font-extrabold flex items-center gap-1.5 hover:bg-red-600 transition shadow-sm">
                        <span>＋</span>
                        <span>Hitung Zakat</span>
                    </a>
                    <button type="button" @click="txModal = true" class="h-10 px-4 rounded-xl bg-[#343A72] text-white text-xs font-extrabold flex items-center gap-1.5 hover:bg-[#252B5B] transition shadow-sm">
                        <span>＋</span>
                        <span>Tambah Dana</span>
                    </button>
                    @if($contact->relation_status !== 'Blokir')
                        <button type="button" @click="fuModal = true" class="h-10 px-4 rounded-xl bg-white border border-[#E1E3EC] text-gray-700 text-xs font-extrabold hover:bg-gray-50 transition">
                            Follow-up
                        </button>
                    @endif
                @endif
            </div>
        </div>

        <!-- 4 Quick Stats -->
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 pt-5">
            <div class="p-3.5 rounded-2xl bg-[#FBFBFE] border border-[#ECEEF5]">
                <div class="text-[10px] font-bold text-gray-400 uppercase">Lifetime Donasi (LTV)</div>
                <div class="text-base font-extrabold text-[#343A72] mt-1">Rp {{ number_format($contact->ltv, 0, ',', '.') }}</div>
            </div>
            <div class="p-3.5 rounded-2xl bg-[#FBFBFE] border border-[#ECEEF5]">
                <div class="text-[10px] font-bold text-gray-400 uppercase">Total Transaksi</div>
                <div class="text-base font-extrabold text-[#252B5B] mt-1">{{ $contact->transactions->count() }} kali</div>
            </div>
            <div class="p-3.5 rounded-2xl bg-[#FBFBFE] border border-[#ECEEF5]">
                <div class="text-[10px] font-bold text-gray-400 uppercase">Zakat Terhitung</div>
                <div class="text-base font-extrabold text-[#C98600] mt-1">
                    Rp {{ number_format($latestCalc?->zakat_amount ?? 0, 0, ',', '.') }}
                </div>
            </div>
            <div class="p-3.5 rounded-2xl bg-[#FBFBFE] border border-[#ECEEF5]">
                <div class="text-[10px] font-bold text-gray-400 uppercase">Aktivitas Terakhir</div>
                <div class="text-base font-extrabold text-gray-700 mt-1">{{ $contact->last_activity_at?->diffForHumans() ?: '-' }}</div>
            </div>
        </div>

        <!-- Tabs Navigation -->
        <div class="flex items-center gap-2 mt-6 border-b border-gray-100 pb-3 text-xs font-extrabold">
            <button type="button" @click="tab = 'overview'" :class="tab === 'overview' ? 'bg-[#343A72] text-white' : 'bg-white text-gray-500 border border-[#E1E3EC] hover:bg-gray-50'" class="px-4 py-2 rounded-full transition">
                Overview
            </button>
            <button type="button" @click="tab = 'zakat'" :class="tab === 'zakat' ? 'bg-[#343A72] text-white' : 'bg-white text-gray-500 border border-[#E1E3EC] hover:bg-gray-50'" class="px-4 py-2 rounded-full transition">
                Zakat ({{ $contact->calculations->count() }})
            </button>
            <button type="button" @click="tab = 'transactions'" :class="tab === 'transactions' ? 'bg-[#343A72] text-white' : 'bg-white text-gray-500 border border-[#E1E3EC] hover:bg-gray-50'" class="px-4 py-2 rounded-full transition">
                Transaksi ({{ $contact->transactions->count() }})
            </button>
            <button type="button" @click="tab = 'followup'" :class="tab === 'followup' ? 'bg-[#343A72] text-white' : 'bg-white text-gray-500 border border-[#E1E3EC] hover:bg-gray-50'" class="px-4 py-2 rounded-full transition">
                Follow-up ({{ $contact->followups->count() }})
            </button>
        </div>

        <!-- Tab 1: Overview -->
        <div x-show="tab === 'overview'" class="pt-5 grid md:grid-cols-2 gap-4 text-xs">
            <div class="p-5 rounded-2xl bg-[#FBFBFE] border border-[#ECEEF5] space-y-3">
                <h3 class="font-extrabold text-sm text-[#252B5B] mb-2">Informasi Kontak</h3>
                <div class="flex justify-between py-1.5 border-b border-dashed border-gray-200">
                    <span class="text-gray-400">NISS</span>
                    <b class="font-mono text-gray-800">{{ $contact->niss }}</b>
                </div>
                <div class="flex justify-between py-1.5 border-b border-dashed border-gray-200">
                    <span class="text-gray-400">WhatsApp</span>
                    <b class="text-gray-800">{{ $contact->phone }}</b>
                </div>
                <div class="flex justify-between py-1.5 border-b border-dashed border-gray-200">
                    <span class="text-gray-400">Kota Domisili</span>
                    <b class="text-gray-800">{{ $contact->city ?: '-' }}</b>
                </div>
                <div class="flex justify-between py-1.5 border-b border-dashed border-gray-200">
                    <span class="text-gray-400">Sumber Kontak</span>
                    <b class="text-gray-800">{{ $contact->source ?: '-' }}</b>
                </div>
                <div class="flex justify-between py-1.5">
                    <span class="text-gray-400">PIC / CS Owner</span>
                    <b class="text-gray-800">{{ $contact->owner?->name ?: '-' }}</b>
                </div>
            </div>

            <div class="p-5 rounded-2xl bg-[#FBFBFE] border border-[#ECEEF5] space-y-3">
                <h3 class="font-extrabold text-sm text-[#252B5B] mb-2">Preferensi & Catatan</h3>
                <div class="flex justify-between py-1.5 border-b border-dashed border-gray-200">
                    <span class="text-gray-400">Program Favorit</span>
                    <b class="text-[#343A72]">{{ $contact->program ?: '-' }}</b>
                </div>
                <div class="flex justify-between py-1.5 border-b border-dashed border-gray-200">
                    <span class="text-gray-400">Status Donor</span>
                    <b class="text-gray-800">{{ $contact->status }}</b>
                </div>
                <div>
                    <span class="text-gray-400 block mb-1">Catatan Relasi</span>
                    <div class="p-2.5 rounded-xl bg-white border border-gray-200 text-gray-700 leading-relaxed">
                        {{ $contact->relationship_note ?: 'Tidak ada catatan khusus.' }}
                    </div>
                </div>
                <div>
                    <span class="text-gray-400 block mb-1">Tags</span>
                    <div class="flex flex-wrap gap-1.5">
                        @forelse($contact->tags ?: [] as $tg)
                            <span class="px-2.5 py-1 rounded-full bg-white border border-[#E1E3EC] text-gray-600 font-bold text-[11px]">{{ $tg }}</span>
                        @empty
                            <span class="text-gray-400">-</span>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        <!-- Tab 2: Zakat -->
        <div x-show="tab === 'zakat'" class="pt-5 space-y-3" x-cloak>
            @forelse($contact->calculations as $calc)
            <div class="p-4 rounded-2xl bg-[#FBFBFE] border border-[#ECEEF5] flex flex-col md:flex-row md:items-center justify-between gap-3 text-xs">
                <div>
                    <div class="flex items-center gap-2">
                        <b class="font-bold text-sm text-[#252B5B]">{{ $calc->ref_no }}</b>
                        <span class="px-2 py-0.5 rounded-full font-extrabold text-[10px]
                            {{ $calc->status === 'Lunas' ? 's-active' : ($calc->status === 'Outstanding' ? 's-red' : 's-risk') }}">
                            {{ $calc->status }}
                        </span>
                    </div>
                    <div class="text-gray-400 mt-1">{{ $calc->type }} • Tanggal: {{ $calc->calculation_date?->format('d M Y') }}</div>
                </div>
                <div class="flex items-center gap-6">
                    <div>
                        <div class="text-gray-400 text-[10px]">Kewajiban Zakat</div>
                        <b class="text-sm font-extrabold text-[#343A72]">Rp {{ number_format($calc->zakat_amount, 0, ',', '.') }}</b>
                    </div>
                    <div>
                        <div class="text-gray-400 text-[10px]">Terbayar</div>
                        <b class="text-sm font-extrabold text-emerald-600">Rp {{ number_format($calc->paid_amount, 0, ',', '.') }}</b>
                    </div>
                    <a href="{{ route('zakat.show', $calc) }}" class="px-3 py-1.5 bg-white border border-[#E1E3EC] rounded-xl font-bold text-[#343A72] hover:bg-gray-50">
                        Detail
                    </a>
                </div>
            </div>
            @empty
            <div class="text-center py-10 text-xs text-gray-400">Belum ada riwayat kalkulasi zakat untuk kontak ini.</div>
            @endforelse
        </div>

        <!-- Tab 3: Transactions -->
        <div x-show="tab === 'transactions'" class="pt-5 space-y-3" x-cloak>
            @forelse($contact->transactions as $tx)
            <div class="p-4 rounded-2xl bg-[#FBFBFE] border border-[#ECEEF5] flex items-center justify-between gap-3 text-xs">
                <div>
                    <b class="block font-bold text-sm text-[#252B5B]">{{ $tx->id }}</b>
                    <div class="text-gray-400 mt-0.5">{{ $tx->type }} • {{ $tx->program }} • {{ $tx->transaction_date?->format('d M Y H:i') }}</div>
                </div>
                <div class="text-right">
                    <b class="block text-sm font-black text-[#343A72]">Rp {{ number_format($tx->amount, 0, ',', '.') }}</b>
                    <span class="text-[11px] text-gray-400">{{ $tx->payment_method }} • Recorded by {{ $tx->recordedBy?->name ?: '-' }}</span>
                </div>
            </div>
            @empty
            <div class="text-center py-10 text-xs text-gray-400">Belum ada transaksi dana tercatat.</div>
            @endforelse
        </div>

        <!-- Tab 4: Follow-up -->
        <div x-show="tab === 'followup'" class="pt-5 space-y-3" x-cloak>
            @forelse($contact->followups as $fu)
            <div class="p-4 rounded-2xl bg-[#FBFBFE] border border-[#ECEEF5] flex items-center justify-between gap-3 text-xs">
                <div>
                    <div class="flex items-center gap-2">
                        <b class="font-bold text-sm text-[#252B5B]">{{ $fu->title }}</b>
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold {{ $fu->status === 'Overdue' ? 'bg-red-100 text-red-700' : ($fu->status === 'Completed' ? 'bg-gray-100 text-gray-600' : 'bg-emerald-100 text-emerald-800') }}">
                            {{ $fu->status }}
                        </span>
                    </div>
                    <div class="text-gray-400 mt-1">{{ $fu->reason }} • Jadwal: {{ $fu->scheduled_at?->translatedFormat('d M Y H:i') }}</div>
                </div>
                @if($fu->status !== 'Completed' && $canEdit)
                <form action="{{ route('followups.complete', $fu) }}" method="POST">
                    @csrf
                    <button type="submit" class="px-3 py-1.5 bg-[#EAF8F1] text-[#166848] font-bold rounded-xl hover:bg-[#caead8]">
                        Selesai
                    </button>
                </form>
                @endif
            </div>
            @empty
            <div class="text-center py-10 text-xs text-gray-400">Belum ada follow-up tercatat.</div>
            @endforelse
        </div>

        <!-- Modal Edit Contact -->
        <div x-show="editModal" x-cloak class="fixed inset-0 bg-[#111536]/40 backdrop-blur-xs flex items-center justify-center z-50 p-4" @click.self="editModal = false">
            <div class="bg-white rounded-[24px] border border-[#E1E3EC] shadow-bmss p-6 max-w-lg w-full max-h-[90vh] overflow-y-auto">
                <h3 class="font-black text-lg text-[#252B5B]">Edit Kontak Donatur</h3>
                <form action="{{ route('contacts.update', $contact) }}" method="POST" class="mt-4 space-y-3.5 text-xs">
                    @csrf
                    @method('PUT')
                    <div>
                        <label class="block font-bold text-gray-700 mb-1">Nama Lengkap *</label>
                        <input type="text" name="name" value="{{ $contact->name }}" required class="w-full h-10 px-3 bg-white border border-[#E1E3EC] rounded-xl text-xs">
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block font-bold text-gray-700 mb-1">WhatsApp *</label>
                            <input type="text" name="phone" value="{{ $contact->phone }}" required class="w-full h-10 px-3 bg-white border border-[#E1E3EC] rounded-xl text-xs">
                        </div>
                        <div>
                            <label class="block font-bold text-gray-700 mb-1">Kota</label>
                            <input type="text" name="city" value="{{ $contact->city }}" class="w-full h-10 px-3 bg-white border border-[#E1E3EC] rounded-xl text-xs">
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block font-bold text-gray-700 mb-1">Status Donor</label>
                            <select name="status" class="w-full h-10 px-3 bg-white border border-[#E1E3EC] rounded-xl text-xs">
                                @foreach(['Baru', 'Aktif', 'Loyal', 'At Risk', 'Dormant'] as $st)
                                    <option value="{{ $st }}" {{ $contact->status === $st ? 'selected' : '' }}>{{ $st }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block font-bold text-gray-700 mb-1">Status Relasi</label>
                            <select name="relation_status" class="w-full h-10 px-3 bg-white border border-[#E1E3EC] rounded-xl text-xs">
                                @foreach(['Normal', 'Blokir', 'Untrust', 'Bosan'] as $rs)
                                    <option value="{{ $rs }}" {{ $contact->relation_status === $rs ? 'selected' : '' }}>{{ $rs }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div>
                        <label class="block font-bold text-gray-700 mb-1">Catatan Status Relasi</label>
                        <textarea name="relationship_note" rows="2" class="w-full p-2.5 bg-white border border-[#E1E3EC] rounded-xl text-xs">{{ $contact->relationship_note }}</textarea>
                    </div>
                    <div class="flex items-center justify-end gap-2 pt-2">
                        <button type="button" @click="editModal = false" class="px-4 py-2 bg-gray-100 text-gray-600 font-bold rounded-xl">Batal</button>
                        <button type="submit" class="px-4 py-2 bg-[#343A72] text-white font-bold rounded-xl hover:bg-[#252B5B]">Simpan</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Modal Tambah Transaksi -->
        <div x-show="txModal" x-cloak class="fixed inset-0 bg-[#111536]/40 backdrop-blur-xs flex items-center justify-center z-50 p-4" @click.self="txModal = false">
            <div class="bg-white rounded-[24px] border border-[#E1E3EC] shadow-bmss p-6 max-w-md w-full max-h-[90vh] overflow-y-auto">
                <h3 class="font-black text-lg text-[#252B5B]">Input Transaksi Dana</h3>
                <form action="{{ route('transactions.store') }}" method="POST" class="mt-4 space-y-3.5 text-xs">
                    @csrf
                    <input type="hidden" name="contact_id" value="{{ $contact->id }}">
                    <div>
                        <label class="block font-bold text-gray-700 mb-1">Jenis Dana *</label>
                        <select name="type" required class="w-full h-10 px-3 bg-white border border-[#E1E3EC] rounded-xl text-xs">
                            <option value="Zakat">Zakat</option>
                            <option value="Infak" selected>Infak</option>
                            <option value="Sedekah">Sedekah</option>
                            <option value="Wakaf">Wakaf</option>
                        </select>
                    </div>
                    <div>
                        <label class="block font-bold text-gray-700 mb-1">Nominal (Rp) *</label>
                        <input type="number" name="amount" min="1000" step="1000" required placeholder="Contoh: 100000" class="w-full h-10 px-3 bg-white border border-[#E1E3EC] rounded-xl text-xs font-bold text-[#343A72]">
                    </div>
                    <div>
                        <label class="block font-bold text-gray-700 mb-1">Metode Pembayaran *</label>
                        <select name="payment_method" required class="w-full h-10 px-3 bg-white border border-[#E1E3EC] rounded-xl text-xs">
                            @foreach(\App\Models\PaymentMethod::where('is_active', true)->get() as $pm)
                                <option value="{{ $pm->name }}">{{ $pm->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block font-bold text-gray-700 mb-1">Program / Keterangan</label>
                        <input type="text" name="program" value="{{ $contact->program }}" placeholder="Contoh: Guru Ngaji / Sumur" class="w-full h-10 px-3 bg-white border border-[#E1E3EC] rounded-xl text-xs">
                    </div>
                    <div class="flex items-center justify-end gap-2 pt-2">
                        <button type="button" @click="txModal = false" class="px-4 py-2 bg-gray-100 text-gray-600 font-bold rounded-xl">Batal</button>
                        <button type="submit" class="px-4 py-2 bg-[#343A72] text-white font-bold rounded-xl hover:bg-[#252B5B]">Simpan Transaksi</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Modal Tambah Followup -->
        <div x-show="fuModal" x-cloak class="fixed inset-0 bg-[#111536]/40 backdrop-blur-xs flex items-center justify-center z-50 p-4" @click.self="fuModal = false">
            <div class="bg-white rounded-[24px] border border-[#E1E3EC] shadow-bmss p-6 max-w-md w-full">
                <h3 class="font-black text-lg text-[#252B5B]">Buat Jadwal Follow-up</h3>
                <form action="{{ route('followups.store') }}" method="POST" class="mt-4 space-y-3.5 text-xs">
                    @csrf
                    <input type="hidden" name="contact_id" value="{{ $contact->id }}">
                    <div>
                        <label class="block font-bold text-gray-700 mb-1">Alasan *</label>
                        <select name="reason" class="w-full h-10 px-3 bg-white border border-[#E1E3EC] rounded-xl text-xs">
                            <option value="Repeat Donation">Repeat Donation</option>
                            <option value="Program Update">Program Update</option>
                            <option value="Zakat">Reminder Zakat</option>
                            <option value="Retention">Retention Donatur</option>
                            <option value="Relationship">Silaturahmi</option>
                        </select>
                    </div>
                    <div>
                        <label class="block font-bold text-gray-700 mb-1">Judul / Pengingat *</label>
                        <input type="text" name="title" required placeholder="Contoh: Sapa via WA dan tanyakan kabar" class="w-full h-10 px-3 bg-white border border-[#E1E3EC] rounded-xl text-xs">
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block font-bold text-gray-700 mb-1">Prioritas</label>
                            <select name="priority" class="w-full h-10 px-3 bg-white border border-[#E1E3EC] rounded-xl text-xs">
                                <option value="Normal">Normal</option>
                                <option value="Tinggi">Tinggi</option>
                                <option value="Rendah">Rendah</option>
                            </select>
                        </div>
                        <div>
                            <label class="block font-bold text-gray-700 mb-1">Waktu *</label>
                            <input type="datetime-local" name="scheduled_at" required class="w-full h-10 px-3 bg-white border border-[#E1E3EC] rounded-xl text-xs">
                        </div>
                    </div>
                    <div class="flex items-center justify-end gap-2 pt-2">
                        <button type="button" @click="fuModal = false" class="px-4 py-2 bg-gray-100 text-gray-600 font-bold rounded-xl">Batal</button>
                        <button type="submit" class="px-4 py-2 bg-[#343A72] text-white font-bold rounded-xl hover:bg-[#252B5B]">Jadwalkan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
