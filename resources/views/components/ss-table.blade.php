{{-- ============================================================
  Komponen tabel global SmartSchool
  Dipakai: Data Siswa, Laporan, Kategori Tagihan, dst.

  Cara pakai:
    <x-ss-table>
        <x-slot name="filters"> ... form/baris filter ... </x-slot>
        <x-slot name="head"> <th>...</th><th>...</th> </x-slot>
        @forelse($data as $row)
            <tr>...</tr>
        @empty
            <tr><td colspan="6" class="...">Kosong.</td></tr>
        @endforelse
        <x-slot name="pagination"> {{ $data->links() }} </x-slot>
    </x-ss-table>

  Slot:
    - filters     : opsional, form/filter di atas tabel
    - head        : WAJIB, deretan <th> (tanpa <tr>/<thead>)
    - (default)   : WAJIB, baris-baris <tr> (pakai @forelse + @empty sendiri)
    - pagination  : opsional, link pagination di bawah tabel
    - footerNote  : opsional, catatan/info di bawah tabel
------------------------------------------------------------ --}}

<div class="ss-card space-y-4 shadow-xs">
    @isset($filters)
        {{ $filters }}
    @endisset

    <div class="overflow-x-auto -mx-4 px-4 sm:mx-0 sm:px-0">
        <table class="w-full min-w-[640px] text-left text-xs">
            <thead>
                <tr class="border-b border-slate-200 dark:border-slate-800 text-slate-400 font-semibold uppercase tracking-wider text-[10px]">
                    {{ $head }}
                </tr>
            </thead>
            <tbody id="ss-table-body" class="divide-y divide-slate-200 dark:divide-slate-800">
                {{ $slot }}
            </tbody>
        </table>
    </div>

    @isset($pagination)
        <div id="ss-pagination" class="pt-3 border-t border-slate-200 dark:border-slate-800 text-xs">
            {{ $pagination }}
        </div>
    @endisset

    @isset($footerNote)
        {{ $footerNote }}
    @endisset
</div>
