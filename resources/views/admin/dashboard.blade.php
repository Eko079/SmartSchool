@extends('layouts.admin')
@section('title', 'Beranda')
@section('breadcrumb', 'Beranda')
{{-- Judul sapaan personal dirender otomatis di topbar (pakai nama akun login). --}}
@section('page-subtitle', 'SmartSchool ERP untuk tata kelola & pembayaran terpadu.')

@section('content')
    @include('admin.dashboard.stats')
    @include('admin.dashboard.charts')

    @include('admin.dashboard.recent-payments')
@endsection
