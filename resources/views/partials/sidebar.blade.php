{{-- Sidebar Overlay (mobile) --}}
<div id="sidebar-overlay" class="fixed inset-0 z-30 bg-black/50 lg:hidden hidden" onclick="toggleSidebar()"></div>

{{-- Sidebar --}}
<aside id="sidebar" class="fixed top-0 left-0 z-40 flex h-screen w-[248px] flex-col gap-2 bg-[#0F1E33] p-4 pt-6 text-white transition-transform -translate-x-full lg:translate-x-0">
    {{-- Logo --}}
    <div class="flex items-center gap-3 mb-2">
        <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-blue-600 text-xl font-bold">S</div>
        <div>
            <div class="text-base font-bold">SmartSchool</div>
            <div class="text-[10px] text-slate-400">ERP Digitalisasi</div>
        </div>
    </div>

    {{-- Menu Utama --}}
    <div class="text-[11px] font-semibold text-slate-500 mt-2 mb-1 px-3">MENU UTAMA</div>
    <nav class="flex flex-col gap-1">
        @php
            $menuUtama = [
                ['route' => 'admin.dashboard', 'label' => 'Beranda', 'icon' => 'layout-dashboard'],
            ];
            $menuMaster = [
                ['route' => 'admin.siswa', 'label' => 'Data Siswa', 'icon' => 'users'],
                ['route' => 'admin.kategori-tagihan', 'label' => 'Kategori Tagihan', 'icon' => 'tag'],
                ['route' => 'admin.akun', 'label' => 'Akun Staf', 'icon' => 'users'],
            ];
            $menuMasterSuper = [
                ['route' => 'admin.kelas', 'label' => 'Data Kelas', 'icon' => 'school'],
                ['route' => 'admin.backup', 'label' => 'Backup', 'icon' => 'download'],
            ];
            $menuTransaksi = [
                ['route' => 'admin.billing', 'label' => 'Billing Generator', 'icon' => 'receipt'],
                ['route' => 'admin.laporan', 'label' => 'Laporan', 'icon' => 'file-text'],
            ];
            $menuLainnya = [
                ['route' => 'admin.tiket', 'label' => 'Tiket Bantuan', 'icon' => 'life-buoy'],
            ];
            $menuSuper = [
                ['route' => 'admin.pengaturan', 'label' => 'Pengaturan', 'icon' => 'settings'],
                ['route' => 'admin.audit', 'label' => 'Log Aktifitas', 'icon' => 'file-text'],
            ];
            $isSuper = ((Auth::user()->role ?? '') === 'superadmin');
            $roleLabel = $isSuper ? 'Super Admin' : 'Bendahara';
        @endphp
        @foreach($menuUtama as $item)
            <a href="{{ route($item['route']) }}"
               class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm transition
                      {{ request()->routeIs($item['route'] . '*') ? 'bg-blue-600 font-semibold text-white' : 'text-slate-300 hover:bg-white/10' }}">
                @include('partials.icons.' . $item['icon'])
                {{ $item['label'] }}
            </a>
        @endforeach
    </nav>

    <div class="text-[11px] font-semibold text-slate-500 mt-4 mb-1 px-3">MASTER DATA</div>
    <nav class="flex flex-col gap-1">
        @foreach($menuMaster as $item)
            <a href="{{ route($item['route']) }}"
               class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm transition
                      {{ request()->routeIs($item['route'] . '*') ? 'bg-blue-600 font-semibold text-white' : 'text-slate-300 hover:bg-white/10' }}">
                @include('partials.icons.' . $item['icon'])
                {{ $item['label'] }}
            </a>
        @endforeach
        @if($isSuper)
            @foreach($menuMasterSuper as $item)
                <a href="{{ route($item['route']) }}"
                   class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm transition
                          {{ request()->routeIs($item['route'] . '*') ? 'bg-blue-600 font-semibold text-white' : 'text-slate-300 hover:bg-white/10' }}">
                    @include('partials.icons.' . $item['icon'])
                    {{ $item['label'] }}
                </a>
            @endforeach
        @endif
    </nav>

    <div class="text-[11px] font-semibold text-slate-500 mt-4 mb-1 px-3">TRANSAKSI</div>
    <nav class="flex flex-col gap-1">
        @foreach($menuTransaksi as $item)
            <a href="{{ route($item['route']) }}"
               class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm transition
                      {{ request()->routeIs($item['route'] . '*') ? 'bg-blue-600 font-semibold text-white' : 'text-slate-300 hover:bg-white/10' }}">
                @include('partials.icons.' . $item['icon'])
                {{ $item['label'] }}
            </a>
        @endforeach
    </nav>

    {{-- Lainnya --}}
    <div class="text-[11px] font-semibold text-slate-500 mt-4 mb-1 px-3">LAINNYA</div>
    <nav class="flex flex-col gap-1">
        @foreach($menuLainnya as $item)
            <a href="{{ route($item['route']) }}"
               class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm transition
                      {{ request()->routeIs($item['route'] . '*') ? 'bg-blue-600 font-semibold text-white' : 'text-slate-300 hover:bg-white/10' }}">
                @include('partials.icons.' . $item['icon'])
                {{ $item['label'] }}
            </a>
        @endforeach
        @if($isSuper)
            @foreach($menuSuper as $item)
                <a href="{{ route($item['route']) }}"
                   class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm transition
                          {{ request()->routeIs($item['route'] . '*') ? 'bg-blue-600 font-semibold text-white' : 'text-slate-300 hover:bg-white/10' }}">
                    @include('partials.icons.' . $item['icon'])
                    {{ $item['label'] }}
                </a>
            @endforeach
        @endif
    </nav>

    {{-- Spacer (dorong profil ke bawah) --}}
    <div class="flex-1"></div>

    {{-- User --}}
    <div class="flex items-center gap-2.5 px-2 py-3 border-t border-white/10 mt-1">
        <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-blue-600 text-sm font-bold">
            {{ strtoupper(substr(Auth::user()->name ?? 'A', 0, 1)) }}
        </div>
        <div class="flex-1 min-w-0">
            <div class="text-xs font-semibold truncate">{{ Auth::user()->name ?? 'Admin' }}</div>
            <div class="text-[10px] text-slate-400 truncate">{{ $roleLabel ?? '' }} • {{ Auth::user()->email ?? '-' }}</div>
        </div>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" title="Keluar" class="p-1.5 text-slate-400 hover:text-rose-400 rounded-lg hover:bg-white/5 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M9 21H5a2 2 0 01-2-2V5a2 2 0 012-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
            </button>
        </form>
    </div>

</aside>
