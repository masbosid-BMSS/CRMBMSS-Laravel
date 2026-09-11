<x-app-layout>
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6" x-data="{ prModal: false }">
        <div>
            <h1 class="text-2xl md:text-3xl font-extrabold text-[#252B5B] tracking-tight">Program Penyaluran</h1>
            <p class="text-xs md:text-sm text-gray-500 mt-1">
                Katalog program global yang dapat dipilih saat input kontak, kampanye, dan transaksi donatur.
            </p>
        </div>
        @can('is-master')
        <div>
            <button type="button" @click="prModal = true" class="h-11 px-5 rounded-xl bg-[#343A72] text-white text-xs font-extrabold flex items-center gap-2 hover:bg-[#252B5B] transition shadow-sm">
                <span>＋</span>
                <span>Tambah Program Baru</span>
            </button>
        </div>
        @endcan

        <!-- Modal Tambah Program -->
        <div x-show="prModal" x-cloak class="fixed inset-0 bg-[#111536]/40 backdrop-blur-xs flex items-center justify-center z-50 p-4" @click.self="prModal = false">
            <div class="bg-white rounded-[24px] border border-[#E1E3EC] shadow-bmss p-6 max-w-md w-full">
                <h3 class="font-black text-lg text-[#252B5B]">Tambah Program Baru</h3>
                <form action="{{ route('programs.store') }}" method="POST" class="mt-4 space-y-3.5 text-xs">
                    @csrf
                    <div>
                        <label class="block font-bold text-gray-700 mb-1">Nama Program *</label>
                        <input type="text" name="name" required placeholder="Contoh: Makan Santri Jumat" class="w-full h-10 px-3 bg-white border border-[#E1E3EC] rounded-xl text-xs">
                    </div>
                    <div>
                        <label class="block font-bold text-gray-700 mb-1">Kategori *</label>
                        <select name="category" required class="w-full h-10 px-3 bg-white border border-[#E1E3EC] rounded-xl text-xs font-semibold">
                            <option value="Sedekah">Sedekah</option>
                            <option value="Zakat">Zakat</option>
                            <option value="Infak">Infak</option>
                            <option value="Wakaf">Wakaf</option>
                            <option value="Sosial">Sosial</option>
                            <option value="Pendidikan">Pendidikan</option>
                            <option value="Kemanusiaan">Kemanusiaan</option>
                        </select>
                    </div>
                    <div>
                        <label class="block font-bold text-gray-700 mb-1">Status</label>
                        <select name="status" class="w-full h-10 px-3 bg-white border border-[#E1E3EC] rounded-xl text-xs">
                            <option value="Aktif">Aktif</option>
                            <option value="Arsip">Arsip</option>
                        </select>
                    </div>
                    <div>
                        <label class="block font-bold text-gray-700 mb-1">Deskripsi Singkat</label>
                        <textarea name="description" rows="2" placeholder="Penjelasan program..." class="w-full p-2.5 bg-white border border-[#E1E3EC] rounded-xl text-xs"></textarea>
                    </div>
                    <div class="flex items-center justify-end gap-2 pt-2">
                        <button type="button" @click="prModal = false" class="px-4 py-2 bg-gray-100 text-gray-600 font-bold rounded-xl">Batal</button>
                        <button type="submit" class="px-4 py-2 bg-[#343A72] text-white font-bold rounded-xl hover:bg-[#252B5B]">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @if(! $isMaster)
    <div class="mb-5 p-4 rounded-2xl bg-[#FAFBFE] border border-[#DCE1F1] text-xs text-gray-600 flex items-center gap-2">
        <span>👁</span>
        <span>Program bersifat global untuk seluruh tim. Penambahan atau pengubahan program dikelola langsung oleh Master Admin.</span>
    </div>
    @endif

    <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-5">
        @forelse($programs as $pr)
        <div class="bg-white border border-[#E1E3EC] rounded-[24px] p-6 shadow-bm-sm flex flex-col justify-between">
            <div>
                <div class="flex items-start justify-between gap-2 mb-2">
                    <span class="inline-flex items-center text-[10px] font-extrabold px-2.5 py-1 rounded-full
                        {{ $pr->category === 'Zakat' ? 'bg-[#FFF7D8] text-[#8b6500]' : ($pr->category === 'Sedekah' ? 'bg-[#FFF0F2] text-[#FF303B]' : 'bg-[#EEF0F8] text-[#343A72]') }}">
                        {{ $pr->category }}
                    </span>
                    <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold {{ $pr->status === 'Aktif' ? 's-active' : 's-gray' }}">
                        {{ $pr->status }}
                    </span>
                </div>

                <h3 class="font-extrabold text-base text-[#252B5B] mb-2">{{ $pr->name }}</h3>
                <p class="text-xs text-gray-500 leading-relaxed min-h-[36px]">
                    {{ $pr->description ?: 'Belum ada deskripsi khusus.' }}
                </p>
            </div>

            <div class="mt-5 pt-4 border-t border-gray-100 flex items-center justify-between text-xs text-gray-400">
                <span>Campaign terkait: <b>{{ $pr->campaigns_count }}</b></span>
                @can('is-master')
                <span class="text-gray-300">Editable</span>
                @endcan
            </div>
        </div>
        @empty
        <div class="col-span-3 text-center py-16 bg-white border border-[#E1E3EC] rounded-[24px] text-gray-400 text-xs">
            Belum ada program.
        </div>
        @endforelse
    </div>
</x-app-layout>
