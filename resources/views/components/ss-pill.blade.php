{{-- Pill status global: aktif/nonaktif/lulus, lunas/menunggu, dsb.
  Contoh: <x-ss-pill status="aktif" />  /  <x-ss-pill status="paid" label="Lunas" />
--}}
@props(['status', 'label' => null])

@php
    $key = strtolower((string) $status);
    $map = [
        // Status siswa / kategori
        'aktif' => 'pill-success', 'lulus' => 'pill-success', 'active' => 'pill-success',
        'nonaktif' => 'pill-danger', 'inactive' => 'pill-danger',
        // Status tagihan / pembayaran
        'paid' => 'pill-success', 'lunas' => 'pill-success', 'success' => 'pill-success', 'selesai' => 'pill-success',
        'unpaid' => 'pill-warning', 'partial' => 'pill-warning',
        'pending' => 'pill-warning', 'menunggu' => 'pill-warning',
        'overdue' => 'pill-danger', 'failed' => 'pill-danger', 'gagal' => 'pill-danger',
    ];
    $cls = $map[$key] ?? 'pill-warning';
    $dot = str_contains($cls, 'success') ? 'bg-emerald-500' : (str_contains($cls, 'danger') ? 'bg-rose-500' : 'bg-amber-500');
    $text = $label ?? ucfirst((string) $status);
@endphp

<span {{ $attributes->merge(['class' => "pill $cls text-[10px] !py-0.5"]) }}>
    <span class="w-1.5 h-1.5 rounded-full {{ $dot }}"></span>
    {{ $text }}
</span>
