<x-app-layout>
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6" x-data="{ cpModal: false }">
        <div>
            <h1 class="text-2xl md:text-3xl font-extrabold text-[#252B5B] tracking-tight">Campaign Fundraising</h1>
            <p class="text-xs md:text-sm text-gray-500 mt-1">
                Campaign bersifat global untuk seluruh CS. Pencapaian diakumulasi dari seluruh transaksi yang masuk.
            </p>
        </div>
        <div>
            <button type="button" @click="cpModal = true" class="h-11 px-5 rounded-xl bg-[#343A72] text-white text-xs font-extrabold flex items-center gap-2 hover:bg-[#252B5B] transition shadow-sm">
                <span>＋</span>
                <span>Buat Campaign Global</span>
            </button>
        </div>

        <!-- Modal Buat Campaign -->
        <div x-show="cpModal" x-cloak class="fixed inset-0 bg-[#111536]/40 backdrop-blur-xs flex items-center justify-center z-50 p-4" @click.self="cpModal = false">
            <div class="bg-white rounded-[24px] border border-[#E1E3EC] shadow-bmss p-6 max-w-md w-full">
                <h3 class="font-black text-lg text-[#252B5B]">Buat Campaign Global</h3>
                <form action="{{ route('campaigns.store') }}" method="POST" class="mt-4 space-y-3.5 text-xs">
                    @csrf
                    <div>
                        <label class="block font-bold text-gray-700 mb-1">Nama Campaign *</label>
                        <input type="text" name="name" required placeholder="Contoh: Sedekah Guru Ngaji" class="w-full h-10 px-3 bg-white border border-[#E1E3EC] rounded-xl text-xs">
                    </div>
                    <div>
                        <label class="block font-bold text-gray-700 mb-1">Program Terkait</label>
                        <select name="program_id" class="w-full h-10 px-3 bg-white border border-[#E1E3EC] rounded-xl text-xs">
                            <option value="">-- Pilih Program --</option>
                            @foreach($programs as $p)
                                <option value="{{ $p->id }}">{{ $p->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block font-bold text-gray-700 mb-1">Target Dana (Rp) *</label>
                            <input type="number" name="target_amount" required min="1000" placeholder="Contoh: 500000000" class="w-full h-10 px-3 bg-white border border-[#E1E3EC] rounded-xl text-xs font-bold text-[#343A72]">
                        </div>
                        <div>
                            <label class="block font-bold text-gray-700 mb-1">Sisa Hari</label>
                            <input type="number" name="days_remaining" value="30" class="w-full h-10 px-3 bg-white border border-[#E1E3EC] rounded-xl text-xs">
                        </div>
                    </div>
                    <div>
                        <label class="block font-bold text-gray-700 mb-1">Status</label>
                        <select name="status" class="w-full h-10 px-3 bg-white border border-[#E1E3EC] rounded-xl text-xs">
                            <option value="Aktif">Aktif</option>
                            <option value="Selesai">Selesai</option>
                            <option value="Arsip">Arsip</option>
                        </select>
                    </div>
                    <div>
                        <label class="block font-bold text-gray-700 mb-1">Deskripsi Campaign</label>
                        <textarea name="description" rows="2" placeholder="Tujuan campaign..." class="w-full p-2.5 bg-white border border-[#E1E3EC] rounded-xl text-xs"></textarea>
                    </div>
                    <div class="flex items-center justify-end gap-2 pt-2">
                        <button type="button" @click="cpModal = false" class="px-4 py-2 bg-gray-100 text-gray-600 font-bold rounded-xl">Batal</button>
                        <button type="submit" class="px-4 py-2 bg-[#343A72] text-white font-bold rounded-xl hover:bg-[#252B5B]">Simpan Campaign</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Campaign Cards Grid -->
    <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-5">
        @forelse($campaigns as $camp)
        <div class="bg-white border border-[#E1E3EC] rounded-[24px] p-6 shadow-bm-sm flex flex-col justify-between">
            <div>
                <div class="flex items-start justify-between gap-2 mb-2">
                    <span class="inline-flex items-center gap-1 text-[10px] font-extrabold px-2.5 py-1 rounded-full bg-[#EEF0F8] text-[#343A72]">
                        ◎ Global (Semua CS)
                    </span>
                    <span class="text-xs text-gray-400 font-medium">{{ $camp->days_remaining }} hari lagi</span>
                </div>
                <h3 class="font-extrabold text-base text-[#252B5B] leading-tight mb-1">{{ $camp->name }}</h3>
                <div class="text-xs text-gray-400 mb-4">{{ $camp->program?->name ?: 'Umum' }}</div>

                <div class="text-xl font-black text-[#252B5B]">
                    Rp {{ number_format($camp->achieved_amount, 0, ',', '.') }}
                    <span class="text-xs text-gray-400 font-normal">/ Rp {{ number_format($camp->target_amount, 0, ',', '.') }}</span>
                </div>

                <div class="h-2.5 bg-gray-100 rounded-full overflow-hidden mt-3">
                    <div class="h-full bg-gradient-to-r from-[#343A72] to-[#5b63a4] rounded-full" style="width: {{ $camp->progress_percent }}%"></div>
                </div>

                <div class="grid grid-cols-2 gap-3 text-xs mt-5 pt-4 border-t border-gray-100">
                    <div>
                        <span class="text-gray-400 block text-[10px] uppercase font-bold">Total Donatur</span>
                        <b class="text-gray-800 text-sm">{{ number_format($camp->donors_count, 0, ',', '.') }}</b>
                    </div>
                    <div>
                        <span class="text-gray-400 block text-[10px] uppercase font-bold">Pencapaian</span>
                        <b class="text-[#1F9D68] text-sm">{{ $camp->progress_percent }}%</b>
                    </div>
                </div>
            </div>

            @if($camp->description)
            <div class="mt-4 p-3 rounded-xl bg-gray-50 text-[11px] text-gray-500 leading-relaxed">
                {{ $camp->description }}
            </div>
            @endif
        </div>
        @empty
        <div class="col-span-3 text-center py-16 bg-white border border-[#E1E3EC] rounded-[24px] text-gray-400 text-xs">
            Belum ada campaign aktif.
        </div>
        @endforelse
    </div>
</x-app-layout>
