<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Rekap Pembayaran {{ $student->nis ?? '' }}</title>
<style>
  body { font-family: Inter, system-ui, sans-serif; color: #0f172a; margin: 0; padding: 24px; }
  .sheet { max-width: 760px; margin: 0 auto; border: 1px solid #e2e8f0; border-radius: 16px; padding: 24px 28px; }
  .kop { text-align: center; border-bottom: 3px double #0f1e33; padding-bottom: 12px; }
  .kop .brand { font-weight: 800; font-size: 20px; }
  .kop .muted { color: #64748b; font-size: 12px; }
  .kop .title { font-weight: 800; font-size: 15px; letter-spacing: 1px; margin-top: 10px; }
  .info { display: flex; flex-wrap: wrap; gap: 4px 32px; margin: 14px 0 4px; font-size: 13px; }
  .info div { min-width: 200px; }
  .info span { color: #64748b; }
  table { width: 100%; border-collapse: collapse; margin-top: 12px; font-size: 12.5px; }
  th { text-align: left; font-size: 11px; text-transform: uppercase; letter-spacing: .4px; color: #64748b; border-bottom: 2px solid #0f1e33; padding: 8px 6px; }
  td { padding: 8px 6px; border-bottom: 1px solid #f1f5f9; vertical-align: top; }
  td.num, th.num { text-align: right; white-space: nowrap; }
  .totalbar { display: flex; justify-content: space-between; align-items: baseline; gap: 12px; margin-top: 14px; padding-top: 12px; border-top: 2px solid #0f1e33; }
  .totalbar .val { font-size: 20px; font-weight: 800; }
  .terbilang { font-size: 12px; color: #475569; font-style: italic; margin-top: 4px; text-transform: capitalize; }
  .ttd { display: flex; justify-content: space-between; margin-top: 28px; font-size: 12px; text-align: center; }
  .actions { margin-top: 20px; display: flex; gap: 8px; }
  .btn { border: 1px solid #cbd5e1; border-radius: 10px; padding: 8px 14px; font-size: 12px; font-weight: 700; text-decoration: none; color: #0f172a; background: #fff; cursor: pointer; }
  .btn-primary { background: #2563eb; border-color: #2563eb; color: #fff; }
  @media print { .actions { display: none; } body { padding: 0; } .sheet { border: none; border-radius: 0; } }
</style>
</head>
<body>
<div class="sheet">
  <div class="kop">
    <div class="brand">{{ $school['school_name'] ?? 'SmartSchool' }}</div>
    <div class="muted">{{ $school['school_address'] ?? '' }}{{ ! empty($school['school_phone']) ? ' • ' . $school['school_phone'] : '' }}</div>
    <div class="title">REKAP RIWAYAT PEMBAYARAN</div>
    <div class="muted">Dicetak {{ now()->format('d M Y H:i') }}</div>
  </div>
  <div class="info">
    <div><span>Nama:</span> <b>{{ $student->name }}</b></div>
    <div><span>NIS:</span> <b>{{ $student->nis ?? '-' }}</b></div>
    <div><span>Kelas:</span> <b>{{ $student->classRoom->name ?? '-' }}</b></div>
    <div><span>Jumlah transaksi:</span> <b>{{ $payments->count() }}</b></div>
  </div>
  <table>
    <thead>
      <tr><th>No</th><th>Tanggal</th><th>No. Kuitansi</th><th>Tagihan</th><th>Cara Bayar</th><th>Status</th><th class="num">Jumlah</th></tr>
    </thead>
    <tbody>
      @forelse($payments as $i => $pay)
      <tr>
        <td>{{ $i + 1 }}</td>
        <td style="white-space:nowrap">{{ $pay->paid_at?->format('d/m/Y') ?? '-' }}</td>
        <td>{{ $pay->invoice_number }}</td>
        <td>{{ $pay->bill->display_name ?? '-' }}<br><span style="color:#64748b">{{ $pay->bill->bill_code ?? '' }}</span></td>
        <td>{{ $pay->method_label }}</td>
        <td>{{ $pay->status_label }}</td>
        <td class="num">Rp {{ number_format($pay->amount, 0, ',', '.') }}</td>
      </tr>
      @empty
      <tr><td colspan="7" style="text-align:center;color:#64748b">Belum ada riwayat pembayaran.</td></tr>
      @endforelse
    </tbody>
  </table>
  <div class="totalbar">
    <div>
      <div style="font-size:12px;color:#64748b">Total sudah dibayar ({{ $payments->where('status', 'success')->count() }} transaksi)</div>
      <div class="val">Rp {{ number_format($totalSukses, 0, ',', '.') }}</div>
      <div class="terbilang">{{ ucfirst($terbilang) }}</div>
    </div>
  </div>
  <div class="ttd">
    <div>Mengetahui,<br>Bendahara<br><br><br><br>( .................... )</div>
    <div><br>Wali Siswa<br><br><br><br>( .................... )</div>
  </div>
  <div class="actions">
    <button class="btn btn-primary" onclick="window.print()">Cetak / Simpan PDF</button>
    <a class="btn" href="{{ route('portal.riwayat') }}">Kembali</a>
  </div>
</div>
</body>
</html>
