<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Terjadi Kesalahan')</title>
    <style>
        :root {
            --bg: #f4f7fb;
            --card: #ffffff;
            --text: #1f2937;
            --muted: #6b7280;
            --primary: #0f766e;
            --danger: #b91c1c;
            --border: #e5e7eb;
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            min-height: 100vh;
            font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
            background: radial-gradient(circle at top right, #dbeafe 0%, var(--bg) 45%);
            color: var(--text);
            display: grid;
            place-items: center;
            padding: 16px;
        }

        .card {
            width: 100%;
            max-width: 560px;
            background: var(--card);
            border: 1px solid var(--border);
            border-radius: 14px;
            padding: 28px;
            box-shadow: 0 16px 35px rgba(15, 23, 42, 0.08);
        }

        .status {
            font-size: 13px;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            color: var(--danger);
            font-weight: 700;
            margin-bottom: 10px;
        }

        h1 {
            font-size: 28px;
            margin: 0 0 12px;
        }

        p {
            margin: 0;
            line-height: 1.6;
            color: var(--muted);
        }

        .actions {
            margin-top: 22px;
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 10px;
            padding: 10px 14px;
            text-decoration: none;
            font-weight: 600;
            border: 1px solid transparent;
        }

        .btn-primary {
            background: var(--primary);
            color: #ffffff;
        }

        .btn-secondary {
            border-color: var(--border);
            color: var(--text);
            background: #ffffff;
        }

        .code {
            margin-top: 16px;
            font-size: 13px;
            color: var(--muted);
        }
    </style>
</head>
<body>
    <main class="card" role="main" aria-live="polite">
        <div class="status">Error @yield('status', '500')</div>
        <h1>@yield('heading', 'Terjadi gangguan pada sistem')</h1>
        <p>@yield('message', 'Silakan coba beberapa saat lagi.')</p>

        <div class="actions">
            <a href="{{ url()->previous() }}" class="btn btn-secondary">Kembali</a>
            <a href="{{ url('/dashboard') }}" class="btn btn-primary">Ke Dashboard</a>
        </div>

        @if(!empty($errorCode))
            <p class="code">Kode referensi: {{ $errorCode }}</p>
        @endif
    </main>
</body>
</html>
