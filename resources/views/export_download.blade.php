<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Export Ready</title>
    <style>
        body{font-family:Arial,Helvetica,sans-serif;background:#0b1220;color:#fff;display:flex;align-items:center;justify-content:center;min-height:100vh;padding:24px}
        .wrapper{width:100%;max-width:640px}
        .card{background:#0f1724;padding:28px;border-radius:12px;box-shadow:0 10px 30px rgba(2,6,23,.6);width:100%}
        a.button{display:inline-block;padding:12px 18px;background:#3b82f6;color:#fff;border-radius:8px;text-decoration:none;font-weight:700}
        .meta{margin-top:12px;color:#9aa6c3}
        .alert{display:flex;align-items:flex-start;gap:10px;padding:14px 18px;border-radius:10px;margin-bottom:20px;font-size:14px;animation:alertSlideIn .3s ease-out}
        @keyframes alertSlideIn{from{opacity:0;transform:translateY(-10px)}to{opacity:1;transform:translateY(0)}}
        .alert-icon{font-weight:700;font-family:monospace;flex-shrink:0}
        .alert-success{background:rgba(34,197,94,.12);border:1px solid rgba(34,197,94,.3);color:rgba(34,197,94,1)}
        .alert-warning{background:rgba(251,191,36,.12);border:1px solid rgba(251,191,36,.3);color:rgba(251,191,36,1)}
        .alert-error{background:rgba(239,68,68,.12);border:1px solid rgba(239,68,68,.3);color:rgba(239,68,68,1)}
    </style>
</head>
<body>
<div class="wrapper">
    @include('partials.alerts')
    <div class="card">
        <h2>CSV Export Ready</h2>
        <p class="meta">Game: {{ $game ?? 'Unknown' }} &middot; Type: {{ $type ?? 'Unknown' }}</p>
        <p style="margin-top:18px">Your export file has been generated. Click the button below to download the CSV.</p>
        <p style="margin-top:18px">
            <a class="button" href="{{ route('export.download', ['file' => $file]) }}">Download {{ $file }}</a>
        </p>
        <p class="meta" style="margin-top:14px">If the download does not start, you can find the file at <strong>storage/app/exports/{{ $file }}</strong> on the server.</p>
    </div>
</div>
</body>
</html>