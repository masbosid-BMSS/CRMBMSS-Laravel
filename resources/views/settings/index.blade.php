<x-app-layout>
    <div class="max-w-4xl mx-auto">
        <div class="mb-6">
            <h1 class="text-2xl md:text-3xl font-extrabold text-[#252B5B] tracking-tight">Pengaturan Sistem</h1>
            <p class="text-xs md:text-sm text-gray-500 mt-1">
                Konfigurasi umum lembaga dan operasional CRM BMSS.
            </p>
        </div>

        <div class="grid md:grid-cols-2 gap-6">
            <!-- Profil Organisasi -->
            <div class="bg-white border border-[#E1E3EC] rounded-[24px] p-6 shadow-bm-sm">
                <h3 class="font-black text-base text-[#252B5B] mb-4">Profil Lembaga</h3>
                <form action="{{ route('settings.update') }}" method="POST" class="space-y-4 text-xs">
                    @csrf
                    <div>
                        <label class="block font-bold text-gray-700 mb-1">Nama Organisasi</label>
                        <input type="text" name="org_name" value="{{ $org }}" required class="w-full h-10 px-3 bg-white border border-[#E1E3EC] rounded-xl text-xs">
                    </div>
                    <div>
                        <label class="block font-bold text-gray-700 mb-1">Target Fundraising Bulanan (Rp)</label>
                        <input type="number" name="monthly_target" value="{{ $target }}" required class="w-full h-10 px-3 bg-white border border-[#E1E3EC] rounded-xl text-xs font-bold text-[#343A72]">
                    </div>
                    <div>
                        <label class="block font-bold text-gray-700 mb-1">Email Internal</label>
                        <input type="email" name="org_email" value="{{ $email }}" required class="w-full h-10 px-3 bg-white border border-[#E1E3EC] rounded-xl text-xs">
                    </div>
                    <button type="submit" class="w-full py-2.5 bg-[#343A72] text-white font-extrabold rounded-xl hover:bg-[#252B5B] transition shadow-sm">
                        Simpan Pengaturan
                    </button>
                </form>
            </div>

            <!-- Database Management -->
            <div class="bg-white border border-[#E1E3EC] rounded-[24px] p-6 shadow-bm-sm flex flex-col justify-between">
                <div>
                    <h3 class="font-black text-base text-[#252B5B] mb-4">Database Management</h3>
                    <div class="space-y-3 text-xs">
                        <div class="flex items-center justify-between p-3.5 rounded-xl bg-gray-50">
                            <div>
                                <b class="text-gray-800">Total Donatur Aktif</b>
                                <div class="text-gray-400">Tersimpan di database</div>
                            </div>
                            <b class="text-sm text-[#343A72]">{{ number_format($contactsCount, 0, ',', '.') }}</b>
                        </div>
                        <div class="flex items-center justify-between p-3.5 rounded-xl bg-gray-50">
                            <div>
                                <b class="text-gray-800">Export Seluruh Kontak</b>
                                <div class="text-gray-400">Format Microsoft Excel (XLSX)</div>
                            </div>
                            <a href="{{ route('contacts.export-xlsx') }}" class="px-3 py-1.5 bg-white border border-[#E1E3EC] rounded-lg text-xs font-bold text-[#343A72]">
                                Export
                            </a>
                        </div>
                    </div>
                </div>

                <div class="mt-6 p-3.5 rounded-xl bg-[#EEF0F8] text-[11px] text-[#343A72] font-medium leading-relaxed">
                    Database disimpan secara aman di MySQL / MariaDB dengan atomic NISS sequence lock untuk pencegahan duplikasi.
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
