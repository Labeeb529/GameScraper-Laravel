<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width,initial-scale=1" />
    <title>{{ strtoupper($game) }} {{ strtoupper($type) }} - GameScraper</title>
    <style>
        :root {
            --bg1: linear-gradient(135deg, rgba(17, 24, 39, 1) 0%, rgba(10, 12, 20, 1) 100%);
            --glass-bg: rgba(255, 255, 255, 0.06);
            --accent: rgba(255, 255, 255, 0.9);
            --accent-2: rgba(99, 102, 241, 0.95);
            --muted: rgba(255, 255, 255, 0.7);
            --card-radius: 16px;
            --gap: 16px;
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            min-height: 100vh;
            font-family: Inter, ui-sans-serif, system-ui, -apple-system, 'Segoe UI', Roboto, 'Helvetica Neue', Arial;
            background: var(--bg1);
            color: var(--accent);
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
            padding: 24px;
        }

        .wrapper {
            width: 100%;
            max-width: 1400px;
            margin: 0 auto;
            background: linear-gradient(180deg, rgba(255, 255, 255, 0.02), rgba(255, 255, 255, 0.01));
            border-radius: 20px;
            padding: 28px;
            box-shadow: 0 10px 30px rgba(2, 6, 23, 0.6);
            backdrop-filter: blur(8px);
        }

        .header {
            display: flex;
            gap: 12px;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 28px;
            flex-wrap: wrap;
        }

        .brand {
            display: flex;
            gap: 12px;
            align-items: center;
        }

        .logo {
            width: 52px;
            height: 52px;
            border-radius: 12px;
            background: linear-gradient(135deg, rgba(99, 102, 241, 0.95), rgba(59, 130, 246, 0.9));
            display: grid;
            place-items: center;
            font-weight: 700;
            color: white;
            font-size: 18px;
            box-shadow: 0 6px 18px rgba(59, 130, 246, 0.12);
        }

        .title {
            font-size: 24px;
            letter-spacing: 0.2px;
            color: var(--accent);
            margin: 0;
        }

        .subtitle {
            font-size: 13px;
            color: var(--muted);
            margin: 4px 0 0 0;
        }

        .game-selector {
            display: flex;
            gap: 10px;
            margin-top: 20px;
            flex-wrap: wrap;
        }

        .game-btn {
            padding: 10px 16px;
            border: 1px solid rgba(255, 255, 255, 0.1);
            background: rgba(255, 255, 255, 0.06);
            color: var(--muted);
            border-radius: 10px;
            text-decoration: none;
            font-size: 14px;
            font-weight: 600;
            transition: all 0.16s ease;
            display: inline-block;
        }

        .game-btn:hover {
            background: rgba(255, 255, 255, 0.1);
            color: var(--accent);
            transform: translateY(-2px);
        }

        .game-btn.active {
            background: linear-gradient(90deg, rgba(59, 130, 246, 1), rgba(99, 102, 241, 1));
            color: white;
            border-color: transparent;
        }

        .table-container {
            background: var(--glass-bg);
            border-radius: var(--card-radius);
            border: 1px solid rgba(255, 255, 255, 0.06);
            backdrop-filter: blur(6px);
            overflow: hidden;
            margin-block: 20px;
        }

        .table-header {
            background: linear-gradient(135deg, rgba(99, 102, 241, 0.95), rgba(59, 130, 246, 0.9));
            color: white;
            padding: 20px 28px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 10px;
        }

        .table-header h2 {
            font-size: 18px;
            margin: 0;
            font-weight: 600;
        }

        .record-count {
            background: rgba(255, 255, 255, 0.2);
            padding: 6px 14px;
            border-radius: 20px;
            font-size: 13px;
            font-weight: 500;
        }

        .table-wrapper {
            overflow-x: auto;
            padding: 20px 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        thead {
            background: rgba(0, 0, 0, 0.2);
            border-bottom: 1px solid rgba(255, 255, 255, 0.08);
        }

        th {
            padding: 14px 24px;
            text-align: left;
            font-weight: 600;
            color: var(--accent);
            font-size: 13px;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        tbody tr {
            border-bottom: 1px solid rgba(255, 255, 255, 0.06);
            transition: background 0.16s ease;
        }

        tbody tr:hover {
            background: rgba(255, 255, 255, 0.04);
        }

        tbody tr:last-child {
            border-bottom: none;
        }

        td {
            padding: 14px 24px;
            color: var(--muted);
            font-size: 14px;
        }

        .team-cell {
            font-weight: 600;
            color: var(--accent);
        }

        .score {
            font-weight: 700;
            font-size: 15px;
            color: rgba(99, 102, 241, 0.95);
        }

        .status {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 12px;
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
        }

        .status.final {
            background: rgba(34, 197, 94, 0.2);
            color: rgba(34, 197, 94, 1);
            border: 1px solid rgba(34, 197, 94, 0.3);
        }

        .status.live {
            background: rgba(251, 146, 60, 0.2);
            color: rgba(251, 146, 60, 1);
            border: 1px solid rgba(251, 146, 60, 0.3);
            animation: pulse 2s infinite;
        }

        .status.scheduled {
            background: rgba(255, 255, 255, 0.06);
            color: var(--muted);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: var(--muted);
        }

        .empty-state svg {
            width: 80px;
            height: 80px;
            margin-bottom: 20px;
            opacity: 0.5;
            color: var(--muted);
        }

        .empty-state h3 {
            margin: 0 0 10px 0;
            color: var(--accent);
            font-size: 16px;
        }

        .empty-state p {
            margin: 0;
            font-size: 14px;
        }

        @keyframes pulse {

            0%,
            100% {
                opacity: 1;
            }

            50% {
                opacity: 0.6;
            }
        }

        .pagination-wrapper {
            margin-top: 24px;
            display: flex;
            justify-content: center;
        }

        .pagination-wrapper .pagination {
            display: flex;
            gap: 8px;
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .pagination-wrapper .pagination li {
            display: inline-block;
        }

        .pagination-wrapper .pagination a,
        .pagination-wrapper .pagination span {
            display: inline-block;
            padding: 10px 16px;
            border-radius: 10px;
            background: rgba(255, 255, 255, 0.06);
            border: 1px solid rgba(255, 255, 255, 0.1);
            color: var(--accent);
            text-decoration: none;
            font-size: 14px;
            font-weight: 500;
            transition: all 0.16s ease;
        }

        .pagination-wrapper .pagination a:hover {
            background: rgba(255, 255, 255, 0.1);
            transform: translateY(-2px);
        }

        .pagination-wrapper .pagination .active span {
            background: linear-gradient(90deg, rgba(59, 130, 246, 1), rgba(99, 102, 241, 1));
            color: white;
            border-color: transparent;
        }

        .btn-back {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 16px;
            border-radius: 10px;
            background: rgba(255, 255, 255, 0.08);
            border: 1px solid rgba(255, 255, 255, 0.1);
            color: var(--accent);
            text-decoration: none;
            font-size: 14px;
            font-weight: 600;
            transition: all 0.16s ease;
        }

        .btn-back:hover {
            background: rgba(255, 255, 255, 0.12);
            transform: translateY(-2px);
        }

        .export-link {
            background: transparent;
            border: none;
            color: var(--muted);
            text-decoration: underline;
            font-size: 13px;
            padding: 6px 8px;
            cursor: pointer;
        }

        .export-link:hover {
            color: var(--accent);
            text-decoration: none;
        }

        .modal-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.7);
            backdrop-filter: blur(4px);
            z-index: 1000;
            align-items: center;
            justify-content: center;
        }

        .modal-overlay.active {
            display: flex;
        }

        .modal {
            background: linear-gradient(180deg, rgba(255, 255, 255, 0.08), rgba(255, 255, 255, 0.04));
            border-radius: 20px;
            padding: 32px;
            width: 90%;
            max-width: 450px;
            box-shadow: 0 20px 60px rgba(2, 6, 23, 0.8);
            border: 1px solid rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(12px);
        }

        .modal h2 {
            margin: 0 0 8px 0;
            font-size: 22px;
            color: var(--accent);
        }

        .modal p {
            margin: 0 0 24px 0;
            color: var(--muted);
            font-size: 14px;
        }

        .modal-btn {
            flex: 1;
            padding: 12px;
            border-radius: 10px;
            font-weight: 600;
            font-size: 14px;
            border: none;
            cursor: pointer;
            transition: all .16s ease;
        }

        .modal-btn-primary {
            background: linear-gradient(90deg, rgba(59, 130, 246, 1), rgba(99, 102, 241, 1));
            color: white;
        }

        .modal-btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(59, 130, 246, 0.3);
        }

        .modal-btn-secondary {
            background: rgba(255, 255, 255, 0.06);
            color: var(--accent);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .modal-btn-secondary:hover {
            background: rgba(255, 255, 255, 0.08);
        }

        @media (max-width: 768px) {
            .wrapper {
                padding: 20px;
            }

            .header {
                flex-direction: column;
                align-items: flex-start;
            }

            .title {
                font-size: 20px;
            }

            .table-header {
                flex-direction: column;
                align-items: flex-start;
            }

            th,
            td {
                padding: 12px 16px;
                font-size: 13px;
            }

            .game-selector {
                width: 100%;
            }

            .game-btn {
                flex: 1;
                min-width: 120px;
                text-align: center;
            }
        }
    </style>
</head>

<body>
    <div class="wrapper">
        <div class="header">
            <div class="brand">
                <div class="logo">GS</div>
                <div>
                    <div class="title">{{ strtoupper($game ?? 'GAME') }} {{ strtoupper($type ?? 'TYPE') }}</div>
                    <div class="subtitle">Latest game results and scores</div>
                </div>
            </div>
            <div style="display:flex;gap:8px;align-items:center">
                <a href="{{ route('welcome') }}" class="btn-back">← Back</a>
                <a href="#" class="export-link" onclick="openDataExportModal();return false;">Export CSV</a>
            </div>
        </div>

        <div class="game-selector">
            <a href="{{ route('data', ['game' => 'nfl', 'type' => 'bets']) }}"
                class="game-btn {{ ($game ?? '') === 'nfl' && ($type ?? '') === 'bets' ? 'active' : '' }}">
                NFL Bets
            </a>
            <a href="{{ route('data', ['game' => 'nfl', 'type' => 'results']) }}"
                class="game-btn {{ ($game ?? '') === 'nfl' && ($type ?? '') === 'results' ? 'active' : '' }}">
                NFL Results
            </a>
            <a href="{{ route('data', ['game' => 'ncaaf', 'type' => 'bets']) }}"
                class="game-btn {{ ($game ?? '') === 'ncaaf' && ($type ?? '') === 'bets' ? 'active' : '' }}">
                NCAAF Bets
            </a>
            <a href="{{ route('data', ['game' => 'ncaaf', 'type' => 'results']) }}"
                class="game-btn {{ ($game ?? '') === 'ncaaf' && ($type ?? '') === 'results' ? 'active' : '' }}">
                NCAAF Results
            </a>
            <a href="{{ route('data', ['game' => 'ncaab', 'type' => 'bets']) }}"
                class="game-btn {{ ($game ?? '') === 'ncaab' && ($type ?? '') === 'bets' ? 'active' : '' }}">
                NCAAB Bets
            </a>
            <a href="{{ route('data', ['game' => 'ncaab', 'type' => 'results']) }}"
                class="game-btn {{ ($game ?? '') === 'ncaab' && ($type ?? '') === 'results' ? 'active' : '' }}">
                NCAAB Results
            </a>
        </div>

        @if(collect($groupedData)->isEmpty())
            <div class="table-container">
                <div class="table-wrapper">
                    <div class="empty-state">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        <h3>No Results Found</h3>
                        <p>There are no game results available at this time.</p>
                    </div>
                </div>
            </div>
        @else
            @foreach($groupedData as $indexId => $rows)
                @php
                    $rows = collect($rows);
                    $first = $rows->first();
                    $index = $first?->dataIndex; // Safe navigation operator
                @endphp

                <div class="table-container">
                    <div class="table-header">
                        <h2>
                            {{ $index?->scraped_at ? 'Scraped on: ' . \Carbon\Carbon::parse($index->scraped_at)->format('M d, Y H:i') : '' }}
                            <br>
                            {{ $index?->date ? 'Scraped for Date: ' . $index->date : '' }}
                            {{ $index?->year ? 'Scraped for Season ' . $index->year : '' }}
                            {{ $index?->week ? 'Week ' . $index->week : '' }}

                        </h2>
                        <div class="record-count">
                            {{ $rows->count() }} {{ \Illuminate\Support\Str::plural('game', $rows->count()) }}
                        </div>
                    </div>

                    <div class="table-wrapper">
                        @if($rows->isEmpty())
                            <div class="empty-state">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                                <h3>No Results Found</h3>
                                <p>There are no game results available at this time.</p>
                            </div>
                        @else
                            <table>
                                <thead>
                                    <tr>
                                        @if(($type ?? '') === 'results')
                                            <th>Date</th>
                                            <th>Time</th>
                                            <th>Team Left</th>
                                            <th>Score Left</th>
                                            <th>Team Right</th>
                                            <th>Score Right</th>
                                            <th>Winning Spread</th>
                                        @else
                                            <th>Date</th>
                                            <th>Time</th>
                                            <th>Team Left</th>
                                            <th>Spread Left</th>
                                            <th>% Bets Left</th>
                                            <th>% Money Left</th>
                                            <th>Team Right</th>
                                            <th>Spread Right</th>
                                            <th>% Bets Right</th>
                                            <th>% Money Right</th>
                                        @endif
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($rows as $result)
                                        <tr>
                                            <td>{{ $result->game_date ? \Carbon\Carbon::parse($result->game_date)->format('M d, Y') : '-' }}
                                            </td>
                                            <td>
                                                {{ $result->game_time ? \Carbon\Carbon::parse($result->game_time)->format('h:i A') : '-' }}
                                            </td>

                                            <td class="team-cell">{{ $result->team_left ?? '-' }}</td>

                                            @if(($type ?? '') === 'results')
                                                <td class="score">{{ $result->score_left ?? '-' }}</td>
                                                <td class="team-cell">{{ $result->team_right ?? '-' }}</td>
                                                <td class="score">{{ $result->score_right ?? '-' }}</td>
                                                <td>{{ $result->winning_spread ?? '-' }}</td>
                                            @else
                                                <td>{{ $result->spread_left ?? '-' }}</td>
                                                <td>{{ $result->perc_bets_left ?? '-' }}</td>
                                                <td>{{ $result->perc_money_left ?? '-' }}</td>
                                                <td class="team-cell">{{ $result->team_right ?? '-' }}</td>
                                                <td>{{ $result->spread_right ?? '-' }}</td>
                                                <td>{{ $result->perc_bets_right ?? '-' }}</td>
                                                <td>{{ $result->perc_money_right ?? '-' }}</td>
                                            @endif
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        @endif
                    </div>
                </div>
            @endforeach

            @if(isset($paginator) && method_exists($paginator, 'hasPages') && $paginator->hasPages())
                <div class="pagination-wrapper">
                    {{ $paginator->links() }}
                </div>
            @endif
        @endif

    </div>

    <div class="modal-overlay" id="exportModalData">
        <div class="modal">
            <h2>Export CSV — {{ strtoupper($game ?? 'GAME') }}</h2>
            <p>Select which data index runs you want to include in the export for this view.</p>

            <div style="max-height:280px;overflow:auto;border:1px solid rgba(255,255,255,0.04);padding:12px;border-radius:8px;margin-bottom:12px" id="exportDataIndices">
                <div style="margin-bottom:8px;padding-bottom:8px;border-bottom:1px solid rgba(255,255,255,0.08)">
                    <label style="color:#cbd5e1;font-weight:600;cursor:pointer"><input type="checkbox" id="exportData_check_all" style="margin-right:6px"> Check all</label>
                </div>
                @foreach($groupedData as $idx => $rows)
                    @php
                        $first = collect($rows)->first();
                        $meta = $first?->dataIndex;
                    @endphp
                    <div style="margin-bottom:8px">
                        <label style="color:#cbd5e1"><input class="idx-check-data" type="checkbox" value="{{ $idx }}"> Dataset ID {{ $idx }} — {{ $meta?->scraped_at ? \Carbon\Carbon::parse($meta->scraped_at)->format('Y-m-d H:i') : '' }} ({{ count($rows) }} rows)</label>
                    </div>
                @endforeach
            </div>

            <div style="display:flex;gap:8px">
                <button class="modal-btn modal-btn-secondary" onclick="closeDataExportModal()">Cancel</button>
                <button class="modal-btn modal-btn-primary" onclick="startDataExport()">Export Selected</button>
            </div>
        </div>
    </div>
</body>

</html>
<script>
    function openDataExportModal() {
        document.getElementById('exportModalData').classList.add('active');
    }

    function closeDataExportModal() {
        document.getElementById('exportModalData').classList.remove('active');
    }

    document.addEventListener('DOMContentLoaded', function () {
        const checkAll = document.getElementById('exportData_check_all');
        if (checkAll) {
            checkAll.addEventListener('change', function () {
                document.querySelectorAll('.idx-check-data').forEach(cb => {
                    cb.checked = this.checked;
                });
            });
        }
    });

    function startDataExport() {
        const checks = Array.from(document.querySelectorAll('.idx-check-data:checked')).map(n => n.value);
        if (checks.length === 0) {
            alert('Please select at least one index to export');
            return;
        }
        const params = new URLSearchParams();
        params.append('game', '{{ $game }}');
        params.append('type', '{{ $type }}');
        checks.forEach(v => params.append('indices[]', v));
        window.location.href = '/data/export?' + params.toString();
    }

    document.getElementById('exportModalData').addEventListener('click', function (e) {
        if (e.target === this) closeDataExportModal();
    });
</script>