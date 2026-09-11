<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'CRM BMSS' }} — Baitulmaal Sejuta Santri</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        :root {
            --navy: #343A72; --navy2: #252B5B; --navySoft: #EEF0F8;
            --red: #FF303B; --redSoft: #FFF0F2; --gold: #FFC316; --goldSoft: #FFF7D8;
            --green: #1F9D68; --greenSoft: #EAF8F1; --orange: #C98600; --orangeSoft: #FFF5DD;
            --bg: #F8F8FB; --card: #fff; --line: #E1E3EC; --text: #242842; --muted: #697087;
            --side: 260px; --top: 68px; --r: 18px; --sh: 0 12px 30px rgba(52,58,114,.1);
        }
    </style>
</head>
<body class="bg-[#F8F8FB] text-[#242842] antialiased min-h-screen" x-data="{ mobileMenu: false, quickModal: false, notifModal: false }">

    <!-- Sidebar Desktop -->
    <aside class="fixed left-0 top-0 bottom-0 w-[260px] bg-gradient-to-b from-[#343A72] to-[#252B5B] text-white p-[18px_14px] overflow-y-auto hidden md:block z-30">
        <div class="flex items-center gap-3 pb-4 mb-3 border-b border-white/10">
            <div class="w-10 h-10 rounded-xl bg-white/10 grid place-items-center font-black text-white text-lg tracking-wider">BM</div>
            <div>
                <strong class="block text-sm font-bold leading-tight">CRM BMSS</strong>
                <small class="block text-white/70 text-xs">Baitulmaal Sejuta Santri</small>
            </div>
        </div>

        <nav class="space-y-1 text-sm font-medium">
            <a href="{{ route('dashboard') }}" class="w-full flex items-center gap-3 px-3 py-2.5 rounded-xl transition {{ request()->routeIs('dashboard') ? 'bg-white text-[#343A72] font-extrabold shadow-sm' : 'text-white/80 hover:bg-white/10' }}">
                <span class="text-base">⌂</span>
                <span>Dashboard</span>
            </a>

            <div class="text-[10px] uppercase tracking-wider text-white/50 px-3 pt-3 pb-1 font-bold">CRM</div>
            <a href="{{ route('contacts.index') }}" class="w-full flex items-center gap-3 px-3 py-2.5 rounded-xl transition {{ request()->routeIs('contacts.*') ? 'bg-white text-[#343A72] font-extrabold shadow-sm' : 'text-white/80 hover:bg-white/10' }}">
                <span class="text-base">♙</span>
                <span>Semua Kontak</span>
            </a>
            <a href="{{ route('leads.index') }}" class="w-full flex items-center gap-3 px-3 py-2.5 rounded-xl transition {{ request()->routeIs('leads.*') ? 'bg-white text-[#343A72] font-extrabold shadow-sm' : 'text-white/80 hover:bg-white/10' }}">
                <span class="text-base">◇</span>
                <span>Lead & Pipeline</span>
            </a>
            <a href="{{ route('followups.index') }}" class="w-full flex items-center gap-3 px-3 py-2.5 rounded-xl transition {{ request()->routeIs('followups.*') ? 'bg-white text-[#343A72] font-extrabold shadow-sm' : 'text-white/80 hover:bg-white/10' }}">
                <span class="text-base">✓</span>
                <span>Follow-up</span>
            </a>

            <div class="text-[10px] uppercase tracking-wider text-white/50 px-3 pt-3 pb-1 font-bold">Zakat</div>
            <a href="{{ route('zakat.index') }}" class="w-full flex items-center gap-3 px-3 py-2.5 rounded-xl transition {{ request()->routeIs('zakat.index') ? 'bg-white text-[#343A72] font-extrabold shadow-sm' : 'text-white/80 hover:bg-white/10' }}">
                <span class="text-base">∑</span>
                <span>Zakat Center</span>
            </a>
            <a href="{{ route('zakat.calculator') }}" class="w-full flex items-center gap-3 px-3 py-2.5 rounded-xl transition {{ request()->routeIs('zakat.calculator') ? 'bg-white text-[#343A72] font-extrabold shadow-sm' : 'text-white/80 hover:bg-white/10' }}">
                <span class="text-base">＋</span>
                <span>Hitung Zakat</span>
            </a>
            <a href="{{ route('zakat.history') }}" class="w-full flex items-center gap-3 px-3 py-2.5 rounded-xl transition {{ request()->routeIs('zakat.history', 'zakat.show') ? 'bg-white text-[#343A72] font-extrabold shadow-sm' : 'text-white/80 hover:bg-white/10' }}">
                <span class="text-base">↺</span>
                <span>Riwayat Zakat</span>
            </a>

            <div class="text-[10px] uppercase tracking-wider text-white/50 px-3 pt-3 pb-1 font-bold">Fundraising</div>
            <a href="{{ route('transactions.index') }}" class="w-full flex items-center gap-3 px-3 py-2.5 rounded-xl transition {{ request()->routeIs('transactions.*') ? 'bg-white text-[#343A72] font-extrabold shadow-sm' : 'text-white/80 hover:bg-white/10' }}">
                <span class="text-xs font-bold">Rp</span>
                <span>Transaksi Dana</span>
            </a>
            <a href="{{ route('campaigns.index') }}" class="w-full flex items-center gap-3 px-3 py-2.5 rounded-xl transition {{ request()->routeIs('campaigns.*') ? 'bg-white text-[#343A72] font-extrabold shadow-sm' : 'text-white/80 hover:bg-white/10' }}">
                <span class="text-base">◎</span>
                <span>Campaign</span>
            </a>
            <a href="{{ route('programs.index') }}" class="w-full flex items-center gap-3 px-3 py-2.5 rounded-xl transition {{ request()->routeIs('programs.*') ? 'bg-white text-[#343A72] font-extrabold shadow-sm' : 'text-white/80 hover:bg-white/10' }}">
                <span class="text-base">♡</span>
                <span>Program</span>
            </a>

            @can('is-master')
            <div class="text-[10px] uppercase tracking-wider text-white/50 px-3 pt-3 pb-1 font-bold">Management</div>
            <a href="{{ route('payment-methods.index') }}" class="w-full flex items-center gap-3 px-3 py-2.5 rounded-xl transition {{ request()->routeIs('payment-methods.*') ? 'bg-white text-[#343A72] font-extrabold shadow-sm' : 'text-white/80 hover:bg-white/10' }}">
                <span class="text-base">▣</span>
                <span>Metode Pembayaran</span>
            </a>
            <a href="{{ route('whatsapp.index') }}" class="w-full flex items-center gap-3 px-3 py-2.5 rounded-xl transition {{ request()->routeIs('whatsapp.*') ? 'bg-white text-[#343A72] font-extrabold shadow-sm' : 'text-white/80 hover:bg-white/10' }}">
                <span class="text-base">☎</span>
                <span>CS & Nomor WA</span>
            </a>
            <a href="{{ route('users.index') }}" class="w-full flex items-center gap-3 px-3 py-2.5 rounded-xl transition {{ request()->routeIs('users.*') ? 'bg-white text-[#343A72] font-extrabold shadow-sm' : 'text-white/80 hover:bg-white/10' }}">
                <span class="text-base">👥</span>
                <span>Admin & User</span>
            </a>
            <a href="{{ route('kpi-targets.index') }}" class="w-full flex items-center gap-3 px-3 py-2.5 rounded-xl transition {{ request()->routeIs('kpi-targets.*') ? 'bg-white text-[#343A72] font-extrabold shadow-sm' : 'text-white/80 hover:bg-white/10' }}">
                <span class="text-base">🎯</span>
                <span>Target & KPI CS</span>
            </a>
            <a href="{{ route('settings.index') }}" class="w-full flex items-center gap-3 px-3 py-2.5 rounded-xl transition {{ request()->routeIs('settings.*') ? 'bg-white text-[#343A72] font-extrabold shadow-sm' : 'text-white/80 hover:bg-white/10' }}">
                <span class="text-base">⚙</span>
                <span>Pengaturan</span>
            </a>
            @endcan
        </nav>

        <!-- Current User Box -->
        <div class="mt-6 p-2.5 bg-white/10 rounded-2xl flex items-center gap-3">
            <div class="w-9 h-9 rounded-full bg-white text-[#343A72] font-black grid place-items-center flex-shrink-0 text-sm">
                {{ auth()->user()->initials }}
            </div>
            <div class="min-w-0 flex-1">
                <div class="font-bold text-xs truncate">{{ auth()->user()->name }}</div>
                <div class="text-[11px] text-white/70 truncate">{{ auth()->user()->isMaster() ? 'Master Admin' : 'Admin / CS' }}</div>
            </div>
        </div>
    </aside>

    <!-- Main Content Wrapper -->
    <div class="md:ml-[260px] flex flex-col min-h-screen">
        <!-- Top Navigation -->
        <header class="sticky top-0 h-[68px] z-20 bg-white/90 backdrop-blur-md border-b border-[#E1E3EC] px-4 md:px-7 flex items-center justify-between gap-3">
            <!-- Search Form -->
            <form action="{{ route('contacts.index') }}" method="GET" class="flex-1 max-w-[620px]">
                <div class="flex items-center gap-2.5 h-11 border border-[#E1E3EC] rounded-xl bg-[#FBFBFE] px-3 focus-within:border-[#343A72] transition">
                    <span class="text-gray-400">⌕</span>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama donatur, WA, NISS, kota, program..." class="w-full bg-transparent border-0 focus:ring-0 text-sm p-0 text-[#242842] placeholder:text-gray-400">
                </div>
            </form>

            <!-- Actions -->
            <div class="flex items-center gap-2">
                <button type="button" @click="notifModal = true" class="w-10 h-10 rounded-xl border border-[#E1E3EC] bg-white grid place-items-center text-[#343A72] hover:bg-gray-50 transition relative">
                    🔔
                </button>
                <button type="button" @click="quickModal = true" class="h-11 px-4 rounded-xl bg-[#343A72] text-white text-sm font-bold flex items-center gap-2 hover:bg-[#252B5B] transition shadow-sm">
                    <span>＋</span>
                    <span class="hidden sm:inline">Tambah</span>
                </button>
                <div class="relative" x-data="{ open: false }">
                    <button type="button" @click="open = !open" class="w-10 h-10 rounded-xl border border-[#E1E3EC] bg-white font-extrabold text-[#343A72] text-xs grid place-items-center hover:bg-gray-50 transition">
                        {{ auth()->user()->initials }}
                    </button>
                    <div x-show="open" @click.outside="open = false" x-cloak class="absolute right-0 mt-2 w-48 bg-white border border-[#E1E3EC] rounded-2xl shadow-bmss p-2 z-40 text-xs">
                        <div class="px-3 py-2 border-b border-gray-100">
                            <div class="font-bold text-gray-800">{{ auth()->user()->name }}</div>
                            <div class="text-gray-400">{{ auth()->user()->isMaster() ? 'Master Admin' : 'Admin / CS' }}</div>
                        </div>
                        <form method="POST" action="{{ route('logout') }}" class="mt-1">
                            @csrf
                            <button type="submit" class="w-full text-left px-3 py-2 rounded-xl text-red-600 font-bold hover:bg-red-50 transition">
                                Logout
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </header>

        <!-- Flash messages -->
        <div class="px-4 md:px-7 pt-4">
            @if(session('success'))
            <div class="p-3.5 rounded-xl bg-[#EAF8F1] border border-[#caead8] text-[#166848] text-xs font-semibold flex items-center gap-2">
                <span>✓</span>
                <span>{{ session('success') }}</span>
            </div>
            @endif

            @if(session('error'))
            <div class="p-3.5 rounded-xl bg-[#FFF0F2] border border-[#f5ccd0] text-[#8d2e35] text-xs font-semibold flex items-center gap-2">
                <span>⚠</span>
                <span>{{ session('error') }}</span>
            </div>
            @endif

            @if($errors->any())
            <div class="p-3.5 rounded-xl bg-[#FFF0F2] border border-[#f5ccd0] text-[#8d2e35] text-xs font-semibold">
                <div class="font-bold mb-1">Periksa kembali input form Anda:</div>
                <ul class="list-disc list-inside space-y-0.5">
                    @foreach($errors->all() as $err)
                        <li>{{ $err }}</li>
                    @endforeach
                </ul>
            </div>
            @endif
        </div>

        <!-- Page Content -->
        <main class="flex-1 p-4 md:p-7 max-w-[1680px] w-full mx-auto pb-24 md:pb-12">
            {{ $slot }}
        </main>
    </div>

    <!-- Mobile Bottom Navigation -->
    <nav class="md:hidden fixed left-0 right-0 bottom-0 h-16 bg-white border-t border-[#E1E3EC] grid grid-cols-5 z-20">
        <a href="{{ route('dashboard') }}" class="flex flex-col items-center justify-center gap-1 {{ request()->routeIs('dashboard') ? 'text-[#343A72] font-black' : 'text-gray-400' }}">
            <span class="text-lg">⌂</span>
            <span class="text-[10px]">Home</span>
        </a>
        <a href="{{ route('contacts.index') }}" class="flex flex-col items-center justify-center gap-1 {{ request()->routeIs('contacts.*') ? 'text-[#343A72] font-black' : 'text-gray-400' }}">
            <span class="text-lg">♙</span>
            <span class="text-[10px]">Kontak</span>
        </a>
        <a href="{{ route('zakat.index') }}" class="flex flex-col items-center justify-center gap-1 {{ request()->routeIs('zakat.*') ? 'text-[#343A72] font-black' : 'text-gray-400' }}">
            <span class="text-lg">∑</span>
            <span class="text-[10px]">Zakat</span>
        </a>
        <a href="{{ route('followups.index') }}" class="flex flex-col items-center justify-center gap-1 {{ request()->routeIs('followups.*') ? 'text-[#343A72] font-black' : 'text-gray-400' }}">
            <span class="text-lg">✓</span>
            <span class="text-[10px]">Follow-up</span>
        </a>
        <a href="{{ route('transactions.index') }}" class="flex flex-col items-center justify-center gap-1 {{ request()->routeIs('transactions.*') ? 'text-[#343A72] font-black' : 'text-gray-400' }}">
            <span class="text-base font-bold">Rp</span>
            <span class="text-[10px]">Dana</span>
        </a>
    </nav>

    <!-- Quick Add Modal -->
    <div x-show="quickModal" x-cloak class="fixed inset-0 bg-[#111536]/40 backdrop-blur-xs flex items-center justify-center z-50 p-4" @click.self="quickModal = false">
        <div class="bg-white rounded-[22px] border border-[#E1E3EC] shadow-bmss p-6 max-w-sm w-full">
            <h3 class="font-extrabold text-lg text-[#252B5B]">Quick Add</h3>
            <p class="text-xs text-gray-500 mb-4">Aksi cepat operasional CRM BMSS.</p>
            <div class="space-y-2 text-xs font-bold">
                <a href="{{ route('contacts.create') }}" class="w-full flex items-center justify-center gap-2 py-3 bg-[#343A72] text-white rounded-xl hover:bg-[#252B5B] transition">
                    Tambah Kontak Baru
                </a>
                <a href="{{ route('zakat.calculator') }}" class="w-full flex items-center justify-center gap-2 py-3 bg-[#FF303B] text-white rounded-xl hover:bg-red-600 transition">
                    Hitung Zakat
                </a>
                <a href="{{ route('transactions.index') }}#create" class="w-full flex items-center justify-center gap-2 py-3 bg-white text-[#343A72] border border-[#E1E3EC] rounded-xl hover:bg-gray-50 transition">
                    Input Transaksi
                </a>
                <a href="{{ route('followups.index') }}" class="w-full flex items-center justify-center gap-2 py-3 bg-white text-[#343A72] border border-[#E1E3EC] rounded-xl hover:bg-gray-50 transition">
                    Buat Follow-up
                </a>
            </div>
            <button type="button" @click="quickModal = false" class="w-full mt-4 py-2 text-xs font-bold text-gray-400 hover:text-gray-600">
                Tutup
            </button>
        </div>
    </div>

    <!-- Notification Modal -->
    <div x-show="notifModal" x-cloak class="fixed inset-0 bg-[#111536]/40 backdrop-blur-xs flex items-center justify-center z-50 p-4" @click.self="notifModal = false">
        <div class="bg-white rounded-[22px] border border-[#E1E3EC] shadow-bmss p-6 max-w-md w-full">
            <h3 class="font-extrabold text-lg text-[#252B5B]">Notifikasi</h3>
            <p class="text-xs text-gray-500 mb-4">Aktivitas dan issue operasional.</p>
            <div class="space-y-3 text-xs">
                <div class="flex items-center justify-between p-3 rounded-xl bg-[#EEF0F8]">
                    <div class="flex items-center gap-3">
                        <span class="w-8 h-8 rounded-lg bg-white grid place-items-center text-[#343A72] font-bold">✓</span>
                        <div>
                            <b class="text-[#252B5B]">Follow-up Aktif</b>
                            <div class="text-gray-500">Periksa daftar tindak lanjut donatur</div>
                        </div>
                    </div>
                    <a href="{{ route('followups.index') }}" class="px-3 py-1.5 rounded-lg bg-[#343A72] text-white font-bold">Buka</a>
                </div>
                <div class="flex items-center justify-between p-3 rounded-xl bg-[#FFF7D8]">
                    <div class="flex items-center gap-3">
                        <span class="w-8 h-8 rounded-lg bg-white grid place-items-center text-[#8b6500] font-bold">∑</span>
                        <div>
                            <b class="text-[#8b6500]">Zakat Outstanding</b>
                            <div class="text-gray-500">Muzakki yang belum melunasi kewajiban zakat</div>
                        </div>
                    </div>
                    <a href="{{ route('zakat.history') }}" class="px-3 py-1.5 rounded-lg bg-[#FFC316] text-[#3b2c00] font-bold">Buka</a>
                </div>
            </div>
            <button type="button" @click="notifModal = false" class="w-full mt-4 py-2.5 bg-gray-100 rounded-xl text-xs font-bold text-gray-600 hover:bg-gray-200">
                Tutup
            </button>
        </div>
    </div>

    @stack('scripts')
</body>
</html>
