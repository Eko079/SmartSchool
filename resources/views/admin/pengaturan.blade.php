@extends('layouts.admin')
@section('title', 'Pengaturan')
@section('breadcrumb', 'Pengaturan / Umum')
@section('page-title', 'Pengaturan')
@section('page-subtitle', 'Profil sekolah, tahun ajaran & pembayaran.')

@section('topbar-actions')
<button type="button" title="Simpan" onclick="document.getElementById('settings-form').submit()"
    class="ss-tip ss-tip-bottom flex items-center gap-1.5 rounded-lg bg-blue-600 px-3.5 py-2 text-xs font-semibold text-white hover:bg-blue-700 shadow-xs"
    data-tip="Simpan">
    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M19 21H5a2 2 0 01-2-2V5a2 2 0 012-2h11l5 5v11a2 2 0 01-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
    Simpan Perubahan
</button>
@endsection

@section('content')
    <form id="settings-form" method="POST" action="{{ route('admin.pengaturan.update') }}" class="grid grid-cols-1 lg:grid-cols-2 gap-4">
        @csrf
        @include('admin.pengaturan.profile')
        @include('admin.pengaturan.payment-security')
    </form>
@endsection
