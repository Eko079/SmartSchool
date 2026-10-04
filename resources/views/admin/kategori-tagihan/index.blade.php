@extends('layouts.admin')
@section('title', 'Kategori Tagihan')
@section('breadcrumb', 'Master Data / Kategori Tagihan')
@section('page-title', 'Master Kategori Tagihan')
@section('page-subtitle', 'Parameter nominal dasar billing generator.')

@section('topbar-actions')
<button type="button" title="Tambah kategori" onclick="document.getElementById('create-category-modal').classList.remove('hidden')"
    class="ss-tip ss-tip-bottom flex items-center gap-1.5 rounded-lg bg-blue-600 px-3.5 py-2 text-xs font-semibold text-white hover:bg-blue-700 shadow-xs"
    data-tip="Tambah kategori">
    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
    Tambah Kategori
</button>
@endsection

@section('content')
    @include('admin.kategori-tagihan.cards')
    @include('admin.kategori-tagihan.table')
    @include('admin.kategori-tagihan.create-modal')
    @include('admin.kategori-tagihan.edit-modal')
@endsection
