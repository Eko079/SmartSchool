{{-- Popup notifikasi bell: pengingat jatuh tempo + denda menunggak (tanpa halaman baru) --}}
@php $nf = $notifFeed ?? ['count' => 0, 'items' => []]; @endphp
<div id="ss-notif-pop" class="hidden fixed inset-0 z-[70]" role="dialog" aria-modal="true" aria-label="Notifikasi">
    <div class="absolute inset-0 bg-black/40" onclick="document.getElementById('ss-notif-pop').classList.add('hidden')"></div>
    <div class="absolute right-4 top-20 w-[min(92vw,380px)] ss-card !p-0 overflow-hidden shadow-xl">
        <div class="flex items-center justify-between px-4 py-3 border-b border-slate-200 dark:border-slate-700">
            <div>
                <div class="text-sm font-bold text-slate-800 dark:text-white">Notifikasi</div>
                <div class="text-[11px] text-slate-400">{{ $nf['count'] }} pengingat jatuh tempo & denda</div>
            </div>
            <button type="button" onclick="document.getElementById('ss-notif-pop').classList.add('hidden')" aria-label="Tutup"
                class="p-2 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-800 text-slate-400">✕</button>
        </div>
        <div class="max-h-[60vh] overflow-y-auto divide-y divide-slate-100 dark:divide-slate-800">
            @forelse($nf['items'] as $n)
            <a href="{{ $n['url'] }}" class="flex gap-3 px-4 py-3 hover:bg-slate-50 dark:hover:bg-slate-800 transition">
                <span class="mt-1 h-2.5 w-2.5 shrink-0 rounded-full {{ $n['kind'] === 'overdue' ? 'bg-rose-500' : ($n['kind'] === 'reminder' ? 'bg-amber-500' : 'bg-blue-500') }}"></span>
                <span>
                    <span class="block text-xs font-bold text-slate-800 dark:text-white">{{ $n['title'] }}</span>
                    <span class="block text-[11px] text-slate-500 dark:text-slate-400 mt-0.5">{{ $n['desc'] }}</span>
                </span>
            </a>
            @empty
            <div class="px-4 py-6 text-center text-xs text-slate-400">Tidak ada pengingat. Semua tagihan aman.</div>
            @endforelse
        </div>
    </div>
</div>
<script>
function toggleNotifPop() {
    var el = document.getElementById('ss-notif-pop');
    if (el) el.classList.toggle('hidden');
}
document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape') {
        var el = document.getElementById('ss-notif-pop');
        if (el) el.classList.add('hidden');
        var m = document.getElementById('m-notif-pop');
        if (m) m.classList.add('hidden');
    }
});
</script>
