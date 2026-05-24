<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Email Terverifikasi — Pentasera</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .card {
            background: white;
            border-radius: 20px;
            padding: 48px 40px;
            max-width: 480px;
            width: 100%;
            text-align: center;
            box-shadow: 0 20px 60px rgba(0,0,0,0.15);
            animation: slideUp 0.5s ease-out;
        }
        @keyframes slideUp {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .icon {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, #10b981, #059669);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 24px;
            font-size: 40px;
            animation: popIn 0.6s ease-out 0.3s both;
        }
        @keyframes popIn {
            from { transform: scale(0); }
            to { transform: scale(1); }
        }
        h1 {
            color: #1e293b;
            font-size: 24px;
            font-weight: 700;
            margin-bottom: 12px;
        }
        p {
            color: #64748b;
            font-size: 15px;
            line-height: 1.6;
            margin-bottom: 24px;
        }
        .brand {
            color: #6366f1;
            font-weight: 700;
        }
        .info-box {
            background: #f0fdf4;
            border: 1px solid #bbf7d0;
            border-radius: 12px;
            padding: 16px;
            margin-bottom: 24px;
        }
        .info-box p {
            color: #166534;
            font-size: 14px;
            margin: 0;
        }
        .footer {
            color: #94a3b8;
            font-size: 12px;
        }
    </style>
</head>
<body>
    <div class="card">
        <div class="icon">✅</div>
        <h1>Email Berhasil Diverifikasi!</h1>
        <p>
            Selamat! Akun <span class="brand">Pentasera</span> Anda telah diverifikasi.
            Anda sekarang bisa login dan mulai menggunakan semua fitur.
        </p>
        <div class="info-box">
            <p>📱 Silakan buka aplikasi Pentasera dan login dengan akun Anda.</p>
        </div>
        <p class="footer">
            &copy; {{ date('Y') }} Pentasera. All rights reserved.
        </p>
    </div>
</body>
</html>
