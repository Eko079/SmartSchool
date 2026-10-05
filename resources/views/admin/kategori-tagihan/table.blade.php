<x-ss-table>
    <x-slot name="filters">
        <div class="flex flex-wrap items-center justify-between gap-3 pb-3 border-b border-slate-200 dark:border-slate-800">
            <div>
                <h2 class="text-sm font-bold text-slate-800 dark:text-white">Daftar Kategori Tagihan</h2>
                <p class="text-xs text-slate-400">Parameter nominal dasar billing generator</p>
            </div>
            <button type="button" title="Tambah kategori" onclick="document.getElementById('create-category-modal').classList.remove('hidden')"
                class="flex items-center gap-1.5 rounded-lg bg-blue-600 px-3.5 py-2 text-xs font-semibold text-white hover:bg-blue-700 shadow-xs">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                Tambah Kategori
            </button>
        </div>
        <div class="pt-3">
            <form method="GET" action="{{ route('admin.kategori-tagihan') }}" class="flex flex-wrap items-center gap-2">
                <div class="relative">
                    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                    </span>
                    <input type="text" name="q" id="kategori-search" value="{{ request('q') }}" placeholder="Cari kode / nama..."
                        autocomplete="off" class="ss-input text-xs !py-2 !pl-9 w-44 sm:w-56">
                    @if(request()->filled('sort'))<input type="hidden" name="sort" value="{{ request('sort') }}">@endif
                    @if(request()->filled('dir'))<input type="hidden" name="dir" value="{{ request('dir') }}">@endif
                </div>
                <select name="status" onchange="this.form.submit()"
                    class="rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs py-2 px-3 text-slate-700 dark:text-slate-200 outline-none">
                    <option value="">Semua</option>
                    <option value="aktif" {{ request('status') == 'aktif' ? 'selected' : '' }}>Aktif</option>
                    <option value="nonaktif" {{ request('status') == 'nonaktif' ? 'selected' : '' }}>Nonaktif</option>
                </select>
                <select name="per_page" onchange="this.form.submit()" title="Baris per halaman"
                    class="rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs py-2 px-3 text-slate-700 dark:text-slate-200 outline-none">
                    @foreach([10, 25, 50, 100] as $n)
                        <option value="{{ $n }}" {{ (int) request('per_page', 10) === $n ? 'selected' : '' }}>{{ $n }}/hal</option>
                    @endforeach
                </select>
                @if(request()->anyFilled(['q', 'status']))
                    <a href="{{ route('admin.kategori-tagihan', request()->only(['sort', 'dir'])) }}" class="text-xs text-rose-500 hover:underline">Reset</a>
                @endif
            </form>
        </div>
    </x-slot>

    <x-slot name="head">
        <x-ss-th sort="code" label="Kode" baseUrl="{{ route('admin.kategori-tagihan') }}" />
        <x-ss-th sort="name" label="Nama Tagihan" baseUrl="{{ route('admin.kategori-tagihan') }}" />
        <x-ss-th sort="amount" label="Nominal" baseUrl="{{ route('admin.kategori-tagihan') }}" />
        <x-ss-th sort="type" label="Tipe" baseUrl="{{ route('admin.kategori-tagihan') }}" />
        <x-ss-th sort="status" label="Status" align="center" baseUrl="{{ route('admin.kategori-tagihan') }}" />
        <th class="py-2.5 px-3 text-right">Aksi</th>
    </x-slot>

    @forelse($categories ?? [] as $cat)
    <tr class="hover:bg-slate-50 dark:hover:bg-slate-800 transition">
        <td class="py-3 px-3 font-bold text-blue-600 dark:text-blue-400">{{ $cat->code }}</td>
        <td class="py-3 px-3 font-medium text-slate-800 dark:text-white">
            <div>{{ $cat->name }}</div>
            @if($cat->description)
                <div class="text-[11px] text-slate-400">{{ $cat->description }}</div>
            @endif
        </td>
        <td class="py-3 px-3 font-bold text-slate-700 dark:text-slate-300">
            Rp {{ number_format($cat->default_amount, 0, ',', '.') }}
        </td>
        <td class="py-3 px-3 text-slate-500 capitalize">{{ $cat->type }}</td>
        <td class="py-3 px-3 text-center">
            <x-ss-pill :status="$cat->is_active ? 'aktif' : 'nonaktif'" :label="$cat->is_active ? 'Aktif' : 'Nonaktif'" />
        </td>
        <td class="py-3 px-3 text-right">
            <div class="flex items-center justify-end gap-1.5">
                <button type="button" title="Ubah {{ $cat->name }}"
                    onclick="openCategoryEdit({{ $cat->id }}, this)"
                    data-id="{{ $cat->id }}"
                    data-code="{{ $cat->code }}"
                    data-name="{{ $cat->name }}"
                    data-amount="{{ (int) $cat->default_amount }}"
                    data-type="{{ $cat->type }}"
                    data-desc="{{ $cat->description ?? '' }}"
                    class="rounded-md border border-slate-200 dark:border-slate-700 px-2 py-1 text-[11px] font-semibold text-slate-600 dark:text-slate-300 hover:bg-slate-50">Edit</button>
                <form action="{{ route('admin.kategori-tagihan.toggle', $cat->id) }}" method="POST" class="inline">
                    @csrf
                    <button type="submit" title="{{ $cat->is_active ? 'Nonaktifkan' : 'Aktifkan' }} {{ $cat->name }}"
                        class="rounded-md border border-slate-200 dark:border-slate-700 px-2 py-1 text-[11px] font-semibold text-slate-600 dark:text-slate-300 hover:bg-slate-50">
                        {{ $cat->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                    </button>
                </form>
            </div>
        </td>
    </tr>
    @empty
    <tr>
        <td colspan="6" class="py-6 text-center text-slate-400">
            @if(request()->anyFilled(['q', 'status']))
                Tidak cocok dengan filter. <a href="{{ route('admin.kategori-tagihan') }}" class="text-blue-600 hover:underline">Tampilkan semua</a>.
            @else
                Belum ada kategori tagihan. Klik <strong>Tambah Kategori</strong>.
            @endif
        </td>
    </tr>
    @endforelse

    @if(isset($categories) && method_exists($categories, 'hasPages') && $categories->hasPages())
    <x-slot name="pagination">
        {{ $categories->links() }}
    </x-slot>
    @endif

    <x-slot name="footerNote">
        <div class="flex items-center gap-2 rounded-xl bg-amber-50 dark:bg-amber-950 border border-amber-200 dark:border-amber-800 p-3 text-xs text-amber-800 dark:text-amber-300">
            <svg class="w-4 h-4 shrink-0 text-amber-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
            <span>Kategori aktif langsung bisa dipakai di Billing Generator.</span>
        </div>
    </x-slot>
</x-ss-table>

{{-- Live search khusus tabel ini --}}
<script>
(function () {
    var input = document.getElementById('kategori-search');
    if (!input) return;
    var card = input.closest('.ss-card');
    var tbody = card ? card.querySelector('tbody') : null;
    var pager = card ? card.querySelector('#ss-pagination') : null;
    var timer = null;

    function applyFilter() {
        if (!tbody) return;
        var q = input.value.trim().toLowerCase();
        var filtering = q !== '';
        var visible = 0;
        tbody.querySelectorAll('tr').forEach(function (tr) {
            if (tr.querySelector('td[colspan]')) { tr.style.display = 'none'; return; }
            var hit = !filtering || (tr.innerText || '').toLowerCase().indexOf(q) !== -1;
            tr.style.display = hit ? '' : 'none';
            if (hit) visible++;
        });
        if (pager) pager.style.display = filtering ? 'none' : '';
        var old = tbody.querySelector('[data-live-empty]');
        if (old) old.remove();
        if (visible === 0 && filtering) {
            var tr = document.createElement('tr');
            tr.setAttribute('data-live-empty', '1');
            tr.innerHTML = '<td colspan="6" class="py-6 text-center text-slate-400">Tidak ada hasil untuk pencarian ini.</td>';
            tbody.appendChild(tr);
        }
    }

    input.addEventListener('input', function () {
        clearTimeout(timer);
        timer = setTimeout(applyFilter, 120);
    });
    input.closest('form').addEventListener('submit', function (e) { e.preventDefault(); applyFilter(); });
})();
</script>
