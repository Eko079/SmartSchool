@extends('layouts.admin')
@section('title', 'Kategori Tagihan')
@section('breadcrumb', 'Master Data / Kategori Tagihan')
@section('page-title', 'Master Kategori Tagihan')
@section('page-subtitle', 'Parameter nominal dasar billing generator.')

@section('content')
    @include('admin.kategori-tagihan.cards')
    @include('admin.kategori-tagihan.table')
    @include('admin.kategori-tagihan.create-modal')
    @include('admin.kategori-tagihan.edit-modal')
@endsection
