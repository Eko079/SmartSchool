<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use Illuminate\Http\Request;

class AuditController extends Controller
{
    public function index(Request $request)
    {
        $query = AuditLog::with('user')->orderBy('id', 'desc');

        if ($request->filled('aksi')) {
            $query->where('aksi', $request->input('aksi'));
        }
        if ($request->filled('q')) {
            $q = $request->input('q');
            $query->where(function ($sub) use ($q) {
                $sub->where('detail', 'like', "%{$q}%")
                    ->orWhereHas('user', fn ($u) => $u->where('name', 'like', "%{$q}%"));
            });
        }
        if ($request->filled('tanggal')) {
            $query->whereDate('created_at', $request->input('tanggal'));
        }

        $logs = $query->paginate(25)->withQueryString();
        $aksiList = ['kasir.bayar', 'kategori.tambah', 'kategori.ubah', 'kategori.status', 'siswa.hapus', 'siswa.massal',
            'siswa.generate', 'siswa.import', 'billing.generate', 'billing.paket', 'billing.denda',
            'akun.tambah', 'akun.reset', 'akun.status', 'pengaturan.ubah', 'kelas.tambah', 'kelas.ubah', 'kelas.hapus', 'backup.unduh'];

        return view('admin.audit.index', compact('logs', 'aksiList'));
    }
}
