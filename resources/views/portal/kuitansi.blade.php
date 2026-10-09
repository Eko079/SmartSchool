<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Kuitansi {{ $pay->invoice_number }}</title>
<style>
  body { font-family: Inter, system-ui, sans-serif; color: #0f172a; margin: 0; padding: 24px; }
  .sheet { max-width: 640px; margin: 0 auto; border: 1px solid #e2e8f0; border-radius: 16px; padding: 24px; }
  .head { display: flex; justify-content: space-between; align-items: center; border-bottom: 2px solid #0f1e33; padding-bottom: 12px; }
  .brand { font-weight: 800; font-size: 18px; }
  .muted { color: #64748b; font-size: 12px; }
  table { width: 100%; border-collapse: collapse; margin-top: 16px; font-size: 13px; }
  td { padding: 8px 0; border-bottom: 1px solid #f1f5f9; }
  td:last-child { text-align: right; font-weight: 700; }
  .total { font-size: 16px; }
  .stamp { margin-top: 16px; display: flex; justify-content: space-between; font-size: 12px; }
  .actions { margin-top: 16px; display: flex; gap: 8px; }
  .btn { border: 1px solid #cbd5e1; border-radius: 10px; padding: 8px 14px; font-size: 12px; font-weight: 700; text-decoration: none; color: #0f172a; }
  .btn-primary { background: #2563eb; border-color: #2563eb; color: #fff; }
  @media print { .actions { display: none; } body { padding: 0; } .sheet { border: none; } }
</style>
</head>
<body>
<div class="sheet">
  <div class="head">
    <div>
      <div class="brand">{{ $school['school_name'] ?? 'SmartSchool' }}</div>
      <div class="muted">{{ $school['school_address'] ?? '' }} • {{ $school['school_phone'] ?? '' }}</div>
    </div>
    <div style="text-align:right">
      <div class="brand">KUITANSI</div>
      <div class="muted">{{ $pay->invoice_number }}</div>
    </div>
  </div>
  <table>
    <tr><td>Siswa</td><td>{{ $pay->student->name ?? '-' }} ({{ $pay->student->classRoom->name ?? '-' }})</td></tr>
    <tr><td>Tagihan</td><td>{{ $pay->bill->bill_code ?? '-' }} • {{ $pay->bill->feeCategory->name ?? '-' }}</td></tr>
    <tr><td>TA / Semester</td><td>{{ $pay->bill->academic_year ?? '-' }} • {{ $pay->bill->semester ? ucfirst($pay->bill->semester) : '-' }}</td></tr>
    <tr><td>Metode</td><td>{{ $pay->method_label }}</td></tr>
    <tr><td>Tanggal Bayar</td><td>{{ $pay->paid_at?->format('d M Y H:i') ?? '-' }}</td></tr>
    <tr class="total"><td>Total Dibayar</td><td>{{ $rp($pay->amount) }}</td></tr>
  </table>
  <div class="stamp">
    <div class="muted">Status: {{ strtoupper($pay->status) }}</div>
    <div>Bendahara,<br><br><br>( .................... )</div>
  </div>
  <div class="actions">
    <button class="btn btn-primary" onclick="window.print()">Cetak / Simpan PDF</button>
    <a class="btn" href="{{ route('portal.riwayat') }}">Kembali</a>
  </div>
</div>
</body>
</html>
