<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>
        @if($tujuan === 'reset_password')
            Kode Reset Password - TanamKu
        @else
            Kode Verifikasi Email - TanamKu
        @endif
    </title>
    <style>
        body {
            font-family: Arial, Helvetica, sans-serif;
            background-color: #f5f5f5;
            margin: 0;
            padding: 20px;
        }
        .container {
            max-width: 500px;
            margin: 0 auto;
            background: #ffffff;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0,0,0,0.08);
        }
        .header {
            background-color: #16a34a;
            padding: 28px 32px;
            text-align: center;
        }
        .header h1 {
            color: #ffffff;
            margin: 0;
            font-size: 24px;
            letter-spacing: 0.5px;
        }
        .header p {
            color: #bbf7d0;
            margin: 4px 0 0;
            font-size: 13px;
        }
        .body {
            padding: 32px;
        }
        .greeting {
            font-size: 16px;
            color: #374151;
            margin-bottom: 16px;
        }
        .desc {
            font-size: 14px;
            color: #6b7280;
            line-height: 1.6;
            margin-bottom: 24px;
        }
        .otp-box {
            background: #f0fdf4;
            border: 2px dashed #16a34a;
            border-radius: 10px;
            text-align: center;
            padding: 20px;
            margin-bottom: 24px;
        }
        .otp-label {
            font-size: 12px;
            color: #6b7280;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 8px;
        }
        .otp-code {
            font-size: 38px;
            font-weight: bold;
            letter-spacing: 10px;
            color: #15803d;
        }
        .otp-expire {
            font-size: 12px;
            color: #9ca3af;
            margin-top: 10px;
        }
        .warning {
            background: #fffbeb;
            border-left: 4px solid #f59e0b;
            padding: 12px 16px;
            border-radius: 4px;
            font-size: 13px;
            color: #92400e;
            margin-bottom: 24px;
        }
        .footer {
            padding: 20px 32px;
            border-top: 1px solid #e5e7eb;
            text-align: center;
            font-size: 12px;
            color: #9ca3af;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🌿 TanamKu</h1>
            <p>Marketplace Tanaman Hias</p>
        </div>
        <div class="body">
            <p class="greeting">Hai, <strong>{{ $namaUser }}</strong>!</p>

            @if($tujuan === 'reset_password')
                <p class="desc">
                    Kami menerima permintaan reset password untuk akun TanamKu yang terhubung dengan email ini.
                    Gunakan kode berikut untuk membuat password baru:
                </p>
            @else
                <p class="desc">
                    Terima kasih telah mendaftar di TanamKu! Masukkan kode verifikasi berikut
                    untuk mengonfirmasi alamat email Anda:
                </p>
            @endif

            <div class="otp-box">
                <div class="otp-label">Kode Verifikasi</div>
                <div class="otp-code">{{ $otp }}</div>
                <div class="otp-expire">Berlaku selama 10 menit</div>
            </div>

            <div class="warning">
                ⚠️ Jangan bagikan kode ini kepada siapapun, termasuk tim TanamKu.
                Jika Anda tidak merasa meminta kode ini, abaikan email ini.
            </div>

            <p class="desc">Salam hangat,<br><strong>Tim TanamKu</strong></p>
        </div>
        <div class="footer">
            © {{ date('Y') }} TanamKu — Marketplace Tanaman Hias Indonesia
        </div>
    </div>
</body>
</html>

