<div class="rounded-[16px] border border-[#E2E8F0] bg-white p-4 px-6 shadow-sm dark:border-slate-700 dark:bg-slate-900">
    {{-- Baris 1: hamburger + judul + ikon (selalu muat, judul flex-1 min-w-0) --}}
    @php
        $topbarUser = Auth::guard('wali')->user() ?? Auth::user();
        $topbarName = $topbarUser?->student->name ?? $topbarUser->name ?? 'Admin';
    @endphp
    <div class="flex items-center gap-3 sm:gap-5">
    {{-- Mobile hamburger --}}
    <button onclick="toggleSidebar()" title="Buka/tutup menu samping" aria-label="Buka/tutup menu samping"
        class="ss-tip ss-tip-bottom lg:hidden p-2.5 -ml-2 rounded-xl hover:bg-slate-100 dark:hover:bg-slate-800 dark:text-slate-200 shrink-0" data-tip="Buka/tutup menu samping">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M3 12h18M3 6h18M3 18h18"/></svg>
    </button>

    {{-- Title --}}
    <div class="flex-1 min-w-0">
        @hasSection('breadcrumb')
            <div class="text-[13px] font-normal text-[#64748B] mb-1.5 truncate">@yield('breadcrumb')</div>
        @endif
        @if(request()->routeIs('admin.dashboard'))
            @php $sapaan = explode(' ', Auth::user()->name ?? 'Admin')[0]; @endphp
            <h1 class="truncate text-xl sm:text-[28px] font-bold leading-tight sm:leading-none text-[#0F172A] dark:text-white" style="font-family: Inter, system-ui, sans-serif;">👋 Selamat Datang Kembali, {{ $sapaan }}!</h1>
        @else
            <h1 class="text-xl sm:text-[28px] font-bold leading-tight text-[#0F172A] dark:text-white" style="font-family: Inter, system-ui, sans-serif;">@yield('page-title', 'Dashboard')</h1>
        @endif
        @hasSection('page-subtitle')
            <p class="mt-1.5 text-xs sm:text-sm font-normal text-[#64748B] line-clamp-2">@yield('page-subtitle')</p>
        @endif
    </div>

    {{-- Ikon: toggle + bell + profil (nama disembunyikan di <md agar judul dapat ruang) --}}
    <div class="flex items-center gap-2 sm:gap-3 shrink-0">
    {{-- Theme toggle (hanya switch, single elemen) --}}
    <button type="button" onclick="toggleDark(this)"
        role="switch" aria-checked="false" aria-label="Mode gelap terang"
        class="ss-tip ss-tip-bottom theme-toggle track shrink-0" data-tip="Ganti mode gelap / terang">
        <span class="track-deco deco-sun">
            <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="4"/><path d="M12 2v2M12 20v2M4.93 4.93l1.41 1.41M17.66 17.66l1.41 1.41M2 12h2M20 12h2M4.93 19.07l1.41-1.41M17.66 6.34l1.41-1.41"/></svg>
        </span>
        <span class="track-deco deco-moon">
            <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M21 12.79A9 9 0 1111.21 3a7 7 0 009.79 9.79z"/></svg>
        </span>
        <span class="knob">
            <svg class="icon-sun" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="4"/><path d="M12 2v2M12 20v2M4.93 4.93l1.41 1.41M17.66 17.66l1.41 1.41M2 12h2M20 12h2M4.93 19.07l1.41-1.41M17.66 6.34l1.41-1.41"/></svg>
            <svg class="icon-moon" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M21 12.79A9 9 0 1111.21 3a7 7 0 009.79 9.79z"/></svg>
        </span>
        <span class="ripple"></span>
    </button>

    {{-- Bell --}}
    <button title="Notifikasi" aria-label="Notifikasi"
        class="ss-tip ss-tip-bottom flex h-[48px] w-[48px] shrink-0 items-center justify-center rounded-[14px] border border-[#E2E8F0] bg-white hover:bg-slate-50 dark:border-slate-700 dark:bg-slate-900 dark:hover:bg-slate-800" data-tip="Notifikasi">
        <svg class="w-5 h-5 text-[#475569] dark:text-slate-300" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M18 8A6 6 0 006 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 01-3.46 0"/></svg>
    </button>

    {{-- Profile --}}
    <div title="Masuk sebagai {{ $topbarName }}"
        class="ss-tip ss-tip-bottom flex items-center gap-3 rounded-[28px] border border-[#E2E8F0] bg-white py-2 pl-2 pr-2 md:pr-4 dark:border-slate-700 dark:bg-slate-900" data-tip="Profil pengguna">
        <div class="flex h-10 w-10 items-center justify-center rounded-full bg-[#2563EB] text-base font-bold text-white">
            {{ strtoupper(substr($topbarName, 0, 1)) }}
        </div>
        <span class="text-sm font-semibold text-[#0F172A] hidden md:inline dark:text-white">{{ $topbarName }}</span>
    </div>
    </div>
    </div>

</div>
