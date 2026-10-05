<div id="sidebar-overlay" class="fixed inset-0 z-30 bg-black/50 lg:hidden hidden" onclick="toggleSidebar()"></div>

<aside id="sidebar" class="fixed top-0 left-0 z-40 flex h-screen w-[248px] flex-col gap-2 bg-[#0F1E33] p-4 pt-6 text-white transition-transform -translate-x-full lg:translate-x-0">
    <div class="flex items-center gap-3 mb-2">
        <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-blue-600 text-xl font-bold">{{ strtoupper(substr($siteBrand['school_name'] ?? 'S', 0, 1)) }}</div>
        <div>
            <div class="text-base font-bold truncate">{{ $siteBrand['school_name'] ?? 'SmartSchool' }}</div>
            <div class="text-[10px] text-slate-400">Portal Siswa</div>
        </div>
    </div>

    <div class="text-[11px] font-semibold text-slate-500 mt-2 mb-1 px-3">MENU UTAMA</div>
    <nav class="flex flex-col gap-1">
        @php
            $menu = [
                ['route' => 'portal.dashboard', 'label' => 'Beranda', 'icon' => 'layout-dashboard'],
                ['route' => 'portal.tagihan', 'label' => 'Tagihan Saya', 'icon' => 'receipt'],
                ['route' => 'portal.riwayat', 'label' => 'Riwayat Bayar', 'icon' => 'file-text'],
                ['route' => 'portal.profil', 'label' => 'Profil Anak', 'icon' => 'users'],
                ['route' => 'portal.bantuan', 'label' => 'Bantuan', 'icon' => 'tag'],
            ];
        @endphp
        @foreach($menu as $item)
            <a href="{{ route($item['route']) }}"
               class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm transition
                      {{ request()->routeIs($item['route'] . '*') ? 'bg-blue-600 font-semibold text-white' : 'text-slate-300 hover:bg-white/10' }}">
                @include('partials.icons.' . $item['icon'])
                {{ $item['label'] }}
            </a>
        @endforeach
    </nav>

    <div class="text-[11px] font-semibold text-slate-500 mt-4 mb-1 px-3">LAINNYA</div>
    <nav class="flex flex-col gap-1">
        <a href="{{ route('portal.pengaturan') }}" class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm text-slate-300 hover:bg-white/10 {{ request()->routeIs('portal.pengaturan*') ? 'bg-blue-600 font-semibold text-white' : '' }}">
            @include('partials.icons.settings')
            Pengaturan Akun
        </a>
    </nav>

    <div class="rounded-xl bg-[#1E3A5F] p-4 space-y-2">
        <div class="text-sm font-semibold">Butuh bantuan?</div>
        <div class="text-xs text-slate-400">Panduan bayar tagihan & unduh kuitansi.</div>
        <a href="{{ route('portal.bantuan') }}" class="flex items-center justify-center gap-1.5 rounded-lg bg-blue-600 px-3 py-2.5 text-xs font-semibold hover:bg-blue-700 transition">Pusat Bantuan</a>
    </div>

    <div class="flex-1"></div>

    <div class="flex items-center gap-2.5 px-2 py-3 border-t border-white/10 mt-1">
        @php $portalUser = Auth::guard('wali')->user() ?? Auth::user(); @endphp
        <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-blue-600 text-sm font-bold">
            {{ strtoupper(substr($portalUser->name ?? 'W', 0, 1)) }}
        </div>
        <div class="flex-1 min-w-0">
            <div class="text-xs font-semibold truncate">{{ $portalUser->name ?? 'Wali' }}</div>
            <div class="text-[10px] text-slate-400 truncate">{{ $portalUser->student->name ?? $portalUser->email ?? '-' }}</div>
        </div>
        <form method="POST" action="{{ route('portal.logout') }}">
            @csrf
            <button type="submit" title="Keluar" class="p-1.5 text-slate-400 hover:text-rose-400 rounded-lg hover:bg-white/5 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M9 21H5a2 2 0 01-2-2V5a2 2 0 012-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
            </button>
        </form>
    </div>
</aside>
