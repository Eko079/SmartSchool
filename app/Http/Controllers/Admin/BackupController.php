<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SchoolSetting;
use Illuminate\Http\Request;
use Symfony\Component\Process\Process;

class BackupController extends Controller
{
    public function index()
    {
        $last = SchoolSetting::get('last_backup_at');

        return view('admin.backup.index', compact('last'));
    }

    public function download()
    {
        $db = config('database.connections.' . config('database.default'));
        if (($db['driver'] ?? '') !== 'mysql') {
            return back()->with('error', 'Backup otomatis hanya untuk database MySQL.');
        }

        $file = 'smartschool-' . date('Ymd-His') . '.sql';
        $cmd = ['mysqldump', '-h', $db['host'] ?? '127.0.0.1', '-P', (string) ($db['port'] ?? 3306),
            '-u', $db['username'] ?? 'root', '--single-transaction', $db['database']];
        $process = new Process($cmd, null, ['MYSQL_PWD' => $db['password'] ?? ''], null, 300);
        $process->run();

        if (! $process->isSuccessful()) {
            return back()->with('error', 'Backup gagal: mysqldump tidak tersedia atau akses ditolak.');
        }

        SchoolSetting::set('last_backup_at', date('d/m/Y H:i'));
        \App\Models\AuditLog::log('backup.unduh', "Unduh backup {$file}");

        return response()->streamDownload(function () use ($process) {
            echo $process->getOutput();
        }, $file, ['Content-Type' => 'application/sql']);
    }
}
