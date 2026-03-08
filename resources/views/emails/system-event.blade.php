<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $mailSubject ?? config('app.name') }}</title>
</head>
<body style="margin:0; padding:0; background:#f3f6fb; font-family:Arial, sans-serif; color:#1f2d3d;">
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="padding:24px 12px;">
        <tr>
            <td align="center">
                <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="max-width:600px; background:#ffffff; border-radius:10px; overflow:hidden; border:1px solid #e4eaf2;">
                    <tr>
                        <td style="background:#2563eb; color:#ffffff; padding:16px 20px; font-size:18px; font-weight:700;">
                            {{ config('app.name') }}
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:20px;">
                            @if(!empty($recipientName))
                                <p style="margin:0 0 12px;">Halo {{ $recipientName }},</p>
                            @endif

                            <h2 style="margin:0 0 10px; font-size:20px; line-height:1.35; color:#0f172a;">{{ $headline }}</h2>
                            <p style="margin:0 0 16px; line-height:1.6; color:#334155; white-space:pre-line;">{{ $body }}</p>

                            @if(!empty($actionUrl) && !empty($actionLabel))
                                <p style="margin:0 0 18px;">
                                    <a href="{{ $actionUrl }}" style="display:inline-block; background:#2563eb; color:#ffffff; text-decoration:none; padding:10px 16px; border-radius:6px; font-weight:600;">
                                        {{ $actionLabel }}
                                    </a>
                                </p>
                            @endif

                            @if(!empty($footerNote))
                                <p style="margin:0; color:#64748b; font-size:12px;">{{ $footerNote }}</p>
                            @endif
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
