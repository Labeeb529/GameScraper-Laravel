<?php
// Read root's crontab (where actual cron jobs are stored)
$currentCron = shell_exec("sudo crontab -l 2>/dev/null") ?: '';
$scrapers = [
    'nfl-bets-scraper.php' => 'NFL Bets',
    'nfl-results-scraper.php' => 'NFL Results',
    'ncaaf-bets-scraper.php' => 'NCAAF Bets',
    'ncaaf-results-scraper.php' => 'NCAAF Results',
    'ncaab-bets-scraper.php' => 'NCAAB Bets',
    'ncaab-results-scraper.php' => 'NCAAB Results',
];
$scriptPath = dirname(__DIR__) . '/src/scripts/';
?>
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

        .timezone-notice::before {
            content: "🕐";
            font-size: 12px;
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

        .info-box::before {
            content: "ℹ️";
            margin-right: 8px;
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

        .advanced-toggle {
            margin-top: 32px;
            text-align: center;
        }

        .advanced-toggle button {
            background: transparent;
            border: 1px solid rgba(255, 255, 255, 0.1);
            color: var(--muted);
            padding: 10px 20px;
            border-radius: 8px;
            cursor: pointer;
            font-size: 13px;
            transition: all .16s ease;
        }

        .advanced-toggle button:hover {
            background: rgba(255, 255, 255, 0.05);
            color: var(--accent);
        }

        .advanced-section {
            display: none;
            margin-top: 20px;
        }

        .advanced-section.active {
            display: block;
        }

        .textarea {
            width: 100%;
            background: rgba(0, 0, 0, 0.35);
            border: 1px solid rgba(255, 255, 255, 0.04);
            padding: 12px;
            color: var(--accent);
            border-radius: 9px;
            font-size: 12px;
            font-family: 'Courier New', monospace;
            resize: vertical;
            min-height: 200px;
            line-height: 1.6;
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
            <a href="index.php" class="btn btn-primary" style="text-decoration:none">← Back</a>
        </div>

        <div class="tabs-main">
            <button class="tab-main active" data-tab="regular">Regular Weekly Scraping</button>
            <button class="tab-main" data-tab="onetime">One-Time Special Scraping</button>
        </div>

        <div class="tab-panel active" id="panel-regular">
            <div class="card">
                <h3 style="margin:0 0 8px 0;font-size:16px">Add Weekly Schedule</h3>
                <p style="margin:0 0 20px 0;font-size:13px;color:var(--muted)">Select a scraper, time, and day(s) of the
                    week to run automatically</p>

                <form id="regularForm">
                    <div class="form-group">
                        <label class="form-label">Scraper</label>
                        <select id="regular-scraper" class="form-select" required
                            onchange="toggleRegularParams(this.value)">
                            <option value="">Select a scraper...</option>
                            <?php foreach ($scrapers as $file => $name): ?>
                            <option value="<?= htmlspecialchars($file) ?>"><?= htmlspecialchars($name) ?></option>
                            <?php endforeach; ?>
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
                                <select id="regular-nfl-year" class="form-select">
                                    <?php
$currentYear = date('Y');
for ($y = $currentYear; $y >= 2020; $y--) {
    echo "<option value='$y'>$y</option>";
}
                  ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Week</label>
                                <select id="regular-nfl-week" class="form-select">
                                    <?php for ($w = 1; $w <= 18; $w++)
    echo "<option value='$w'>Week $w</option>"; ?>
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
                                <select id="regular-ncaaf-year" class="form-select">
                                    <?php
for ($y = $currentYear; $y >= 2020; $y--) {
    echo "<option value='$y'>$y</option>";
}
                  ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Week</label>
                                <select id="regular-ncaaf-week" class="form-select">
                                    <?php for ($w = 1; $w <= 15; $w++)
    echo "<option value='$w'>Week $w</option>"; ?>
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
                            <input type="date" id="regular-ncaab-date" class="form-control">
                        </div>
                    </div>

                    <div style="margin-top:24px">
                        <h4 style="margin:0 0 16px 0;font-size:14px;color:var(--accent);font-weight:600">Schedule Timing
                        </h4>
                        <div class="form-row">
                            <div class="form-group">
                                <label class="form-label">Time</label>
                                <input type="time" id="regular-time" class="form-control" value="09:00" required>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Day of Week</label>
                                <select id="regular-day" class="form-select" required>
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

                    <button type="submit" class="btn btn-success" style="margin-top:20px">Add Schedule</button>
                </form>
            </div>

            <div id="regular-schedules" class="schedule-list"></div>
        </div>

        <div class="tab-panel" id="panel-onetime">
            <div class="card">
                <h3 style="margin:0 0 8px 0;font-size:16px">Add One-Time Schedule</h3>
                <p style="margin:0 0 20px 0;font-size:13px;color:var(--muted)">Set up a scraper to run once at a
                    specific date and time</p>

                <form id="onetimeForm">
                    <div class="form-group">
                        <label class="form-label">Scraper</label>
                        <select id="onetime-scraper" class="form-select" required
                            onchange="toggleOnetimeParams(this.value)">
                            <option value="">Select a scraper...</option>
                            <?php foreach ($scrapers as $file => $name): ?>
                            <option value="<?= htmlspecialchars($file) ?>"><?= htmlspecialchars($name) ?></option>
                            <?php endforeach; ?>
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
                                <select id="onetime-nfl-year" class="form-select">
                                    <?php
for ($y = $currentYear; $y >= 2020; $y--) {
    echo "<option value='$y'>$y</option>";
}
                  ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Week</label>
                                <select id="onetime-nfl-week" class="form-select">
                                    <?php for ($w = 1; $w <= 18; $w++)
    echo "<option value='$w'>Week $w</option>"; ?>
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
                                <select id="onetime-ncaaf-year" class="form-select">
                                    <?php
for ($y = $currentYear; $y >= 2020; $y--) {
    echo "<option value='$y'>$y</option>";
}
                  ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Week</label>
                                <select id="onetime-ncaaf-week" class="form-select">
                                    <?php for ($w = 1; $w <= 15; $w++)
    echo "<option value='$w'>Week $w</option>"; ?>
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
                            <input type="date" id="onetime-ncaab-date" class="form-control">
                        </div>
                    </div>

                    <div style="margin-top:24px">
                        <h4 style="margin:0 0 16px 0;font-size:14px;color:var(--accent);font-weight:600">Schedule Timing
                        </h4>
                        <div class="form-row">
                            <div class="form-group">
                                <label class="form-label">Run On Date</label>
                                <input type="date" id="onetime-date" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Time</label>
                                <input type="time" id="onetime-time" class="form-control" value="09:00" required>
                            </div>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-success" style="margin-top:20px">Add One-Time Schedule</button>
                </form>
            </div>

            <div id="onetime-schedules" class="schedule-list"></div>
        </div>

        <div class="advanced-toggle">
            <button id="advancedToggle">Advanced: Manual Cron Editing</button>
        </div>

        <div class="advanced-section" id="advancedSection">
            <div class="card">
                <h3 style="margin:0 0 8px 0;font-size:16px">Manual Cron Editing</h3>
                <p style="margin:0 0 16px 0;font-size:13px;color:var(--muted)">For advanced users: directly edit the
                    cron file
                </p>
                <textarea id="cronText" class="textarea" rows="10"><?= htmlspecialchars($currentCron) ?></textarea>
                <div style="margin-top:12px;display:flex;gap:10px">
                    <button class="btn btn-primary" onclick="saveCron()">Save Cron File</button>
                    <button class="btn btn-secondary" onclick="refreshCron()">Refresh</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        const scriptPath = '<?= str_replace('\\', '/', $scriptPath) ?>';
        const scrapers = <?= json_encode($scrapers) ?>;

        function toggleRegularParams(scraper) {
            document.getElementById('regular-params-nfl').style.display = 'none';
            document.getElementById('regular-params-ncaaf').style.display = 'none';
            document.getElementById('regular-params-ncaab').style.display = 'none';

            if (scraper === 'nfl-results-scraper.php') {
                document.getElementById('regular-params-nfl').style.display = 'block';
            } else if (scraper === 'ncaaf-results-scraper.php') {
                document.getElementById('regular-params-ncaaf').style.display = 'block';
            } else if (scraper === 'ncaab-results-scraper.php') {
                document.getElementById('regular-params-ncaab').style.display = 'block';
            }
        }

        function toggleOnetimeParams(scraper) {
            document.getElementById('onetime-params-nfl').style.display = 'none';
            document.getElementById('onetime-params-ncaaf').style.display = 'none';
            document.getElementById('onetime-params-ncaab').style.display = 'none';

            if (scraper === 'nfl-results-scraper.php') {
                document.getElementById('onetime-params-nfl').style.display = 'block';
            } else if (scraper === 'ncaaf-results-scraper.php') {
                document.getElementById('onetime-params-ncaaf').style.display = 'block';
            } else if (scraper === 'ncaab-results-scraper.php') {
                document.getElementById('onetime-params-ncaab').style.display = 'block';
            }
        }

        console.log('%c=== TIMEZONE INFORMATION ===', 'font-weight: bold; font-size: 14px; color: #3b82f6;');
        console.log('Server Timezone:', '<?= date_default_timezone_get() ?>');
        console.log('PHP Timezone:', '<?= date_default_timezone_get() ?>');
        console.log('Server Current Time:', '<?= date('Y-m-d H:i:s T') ?>');
        console.log('Browser Timezone:', Intl.DateTimeFormat().resolvedOptions().timeZone);
        console.log('Browser Current Time:', new Date().toString());
        console.log('%c============================', 'font-weight: bold; font-size: 14px; color: #3b82f6;');

        document.querySelectorAll('.tab-main').forEach(tab => {
            tab.addEventListener('click', function () {
                document.querySelectorAll('.tab-main').forEach(t => t.classList.remove('active'));
                document.querySelectorAll('.tab-panel').forEach(p => p.classList.remove('active'));
                this.classList.add('active');
                document.getElementById('panel-' + this.dataset.tab).classList.add('active');
            });
        });

        document.getElementById('advancedToggle').addEventListener('click', function () {
            document.getElementById('advancedSection').classList.toggle('active');
            this.textContent = document.getElementById('advancedSection').classList.contains('active')
                ? 'Hide Advanced Options'
                : 'Advanced: Manual Cron Editing';
        });

        function parseCronFile() {
            const cronText = document.getElementById('cronText').value;
            const lines = cronText.split('\n').filter(line => line.trim() && !line.trim().startsWith('#'));

            const regular = [];
            const onetime = [];

            lines.forEach(line => {
                const parts = line.trim().split(/\s+/);
                if (parts.length >= 6) {
                    const [min, hour, dom, month, dow, ...cmdParts] = parts;
                    const cmd = cmdParts.join(' ');

                    const scraperMatch = cmd.match(/([a-z-]+\.php)/i);
                    if (!scraperMatch) return;

                    const scraperFile = scraperMatch[1];
                    const scraperName = scrapers[scraperFile] || scraperFile;

                    if (dom !== '*' && month !== '*') {
                        onetime.push({
                            scraper: scraperFile,
                            name: scraperName,
                            time: `${hour.padStart(2, '0')}:${min.padStart(2, '0')}`,
                            date: `${month.padStart(2, '0')}-${dom.padStart(2, '0')}`,
                            line: line
                        });
                    } else if (dom === '*' && month === '*') {
                        const dayName = dow === '*' ? 'Every Day' :
                            dow === '0' ? 'Sunday' :
                                dow === '1' ? 'Monday' :
                                    dow === '2' ? 'Tuesday' :
                                        dow === '3' ? 'Wednesday' :
                                            dow === '4' ? 'Thursday' :
                                                dow === '5' ? 'Friday' : 'Saturday';

                        regular.push({
                            scraper: scraperFile,
                            name: scraperName,
                            time: `${hour.padStart(2, '0')}:${min.padStart(2, '0')}`,
                            day: dayName,
                            line: line
                        });
                    }
                }
            });

            return { regular, onetime };
        }

        function renderSchedules() {
            const { regular, onetime } = parseCronFile();

            const regularDiv = document.getElementById('regular-schedules');
            const onetimeDiv = document.getElementById('onetime-schedules');

            if (regular.length === 0) {
                regularDiv.innerHTML = '<div class="empty-state">No weekly schedules yet. Add one above to get started.</div>';
            } else {
                regularDiv.innerHTML = regular.map(item => `
      <div class="schedule-item">
        <div class="schedule-info">
          <div class="schedule-name">${item.name}</div>
          <div class="schedule-time">${item.day} at ${item.time}</div>
        </div>
        <div class="schedule-actions">
          <button class="btn btn-danger" onclick="deleteSchedule('${item.line.replace(/'/g, "\\'")}')">Delete</button>
        </div>
      </div>
    `).join('');
            }

            if (onetime.length === 0) {
                onetimeDiv.innerHTML = '<div class="empty-state">No one-time schedules yet. Add one above for special occasions.</div>';
            } else {
                onetimeDiv.innerHTML = onetime.map(item => `
      <div class="schedule-item">
        <div class="schedule-info">
          <div class="schedule-name">${item.name}</div>
          <div class="schedule-time">${item.date} at ${item.time}</div>
        </div>
        <div class="schedule-actions">
          <button class="btn btn-secondary" onclick="rescheduleNextMonth('${item.line.replace(/'/g, "\\'")}')">Reschedule Next Month</button>
          <button class="btn btn-danger" onclick="deleteSchedule('${item.line.replace(/'/g, "\\'")}')">Delete</button>
        </div>
      </div>
    `).join('');
            }
        }

        function deleteSchedule(line) {
            const cronBox = document.getElementById('cronText');
            const lines = cronBox.value.split('\n');
            const filtered = lines.filter(l => l.trim() !== line.trim());
            cronBox.value = filtered.join('\n');
            saveCronAndRefresh();
        }

        function rescheduleNextMonth(line) {
            const parts = line.trim().split(/\s+/);
            if (parts.length >= 6) {
                let [min, hour, dom, month, dow, ...cmdParts] = parts;

                let newMonth = parseInt(month) + 1;
                let newYear = new Date().getFullYear();

                if (newMonth > 12) {
                    newMonth = 1;
                }

                const newLine = `${min} ${hour} ${dom} ${newMonth} ${dow} ${cmdParts.join(' ')}`;

                deleteSchedule(line);

                const cronBox = document.getElementById('cronText');
                const currentValue = cronBox.value.trim();
                cronBox.value = currentValue ? currentValue + '\n' + newLine : newLine;

                saveCronAndRefresh();
            }
        }

        document.getElementById('regularForm').addEventListener('submit', function (e) {
            e.preventDefault();

            const scraper = document.getElementById('regular-scraper').value;
            const time = document.getElementById('regular-time').value;
            const day = document.getElementById('regular-day').value;

            if (!scraper || !time) {
                alert('Please fill in all fields');
                return;
            }

            const [hour, min] = time.split(':');
            let command = `php ${scriptPath}${scraper}`;

            if (scraper === 'nfl-results-scraper.php') {
                const year = document.getElementById('regular-nfl-year').value;
                const week = document.getElementById('regular-nfl-week').value;
                command += ` year=${year} week=${week}`;
            } else if (scraper === 'ncaaf-results-scraper.php') {
                const year = document.getElementById('regular-ncaaf-year').value;
                const week = document.getElementById('regular-ncaaf-week').value;
                command += ` year=${year} week=${week}`;
            } else if (scraper === 'ncaab-results-scraper.php') {
                const date = document.getElementById('regular-ncaab-date').value;
                if (date) {
                    command += ` date=${date}`;
                }
            }

            const line = `${parseInt(min)} ${parseInt(hour)} * * ${day} ${command}`;

            const cronBox = document.getElementById('cronText');
            const currentValue = cronBox.value.trim();
            cronBox.value = currentValue ? currentValue + '\n' + line : line;

            saveCronAndRefresh();
            this.reset();
            document.getElementById('regular-time').value = '09:00';
            toggleRegularParams('');
        });

        document.getElementById('onetimeForm').addEventListener('submit', function (e) {
            e.preventDefault();

            const scraper = document.getElementById('onetime-scraper').value;
            const date = document.getElementById('onetime-date').value;
            const time = document.getElementById('onetime-time').value;

            if (!scraper || !date || !time) {
                alert('Please fill in all fields');
                return;
            }

            const [year, month, dom] = date.split('-');
            const [hour, min] = time.split(':');
            let command = `php ${scriptPath}${scraper}`;

            if (scraper === 'nfl-results-scraper.php') {
                const nflYear = document.getElementById('onetime-nfl-year').value;
                const nflWeek = document.getElementById('onetime-nfl-week').value;
                command += ` year=${nflYear} week=${nflWeek}`;
            } else if (scraper === 'ncaaf-results-scraper.php') {
                const ncaafYear = document.getElementById('onetime-ncaaf-year').value;
                const ncaafWeek = document.getElementById('onetime-ncaaf-week').value;
                command += ` year=${ncaafYear} week=${ncaafWeek}`;
            } else if (scraper === 'ncaab-results-scraper.php') {
                const ncaabDate = document.getElementById('onetime-ncaab-date').value;
                if (ncaabDate) {
                    command += ` date=${ncaabDate}`;
                }
            }

            const line = `${parseInt(min)} ${parseInt(hour)} ${parseInt(dom)} ${parseInt(month)} * ${command}`;

            const cronBox = document.getElementById('cronText');
            const currentValue = cronBox.value.trim();
            cronBox.value = currentValue ? currentValue + '\n' + line : line;

            saveCronAndRefresh();
            this.reset();
            document.getElementById('onetime-time').value = '09:00';
            toggleOnetimeParams('');
        });

        function saveCronAndRefresh() {
            let data = new FormData();
            data.append("cron", document.getElementById("cronText").value);

            fetch("save.php", { method: "POST", body: data })
                .then(r => {
                    if (!r.ok) {
                        throw new Error('Save failed with status: ' + r.status);
                    }
                    return r.text();
                })
                .then(t => {
                    console.log('Save response:', t);
                    if (t.includes('Error') || t.includes('FAILED')) {
                        alert("Save failed:\n" + t);
                    } else {
                        // Reload page to get fresh data from server
                        setTimeout(() => location.reload(), 500);
                    }
                })
                .catch(err => {
                    alert("Error saving: " + err);
                    console.error('Save error:', err);
                });
        }

        function saveCron() {
            let data = new FormData();
            data.append("cron", document.getElementById("cronText").value);

            fetch("save.php", { method: "POST", body: data })
                .then(r => r.text())
                .then(t => {
                    alert("Cron file saved successfully!");
                    renderSchedules();
                })
                .catch(err => {
                    alert("Error saving: " + err);
                });
        }

        function refreshCron() {
            location.reload();
        }

        const today = new Date().toISOString().split('T')[0];
        // document.getElementById('onetime-date').setAttribute('min', today);

        renderSchedules();
    </script>
</body>

</html>