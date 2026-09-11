<x-app-layout>
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl md:text-3xl font-extrabold text-[#252B5B] tracking-tight">CS & Nomor WhatsApp</h1>
            <p class="text-xs md:text-sm text-gray-500 mt-1">
                Setiap CS mengelola hingga 5 nomor WhatsApp. Kapasitas kontak per nomor dibatasi sekitar 3.000–5.000 database.
            </p>
        </div>
    </div>

    <!-- 4 KPI Cards -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3.5 mb-6">
        <div class="bg-white border border-[#E1E3EC] rounded-[20px] p-5 shadow-bm-sm">
            <div class="text-[11px] font-extrabold text-gray-400 mb-2">CS AKTIF</div>
            <div class="text-3xl font-extrabold text-[#252B5B]">{{ $admins->count() }}</div>
            <div class="text-[11px] text-gray-400 mt-2">Maks 5 nomor / CS</div>
        </div>

        <div class="bg-white border border-[#E1E3EC] rounded-[20px] p-5 shadow-bm-sm">
            <div class="text-[11px] font-extrabold text-gray-400 mb-2">NOMOR TERDAFTAR</div>
            <div class="text-3xl font-extrabold text-[#C98600]">{{ $registeredNumbers }}</div>
            <div class="text-[11px] text-gray-400 mt-2">Dari {{ $admins->count() * 5 }} total slot</div>
        </div>

        <div class="bg-white border border-[#E1E3EC] rounded-[20px] p-5 shadow-bm-sm">
            <div class="text-[11px] font-extrabold text-gray-400 mb-2">TOTAL DATABASE</div>
            <div class="text-3xl font-extrabold text-[#343A72]">{{ number_format($totalContacts, 0, ',', '.') }}</div>
            <div class="text-[11px] text-gray-400 mt-2">Kontak aktif</div>
        </div>

        <div class="bg-white border border-[#E1E3EC] rounded-[20px] p-5 shadow-bm-sm">
            <div class="text-[11px] font-extrabold text-gray-400 mb-2">TOTAL KAPASITAS</div>
            <div class="text-3xl font-extrabold text-[#1F9D68]">{{ number_format($totalCapacity, 0, ',', '.') }}</div>
            <div class="text-[11px] text-gray-400 mt-2">Batas seluruh nomor</div>
        </div>
    </div>

    <!-- CS List & WA Slots -->
    <div class="space-y-6">
        @foreach($admins as $adm)
        <div class="bg-white border border-[#E1E3EC] rounded-[24px] p-6 shadow-bm-sm">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-4 mb-4 border-b border-gray-100">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-[#EEF0F8] text-[#343A72] font-black text-sm grid place-items-center">
                        {{ $adm->initials }}
                    </div>
                    <div>
                        <h3 class="font-extrabold text-base text-[#252B5B]">{{ $adm->name }}</h3>
                        <div class="text-xs text-gray-400">Username: {{ $adm->username }} · Database kontak: {{ $adm->contacts->count() }}</div>
                    </div>
                </div>
            </div>

            <div class="grid sm:grid-cols-2 lg:grid-cols-5 gap-3.5">
                @foreach($adm->waAccounts as $wa)
                @php
                    $assigned = $wa->assigned_count;
                    $pct = $wa->capacity > 0 ? min(100, round(($assigned / $wa->capacity) * 100)) : 0;
                @endphp
                <div class="p-4 rounded-2xl bg-[#FCFCFF] border border-[#ECEEF5] flex flex-col justify-between" x-data="{ editWa: false }">
                    <div>
                        <div class="flex items-center justify-between gap-1 mb-2">
                            <span class="px-2 py-0.5 rounded-full bg-[#EEF0F8] text-[#343A72] font-extrabold text-[10px]">
                                Slot {{ $wa->slot }}
                            </span>
                            <span class="text-[10px] font-extrabold {{ $wa->status === 'Aktif' ? 'text-emerald-600' : 'text-gray-400' }}">
                                {{ $wa->status }}
                            </span>
                        </div>

                        <b class="block text-xs font-bold text-[#252B5B] mb-0.5">{{ $wa->label }}</b>
                        <div class="text-[11px] text-gray-400 font-mono mb-3">{{ $wa->phone ?: 'Belum diisi' }}</div>

                        <div class="flex justify-between text-[10px] font-bold text-gray-500 mb-1">
                            <span>Database</span>
                            <span>{{ number_format($assigned, 0, ',', '.') }} / {{ number_format($wa->capacity, 0, ',', '.') }}</span>
                        </div>
                        <div class="h-1.5 bg-gray-200 rounded-full overflow-hidden">
                            <div class="h-full bg-[#343A72] rounded-full" style="width: {{ $pct }}%"></div>
                        </div>
                        <div class="text-[10px] text-gray-400 mt-1">Sisa {{ number_format($wa->remaining_capacity, 0, ',', '.') }} slot</div>
                    </div>

                    <button type="button" @click="editWa = true" class="w-full mt-4 py-1.5 bg-white border border-[#E1E3EC] rounded-xl text-[11px] font-extrabold text-[#343A72] hover:bg-gray-50 transition">
                        Atur Slot
                    </button>

                    <!-- Edit WA Slot Modal -->
                    <div x-show="editWa" x-cloak class="fixed inset-0 bg-[#111536]/40 backdrop-blur-xs flex items-center justify-center z-50 p-4" @click.self="editWa = false">
                        <div class="bg-white rounded-[24px] border border-[#E1E3EC] shadow-bmss p-6 max-w-sm w-full text-left">
                            <h3 class="font-black text-base text-[#252B5B]">Atur {{ $adm->name }} · {{ $wa->label }}</h3>
                            <form action="{{ route('whatsapp.update-account', $wa) }}" method="POST" class="mt-4 space-y-3 text-xs">
                                @csrf
                                @method('PUT')
                                <div>
                                    <label class="block font-bold text-gray-700 mb-1">Label</label>
                                    <input type="text" name="label" value="{{ $wa->label }}" required class="w-full h-10 px-3 bg-white border border-[#E1E3EC] rounded-xl text-xs">
                                </div>
                                <div>
                                    <label class="block font-bold text-gray-700 mb-1">Nomor WhatsApp</label>
                                    <input type="text" name="phone" value="{{ $wa->phone }}" placeholder="08xxxxxxxxxx" class="w-full h-10 px-3 bg-white border border-[#E1E3EC] rounded-xl text-xs">
                                </div>
                                <div>
                                    <label class="block font-bold text-gray-700 mb-1">Kapasitas Maksimal Database</label>
                                    <input type="number" name="capacity" min="{{ $assigned }}" value="{{ $wa->capacity }}" required class="w-full h-10 px-3 bg-white border border-[#E1E3EC] rounded-xl text-xs">
                                    <span class="text-[10px] text-gray-400">Minimal {{ $assigned }} (kontak yang sudah terdaftar)</span>
                                </div>
                                <div>
                                    <label class="block font-bold text-gray-700 mb-1">Status</label>
                                    <select name="status" class="w-full h-10 px-3 bg-white border border-[#E1E3EC] rounded-xl text-xs">
                                        <option value="Aktif" {{ $wa->status === 'Aktif' ? 'selected' : '' }}>Aktif</option>
                                        <option value="Nonaktif" {{ $wa->status === 'Nonaktif' ? 'selected' : '' }}>Nonaktif</option>
                                    </select>
                                </div>
                                <div class="flex items-center justify-end gap-2 pt-2">
                                    <button type="button" @click="editWa = false" class="px-4 py-2 bg-gray-100 text-gray-600 font-bold rounded-xl">Batal</button>
                                    <button type="submit" class="px-4 py-2 bg-[#343A72] text-white font-bold rounded-xl hover:bg-[#252B5B]">Simpan</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endforeach
    </div>
</x-app-layout>
