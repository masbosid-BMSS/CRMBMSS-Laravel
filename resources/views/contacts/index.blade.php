<x-app-layout>
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl md:text-3xl font-extrabold text-[#252B5B] tracking-tight">Semua Kontak</h1>
            <p class="text-xs md:text-sm text-gray-500 mt-1">
                Database donatur terpusat. Setiap kontak terasosiasi dengan NISS unik, status relasi, dan PIC CS.
            </p>
        </div>
        <div class="flex flex-wrap items-center gap-2">
            <a href="{{ route('contacts.export-xlsx') }}" class="h-11 px-4 rounded-xl bg-white border border-[#E1E3EC] text-[#343A72] text-xs font-extrabold flex items-center gap-1.5 hover:bg-gray-50 transition">
                <span>⇩</span>
                <span>Export XLSX</span>
            </a>
            <a href="{{ route('contacts.create') }}" class="h-11 px-4 rounded-xl bg-[#343A72] text-white text-xs font-extrabold flex items-center gap-1.5 hover:bg-[#252B5B] transition shadow-sm">
                <span>＋</span>
                <span>Tambah Kontak</span>
            </a>
        </div>
    </div>

    <!-- Toolbar Filters -->
    <div class="bg-white border border-[#E1E3EC] rounded-2xl p-4 mb-5 shadow-bm-sm">
        <form action="{{ route('contacts.index') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-6 gap-3 text-xs">
            <!-- Search -->
            <div class="lg:col-span-2">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama, WA, NISS, kota..." class="w-full h-10 px-3 bg-[#FBFBFE] border border-[#E1E3EC] rounded-xl text-xs focus:border-[#343A72] focus:ring-0">
            </div>

            <!-- Owner -->
            <div>
                <select name="owner" onchange="this.form.submit()" class="w-full h-10 px-3 bg-white border border-[#E1E3EC] rounded-xl text-xs focus:border-[#343A72] focus:ring-0">
                    <option value="all">Semua Admin/CS</option>
                    @if(!auth()->user()->isMaster())
                        <option value="mine" {{ request('owner') === 'mine' ? 'selected' : '' }}>Milik Saya</option>
                    @endif
                    @foreach($admins as $adm)
                        <option value="{{ $adm->id }}" {{ request('owner') === $adm->id ? 'selected' : '' }}>{{ $adm->name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Relation -->
            <div>
                <select name="relation" onchange="this.form.submit()" class="w-full h-10 px-3 bg-white border border-[#E1E3EC] rounded-xl text-xs focus:border-[#343A72] focus:ring-0">
                    <option value="all">Status Relasi</option>
                    <option value="Normal" {{ request('relation') === 'Normal' ? 'selected' : '' }}>Normal</option>
                    <option value="Blokir" {{ request('relation') === 'Blokir' ? 'selected' : '' }}>Blokir</option>
                    <option value="Untrust" {{ request('relation') === 'Untrust' ? 'selected' : '' }}>Untrust</option>
                    <option value="Bosan" {{ request('relation') === 'Bosan' ? 'selected' : '' }}>Bosan</option>
                </select>
            </div>

            <!-- Zakat Status -->
            <div>
                <select name="zakat" onchange="this.form.submit()" class="w-full h-10 px-3 bg-white border border-[#E1E3EC] rounded-xl text-xs focus:border-[#343A72] focus:ring-0">
                    <option value="all">Status Zakat</option>
                    <option value="Belum Dihitung" {{ request('zakat') === 'Belum Dihitung' ? 'selected' : '' }}>Belum Dihitung</option>
                    <option value="Prospek" {{ request('zakat') === 'Prospek' ? 'selected' : '' }}>Prospek</option>
                    <option value="Outstanding" {{ request('zakat') === 'Outstanding' ? 'selected' : '' }}>Outstanding</option>
                    <option value="Sebagian" {{ request('zakat') === 'Sebagian' ? 'selected' : '' }}>Sebagian</option>
                    <option value="Lunas" {{ request('zakat') === 'Lunas' ? 'selected' : '' }}>Lunas</option>
                </select>
            </div>

            <!-- Reset -->
            <div class="flex items-center gap-2">
                <button type="submit" class="w-full h-10 bg-[#343A72] text-white font-bold rounded-xl hover:bg-[#252B5B] transition">
                    Filter
                </button>
                @if(request()->hasAny(['search', 'owner', 'relation', 'zakat', 'access', 'issue']))
                <a href="{{ route('contacts.index') }}" class="h-10 px-3 bg-gray-100 text-gray-600 font-bold rounded-xl hover:bg-gray-200 transition flex items-center justify-center">
                    Reset
                </a>
                @endif
            </div>
        </form>
    </div>

    @if($issue)
    <div class="mb-4 p-3 rounded-2xl bg-amber-50 border border-amber-200 flex items-center justify-between text-xs text-amber-800">
        <div class="flex items-center gap-2">
            <span class="font-extrabold">Filter Issue Aktif:</span>
            <span class="px-2 py-0.5 rounded-full bg-amber-200 text-amber-900 font-bold uppercase text-[10px]">{{ $issue }}</span>
        </div>
        <a href="{{ route('contacts.index') }}" class="text-xs font-bold underline">Hapus Filter Issue</a>
    </div>
    @endif

    <!-- Table Contacts -->
    <div class="bg-white border border-[#E1E3EC] rounded-[24px] shadow-bm-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="bg-[#FBFBFE] text-gray-400 font-extrabold uppercase text-[10px] tracking-wider border-b border-[#E1E3EC]">
                        <th class="py-3.5 px-4">Kontak Donatur</th>
                        <th class="py-3.5 px-3">NISS</th>
                        <th class="py-3.5 px-3">CS Owner</th>
                        <th class="py-3.5 px-3">Status Donor</th>
                        <th class="py-3.5 px-3">Relasi</th>
                        <th class="py-3.5 px-3">Zakat</th>
                        <th class="py-3.5 px-3">Lifetime (LTV)</th>
                        <th class="py-3.5 px-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($contacts as $c)
                    @php
                        $canEdit = auth()->user()->can('update', $c);
                    @endphp
                    <tr class="hover:bg-gray-50/50 transition">
                        <td class="py-3.5 px-4">
                            <a href="{{ route('contacts.show', $c) }}" class="flex items-center gap-3 group">
                                <div class="w-9 h-9 rounded-full bg-[#EEF0F8] text-[#343A72] font-black text-xs grid place-items-center flex-shrink-0 group-hover:scale-105 transition">
                                    {{ $c->initials }}
                                </div>
                                <div>
                                    <b class="block text-xs font-bold text-[#252B5B] group-hover:text-[#343A72]">{{ $c->name }}</b>
                                    <div class="text-[11px] text-gray-400">{{ $c->city ?: '-' }} · {{ $c->phone }}</div>
                                </div>
                            </a>
                        </td>
                        <td class="py-3.5 px-3 font-mono font-bold text-gray-700">{{ $c->niss }}</td>
                        <td class="py-3.5 px-3 font-medium">{{ $c->owner?->name ?: '-' }}</td>
                        <td class="py-3.5 px-3">
                            <span class="px-2.5 py-1 rounded-full text-[10px] font-extrabold
                                {{ $c->status === 'Loyal' ? 's-loyal' : ($c->status === 'Aktif' ? 's-active' : ($c->status === 'At Risk' ? 's-risk' : ($c->status === 'Dormant' ? 's-dorm' : 's-gray'))) }}">
                                {{ $c->status }}
                            </span>
                        </td>
                        <td class="py-3.5 px-3">
                            <span class="px-2.5 py-1 rounded-full text-[10px] font-extrabold
                                {{ $c->relation_status === 'Blokir' ? 's-block' : ($c->relation_status === 'Untrust' ? 's-untrust' : ($c->relation_status === 'Bosan' ? 's-bored' : 's-active')) }}">
                                {{ $c->relation_status }}
                            </span>
                        </td>
                        <td class="py-3.5 px-3">
                            <span class="px-2.5 py-1 rounded-full text-[10px] font-extrabold
                                {{ $c->zakat_status === 'Lunas' ? 's-active' : ($c->zakat_status === 'Outstanding' ? 's-red' : ($c->zakat_status === 'Sebagian' ? 's-risk' : ($c->zakat_status === 'Prospek' ? 's-gold' : 's-gray'))) }}">
                                {{ $c->zakat_status }}
                            </span>
                        </td>
                        <td class="py-3.5 px-3 font-bold text-[#343A72]">
                            Rp {{ number_format($c->ltv, 0, ',', '.') }}
                        </td>
                        <td class="py-3.5 px-3 text-right">
                            <div class="inline-flex items-center gap-1.5">
                                @if($c->relation_status === 'Blokir')
                                    <span class="px-2 py-1 bg-gray-800 text-white rounded-lg text-[10px] font-extrabold cursor-not-allowed" title="Kontak Berstatus Blokir">
                                        🔒 WA
                                    </span>
                                @else
                                    <a href="https://wa.me/{{ preg_replace('/\D/', '', $c->phone) }}" target="_blank" rel="noopener" class="px-2.5 py-1 bg-[#EAF8F1] text-[#166848] rounded-lg text-xs font-extrabold hover:bg-[#caead8] transition">
                                        WA
                                    </a>
                                @endif
                                <a href="{{ route('contacts.show', $c) }}" class="px-2.5 py-1 bg-white border border-[#E1E3EC] text-[#343A72] rounded-lg text-xs font-extrabold hover:bg-gray-50 transition">
                                    Detail
                                </a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center py-12 text-gray-400">
                            Tidak ada kontak yang cocok dengan kriteria pencarian.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-4 border-t border-gray-100">
            {{ $contacts->links() }}
        </div>
    </div>
</x-app-layout>
