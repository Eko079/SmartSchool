<!DOCTYPE html>
<html lang="id" class="">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Login Portal | {{ $siteBrand['school_name'] ?? 'SmartSchool' }}</title>
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/mobile.css', 'resources/js/mobile.js'])
</head>
<body class="m-body">
    <div class="m-shell" style="background:linear-gradient(-167deg, #0F1E33 7%, #1D4ED8 93%);min-height:100vh">
        <div class="m-hero m-auth-hero">
            <div class="m-glow m-glow-1"></div>
            <div class="m-glow m-glow-2"></div>
            <div class="m-toprow">
                <div class="m-avatar">{{ strtoupper(substr($siteBrand['school_name'] ?? 'S', 0, 1)) }}</div>
                <div class="m-meta">
                    <div class="m-name">{{ $siteBrand['school_name'] ?? 'SmartSchool' }} • Portal</div>
                </div>
                <a href="{{ route('portal.bantuan') }}" class="m-see" style="color:#BFDBFE">Bantuan</a>
            </div>
            <div class="m-rev-label">SISWA & ORANG TUA</div>
            <div class="m-rev-row"><div class="m-rev-value" style="font-size:24px">Selamat Datang Kembali</div></div>
            <div class="m-rev-sub">Masuk untuk lihat tagihan & riwayat ananda.</div>
        </div>
        <main class="m-main m-auth-main">
            <div class="m-card m-auth-card">
                @if(session('status'))
                    <div class="m-fdesc" style="color:#15803D">{{ session('status') }}</div>
                @endif
                @if($errors->any())
                    <div class="m-fdesc" style="color:#DC2626">{{ $errors->first() }}</div>
                @endif
                <form method="POST" action="{{ route('portal.login.attempt') }}">
                    @csrf
                    <label class="m-label">NIS / Email</label>
                    <input type="text" name="identifier" value="{{ old('identifier') }}" class="m-input" placeholder="cth: 20261001 / wali@email.id" required autofocus>
                    <label class="m-label" style="margin-top:8px">Kata Sandi</label>
                    <input type="password" name="password" class="m-input" placeholder="••••••••••" required>
                    <div class="m-actions" style="margin-top:10px">
                        <label class="m-stu-sub"><input type="checkbox" name="remember"> Ingat saya</label>
                        <button type="button" class="m-btn" onclick="document.getElementById('m-otp').classList.toggle('hidden')">Lupa sandi?</button>
                    </div>
                    <div class="m-actions" style="margin-top:10px">
                        <button type="submit" class="m-btn m-btn-primary" style="flex:1">Masuk ke Portal</button>
                    </div>
                </form>
            </div>

            <div id="m-otp" class="m-card hidden">
                <div class="m-sec-head"><div class="m-sec-title">Lupa Kata Sandi</div></div>
                <div class="m-fdesc">Masuk email wali, terima OTP 6 digit, buat sandi baru.</div>
                <form method="POST" action="{{ route('portal.otp') }}">
                    @csrf
                    <input type="text" name="identifier" class="m-input" placeholder="Email / No. WA wali" required>
                    <div class="m-actions" style="margin-top:8px"><button type="submit" class="m-btn">Kirim OTP</button></div>
                </form>
                <form method="POST" action="{{ route('portal.reset') }}" style="margin-top:10px">
                    @csrf
                    <input type="text" name="identifier" class="m-input" placeholder="Email / No. WA wali" required>
                    <input type="text" name="code" class="m-input" style="margin-top:8px" placeholder="OTP 6 digit" maxlength="6" required>
                    <input type="password" name="password" class="m-input" style="margin-top:8px" placeholder="Sandi baru min. 8 karakter" required>
                    <input type="password" name="password_confirmation" class="m-input" style="margin-top:8px" placeholder="Ulangi sandi baru" required>
                    <div class="m-actions" style="margin-top:8px"><button type="submit" class="m-btn m-btn-primary">Simpan Sandi Baru</button></div>
                </form>
                <div class="m-fdesc">OTP berlaku 5 menit • salah 5x kunci 15 menit.</div>
            </div>

            <div class="m-foot"><div class="m-foot-tx" style="color:#93A4C4">© {{ date('Y') }} {{ $siteBrand['school_name'] ?? 'SmartSchool' }} • {{ $siteBrand['version'] ?? 'v2.4.1' }}</div></div>
            <div class="m-bottompad"></div>
        </main>
    </div>
</body>
</html>
