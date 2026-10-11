{{-- Popup notifikasi mobile: pengingat jatuh tempo + denda --}}
@php $nf = $notifFeed ?? ['count' => 0, 'items' => []]; @endphp
<div id="m-notif-pop" class="m-modal hidden" role="dialog" aria-modal="true" aria-label="Notifikasi">
    <div class="m-modal-card">
        <div class="m-modal-head">
            <div class="m-stu-mid">
                <div class="m-stu-name">Notifikasi</div>
                <div class="m-stu-sub">{{ $nf['count'] }} pengingat jatuh tempo & denda</div>
            </div>
            <button type="button" class="m-modal-x" onclick="document.getElementById('m-notif-pop').classList.add('hidden')" aria-label="Tutup">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        @forelse($nf['items'] as $n)
        <a href="{{ $n['url'] }}" class="m-feed" style="text-decoration:none;color:inherit">
            <div class="m-fav" style="color:{{ $n['kind'] === 'overdue' ? '#EF4444' : ($n['kind'] === 'reminder' ? '#F59E0B' : '#2563EB') }}">•</div>
            <div class="m-fmid">
                <div class="m-fname">{{ $n['title'] }}</div>
                <div class="m-fdesc">{{ $n['desc'] }}</div>
            </div>
        </a>
        @empty
        <div class="m-fdesc" style="text-align:center;padding:16px">Tidak ada pengingat. Semua tagihan aman.</div>
        @endforelse
        <div class="m-actions">
            <button type="button" class="m-btn" onclick="document.getElementById('m-notif-pop').classList.add('hidden')">Tutup</button>
        </div>
    </div>
</div>
<script>
function toggleNotifPop() {
    var el = document.getElementById('m-notif-pop');
    if (el) el.classList.toggle('hidden');
}
(function () {
    var el = document.getElementById('m-notif-pop');
    if (el) el.addEventListener('click', function (e) { if (e.target === this) this.classList.add('hidden'); });
})();
</script>
