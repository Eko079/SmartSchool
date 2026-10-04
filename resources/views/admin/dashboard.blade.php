@extends('layouts.admin')
@section('title', 'Beranda')
{{-- Judul sapaan personal dirender otomatis di topbar (pakai nama akun login). --}}
@section('page-subtitle', 'SmartSchool ERP untuk tata kelola & pembayaran terpadu.')

@section('content')
    @include('admin.dashboard.stats')
    @include('admin.dashboard.charts')

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
        @include('admin.dashboard.recent-payments')
        @include('admin.dashboard.activity')
    </div>
@endsection
