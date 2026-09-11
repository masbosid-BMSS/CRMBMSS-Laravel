<x-app-layout>
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6" x-data="{ userModal: false }">
        <div>
            <h1 class="text-2xl md:text-3xl font-extrabold text-[#252B5B] tracking-tight">Admin & User</h1>
            <p class="text-xs md:text-sm text-gray-500 mt-1">
                Hanya Master Admin yang dapat mendaftarkan akun, reset password, dan melakukan transfer ownership database.
            </p>
        </div>
        <div>
            <button type="button" @click="userModal = true" class="h-11 px-5 rounded-xl bg-[#343A72] text-white text-xs font-extrabold flex items-center gap-2 hover:bg-[#252B5B] transition shadow-sm">
                <span>＋</span>
                <span>Tambah Admin / CS</span>
            </button>
        </div>

        <!-- Modal Tambah User -->
        <div x-show="userModal" x-cloak class="fixed inset-0 bg-[#111536]/40 backdrop-blur-xs flex items-center justify-center z-50 p-4" @click.self="userModal = false">
            <div class="bg-white rounded-[24px] border border-[#E1E3EC] shadow-bmss p-6 max-w-md w-full">
                <h3 class="font-black text-lg text-[#252B5B]">Tambah Admin Baru</h3>
                <p class="text-xs text-gray-400 mb-4">Akun otomatis dibuatkan 5 slot nomor WhatsApp dan target KPI default.</p>
                <form action="{{ route('users.store') }}" method="POST" class="space-y-3.5 text-xs">
                    @csrf
                    <div>
                        <label class="block font-bold text-gray-700 mb-1">Nama Lengkap *</label>
                        <input type="text" name="name" required placeholder="Contoh: Dina" class="w-full h-10 px-3 bg-white border border-[#E1E3EC] rounded-xl text-xs">
                    </div>
                    <div>
                        <label class="block font-bold text-gray-700 mb-1">Username *</label>
                        <input type="text" name="username" required placeholder="Contoh: dina.cs" class="w-full h-10 px-3 bg-white border border-[#E1E3EC] rounded-xl text-xs">
                    </div>
                    <div>
                        <label class="block font-bold text-gray-700 mb-1">Password Sementara *</label>
                        <input type="password" name="password" required minlength="8" placeholder="Minimal 8 karakter" class="w-full h-10 px-3 bg-white border border-[#E1E3EC] rounded-xl text-xs">
                    </div>
                    <div class="flex items-center justify-end gap-2 pt-2">
                        <button type="button" @click="userModal = false" class="px-4 py-2 bg-gray-100 text-gray-600 font-bold rounded-xl">Batal</button>
                        <button type="submit" class="px-4 py-2 bg-[#343A72] text-white font-bold rounded-xl hover:bg-[#252B5B]">Buat Akun</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Table Users -->
    <div class="bg-white border border-[#E1E3EC] rounded-[24px] shadow-bm-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="bg-[#FBFBFE] text-gray-400 font-extrabold uppercase text-[10px] tracking-wider border-b border-[#E1E3EC]">
                        <th class="py-3.5 px-4">Nama Lengkap</th>
                        <th class="py-3.5 px-3">Username</th>
                        <th class="py-3.5 px-3">Role</th>
                        <th class="py-3.5 px-3">Status</th>
                        <th class="py-3.5 px-3">Database Kontak</th>
                        <th class="py-3.5 px-3">Login Terakhir</th>
                        <th class="py-3.5 px-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($users as $u)
                    <tr class="hover:bg-gray-50/50 transition" x-data="{ transferModal: false }">
                        <td class="py-3.5 px-4 font-bold text-[#252B5B]">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-full bg-[#EEF0F8] text-[#343A72] font-black text-xs grid place-items-center">
                                    {{ $u->initials }}
                                </div>
                                <span>{{ $u->name }}</span>
                            </div>
                        </td>
                        <td class="py-3.5 px-3 font-mono">{{ $u->username }}</td>
                        <td class="py-3.5 px-3">
                            <span class="px-2.5 py-1 rounded-full text-[10px] font-extrabold {{ $u->isMaster() ? 's-master' : 's-loyal' }}">
                                {{ $u->isMaster() ? 'Master Admin' : 'Admin / CS' }}
                            </span>
                        </td>
                        <td class="py-3.5 px-3">
                            <span class="px-2.5 py-1 rounded-full text-[10px] font-extrabold s-active">Aktif</span>
                        </td>
                        <td class="py-3.5 px-3 font-semibold text-gray-700">
                            {{ $u->contacts()->count() }} kontak
                        </td>
                        <td class="py-3.5 px-3 text-gray-400">{{ $u->last_login_at?->diffForHumans() ?: '-' }}</td>
                        <td class="py-3.5 px-3 text-right">
                            <div class="inline-flex items-center gap-1.5">
                                <form action="{{ route('users.reset-password', $u) }}" method="POST" onsubmit="return confirm('Reset password {{ $u->name }} menjadi admin12345?')">
                                    @csrf
                                    <button type="submit" class="px-2.5 py-1 bg-white border border-[#E1E3EC] text-[#343A72] rounded-lg text-xs font-extrabold hover:bg-gray-50">
                                        Reset Password
                                    </button>
                                </form>

                                @if(! $u->isMaster())
                                <button type="button" @click="transferModal = true" class="px-2.5 py-1 bg-[#FFF7D8] text-[#8b6500] rounded-lg text-xs font-extrabold hover:bg-amber-100">
                                    Transfer Data
                                </button>

                                <!-- Transfer Modal -->
                                <div x-show="transferModal" x-cloak class="fixed inset-0 bg-[#111536]/40 backdrop-blur-xs flex items-center justify-center z-50 p-4" @click.self="transferModal = false">
                                    <div class="bg-white rounded-[24px] border border-[#E1E3EC] shadow-bmss p-6 max-w-sm w-full text-left">
                                        <h3 class="font-black text-base text-[#252B5B]">Transfer Seluruh Data</h3>
                                        <p class="text-xs text-gray-500 mb-3">Pindahkan seluruh kontak, transaksi, dan follow-up milik <b>{{ $u->name }}</b> ke CS lain.</p>
                                        <form action="{{ route('users.transfer', $u) }}" method="POST" class="space-y-3 text-xs">
                                            @csrf
                                            <div>
                                                <label class="block font-bold text-gray-700 mb-1">Transfer ke CS Tujuan *</label>
                                                <select name="target_user_id" required class="w-full h-10 px-3 bg-white border border-[#E1E3EC] rounded-xl text-xs">
                                                    @foreach($users->where('id', '!=', $u->id)->where('role', 'admin') as $other)
                                                        <option value="{{ $other->id }}">{{ $other->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="flex items-center justify-end gap-2 pt-2">
                                                <button type="button" @click="transferModal = false" class="px-4 py-2 bg-gray-100 text-gray-600 font-bold rounded-xl">Batal</button>
                                                <button type="submit" class="px-4 py-2 bg-[#FF303B] text-white font-bold rounded-xl hover:bg-red-600">Transfer</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>
