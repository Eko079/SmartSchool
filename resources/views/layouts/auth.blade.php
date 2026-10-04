<!DOCTYPE html>
<html lang="id" class="">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'SmartSchool') — SmartSchool ERP</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-white dark:bg-slate-900">
    <div class="flex min-h-screen">
        {{-- Branding Panel (left) --}}
        <div class="hidden lg:flex w-[520px] shrink-0 flex-col justify-between bg-[#0F1E33] p-12 text-white">
            {{-- Logo --}}
            <div class="flex items-center gap-3">
                <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-blue-600 text-xl font-bold">S</div>
                <div>
                    <div class="text-lg font-bold tracking-wide">SmartSchool ERP</div>
                    <div class="text-xs text-slate-400">Digitalisasi Data Siswa & Pembayaran Terpadu</div>
                </div>
            </div>

            {{-- Middle --}}
            <div class="space-y-5">
                <div class="text-xs font-semibold tracking-wider text-blue-400">● PANEL ADMIN v2.4</div>
                <h1 class="text-3xl font-bold leading-tight">Kelola Sekolah &amp; Pembayaran dalam Satu Dasbor</h1>
                <p class="text-sm leading-relaxed text-slate-400">Pantau 1.284 siswa, tagihan SPP/LAB/GEDUNG/KEG, dan laporan pembayaran real-time.</p>

                {{-- Stats card --}}
                <div class="rounded-2xl border border-[#243B5E] bg-[#162C4E] p-5 space-y-4">
                    <div class="flex items-center justify-between">
                        <span class="text-sm font-semibold">Aktivitas Semester Ganjil</span>
                        <span class="text-xs font-semibold text-green-400">+12,4% ▲</span>
                    </div>
                    <div class="flex items-end gap-2 h-24">
                        @php $bars = [42,68,52,84,61,96,74,58,88,70,50,78]; @endphp
                        @foreach($bars as $h)
                            <div class="flex-1 rounded-md {{ in_array($h, [84,96,88]) ? 'bg-blue-600' : 'bg-[#2D4A71]' }}" style="height: {{ $h }}px"></div>
                        @endforeach
                    </div>
                    <div class="flex gap-3">
                        @foreach([['1.284','Siswa'],['87','Guru'],['36','Kelas']] as [$val,$label])
                        <div class="flex-1 rounded-xl bg-[#0F1E33] p-3">
                            <div class="text-lg font-bold">{{ $val }}</div>
                            <div class="text-xs text-slate-400">{{ $label }}</div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- Footer --}}
            <div class="space-y-3">
                <p class="text-xs italic text-slate-300">"Sejak pakai SmartSchool, rekap akademik dan pembayaran 3x lebih cepat." — Dr. Ratna, Kepala Sekolah</p>
                <p class="text-xs text-slate-500">© 2026 SMA Nusantara Plus • support@smartschool.id • v2.4.1</p>
            </div>
        </div>

        {{-- Right Panel --}}
        <div class="flex flex-1 flex-col">
            {{-- Top bar --}}
            <div class="flex items-center justify-between px-8 pt-6 lg:px-16">
                <span class="text-xs font-bold tracking-wide text-slate-800 dark:text-slate-200">SmartSchool • Admin</span>
                <div class="flex items-center gap-2 text-xs text-slate-500">
                    <span>ID · v2.4.1</span>
                    <span>Bantuan</span>
                </div>
            </div>

            {{-- Content --}}
            <div class="flex flex-1 items-center justify-center px-8 lg:px-16">
                <div class="w-full max-w-md space-y-4">
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
