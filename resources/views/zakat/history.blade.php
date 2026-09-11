<x-app-layout>
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl md:text-3xl font-extrabold text-[#252B5B] tracking-tight">Riwayat Perhitungan Zakat</h1>
            <p class="text-xs md:text-sm text-gray-500 mt-1">
                Seluruh catatan kalkulasi zakat donatur tersimpan dan terhubung langsung ke profil muzakki.
            </p>
        </div>
        <div>
            <a href="{{ route('zakat.calculator') }}" class="h-11 px-5 rounded-xl bg-[#343A72] text-white text-xs font-extrabold flex items-center gap-2 hover:bg-[#252B5B] transition shadow-sm">
                <span>＋</span>
                <span>Hitung Zakat Baru</span>
            </a>
        </div>
    </div>

    <!-- Toolbar Filters -->
    <div class="bg-white border border-[#E1E3EC] rounded-2xl p-4 mb-5 shadow-bm-sm">
        <form action="{{ route('zakat.history') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-3 gap-3 text-xs">
            <div>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari no hitung, nama muzakki..." class="w-full h-10 px-3 bg-[#FBFBFE] border border-[#E1E3EC] rounded-xl text-xs focus:border-[#343A72] focus:ring-0">
            </div>
            <div>
                <select name="owner" onchange="this.form.submit()" class="w-full h-10 px-3 bg-white border border-[#E1E3EC] rounded-xl text-xs">
                    <option value="all">Semua Admin/CS</option>
                    @if(!auth()->user()->isMaster())
                        <option value="mine" {{ request('owner') === 'mine' ? 'selected' : '' }}>Milik Saya</option>
                    @endif
                    @foreach($admins as $adm)
                        <option value="{{ $adm->id }}" {{ request('owner') === $adm->id ? 'selected' : '' }}>{{ $adm->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex items-center gap-2">
                <button type="submit" class="w-full h-10 bg-[#343A72] text-white font-bold rounded-xl hover:bg-[#252B5B] transition">
                    Filter
                </button>
                @if(request()->hasAny(['search', 'owner']))
                <a href="{{ route('zakat.history') }}" class="h-10 px-3 bg-gray-100 text-gray-600 font-bold rounded-xl hover:bg-gray-200 transition flex items-center justify-center">
                    Reset
                </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Table Calculations -->
    <div class="bg-white border border-[#E1E3EC] rounded-[24px] shadow-bm-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="bg-[#FBFBFE] text-gray-400 font-extrabold uppercase text-[10px] tracking-wider border-b border-[#E1E3EC]">
                        <th class="py-3.5 px-4">No. Hitung</th>
                        <th class="py-3.5 px-3">Muzakki</th>
                        <th class="py-3.5 px-3">CS Owner</th>
                        <th class="py-3.5 px-3">Jenis Zakat</th>
                        <th class="py-3.5 px-3">Harta Bersih</th>
                        <th class="py-3.5 px-3">Kewajiban Zakat</th>
                        <th class="py-3.5 px-3">Terbayar</th>
                        <th class="py-3.5 px-3">Status</th>
                        <th class="py-3.5 px-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($calculations as $calc)
                    <tr class="hover:bg-gray-50/50 transition">
                        <td class="py-3.5 px-4 font-mono font-bold text-[#343A72]">
                            <a href="{{ route('zakat.show', $calc) }}" class="hover:underline">{{ $calc->ref_no }}</a>
                        </td>
                        <td class="py-3.5 px-3">
                            <a href="{{ route('contacts.show', $calc->contact) }}" class="font-bold text-[#252B5B] hover:text-[#343A72]">
                                {{ $calc->contact?->name ?: '-' }}
                            </a>
                        </td>
                        <td class="py-3.5 px-3 font-medium">{{ $calc->owner?->name ?: '-' }}</td>
                        <td class="py-3.5 px-3">{{ $calc->type }}</td>
                        <td class="py-3.5 px-3">Rp {{ number_format($calc->net_amount, 0, ',', '.') }}</td>
                        <td class="py-3.5 px-3 font-extrabold text-[#343A72]">Rp {{ number_format($calc->zakat_amount, 0, ',', '.') }}</td>
                        <td class="py-3.5 px-3 font-bold text-emerald-600">Rp {{ number_format($calc->paid_amount, 0, ',', '.') }}</td>
                        <td class="py-3.5 px-3">
                            <span class="px-2.5 py-1 rounded-full text-[10px] font-extrabold
                                {{ $calc->status === 'Lunas' ? 's-active' : ($calc->status === 'Outstanding' ? 's-red' : ($calc->status === 'Sebagian' ? 's-risk' : 's-gray')) }}">
                                {{ $calc->status }}
                            </span>
                        </td>
                        <td class="py-3.5 px-3 text-right">
                            <div class="inline-flex items-center gap-1.5">
                                <a href="{{ route('zakat.show', $calc) }}" class="px-2.5 py-1 bg-white border border-[#E1E3EC] text-[#343A72] rounded-lg text-xs font-extrabold hover:bg-gray-50">
                                    Detail
                                </a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="text-center py-12 text-gray-400">
                            Belum ada riwayat perhitungan zakat.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-4 border-t border-gray-100">
            {{ $calculations->links() }}
        </div>
    </div>
</x-app-layout>
