{{-- FAQ + kontak support + form tiket --}}
<div class="grid grid-cols-1 gap-4 lg:grid-cols-3">

    {{-- Daftar FAQ --}}
    <div class="ss-card space-y-4 lg:col-span-2">
        <div>
            <h2 class="text-sm font-bold text-slate-800 dark:text-white">Pertanyaan Umum (FAQ)</h2>
            <p class="text-xs text-slate-400">Jawaban paling sering dicari admin</p>
        </div>

        <div class="space-y-3 text-xs" id="faq-list">
            @php
                $faqs = [
                    [
                        'q' => 'Bagaimana cara tambah siswa baru?',
                        'a' => 'Masuk ke menu Data Siswa → klik tombol + Tambah Siswa → lengkapi NIS, Nama, Kelas, dan Wali → Simpan. Anda juga dapat menggunakan tombol Import CSV untuk menambahkan ratusan siswa sekaligus.',
                    ],
                    [
                        'q' => 'Bagaimana membuat Kategori Tagihan SPP baru?',
                        'a' => 'Masuk ke menu Kategori Tagihan → klik Tambah Kategori → masukkan kode SPP, nominal dasar (misal Rp 850.000) → pilih tipe per bulan → simpan dan aktifkan.',
                    ],
                    [
                        'q' => 'Billing gagal generate pada beberapa siswa?',
                        'a' => 'Buka Riwayat Generate → periksa log error (biasanya karena nomor induk siswa sudah terbit tagihan di periode yang sama) → perbaiki data target → jalankan Generate Ulang.',
                    ],
                    [
                        'q' => 'Bagaimana cara mengunduh Laporan pembayaran?',
                        'a' => 'Buka menu Laporan → filter periode bulan yang diinginkan (misal Sep 2026) → klik tombol Export PDF atau Excel di sudut kanan atas.',
                    ],
                ];
            @endphp

            @foreach ($faqs as $index => $faq)
                <details data-faq
                    class="group rounded-xl border border-slate-200 p-3 dark:border-slate-800 [&_summary::-webkit-details-marker]:hidden"
                    {{ $index === 0 ? 'open' : '' }}
                >
                    <summary class="flex cursor-pointer items-center justify-between font-semibold text-slate-800 dark:text-white">
                        <span>{{ $faq['q'] }}</span>
                        <span class="ml-2 text-slate-400 transition group-open:rotate-180">▾</span>
                    </summary>
                    <p class="mt-2 leading-relaxed text-slate-600 dark:text-slate-300">
                        {{ $faq['a'] }}
                    </p>
                </details>
            @endforeach
        </div>
    </div>

    {{-- Kontak support + tiket --}}
    <div class="space-y-4">
        <div class="ss-card space-y-3">
            <h3 class="text-xs font-bold uppercase tracking-wider text-slate-500">
                Hubungi Support
            </h3>

            <div class="space-y-2 text-xs">
                @php
                    $supportEmail = $settings['school_email'] ?? 'support@smartschool.id';
                    $supportPhone = $settings['school_phone'] ?? '(021) 5090-1234';
                @endphp
                <div class="flex items-center gap-3 rounded-xl bg-slate-50 p-2.5 dark:bg-slate-800">
                    <span class="h-2.5 w-2.5 rounded-full bg-emerald-500"></span>
                    <div>
                        <div class="font-bold text-slate-800 dark:text-white">Live Chat Support</div>
                        <div class="text-[11px] text-slate-400">Online • jam kerja sekolah</div>
                    </div>
                </div>
                <div class="flex items-center gap-3 rounded-xl bg-slate-50 p-2.5 dark:bg-slate-800">
                    <span class="h-2.5 w-2.5 rounded-full bg-blue-500"></span>
                    <div>
                        <div class="font-bold text-slate-800 dark:text-white">{{ $supportPhone }}</div>
                        <div class="text-[11px] text-slate-400">Telepon sekolah (dari Pengaturan)</div>
                    </div>
                </div>
                <div class="flex items-center gap-3 rounded-xl bg-slate-50 p-2.5 dark:bg-slate-800">
                    <span class="h-2.5 w-2.5 rounded-full bg-purple-500"></span>
                    <div>
                        <div class="font-bold text-slate-800 dark:text-white">{{ $supportEmail }}</div>
                        <div class="text-[11px] text-slate-400">Email sekolah (dari Pengaturan)</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="ss-card space-y-3">
            <h3 class="text-xs font-bold uppercase tracking-wider text-slate-500">
                Buat Tiket Bantuan
            </h3>

            <form data-ticket-form id="ticket-form" class="space-y-2.5 text-xs">
                <div>
                    <label class="mb-1 block font-medium text-slate-700 dark:text-slate-300">
                        Subjek Kendala
                    </label>
                    <input
                        type="text" id="ticket-subject"
                        placeholder="Contoh: Gagal generate billing"
                        class="ss-input text-xs !py-2"
                        required
                    >
                </div>

                <div>
                    <label class="mb-1 block font-medium text-slate-700 dark:text-slate-300">
                        Pesan / Detail
                    </label>
                    <textarea
                        rows="3" id="ticket-message"
                        placeholder="Jelaskan masalah secara singkat..."
                        class="ss-input text-xs !py-2"
                        required
                    ></textarea>
                </div>

                <p id="ticket-feedback" class="hidden rounded-lg bg-emerald-50 dark:bg-emerald-950 border border-emerald-200 dark:border-emerald-800 px-3 py-2 text-emerald-700 dark:text-emerald-300"></p>

                <button type="submit" class="ss-btn-primary !py-2.5 text-xs">
                    Kirim Tiket
                </button>
            </form>
            <script>
            (function () {
                var form = document.getElementById('ticket-form');
                if (!form || form.dataset.bound) return;
                form.dataset.bound = '1';
                form.addEventListener('submit', function (e) {
                    e.preventDefault();
                    var fb = document.getElementById('ticket-feedback');
                    var subj = (document.getElementById('ticket-subject').value || '').trim();
                    if (fb) {
                        fb.textContent = 'Tiket "' + (subj || 'tanpa subjek') + '" dicatat (demo lokal). Tim support akan menghubungi via email sekolah.';
                        fb.classList.remove('hidden');
                    }
                    form.reset();
                });
            })();
            </script>
        </div>
    </div>
</div>
