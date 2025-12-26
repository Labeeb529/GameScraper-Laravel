<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Export Ready</title>
    <style>
        body{font-family:Arial,Helvetica,sans-serif;background:#0b1220;color:#fff;display:flex;align-items:center;justify-content:center;min-height:100vh}
        .card{background:#0f1724;padding:28px;border-radius:12px;box-shadow:0 10px 30px rgba(2,6,23,.6);max-width:640px;width:100%}
        a.button{display:inline-block;padding:12px 18px;background:#3b82f6;color:#fff;border-radius:8px;text-decoration:none;font-weight:700}
        .meta{margin-top:12px;color:#9aa6c3}
    </style>
</head>
<body>
<div class="card">
    <h2>CSV Export Ready</h2>
    <p class="meta">Game: {{ $game ?? 'Unknown' }} &middot; Type: {{ $type ?? 'Unknown' }}</p>
    <p style="margin-top:18px">Your export file has been generated. Click the button below to download the CSV.</p>
    <p style="margin-top:18px">
        <a class="button" href="{{ route('export.download', ['file' => $file]) }}">Download {{ $file }}</a>
    </p>
    <p class="meta" style="margin-top:14px">If the download does not start, you can find the file at <strong>storage/app/exports/{{ $file }}</strong> on the server.</p>
</div>
</body>
</html>