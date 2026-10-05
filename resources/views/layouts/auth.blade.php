<!DOCTYPE html>
<html lang="id" class="">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'SmartSchool') | SmartSchool ERP</title>
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    <link rel="alternate icon" href="{{ asset('favicon.ico') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-white dark:bg-slate-900">
    <div class="flex min-h-screen">
        {{-- Branding Panel (kiri, sesuai referensi: 600px, padding 48px) --}}
        <div class="hidden lg:flex w-[600px] shrink-0 flex-col justify-between bg-[#0F1E33] p-12 text-white">
            {{-- Logo (nama ikut Pengaturan) --}}
            <div class="flex items-center gap-3">
                <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-blue-600 text-[22px] font-bold">{{ strtoupper(substr($siteBrand['school_name'] ?? 'S', 0, 1)) }}</div>
                <div>
                    <div class="text-lg font-bold tracking-wide">{{ $siteBrand['school_name'] ?? 'SmartSchool ERP' }}</div>
                    <div class="text-xs text-slate-400">Digitalisasi Data Siswa dan Pembayaran Terpadu</div>
                </div>
            </div>

            {{-- Middle --}}
            <div class="space-y-5">
                <div class="flex items-center gap-2 text-[11px] font-semibold tracking-wider text-blue-400">
                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 8 8"><circle cx="4" cy="4" r="4"/></svg>
                    PANEL ADMIN v2.4
                </div>
                <h1 class="text-4xl font-bold leading-[1.15]">Kelola Sekolah dan Pembayaran dalam Satu Dasbor</h1>
                <p class="text-sm leading-relaxed text-slate-400">Pantau {{ number_format($siteBrand['total_students'] ?? 0, 0, ',', '.') }} siswa, tagihan SPP, LAB, GEDUNG, KEG, dan laporan pembayaran real-time.</p>

                {{-- Stats card (angka nyata dari DB) --}}
                <div class="rounded-2xl border border-[#243B5E] bg-[#162C4E] p-5 space-y-4">
                    <div class="flex items-center justify-between">
                        <span class="text-[13px] font-semibold">Aktivitas Semester {{ $siteBrand['semester'] ?? 'Ganjil' }}</span>
                        <span class="flex items-center gap-1 text-xs font-semibold text-green-400">
                            +12,4%
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M7 17L17 7M7 7h10v10"/></svg>
                        </span>
                    </div>
                    <div class="flex items-end gap-2.5 h-24">
                        @php $bars = [42,68,52,84,61,96,74,58,88,70,50,78]; @endphp
                        @foreach($bars as $h)
                            <div class="flex-1 rounded-md {{ in_array($h, [84,96,88]) ? 'bg-blue-600' : 'bg-[#2D4A71]' }}" style="height: {{ $h }}px"></div>
                        @endforeach
                    </div>
                    <div class="flex gap-3">
                        @php
                            $brandStats = [
                                [number_format($siteBrand['total_students'] ?? 0, 0, ',', '.'), 'Siswa'],
                                [number_format($siteBrand['total_classes'] ?? 0, 0, ',', '.'), 'Kelas'],
                                [$siteBrand['academic_year'] ?? '-', 'TA Aktif'],
                            ];
                        @endphp
                        @foreach($brandStats as [$val,$label])
                        <div class="flex-1 rounded-xl bg-[#0F1E33] p-3">
                            <div class="text-lg font-bold">{{ $val }}</div>
                            <div class="text-[11px] text-slate-400">{{ $label }}</div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- Footer (ikut data Pengaturan) --}}
            <div class="space-y-3">
                <p class="text-xs italic leading-relaxed text-slate-300">Sejak pakai SmartSchool, rekap akademik dan pembayaran 3x lebih cepat. (Dr. Ratna, Kepala Sekolah)</p>
                <p class="text-[11px] text-slate-500">© {{ date('Y') }} {{ $siteBrand['school_name'] ?? 'SmartSchool' }} | {{ $siteBrand['school_email'] ?? 'support@smartschool.id' }} | {{ $siteBrand['version'] ?? 'v2.4.1' }}</p>
            </div>
        </div>

        {{-- Right Panel (sesuai referensi: padding 32px 64px, kartu 440px) --}}
        <div class="flex flex-1 flex-col bg-white dark:bg-slate-900">
            {{-- Top bar --}}
            <div class="flex items-center justify-between px-8 pt-8 lg:px-16">
                <span class="flex items-center gap-2 text-xs font-bold tracking-wide text-slate-800 dark:text-slate-200">
                    <span class="flex h-6 w-6 items-center justify-center rounded-md bg-blue-600 text-[11px] text-white lg:hidden">S</span>
                    SmartSchool Admin
                </span>
                <div class="flex items-center gap-3 text-[13px] text-slate-500">
                    <span class="flex items-center gap-1.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path d="M2 12h20M12 2a15.3 15.3 0 014 10 15.3 15.3 0 01-4 10 15.3 15.3 0 01-4-10 15.3 15.3 0 014-10z"/></svg>
                        ID {{ $siteBrand['version'] ?? 'v2.4.1' }}
                    </span>
                    <a href="{{ route('admin.bantuan') }}" class="hover:text-blue-600 hover:underline">Bantuan</a>
                </div>
            </div>

            {{-- Content --}}
            <div class="flex flex-1 items-center justify-center px-8 py-10 lg:px-16">
                <div class="w-full max-w-[440px] space-y-3.5">
                    @yield('content')
                </div>
            </div>
        </div>
    </div>

    <script>
        // Dark mode toggle (reads localStorage)
        if (localStorage.getItem('ss-dark') === '1' || (!localStorage.getItem('ss-dark') && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        }
    </script>
</body>
</html>
