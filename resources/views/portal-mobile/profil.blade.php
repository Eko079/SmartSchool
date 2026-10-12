@extends('layouts.portal-mobile')
@section('title', 'Profile')

@section('hero')
    <div class="m-rev-row"><div class="m-rev-value" style="font-size:26px">Profile</div></div>
@endsection

@section('content')
    <div class="m-card">
        <div class="m-sec-head" style="margin-bottom:8px"><div class="m-sec-title">Profile</div></div>
        <div class="m-kv"><span>NIS</span><b>{{ $student->nis ?? '-' }}</b></div>
        <div class="m-kv"><span>NISN</span><b>{{ $student->nisn ?? '-' }}</b></div>
        <div class="m-kv"><span>Email</span><b style="word-break:break-all">{{ $student->email ?? '-' }}</b></div>
        <div class="m-kv"><span>No. WA</span><b>{{ $student->guardian_phone ?? '-' }}</b></div>
        <div class="m-kv"><span>Alamat</span><b>{{ $student->address ?? '-' }}</b></div>
        <div class="m-kv"><span>Tahun Masuk</span><b>{{ $student->entry_year ?? '-' }}</b></div>
        <div class="m-kv"><span>Status Siswa</span><b>{{ ucfirst($student->status) }}</b></div>
    </div>
@endsection
