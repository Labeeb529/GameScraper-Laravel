<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width,initial-scale=1" />
    <title>Schedule Scrapers</title>
    <style>
        :root {
            --bg1: linear-gradient(135deg, rgba(17, 24, 39, 1) 0%, rgba(10, 12, 20, 1) 100%);
            --glass-bg: rgba(255, 255, 255, 0.06);
            --accent: rgba(255, 255, 255, 0.9);
            --accent-2: rgba(99, 102, 241, 0.95);
            --muted: rgba(255, 255, 255, 0.7);
            --card-radius: 16px;
        }

        * {
            box-sizing: border-box
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
            max-width: 1000px;
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
            font-size: 18px;
            letter-spacing: 0.2px;
            color: var(--accent);
        }

        .subtitle {
            font-size: 13px;
            color: var(--muted);
        }

        .timezone-notice {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-size: 11px;
            color: rgba(147, 197, 253, 0.85);
            background: rgba(59, 130, 246, 0.1);
            padding: 4px 10px;
            border-radius: 5px;
            border: 1px solid rgba(59, 130, 246, 0.18);
            margin-top: 8px;
        }

        .tabs-main {
            display: flex;
            gap: 8px;
            margin-bottom: 24px;
            border-bottom: 2px solid rgba(255, 255, 255, 0.08);
        }

        .tab-main {
            padding: 12px 24px;
            background: transparent;
            border: none;
            border-bottom: 3px solid transparent;
            color: var(--muted);
            font-weight: 600;
            font-size: 15px;
            cursor: pointer;
            transition: all .2s ease;
            margin-bottom: -2px;
        }

        .tab-main:hover {
            color: var(--accent);
            background: rgba(255, 255, 255, 0.03);
        }

        .tab-main.active {
            color: var(--accent);
            border-bottom-color: var(--accent-2);
            background: rgba(99, 102, 241, 0.08);
        }

        .tab-panel {
            display: none;
        }

        .tab-panel.active {
            display: block;
        }

        .card {
            border-radius: var(--card-radius);
            padding: 20px;
            background: var(--glass-bg);
            border: 1px solid rgba(255, 255, 255, 0.06);
            backdrop-filter: blur(6px);
            margin-bottom: 16px;
        }

        .form-group {
            margin-bottom: 18px;
        }

        .form-label {
            display: block;
            margin-bottom: 8px;
            font-size: 14px;
            color: var(--accent);
            font-weight: 500;
        }

        .info-box {
            background: rgba(59, 130, 246, 0.08);
            border: 1px solid rgba(59, 130, 246, 0.2);
            border-radius: 10px;
            padding: 12px 16px;
            margin-bottom: 20px;
            font-size: 13px;
            color: rgba(147, 197, 253, 0.95);
            line-height: 1.6;
        }

        .form-control,
        .form-select {
            width: 100%;
            background: rgba(0, 0, 0, 0.35);
            border: 1px solid rgba(255, 255, 255, 0.04);
            padding: 12px 14px;
            color: var(--accent);
            border-radius: 10px;
            font-size: 14px;
            font-family: inherit;
            transition: all .16s ease;
        }

        .form-control:hover,
        .form-select:hover {
            border-color: rgba(255, 255, 255, 0.1);
            background-color: rgba(0, 0, 0, 0.4);
        }

        .form-control:focus,
        .form-select:focus {
            outline: none;
            border-color: var(--accent-2);
            box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
            background-color: rgba(0, 0, 0, 0.45);
        }

        .form-select {
            appearance: none;
            -webkit-appearance: none;
            -moz-appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 12 12'%3E%3Cpath fill='%23ffffff' d='M6 9L1 4h10z'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 14px center;
            background-size: 12px;
            padding-right: 40px;
        }

        .form-select option {
            background: rgba(17, 24, 39, 1);
            color: var(--accent);
            padding: 8px;
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 12px 24px;
            border-radius: 10px;
            font-weight: 600;
            font-size: 14px;
            text-decoration: none;
            border: none;
            cursor: pointer;
            transition: all .16s ease;
            font-family: inherit;
        }

        .btn-primary {
            background: linear-gradient(90deg, rgba(59, 130, 246, 1), rgba(99, 102, 241, 1));
            color: white;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(59, 130, 246, 0.3);
        }

        .btn-success {
            background: linear-gradient(90deg, rgba(34, 197, 94, 1), rgba(22, 163, 74, 1));
            color: white;
        }

        .btn-success:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(34, 197, 94, 0.3);
        }

        .btn-danger {
            background: linear-gradient(90deg, rgba(239, 68, 68, 1), rgba(220, 38, 38, 1));
            color: white;
            font-size: 13px;
            padding: 8px 16px;
        }

        .btn-danger:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(239, 68, 68, 0.3);
        }

        .btn-secondary {
            background: rgba(255, 255, 255, 0.08);
            color: var(--accent);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .btn-secondary:hover {
            background: rgba(255, 255, 255, 0.12);
        }

        .schedule-list {
            margin-top: 20px;
        }

        .schedule-item {
            background: rgba(0, 0, 0, 0.2);
            border: 1px solid rgba(255, 255, 255, 0.06);
            border-radius: 10px;
            padding: 16px;
            margin-bottom: 12px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            transition: all .16s ease;
        }

        .schedule-item:hover {
            background: rgba(0, 0, 0, 0.3);
            border-color: rgba(255, 255, 255, 0.1);
        }

        .schedule-info {
            flex: 1;
        }

        .schedule-name {
            font-weight: 600;
            font-size: 15px;
            color: var(--accent);
            margin-bottom: 4px;
        }

        .schedule-time {
            font-size: 13px;
            color: var(--muted);
        }

        .schedule-status {
            font-size: 12px;
            padding: 4px 8px;
            border-radius: 6px;
            margin-left: 12px;
        }

        .status-active {
            background: rgba(34, 197, 94, 0.2);
            color: rgba(34, 197, 94, 1);
        }

        .status-inactive {
            background: rgba(239, 68, 68, 0.2);
            color: rgba(239, 68, 68, 1);
        }

        .schedule-actions {
            display: flex;
            gap: 8px;
        }

        .empty-state {
            text-align: center;
            padding: 40px 20px;
            color: var(--muted);
            font-size: 14px;
        }

        .alert {
            display: flex;
            align-items: flex-start;
            gap: 10px;
            padding: 14px 18px;
            border-radius: 10px;
            margin-bottom: 20px;
            font-size: 14px;
            animation: alertSlideIn 0.3s ease-out;
        }

        @keyframes alertSlideIn {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .alert-icon {
            font-weight: 700;
            font-family: monospace;
            flex-shrink: 0;
        }

        .alert-success {
            background: rgba(34, 197, 94, 0.12);
            border: 1px solid rgba(34, 197, 94, 0.3);
            color: rgba(34, 197, 94, 1);
        }

        .alert-warning {
            background: rgba(251, 191, 36, 0.12);
            border: 1px solid rgba(251, 191, 36, 0.3);
            color: rgba(251, 191, 36, 1);
        }

        .alert-error {
            background: rgba(239, 68, 68, 0.12);
            border: 1px solid rgba(239, 68, 68, 0.3);
            color: rgba(239, 68, 68, 1);
        }

        .loading {
            opacity: 0.6;
            pointer-events: none;
        }

        @media (max-width:768px) {
            .form-row {
                grid-template-columns: 1fr;
            }

            .tabs-main {
                overflow-x: auto;
            }

            .tab-main {
                padding: 10px 16px;
                font-size: 14px;
            }

            .schedule-item {
                flex-direction: column;
                align-items: flex-start;
                gap: 12px;
            }

            .schedule-actions {
                width: 100%;
                justify-content: flex-end;
            }
        }
    </style>
</head>

<body>
    <div class="wrapper">
        <div class="header">
            <div class="brand">
                <div class="logo">SC</div>
                <div>
                    <div class="title">Schedule Scrapers</div>
                    <div class="subtitle">Set up automatic scraping schedules</div>
                    <div class="timezone-notice">Times indicated are in Mountain Time (MT).</div>
                </div>
            </div>
            <a href="{{ route('welcome') }}" class="btn btn-primary" style="text-decoration:none">← Back</a>
        </div>

        @include('partials.alerts')

        <div class="tabs-main">
            <button class="tab-main active" data-tab="regular">Regular Weekly Scraping</button>
            <button class="tab-main" data-tab="onetime">One-Time Special Scraping</button>
        </div>

        <div class="tab-panel active" id="panel-regular">
            <div class="card">
                <h3 style="margin:0 0 8px 0;font-size:16px">Add Weekly Schedule</h3>
                <p style="margin:0 0 20px 0;font-size:13px;color:var(--muted)">Select a scraper, time, and day(s) of the
                    week to run automatically</p>

                <form id="regularForm" method="POST" action="{{ route('scraper-jobs.store') }}">
                    @csrf
                    <input type="hidden" name="schedule_type" value="regular">
                    <input type="hidden" name="run_once" value="0">

                    <div class="form-group">
                        <label class="form-label">Scraper</label>
                        <select name="scraper_type" id="regular-scraper" class="form-select" required
                            onchange="toggleRegularParams(this.value)">
                            <option value="">Select a scraper...</option>
                            <option value="nfl-bets">NFL Bets</option>
                            <option value="nfl-results">NFL Results</option>
                            <option value="ncaaf-bets">NCAAF Bets</option>
                            <option value="ncaaf-results">NCAAF Results</option>
                            <option value="ncaab-bets">NCAAB Bets</option>
                            <option value="ncaab-results">NCAAB Results</option>
                        </select>
                    </div>

                    <div id="regular-params-nfl" style="display:none">
                        <div class="info-box">
                            <strong>Game Selection Parameters:</strong> Choose which season and week of games to scrape.
                            This determines the game data to collect, not when the scraper runs.
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label class="form-label">Season</label>
                                <select name="year" id="regular-nfl-year" class="form-select">
                                    @for($y = date('Y'); $y >= 2020; $y--)
                                    <option value="{{ $y }}">{{ $y }}</option>
                                    @endfor
                                </select>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Week</label>
                                <select name="week" id="regular-nfl-week" class="form-select">
                                    @for($w = 1; $w <= 18; $w++)
                                    <option value="{{ $w }}">Week {{ $w }}</option>
                                    @endfor
                                </select>
                            </div>
                        </div>
                    </div>

                    <div id="regular-params-ncaaf" style="display:none">
                        <div class="info-box">
                            <strong>Game Selection Parameters:</strong> Choose which season and week of games to scrape.
                            This determines the game data to collect, not when the scraper runs.
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label class="form-label">Season</label>
                                <select name="year" id="regular-ncaaf-year" class="form-select">
                                    @for($y = date('Y'); $y >= 2020; $y--)
                                    <option value="{{ $y }}">{{ $y }}</option>
                                    @endfor
                                </select>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Week</label>
                                <select name="week" id="regular-ncaaf-week" class="form-select">
                                    @for($w = 1; $w <= 15; $w++)
                                    <option value="{{ $w }}">Week {{ $w }}</option>
                                    @endfor
                                </select>
                            </div>
                        </div>
                    </div>

                    <div id="regular-params-ncaab" style="display:none">
                        <div class="info-box">
                            <strong>Game Selection Parameter:</strong> Select which date's games to scrape. This
                            determines the game data to collect, not when the scraper runs.
                        </div>
                        <div class="form-group">
                            <label class="form-label">Game Date</label>
                            <input type="date" name="date" id="regular-ncaab-date" class="form-control">
                        </div>
                    </div>

                    <div style="margin-top:24px">
                        <h4 style="margin:0 0 16px 0;font-size:14px;color:var(--accent);font-weight:600">Schedule Timing
                        </h4>
                        <div class="form-row">
                            <div class="form-group">
                                <label class="form-label">Time</label>
                                <input type="time" name="time" id="regular-time" class="form-control" value="09:00" required>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Day of Week</label>
                                <select name="day_of_week" id="regular-day" class="form-select" required>
                                    <option value="*">Every Day</option>
                                    <option value="1">Monday</option>
                                    <option value="2">Tuesday</option>
                                    <option value="3">Wednesday</option>
                                    <option value="4">Thursday</option>
                                    <option value="5">Friday</option>
                                    <option value="6">Saturday</option>
                                    <option value="0">Sunday</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="form-group" style="margin-top:20px">
                        <label class="form-label">Job Name</label>
                        <input type="text" name="name" class="form-control" placeholder="e.g., NFL Results Weekly" required>
                    </div>

                    <button type="submit" class="btn btn-success" style="margin-top:20px">Add Schedule</button>
                </form>
            </div>

            <div id="regular-schedules" class="schedule-list">
                @forelse($regularJobs as $job)
                <div class="schedule-item">
                    <div class="schedule-info">
                        <div class="schedule-name">
                            {{ $job->name }}
                            <span class="schedule-status {{ $job->active ? 'status-active' : 'status-inactive' }}">
                                {{ $job->active ? 'Active' : 'Inactive' }}
                            </span>
                        </div>
                        <div class="schedule-time">{{ $job->scraper_type }} - {{ $job->cron_expression }}</div>
                    </div>
                    <div class="schedule-actions">
                        <form method="POST" action="{{ route('scraper-jobs.toggle', $job->id) }}" style="display:inline">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="btn btn-secondary">
                                {{ $job->active ? 'Deactivate' : 'Activate' }}
                            </button>
                        </form>
                        <form method="POST" action="{{ route('scraper-jobs.destroy', $job->id) }}" style="display:inline"
                            onsubmit="return confirm('Are you sure you want to delete this schedule?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger">Delete</button>
                        </form>
                    </div>
                </div>
                @empty
                <div class="empty-state">No weekly schedules yet. Add one above to get started.</div>
                @endforelse
            </div>
        </div>

        <div class="tab-panel" id="panel-onetime">
            <div class="card">
                <h3 style="margin:0 0 8px 0;font-size:16px">Add One-Time Schedule</h3>
                <p style="margin:0 0 20px 0;font-size:13px;color:var(--muted)">Set up a scraper to run once at a
                    specific date and time</p>

                <form id="onetimeForm" method="POST" action="{{ route('scraper-jobs.store') }}">
                    @csrf
                    <input type="hidden" name="schedule_type" value="onetime">
                    <input type="hidden" name="run_once" value="1">

                    <div class="form-group">
                        <label class="form-label">Scraper</label>
                        <select name="scraper_type" id="onetime-scraper" class="form-select" required
                            onchange="toggleOnetimeParams(this.value)">
                            <option value="">Select a scraper...</option>
                            <option value="nfl-bets">NFL Bets</option>
                            <option value="nfl-results">NFL Results</option>
                            <option value="ncaaf-bets">NCAAF Bets</option>
                            <option value="ncaaf-results">NCAAF Results</option>
                            <option value="ncaab-bets">NCAAB Bets</option>
                            <option value="ncaab-results">NCAAB Results</option>
                        </select>
                    </div>

                    <div id="onetime-params-nfl" style="display:none">
                        <div class="info-box">
                            <strong>Game Selection Parameters:</strong> Choose which season and week of games to scrape.
                            This determines the game data to collect, not when the scraper runs.
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label class="form-label">Season</label>
                                <select name="year" id="onetime-nfl-year" class="form-select">
                                    @for($y = date('Y'); $y >= 2020; $y--)
                                    <option value="{{ $y }}">{{ $y }}</option>
                                    @endfor
                                </select>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Week</label>
                                <select name="week" id="onetime-nfl-week" class="form-select">
                                    @for($w = 1; $w <= 18; $w++)
                                    <option value="{{ $w }}">Week {{ $w }}</option>
                                    @endfor
                                </select>
                            </div>
                        </div>
                    </div>

                    <div id="onetime-params-ncaaf" style="display:none">
                        <div class="info-box">
                            <strong>Game Selection Parameters:</strong> Choose which season and week of games to scrape.
                            This determines the game data to collect, not when the scraper runs.
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label class="form-label">Season</label>
                                <select name="year" id="onetime-ncaaf-year" class="form-select">
                                    @for($y = date('Y'); $y >= 2020; $y--)
                                    <option value="{{ $y }}">{{ $y }}</option>
                                    @endfor
                                </select>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Week</label>
                                <select name="week" id="onetime-ncaaf-week" class="form-select">
                                    @for($w = 1; $w <= 15; $w++)
                                    <option value="{{ $w }}">Week {{ $w }}</option>
                                    @endfor
                                </select>
                            </div>
                        </div>
                    </div>

                    <div id="onetime-params-ncaab" style="display:none">
                        <div class="info-box">
                            <strong>Game Selection Parameter:</strong> Select which date's games to scrape. This
                            determines the game data to collect, not when the scraper runs.
                        </div>
                        <div class="form-group">
                            <label class="form-label">Game Date</label>
                            <input type="date" name="date" id="onetime-ncaab-date" class="form-control">
                        </div>
                    </div>

                    <div style="margin-top:24px">
                        <h4 style="margin:0 0 16px 0;font-size:14px;color:var(--accent);font-weight:600">Schedule Timing
                        </h4>
                        <div class="form-row">
                            <div class="form-group">
                                <label class="form-label">Run On Date</label>
                                <input type="date" name="run_date" id="onetime-date" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Time</label>
                                <input type="time" name="time" id="onetime-time" class="form-control" value="09:00" required>
                            </div>
                        </div>
                    </div>

                    <div class="form-group" style="margin-top:20px">
                        <label class="form-label">Job Name</label>
                        <input type="text" name="name" class="form-control" placeholder="e.g., NFL Results One-Time" required>
                    </div>

                    <button type="submit" class="btn btn-success" style="margin-top:20px">Add One-Time Schedule</button>
                </form>
            </div>

            <div id="onetime-schedules" class="schedule-list">
                @forelse($onetimeJobs as $job)
                <div class="schedule-item">
                    <div class="schedule-info">
                        <div class="schedule-name">
                            {{ $job->name }}
                            <span class="schedule-status {{ $job->active ? 'status-active' : 'status-inactive' }}">
                                {{ $job->active ? 'Active' : 'Inactive' }}
                            </span>
                        </div>
                        <div class="schedule-time">{{ $job->scraper_type }} - {{ $job->cron_expression }}</div>
                    </div>
                    <div class="schedule-actions">
                        <form method="POST" action="{{ route('scraper-jobs.toggle', $job->id) }}" style="display:inline">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="btn btn-secondary">
                                {{ $job->active ? 'Deactivate' : 'Activate' }}
                            </button>
                        </form>
                        <form method="POST" action="{{ route('scraper-jobs.destroy', $job->id) }}" style="display:inline"
                            onsubmit="return confirm('Are you sure you want to delete this schedule?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger">Delete</button>
                        </form>
                    </div>
                </div>
                @empty
                <div class="empty-state">No one-time schedules yet. Add one above for special occasions.</div>
                @endforelse
            </div>
        </div>
    </div>

    <script>
        function toggleRegularParams(scraper) {
            document.getElementById('regular-params-nfl').style.display = 'none';
            document.getElementById('regular-params-ncaaf').style.display = 'none';
            document.getElementById('regular-params-ncaab').style.display = 'none';

            if (scraper === 'nfl-results') {
                document.getElementById('regular-params-nfl').style.display = 'block';
            } else if (scraper === 'ncaaf-results') {
                document.getElementById('regular-params-ncaaf').style.display = 'block';
            } else if (scraper === 'ncaab-results') {
                document.getElementById('regular-params-ncaab').style.display = 'block';
            }
        }

        function toggleOnetimeParams(scraper) {
            document.getElementById('onetime-params-nfl').style.display = 'none';
            document.getElementById('onetime-params-ncaaf').style.display = 'none';
            document.getElementById('onetime-params-ncaab').style.display = 'none';

            if (scraper === 'nfl-results') {
                document.getElementById('onetime-params-nfl').style.display = 'block';
            } else if (scraper === 'ncaaf-results') {
                document.getElementById('onetime-params-ncaaf').style.display = 'block';
            } else if (scraper === 'ncaab-results') {
                document.getElementById('onetime-params-ncaab').style.display = 'block';
            }
        }

        document.querySelectorAll('.tab-main').forEach(tab => {
            tab.addEventListener('click', function () {
                document.querySelectorAll('.tab-main').forEach(t => t.classList.remove('active'));
                document.querySelectorAll('.tab-panel').forEach(p => p.classList.remove('active'));
                this.classList.add('active');
                document.getElementById('panel-' + this.dataset.tab).classList.add('active');
            });
        });
    </script>
</body>

</html>
