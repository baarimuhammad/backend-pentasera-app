<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verifikasi Email — Pentasera</title>
</head>
<body style="margin: 0; padding: 0; background-color: #f4f6f9; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;">
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background-color: #f4f6f9; padding: 40px 0;">
        <tr>
            <td align="center">
                <!-- Main Container -->
                <table role="presentation" width="600" cellpadding="0" cellspacing="0" style="background-color: #ffffff; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 24px rgba(0,0,0,0.08);">

                    <!-- Header -->
                    <tr>
                        <td style="background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 50%, #a855f7 100%); padding: 40px 40px 32px; text-align: center;">
                            <h1 style="margin: 0; color: #ffffff; font-size: 28px; font-weight: 700; letter-spacing: -0.5px;">
                                ✦ Pentasera
                            </h1>
                            <p style="margin: 8px 0 0; color: rgba(255,255,255,0.85); font-size: 14px;">
                                Platform Event & Ticketing
                            </p>
                        </td>
                    </tr>

                    <!-- Body -->
                    <tr>
                        <td style="padding: 40px;">
                            <h2 style="margin: 0 0 8px; color: #1e293b; font-size: 22px; font-weight: 600;">
                                Verifikasi Email Anda
                            </h2>
                            <p style="margin: 0 0 24px; color: #64748b; font-size: 15px; line-height: 1.6;">
                                Halo <strong style="color: #1e293b;">{{ $user->nama }}</strong>,
                                <br><br>
                                Terima kasih telah mendaftar di Pentasera! Silakan klik tombol di bawah untuk memverifikasi alamat email Anda.
                            </p>

                            <!-- CTA Button -->
                            <table role="presentation" cellpadding="0" cellspacing="0" style="margin: 0 auto 24px;">
                                <tr>
                                    <td style="border-radius: 8px; background: linear-gradient(135deg, #6366f1, #8b5cf6);">
                                        <a href="{{ $verificationUrl }}"
                                           target="_blank"
                                           style="display: inline-block; padding: 14px 40px; color: #ffffff; text-decoration: none; font-size: 16px; font-weight: 600; letter-spacing: 0.3px;">
                                            ✉️ Verifikasi Email Saya
                                        </a>
                                    </td>
                                </tr>
                            </table>

                            <!-- Expiry Notice -->
                            <div style="background-color: #fef3c7; border-left: 4px solid #f59e0b; padding: 12px 16px; border-radius: 6px; margin-bottom: 24px;">
                                <p style="margin: 0; color: #92400e; font-size: 13px; line-height: 1.5;">
                                    ⏰ Link ini berlaku selama <strong>{{ $expiresInMinutes }} menit</strong>.
                                    Setelah expired, Anda bisa meminta link baru melalui aplikasi.
                                </p>
                            </div>

                            <!-- Fallback URL -->
                            <p style="margin: 0 0 8px; color: #94a3b8; font-size: 12px;">
                                Jika tombol tidak berfungsi, copy-paste link berikut di browser:
                            </p>
                            <p style="margin: 0; padding: 12px; background-color: #f1f5f9; border-radius: 6px; word-break: break-all; font-size: 11px; color: #6366f1; font-family: monospace;">
                                {{ $verificationUrl }}
                            </p>
                        </td>
                    </tr>

                    <!-- Divider -->
                    <tr>
                        <td style="padding: 0 40px;">
                            <hr style="border: none; border-top: 1px solid #e2e8f0; margin: 0;">
                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td style="padding: 24px 40px 32px; text-align: center;">
                            <p style="margin: 0 0 4px; color: #94a3b8; font-size: 12px;">
                                Anda menerima email ini karena mendaftar di Pentasera.
                            </p>
                            <p style="margin: 0; color: #94a3b8; font-size: 12px;">
                                Jika Anda tidak merasa mendaftar, abaikan email ini.
                            </p>
                            <p style="margin: 16px 0 0; color: #cbd5e1; font-size: 11px;">
                                &copy; {{ date('Y') }} Pentasera. All rights reserved.
                            </p>
                        </td>
                    </tr>

                </table>
            </td>
        </tr>
    </table>
</body>
</html>
