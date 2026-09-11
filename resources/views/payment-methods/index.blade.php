<x-app-layout>
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6" x-data="{ pmModal: false }">
        <div>
            <h1 class="text-2xl md:text-3xl font-extrabold text-[#252B5B] tracking-tight">Metode Pembayaran</h1>
            <p class="text-xs md:text-sm text-gray-500 mt-1">
                Kelola pilihan metode pembayaran global untuk transaksi dana. Hanya Master Admin yang dapat menambah, mengubah, atau menghapus metode.
            </p>
        </div>
        <div>
            <button type="button" @click="pmModal = true" class="h-11 px-5 rounded-xl bg-[#343A72] text-white text-xs font-extrabold flex items-center gap-2 hover:bg-[#252B5B] transition shadow-sm">
                <span>＋</span>
                <span>Tambah Metode</span>
            </button>
        </div>

        <!-- Modal Tambah Metode -->
        <div x-show="pmModal" x-cloak class="fixed inset-0 bg-[#111536]/40 backdrop-blur-xs flex items-center justify-center z-50 p-4" @click.self="pmModal = false">
            <div class="bg-white rounded-[24px] border border-[#E1E3EC] shadow-bmss p-6 max-w-md w-full">
                <h3 class="font-black text-lg text-[#252B5B]">Tambah Metode Pembayaran</h3>
                <form action="{{ route('payment-methods.store') }}" method="POST" class="mt-4 space-y-3.5 text-xs">
                    @csrf
                    <div>
                        <label class="block font-bold text-gray-700 mb-1">Nama Metode *</label>
                        <input type="text" name="name" required placeholder="Contoh: BSI Transfer" class="w-full h-10 px-3 bg-white border border-[#E1E3EC] rounded-xl text-xs">
                    </div>
                    <div>
                        <label class="block font-bold text-gray-700 mb-1">Kategori *</label>
                        <select name="category" required class="w-full h-10 px-3 bg-white border border-[#E1E3EC] rounded-xl text-xs font-semibold">
                            <option value="Bank Transfer">Bank Transfer</option>
                            <option value="QRIS">QRIS</option>
                            <option value="Tunai">Tunai / Kas</option>
                            <option value="E-Wallet">E-Wallet</option>
                            <option value="Virtual Account">Virtual Account</option>
                            <option value="Lainnya">Lainnya</option>
                        </select>
                    </div>
                    <div>
                        <label class="block font-bold text-gray-700 mb-1">Detail / Keterangan Rekening</label>
                        <input type="text" name="detail" placeholder="Contoh: No Rek 123456789 a.n BMSS" class="w-full h-10 px-3 bg-white border border-[#E1E3EC] rounded-xl text-xs">
                    </div>
                    <div>
                        <label class="block font-bold text-gray-700 mb-1">Status</label>
                        <select name="is_active" class="w-full h-10 px-3 bg-white border border-[#E1E3EC] rounded-xl text-xs">
                            <option value="1">Aktif</option>
                            <option value="0">Nonaktif</option>
                        </select>
                    </div>
                    <div class="flex items-center justify-end gap-2 pt-2">
                        <button type="button" @click="pmModal = false" class="px-4 py-2 bg-gray-100 text-gray-600 font-bold rounded-xl">Batal</button>
                        <button type="submit" class="px-4 py-2 bg-[#343A72] text-white font-bold rounded-xl hover:bg-[#252B5B]">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- 4 KPI Cards -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3.5 mb-6">
        <div class="bg-white border border-[#E1E3EC] rounded-[20px] p-5 shadow-bm-sm">
            <div class="text-[11px] font-extrabold text-gray-400 mb-2">METODE AKTIF</div>
            <div class="text-3xl font-extrabold text-[#252B5B]">{{ $activeCount }}</div>
            <div class="text-[11px] text-gray-400 mt-2">Muncul di form transaksi</div>
        </div>

        <div class="bg-white border border-[#E1E3EC] rounded-[20px] p-5 shadow-bm-sm">
            <div class="text-[11px] font-extrabold text-gray-400 mb-2">TOTAL METODE</div>
            <div class="text-3xl font-extrabold text-[#C98600]">{{ $methods->count() }}</div>
            <div class="text-[11px] text-gray-400 mt-2">Aktif + Nonaktif</div>
        </div>

        <div class="bg-white border border-[#E1E3EC] rounded-[20px] p-5 shadow-bm-sm">
            <div class="text-[11px] font-extrabold text-gray-400 mb-2">TERPAKAI DI TRANSAKSI</div>
            <div class="text-3xl font-extrabold text-[#1F9D68]">{{ $usedCount }}</div>
            <div class="text-[11px] text-gray-400 mt-2">Termasuk historis</div>
        </div>

        <div class="bg-white border border-[#E1E3EC] rounded-[20px] p-5 shadow-bm-sm">
            <div class="text-[11px] font-extrabold text-gray-400 mb-2">HAK KELOLA</div>
            <div class="text-2xl font-extrabold text-[#FF303B]">MASTER ADMIN</div>
            <div class="text-[11px] text-gray-400 mt-2">CS hanya memilih</div>
        </div>
    </div>

    <!-- Payment Methods Cards Grid -->
    <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-5">
        @foreach($methods as $pm)
        <div class="bg-white border border-[#E1E3EC] rounded-[24px] p-6 shadow-bm-sm flex flex-col justify-between {{ ! $pm->is_active ? 'opacity-65' : '' }}">
            <div>
                <div class="flex items-center justify-between gap-2 mb-3">
                    <span class="w-10 h-10 rounded-xl bg-[#EEF0F8] text-[#343A72] text-lg grid place-items-center">
                        @if(str_contains(strtolower($pm->category), 'bank'))
                            🏦
                        @elseif(str_contains(strtolower($pm->category), 'qris'))
                            ▦
                        @elseif(str_contains(strtolower($pm->category), 'tunai'))
                            💵
                        @else
                            ◈
                        @endif
                    </span>
                    <span class="px-2.5 py-1 rounded-full text-[10px] font-extrabold {{ $pm->is_active ? 's-active' : 's-gray' }}">
                        {{ $pm->is_active ? 'Aktif' : 'Nonaktif' }}
                    </span>
                </div>

                <h3 class="font-extrabold text-base text-[#252B5B] mb-1">{{ $pm->name }}</h3>
                <div class="text-xs text-gray-400 font-medium mb-3">{{ $pm->category }}</div>
                <div class="p-3 rounded-xl bg-gray-50 text-xs text-gray-600 min-h-[46px]">
                    {{ $pm->detail ?: 'Tanpa nomor rekening atau detail tambahan.' }}
                </div>
            </div>

            <div class="flex items-center gap-2 pt-4 border-t border-gray-100 mt-5">
                <form action="{{ route('payment-methods.toggle', $pm) }}" method="POST" class="flex-1">
                    @csrf
                    <button type="submit" class="w-full py-2 rounded-xl text-xs font-bold border border-gray-200 hover:bg-gray-50">
                        {{ $pm->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                    </button>
                </form>
                <form action="{{ route('payment-methods.destroy', $pm) }}" method="POST" onsubmit="return confirm('Hapus metode pembayaran ini?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="px-3 py-2 rounded-xl text-xs font-bold text-red-600 hover:bg-red-50">
                        Hapus
                    </button>
                </form>
            </div>
        </div>
        @endforeach
    </div>
</x-app-layout>
