@extends('layouts.portal')
@section('title', 'Profile')
@section('page-title', 'Profile')

@section('content')
    <div class="ss-card space-y-3">
        <div class="flex flex-wrap items-center gap-3">
            <div class="flex h-12 w-12 items-center justify-center rounded-full bg-blue-600 text-lg font-bold text-white">{{ strtoupper(substr($student->name, 0, 1)) }}</div>
            <h2 class="text-sm font-bold">{{ $student->name }}</h2>
            <span class="ml-auto"><x-ss-pill status="{{ $student->status }}" label="{{ ucfirst($student->status) }}" /></span>
        </div>
        <dl class="grid grid-cols-2 sm:grid-cols-3 gap-3 text-xs">
            <div class="rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 p-3">
                <dt class="text-[10px] font-bold uppercase tracking-wider text-slate-400">NIS</dt>
                <dd class="mt-1 font-semibold">{{ $student->nis }}</dd>
            </div>
            <div class="rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 p-3">
                <dt class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Kelas</dt>
                <dd class="mt-1 font-semibold">{{ $student->classRoom->name ?? '-' }}</dd>
            </div>
            <div class="rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 p-3">
                <dt class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Tahun Masuk</dt>
                <dd class="mt-1 font-semibold">{{ $student->entry_year ?? '-' }}</dd>
            </div>
            <div class="rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 p-3">
                <dt class="text-[10px] font-bold uppercase tracking-wider text-slate-400">NISN</dt>
                <dd class="mt-1 font-semibold">{{ $student->nisn ?? '-' }}</dd>
            </div>
            <div class="rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 p-3">
                <dt class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Email</dt>
                <dd class="mt-1 font-semibold break-all">{{ $student->email ?? '-' }}</dd>
            </div>
            <div class="rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 p-3">
                <dt class="text-[10px] font-bold uppercase tracking-wider text-slate-400">No. WA</dt>
                <dd class="mt-1 font-semibold">{{ $student->guardian_phone ?? '-' }}</dd>
            </div>
        </dl>
    </div>
@endsection
