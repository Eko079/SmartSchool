@extends('layouts.admin')
@section('title', 'Pusat Bantuan')
@section('breadcrumb', 'Lainnya / Bantuan')
@section('page-title', 'Pusat Bantuan')
@section('page-subtitle', 'Panduan pemakaian tiap menu & kontak.')

@section('content')
    @include('admin.bantuan.hero-guides')
    @include('admin.bantuan.faq-ticket')
@endsection
