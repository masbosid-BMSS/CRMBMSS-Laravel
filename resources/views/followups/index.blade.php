<x-app-layout>
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6" x-data="{ fuModal: false }">
        <div>
            <h1 class="text-2xl md:text-3xl font-extrabold text-[#252B5B] tracking-tight">
                {{ $isMaster ? 'Follow-up Semua CS' : 'Follow-up Saya' }}
            </h1>
            <p class="text-xs md:text-sm text-gray-500 mt-1">
                Jadwalkan komunikasi, pengingat zakat, dan update program dengan teratur.
            </p>
        </div>
        <div>
            <button type="button" @click="fuModal = true" class="h-11 px-5 rounded-xl bg-[#343A72] text-white text-xs font-extrabold flex items-center gap-2 hover:bg-[#252B5B] transition shadow-sm">
                <span>＋</span>
                <span>Buat Follow-up</span>
            </button>
        </div>

        <!-- Modal Tambah Followup -->
        <div x-show="fuModal" x-cloak class="fixed inset-0 bg-[#111536]/40 backdrop-blur-xs flex items-center justify-center z-50 p-4" @click.self="fuModal = false">
            <div class="bg-white rounded-[24px] border border-[#E1E3EC] shadow-bmss p-6 max-w-md w-full">
                <h3 class="font-black text-lg text-[#252B5B]">Tambah Follow-up</h3>
                <form action="{{ route('followups.store') }}" method="POST" class="mt-4 space-y-3.5 text-xs">
                    @csrf
                    <div>
                        <label class="block font-bold text-gray-700 mb-1">Kontak Donatur *</label>
                        <select name="contact_id" required class="w-full h-10 px-3 bg-white border border-[#E1E3EC] rounded-xl text-xs font-medium">
                            <option value="">-- Pilih Kontak --</option>
                            @foreach($contacts as $c)
                                <option value="{{ $c->id }}">{{ $c->name }} ({{ $c->niss }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block font-bold text-gray-700 mb-1">Alasan Tindak Lanjut *</label>
                        <select name="reason" class="w-full h-10 px-3 bg-white border border-[#E1E3EC] rounded-xl text-xs font-semibold">
                            <option value="Zakat">Reminder Zakat</option>
                            <option value="Program Update">Update Program / Penyaluran</option>
                            <option value="Repeat Donation">Repeat Donation</option>
                            <option value="Retention">Retention Donatur Pasif</option>
                            <option value="Relationship">Silaturahmi / Sapaan</option>
                            <option value="Kontak Baru">Kontak Baru Masuk</option>
                        </select>
                    </div>
                    <div>
                        <label class="block font-bold text-gray-700 mb-1">Judul / Catatan Pesan *</label>
                        <input type="text" name="title" required placeholder="Contoh: Kirimkan foto pembangunan sumur #58" class="w-full h-10 px-3 bg-white border border-[#E1E3EC] rounded-xl text-xs">
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
                            <label class="block font-bold text-gray-700 mb-1">Jadwal *</label>
                            <input type="datetime-local" name="scheduled_at" required value="{{ now()->addHours(2)->format('Y-m-d\TH:i') }}" class="w-full h-10 px-3 bg-white border border-[#E1E3EC] rounded-xl text-xs">
                        </div>
                    </div>
                    <div class="flex items-center justify-end gap-2 pt-2">
                        <button type="button" @click="fuModal = false" class="px-4 py-2 bg-gray-100 text-gray-600 font-bold rounded-xl">Batal</button>
                        <button type="submit" class="px-4 py-2 bg-[#343A72] text-white font-bold rounded-xl hover:bg-[#252B5B]">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- 4 KPI Cards -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3.5 mb-6">
        <div class="bg-white border border-[#E1E3EC] rounded-[20px] p-5 shadow-bm-sm">
            <div class="text-[11px] font-extrabold text-gray-400 mb-2">OVERDUE (TERLAMBAT)</div>
            <div class="text-3xl font-extrabold text-[#FF303B]">{{ $overdueCount }}</div>
            <div class="text-[11px] text-red-500 font-semibold mt-2">Segera selesaikan</div>
        </div>

        <div class="bg-white border border-[#E1E3EC] rounded-[20px] p-5 shadow-bm-sm">
            <div class="text-[11px] font-extrabold text-gray-400 mb-2">HARI INI</div>
            <div class="text-3xl font-extrabold text-[#252B5B]">{{ $todayCount }}</div>
            <div class="text-[11px] text-gray-400 mt-2">Jadwal operasional</div>
        </div>

        <div class="bg-white border border-[#E1E3EC] rounded-[20px] p-5 shadow-bm-sm">
            <div class="text-[11px] font-extrabold text-gray-400 mb-2">TERJADWAL</div>
            <div class="text-3xl font-extrabold text-[#C98600]">{{ $scheduledCount }}</div>
            <div class="text-[11px] text-gray-400 mt-2">Mendatang</div>
        </div>

        <div class="bg-white border border-[#E1E3EC] rounded-[20px] p-5 shadow-bm-sm">
            <div class="text-[11px] font-extrabold text-gray-400 mb-2">TOTAL AKTIF</div>
            <div class="text-3xl font-extrabold text-[#1F9D68]">{{ $activeList->count() }}</div>
            <div class="text-[11px] text-gray-400 mt-2">{{ $isMaster ? 'Semua CS' : 'Portfolio saya' }}</div>
        </div>
    </div>

    <!-- Followup List -->
    <div class="bg-white border border-[#E1E3EC] rounded-[24px] p-6 shadow-bm-sm">
        <h3 class="font-extrabold text-base text-[#252B5B] mb-4">Daftar Follow-up Aktif</h3>

        <div class="space-y-3 text-xs">
            @forelse($activeList as $f)
            <div class="flex flex-col sm:flex-row sm:items-center justify-between p-4 rounded-2xl bg-[#FCFCFF] border border-[#ECEEF5] gap-3">
                <div class="flex items-start gap-3">
                    <div class="w-10 h-10 rounded-full bg-white border border-[#E1E3EC] text-[#343A72] font-black text-xs grid place-items-center flex-shrink-0">
                        {{ $f->contact?->initials ?? '?' }}
                    </div>
                    <div>
                        <div class="flex items-center gap-2">
                            <a href="{{ route('contacts.show', $f->contact) }}" class="font-bold text-sm text-[#252B5B] hover:text-[#343A72]">
                                {{ $f->contact?->name ?? 'Kontak' }}
                            </a>
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold {{ $f->status === 'Overdue' ? 'bg-red-100 text-red-700' : ($f->status === 'Today' ? 'bg-amber-100 text-amber-800' : 'bg-gray-100 text-gray-600') }}">
                                {{ $f->status }}
                            </span>
                        </div>
                        <div class="text-gray-500 mt-1 font-medium">
                            <span class="text-gray-400">{{ $f->reason }} ·</span>
                            <span>{{ $f->title }}</span>
                            <span class="text-gray-400">· Owner: <b>{{ $f->owner?->name ?: '-' }}</b></span>
                        </div>
                    </div>
                </div>

                <div class="flex items-center gap-2 self-end sm:self-center">
                    <span class="text-xs font-extrabold {{ $f->status === 'Overdue' ? 'text-red-600' : 'text-gray-500' }}">
                        {{ $f->scheduled_at?->translatedFormat('d M H:i') }}
                    </span>

                    @if($f->contact && $f->contact->relation_status !== 'Blokir')
                        <a href="https://wa.me/{{ preg_replace('/\D/', '', $f->contact->phone) }}" target="_blank" rel="noopener" class="px-3 py-1.5 bg-[#EAF8F1] text-[#166848] font-bold rounded-xl hover:bg-[#caead8]">
                            WA
                        </a>
                    @endif

                    @if(auth()->user()->can('update', $f))
                        <form action="{{ route('followups.complete', $f) }}" method="POST">
                            @csrf
                            <button type="submit" class="px-3.5 py-1.5 bg-[#343A72] text-white font-bold rounded-xl hover:bg-[#252B5B]">
                                Selesai
                            </button>
                        </form>
                    @endif
                </div>
            </div>
            @empty
            <div class="text-center py-12 text-gray-400">Semua follow-up telah selesai dikerjakan! 🎉</div>
            @endforelse
        </div>
    </div>
</x-app-layout>
