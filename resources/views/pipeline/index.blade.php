<x-app-layout>
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6" x-data="{ leadModal: false }">
        <div>
            <h1 class="text-2xl md:text-3xl font-extrabold text-[#252B5B] tracking-tight">Lead & Pipeline</h1>
            <p class="text-xs md:text-sm text-gray-500 mt-1">
                Pantau konversi calon donatur melalui kanban stage dari lead baru hingga penunaian donasi.
            </p>
        </div>
        <div>
            <button type="button" @click="leadModal = true" class="h-11 px-5 rounded-xl bg-[#343A72] text-white text-xs font-extrabold flex items-center gap-2 hover:bg-[#252B5B] transition shadow-sm">
                <span>＋</span>
                <span>Lead Baru</span>
            </button>
        </div>

        <!-- Modal Tambah Lead -->
        <div x-show="leadModal" x-cloak class="fixed inset-0 bg-[#111536]/40 backdrop-blur-xs flex items-center justify-center z-50 p-4" @click.self="leadModal = false">
            <div class="bg-white rounded-[24px] border border-[#E1E3EC] shadow-bmss p-6 max-w-md w-full">
                <h3 class="font-black text-lg text-[#252B5B]">Tambah Lead Baru</h3>
                <form action="{{ route('leads.store') }}" method="POST" class="mt-4 space-y-3.5 text-xs">
                    @csrf
                    <div>
                        <label class="block font-bold text-gray-700 mb-1">Nama Calon Donatur *</label>
                        <input type="text" name="name" required placeholder="Contoh: Bpk. Fajar" class="w-full h-10 px-3 bg-white border border-[#E1E3EC] rounded-xl text-xs">
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block font-bold text-gray-700 mb-1">WhatsApp</label>
                            <input type="text" name="phone" placeholder="08xxxxxxxxxx" class="w-full h-10 px-3 bg-white border border-[#E1E3EC] rounded-xl text-xs">
                        </div>
                        <div>
                            <label class="block font-bold text-gray-700 mb-1">Kota</label>
                            <input type="text" name="city" placeholder="Contoh: Solo" class="w-full h-10 px-3 bg-white border border-[#E1E3EC] rounded-xl text-xs">
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block font-bold text-gray-700 mb-1">Sumber Lead</label>
                            <input type="text" name="source" placeholder="Contoh: Meta Ads" class="w-full h-10 px-3 bg-white border border-[#E1E3EC] rounded-xl text-xs">
                        </div>
                        <div>
                            <label class="block font-bold text-gray-700 mb-1">Minat Program</label>
                            <input type="text" name="interest" placeholder="Contoh: Guru Ngaji" class="w-full h-10 px-3 bg-white border border-[#E1E3EC] rounded-xl text-xs">
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block font-bold text-gray-700 mb-1">Potensi (Rp)</label>
                            <input type="number" name="potential_amount" placeholder="0" class="w-full h-10 px-3 bg-white border border-[#E1E3EC] rounded-xl text-xs">
                        </div>
                        <div>
                            <label class="block font-bold text-gray-700 mb-1">Tahap / Stage</label>
                            <select name="stage" class="w-full h-10 px-3 bg-white border border-[#E1E3EC] rounded-xl text-xs font-semibold">
                                <option value="Lead Baru">Lead Baru</option>
                                <option value="Contacted">Contacted</option>
                                <option value="Interested">Interested</option>
                                <option value="Follow-up">Follow-up</option>
                                <option value="Donasi">Donasi</option>
                            </select>
                        </div>
                    </div>
                    @can('is-master')
                    <div>
                        <label class="block font-bold text-gray-700 mb-1">Owner CS</label>
                        <select name="owner_id" class="w-full h-10 px-3 bg-white border border-[#E1E3EC] rounded-xl text-xs">
                            @foreach($admins as $adm)
                                <option value="{{ $adm->id }}" {{ $adm->id === auth()->id() ? 'selected' : '' }}>{{ $adm->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    @endcan
                    <div class="flex items-center justify-end gap-2 pt-2">
                        <button type="button" @click="leadModal = false" class="px-4 py-2 bg-gray-100 text-gray-600 font-bold rounded-xl">Batal</button>
                        <button type="submit" class="px-4 py-2 bg-[#343A72] text-white font-bold rounded-xl hover:bg-[#252B5B]">Simpan Lead</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Kanban Board -->
    <div class="flex gap-4 overflow-x-auto pb-6 items-start">
        @foreach($stages as $st)
        @php
            $stageLeads = $leads->where('stage', $st);
        @endphp
        <div class="min-w-[280px] w-[280px] bg-[#F2F4FA] border border-[#E1E5F1] rounded-[20px] p-3.5 flex-shrink-0">
            <div class="flex items-center justify-between pb-3 mb-3 border-b border-gray-200/60">
                <b class="text-xs font-extrabold text-[#252B5B]">{{ $st }}</b>
                <span class="w-6 h-6 rounded-full bg-white text-gray-500 font-extrabold text-[11px] grid place-items-center shadow-xs">
                    {{ $stageLeads->count() }}
                </span>
            </div>

            <div class="space-y-2.5">
                @forelse($stageLeads as $l)
                <div class="bg-white border border-[#E1E3EC] rounded-[16px] p-3.5 shadow-bm-sm text-xs hover:border-[#343A72] transition" x-data="{ moveOpen: false }">
                    <div class="flex items-start justify-between gap-2">
                        <b class="text-[#252B5B] text-xs font-bold leading-tight">{{ $l->name }}</b>
                        <span class="text-[10px] text-gray-400 font-semibold">{{ $l->owner?->name ?: '-' }}</span>
                    </div>

                    <div class="text-gray-400 text-[11px] mt-1">
                        {{ $l->source ?: 'Organik' }} · {{ $l->city ?: '-' }}
                    </div>

                    <div class="flex flex-wrap gap-1.5 mt-2.5">
                        @if($l->interest)
                            <span class="px-2 py-0.5 rounded-md bg-gray-100 text-gray-600 text-[10px] font-bold">{{ $l->interest }}</span>
                        @endif
                        @if($l->potential_amount > 0)
                            <span class="px-2 py-0.5 rounded-md bg-[#EEF0F8] text-[#343A72] text-[10px] font-extrabold">
                                Rp {{ number_format($l->potential_amount, 0, ',', '.') }}
                            </span>
                        @endif
                    </div>

                    <!-- Change Stage trigger -->
                    @if(auth()->user()->can('update', $l))
                    <div class="mt-3 pt-2.5 border-t border-gray-100 flex items-center justify-between text-[11px]">
                        <span class="text-gray-400">Pindah stage:</span>
                        <div class="relative">
                            <button type="button" @click="moveOpen = !moveOpen" class="font-bold text-[#343A72] hover:underline">
                                {{ $l->stage }} ▾
                            </button>
                            <div x-show="moveOpen" @click.outside="moveOpen = false" x-cloak class="absolute right-0 bottom-full mb-1 w-36 bg-white border border-gray-200 rounded-xl shadow-lg p-1 z-30">
                                @foreach($stages as $nextStage)
                                    @if($nextStage !== $l->stage)
                                    <form action="{{ route('leads.update-stage', $l) }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="stage" value="{{ $nextStage }}">
                                        <button type="submit" class="w-full text-left px-2.5 py-1.5 rounded-lg text-[11px] font-semibold text-gray-700 hover:bg-gray-100">
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
                <div class="text-center py-8 text-xs text-gray-400">Belum ada lead</div>
                @endforelse
            </div>
        </div>
        @endforeach
    </div>
</x-app-layout>
