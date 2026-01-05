<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width,initial-scale=1" />
    <title>Scraper Control</title>
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
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .wrapper {
            width: 100%;
            max-width: 1100px;
            background: linear-gradient(180deg, rgba(255, 255, 255, 0.02), rgba(255, 255, 255, 0.01));
            border-radius: 20px;
            padding: 28px;
            box-shadow: 0 10px 30px rgba(2, 6, 23, 0.6);
            backdrop-filter: blur(8px);
        }

        /* header */
        .header {
            display: flex;
            gap: 12px;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 22px;
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

        /* grid of cards */
        .grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: var(--gap);
            margin-bottom: 20px;
        }

        .card {
            border-radius: var(--card-radius);
            padding: 18px;
            background: var(--glass-bg);
            border: 1px solid rgba(255, 255, 255, 0.06);
            backdrop-filter: blur(6px);
            min-height: 140px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            transition: transform .16s ease, box-shadow .16s ease;
        }

        .card:hover {
            transform: translateY(-3px);
            box-shadow: 0 12px 30px rgba(2, 6, 23, 0.5);
        }

        .card[style*="margin-bottom:0"]:hover {
            transform: none;
            box-shadow: 0 10px 30px rgba(2, 6, 23, 0.6);
        }

        .card h3 {
            margin: 0;
            font-size: 16px;
            color: var(--accent);
        }

        .card p {
            margin: 8px 0 0 0;
            color: var(--muted);
            font-size: 13px
        }

        /* tab group */
        .tabs {
            display: flex;
            gap: 8px;
            margin-top: 14px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.08);
        }

        .tab {
            padding: 8px 16px;
            background: transparent;
            border: none;
            border-bottom: 2px solid transparent;
            color: var(--muted);
            font-weight: 600;
            font-size: 13px;
            cursor: pointer;
            transition: all .16s ease;
        }

        .tab.active {
            color: var(--accent);
            border-bottom-color: var(--accent-2);
        }

        .tab:hover {
            color: var(--accent);
        }

        /* tab content */
        .tab-content {
            display: none;
            margin-top: 12px;
        }

        .tab-content.active {
            display: flex;
        }

        /* button group */
        .actions {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            margin-top: 14px;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 12px;
            border-radius: 10px;
            font-weight: 600;
            font-size: 14px;
            text-decoration: none;
            border: none;
            cursor: pointer;
        }

        .btn-run {
            background: linear-gradient(90deg, rgba(59, 130, 246, 1), rgba(99, 102, 241, 1));
            color: white;
            margin-inline: 0.25rem
        }

        .btn-view {
            background: transparent;
            border: 1px solid rgba(255, 255, 255, 0.08);
            color: var(--accent);
        }

        /* extra controls */
        .controls {
            display: flex;
            gap: 12px;
            align-items: center;
            margin-top: 10px;
            flex-wrap: wrap;
        }

        .select {
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid rgba(255, 255, 255, 0.05);
            padding: 8px 10px;
            border-radius: 10px;
            color: var(--accent);
            font-size: 14px;
        }

        /* footer small area for schedule quick edit */
        .schedule {
            margin-top: 18px;
            display: flex;
            gap: 10px;
            align-items: center;
            flex-wrap: wrap;
        }

        .input {
            background: rgba(0, 0, 0, 0.35);
            border: 1px solid rgba(255, 255, 255, 0.04);
            padding: 8px 10px;
            color: var(--muted);
            border-radius: 9px;
            font-size: 13px;
            width: 220px;
        }

        /* modal */
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

        .modal-input {
            width: 100%;
            background: rgba(0, 0, 0, 0.35);
            border: 1px solid rgba(255, 255, 255, 0.1);
            padding: 12px 14px;
            color: var(--accent);
            border-radius: 10px;
            font-size: 15px;
            margin-bottom: 20px;
            font-family: inherit;
        }

        .modal-input:focus {
            outline: none;
            border-color: var(--accent-2);
        }

        .modal-actions {
            display: flex;
            gap: 10px;
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

        .export-link {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 12px;
            border-radius: 10px;
            font-weight: 600;
            font-size: 14px;
            background: transparent;
            border: 1px solid rgba(255, 255, 255, 0.15);
            color: var(--accent);
            text-decoration: none;
            cursor: pointer;
            transition: all .16s ease;
        }

        .export-link:hover {
            background: rgba(255, 255, 255, 0.05);
            border-color: rgba(255, 255, 255, 0.2);
        }

        /* responsive */
        @media (max-width:880px) {
            .grid {
                grid-template-columns: repeat(2, 1fr)
            }

            .wrapper {
                padding: 20px
            }

            .tabs {
                gap: 4px
            }

            .tab {
                padding: 8px 12px;
                font-size: 12px
            }
        }

        @media (max-width:560px) {
            .grid {
                grid-template-columns: 1fr
            }

            .logo {
                width: 44px;
                height: 44px;
                font-size: 16px
            }

            .input {
                width: 100%
            }

            .controls {
                width: 100%
            }

            .tabs {
                gap: 4px;
                overflow-x: auto
            }

            .tab {
                padding: 8px 10px;
                font-size: 12px;
                white-space: nowrap
            }

            .actions {
                gap: 6px
            }

            .btn {
                padding: 8px 10px;
                font-size: 12px
            }
        }
        .loading-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(2,6,23,0.6);
            z-index: 2000;
            align-items: center;
            justify-content: center;
            backdrop-filter: blur(6px);
        }

        .loading-overlay.active {
            display: flex;
        }

        .loading-card {
            background: linear-gradient(180deg, rgba(255,255,255,0.04), rgba(255,255,255,0.02));
            border-radius: 12px;
            padding: 18px 22px;
            display: flex;
            gap: 14px;
            align-items: center;
            color: var(--accent);
            border: 1px solid rgba(255,255,255,0.06);
            box-shadow: 0 12px 40px rgba(2,6,23,0.6);
            font-weight: 700;
        }

        .loading-text {
            display: flex;
            flex-direction: column;
            gap: 6px;
        }

        .loading-title {
            font-size: 15px;
            line-height: 1;
        }

        .loading-note {
            font-size: 12px;
            color: var(--muted);
            font-weight: 600;
            line-height: 1.1;
        }

        .spinner {
            width: 34px;
            height: 34px;
            border-radius: 50%;
            border: 4px solid rgba(255,255,255,0.08);
            border-top-color: rgba(99,102,241,0.95);
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
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
    </style>
</head>

<body>
    <div class="wrapper">
        <div class="header">
            <div class="brand">
                <div class="logo">SC</div>
                <div>
                    <div class="title">Scraper Control</div>
                    <div class="subtitle">Run scrapers • view results • tweak schedule</div>
                </div>
            </div>
            <div class="subtitle">No auth • Local use</div>
        </div>

        @include('partials.alerts')

        <div class="grid">
            <div class="card">
                <div>
                    <h3>NFL</h3>
                    <p>Results and bets for NFL.</p>
                </div>
                <div>
                    <div class="tabs">
                        <button class="tab active" data-tab="nfl-run">Run</button>
                        <button class="tab" data-tab="nfl-results">Data</button>
                    </div>
                    <div class="tab-content active" id="nfl-run">
                        <div style="display:flex;flex-direction:column;gap:10px">
                            <div style="display:flex;gap:10px;flex-wrap:wrap">
                                <button class="btn btn-run" onclick="openNflModal()">Results Scraping</button>
                                <a class="btn btn-run" href="{{ url('/scrape-bets/nfl') }}" data-redirect>Bets Scraping</a>
                            </div>
                            <a href="#" class="export-link" onclick="openExportModal('nfl');return false;">Export CSV</a>
                        </div>
                    </div>
                    <div class="tab-content" id="nfl-results">
                        <div style="display:flex;flex-direction:column;gap:10px">
                            <div style="display:flex;gap:10px;flex-wrap:wrap">
                                <a class="btn btn-view" href="{{ route('data', ['game' => 'nfl', 'type' => 'results']) }}">Results</a>
                                <a class="btn btn-view" href="{{ route('data', ['game' => 'nfl', 'type' => 'bets']) }}">Bets</a>
                            </div>
                            <a href="#" class="export-link" onclick="openExportModal('nfl');return false;">Export CSV</a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div>
                    <h3>NCAAF</h3>
                    <p>Results and bets for College Football.</p>
                </div>
                <div>
                    <div class="tabs">
                        <button class="tab active" data-tab="ncaaf-run">Run</button>
                        <button class="tab" data-tab="ncaaf-results">Data</button>
                    </div>
                    <div class="tab-content active" id="ncaaf-run">
                        <div style="display:flex;flex-direction:column;gap:10px">
                            <div style="display:flex;gap:10px;flex-wrap:wrap">
                                <button class="btn btn-run" onclick="openNcaafModal()">Results Scraping</button>
                                <a class="btn btn-run" href="{{ url('/scrape-bets/ncaaf') }}" data-redirect>Bets Scraping</a>
                            </div>
                            <a href="#" class="export-link" onclick="openExportModal('ncaaf');return false;">Export CSV</a>
                        </div>
                    </div>
                    <div class="tab-content" id="ncaaf-results">
                        <div style="display:flex;flex-direction:column;gap:10px">
                            <div style="display:flex;gap:10px;flex-wrap:wrap">
                                <a class="btn btn-view" href="{{ route('data', ['game' => 'ncaaf', 'type' => 'results']) }}">Results</a>
                                <a class="btn btn-view" href="{{ route('data', ['game' => 'ncaaf', 'type' => 'bets']) }}">Bets</a>
                            </div>
                            <a href="#" class="export-link" onclick="openExportModal('ncaaf');return false;">Export CSV</a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div>
                    <h3>NCAAB</h3>
                    <p>Results and bets for College Basketball.</p>
                </div>
                <div>
                    <div class="tabs">
                        <button class="tab active" data-tab="ncaab-run">Run</button>
                        <button class="tab" data-tab="ncaab-results">Data</button>
                    </div>
                    <div class="tab-content active" id="ncaab-run">
                        <div style="display:flex;flex-direction:column;gap:10px">
                            <div style="display:flex;gap:10px;flex-wrap:wrap">
                                <button class="btn btn-run" onclick="openDateModal('ncaab', 'NCAAB')">Results
                                    Scraping</button>
                                <a class="btn btn-run" href="{{ url('/scrape-bets/ncaab') }}" data-redirect>Bets Scraping</a>
                            </div>
                            <a href="#" class="export-link" onclick="openExportModal('ncaab');return false;">Export CSV</a>
                        </div>
                    </div>
                    <div class="tab-content" id="ncaab-results">
                        <div style="display:flex;flex-direction:column;gap:10px">
                            <div style="display:flex;gap:10px;flex-wrap:wrap">
                                <a class="btn btn-view" href="{{ route('data', ['game' => 'ncaab', 'type' => 'results']) }}">Results</a>
                                <a class="btn btn-view" href="{{ route('data', ['game' => 'ncaab', 'type' => 'bets']) }}">Bets</a>
                            </div>
                            <a href="#" class="export-link" onclick="openExportModal('ncaab');return false;">Export CSV</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card" style="margin-bottom:0">
            <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px">
                <div>
                    <strong style="display:block;color:var(--accent)">Schedule Manager</strong>
                    <span class="subtitle" style="font-size:13px">Manage cron jobs and schedule scrapers to run
                        automatically.</span>
                </div>
                <div>
                    <a href="/cron-management" class="btn btn-run" style="text-decoration:none">Open Cron Manager</a>
                </div>
            </div>
        </div>

    </div>

    <div class="modal-overlay" id="dateModal">
        <div class="modal">
            <h2>Select Scraping Date</h2>
            <p>Choose a date to scrape results for <span id="modalSportName"></span></p>
            <input type="date" class="modal-input" id="scrapingDate" value="<?php echo date('Y-m-d'); ?>">
            <div class="modal-actions">
                <button class="modal-btn modal-btn-secondary" onclick="closeModal()">Cancel</button>
                <button class="modal-btn modal-btn-primary" onclick="confirmDate()">Start Scraping</button>
            </div>
        </div>
    </div>

    <!-- Export Modal (reused for homepage) -->
    <div class="modal-overlay" id="exportModal">
        <div class="modal">
            <h2>Export CSV — <span id="exportModalGameName"></span></h2>
            <p>Select which data index runs you want to include in the export.</p>

            <div style="margin-bottom:12px">
                <label style="display:block;color:var(--muted);font-size:13px;margin-bottom:6px">Type</label>
                <select id="exportType" class="modal-input">
                    <option value="results">results</option>
                    <option value="bets">bets</option>
                </select>
            </div>

            <div id="exportIndices" style="max-height:220px;overflow:auto;border:1px solid rgba(255,255,255,0.04);padding:12px;border-radius:8px;margin-bottom:12px">
                <!-- indices checkboxes inserted here -->
            </div>

            <div style="display:flex;gap:8px">
                <button class="modal-btn modal-btn-secondary" onclick="closeExportModal()">Cancel</button>
                <button class="modal-btn modal-btn-primary" onclick="startExport()">Export Selected</button>
            </div>
        </div>
    </div>

    <div class="modal-overlay" id="nflModal">
        <div class="modal">
            <h2>Select NFL Week & Season</h2>
            <p>Choose the week and year to scrape NFL results</p>
            <div style="display:flex;gap:12px;margin-bottom:20px">
                <div style="flex:1">
                    <label style="display:block;color:var(--muted);font-size:13px;margin-bottom:6px">Season</label>
                    <select class="modal-input" id="nflYear" style="margin-bottom:0">
                        <?php
$currentYear = date('Y');
for ($y = $currentYear; $y >= 2020; $y--) {
    echo "<option value='$y'>$y</option>";
}
          ?>
                    </select>
                </div>
                <div style="flex:1">
                    <label style="display:block;color:var(--muted);font-size:13px;margin-bottom:6px">Week</label>
                    <select class="modal-input" id="nflWeek" style="margin-bottom:0">
                    </select>
                </div>
            </div>
            <div class="modal-actions">
                <button class="modal-btn modal-btn-secondary" onclick="closeNflModal()">Cancel</button>
                <button class="modal-btn modal-btn-primary" onclick="confirmNflWeek()">Start Scraping</button>
            </div>
        </div>
    </div>

    <div class="modal-overlay" id="ncaafModal">
        <div class="modal">
            <h2>Select NCAAF Week & Season</h2>
            <p>Choose the week and year to scrape NCAAF results</p>
            <div style="display:flex;gap:12px;margin-bottom:20px">
                <div style="flex:1">
                    <label style="display:block;color:var(--muted);font-size:13px;margin-bottom:6px">Season</label>
                    <select class="modal-input" id="ncaafYear" style="margin-bottom:0">
                        <?php
$currentYear = date('Y');
for ($y = $currentYear; $y >= 2020; $y--) {
    echo "<option value='$y'>$y</option>";
}
          ?>
                    </select>
                </div>
                <div style="flex:1">
                    <label style="display:block;color:var(--muted);font-size:13px;margin-bottom:6px">Week</label>
                    <select class="modal-input" id="ncaafWeek" style="margin-bottom:0">
                    </select>
                </div>
            </div>
            <div class="modal-actions">
                <button class="modal-btn modal-btn-secondary" onclick="closeNcaafModal()">Cancel</button>
                <button class="modal-btn modal-btn-primary" onclick="confirmNcaafWeek()">Start Scraping</button>
            </div>
        </div>
    </div>

    <script>
        let currentScraperPage = '';

        function openDateModal(page, sportName) {
            currentScraperPage = page;
            document.getElementById('modalSportName').textContent = sportName;
            document.getElementById('dateModal').classList.add('active');
        }

        function closeModal() {
            document.getElementById('dateModal').classList.remove('active');
        }

        function confirmDate() {
            const date = document.getElementById('scrapingDate').value;
            if (date) {
                showScrapingOverlay();
                requestAnimationFrame(function () {
                    window.location.href = '/scrape-results/' + currentScraperPage + '?date=' + date;
                });
            }
        }

        document.getElementById('dateModal').addEventListener('click', function (e) {
            if (e.target === this) {
                closeModal();
            }
        });

        function openNflModal() {
            populateNflWeeks();
            document.getElementById('nflModal').classList.add('active');
        }

        function closeNflModal() {
            document.getElementById('nflModal').classList.remove('active');
        }

        function confirmNflWeek() {
            const year = document.getElementById('nflYear').value;
            const week = document.getElementById('nflWeek').value;
            if (year && week) {
                showScrapingOverlay();
                requestAnimationFrame(function () {
                    window.location.href = '/scrape-results/nfl?year=' + year + '&week=' + week;
                });
            }
        }

        function populateNflWeeks() {
            const yearSelect = document.getElementById('nflYear');
            const weekSelect = document.getElementById('nflWeek');
            const selectedYear = parseInt(yearSelect.value);
            const currentYear = new Date().getFullYear();

            weekSelect.innerHTML = '';

            let maxWeek = 18;

            if (selectedYear === currentYear) {
                const seasonStart = new Date(selectedYear, 8, 4);
                const today = new Date();
                const daysDiff = Math.floor((today - seasonStart) / (1000 * 60 * 60 * 24));
                if (daysDiff >= 0) {
                    maxWeek = Math.min(18, Math.floor(daysDiff / 7) + 1);
                }
            }

            for (let w = 1; w <= maxWeek; w++) {
                const option = document.createElement('option');
                option.value = w;
                option.textContent = 'Week ' + w;
                weekSelect.appendChild(option);
            }

            weekSelect.value = maxWeek;
        }

        document.getElementById('nflYear').addEventListener('change', populateNflWeeks);

        document.getElementById('nflModal').addEventListener('click', function (e) {
            if (e.target === this) {
                closeNflModal();
            }
        });

        function openNcaafModal() {
            populateNcaafWeeks();
            document.getElementById('ncaafModal').classList.add('active');
        }

        function closeNcaafModal() {
            document.getElementById('ncaafModal').classList.remove('active');
        }

        function confirmNcaafWeek() {
            const year = document.getElementById('ncaafYear').value;
            const week = document.getElementById('ncaafWeek').value;
            if (year && week) {
                showScrapingOverlay();
                requestAnimationFrame(function () {
                    window.location.href = '/scrape-results/ncaaf?year=' + year + '&week=' + week;
                });
            }
        }

        function populateNcaafWeeks() {
            const yearSelect = document.getElementById('ncaafYear');
            const weekSelect = document.getElementById('ncaafWeek');
            const selectedYear = parseInt(yearSelect.value);
            const currentYear = new Date().getFullYear();

            weekSelect.innerHTML = '';

            let maxWeek = 15;

            if (selectedYear === currentYear) {
                const seasonStart = new Date(selectedYear, 7, 30);
                const today = new Date();
                const daysDiff = Math.floor((today - seasonStart) / (1000 * 60 * 60 * 24));
                if (daysDiff >= 0) {
                    maxWeek = Math.min(15, Math.floor(daysDiff / 7) + 1);
                }
            }

            for (let w = 1; w <= maxWeek; w++) {
                const option = document.createElement('option');
                option.value = w;
                option.textContent = 'Week ' + w;
                weekSelect.appendChild(option);
            }

            weekSelect.value = maxWeek;
        }

        document.getElementById('ncaafYear').addEventListener('change', populateNcaafWeeks);

        document.getElementById('ncaafModal').addEventListener('click', function (e) {
            if (e.target === this) {
                closeNcaafModal();
            }
        });
 
        // Tab switching
        document.querySelectorAll('.tab').forEach(tab => {
            tab.addEventListener('click', function () {
                const tabId = this.dataset.tab;
                const card = this.closest('.card');

                // Remove active class from all tabs and contents in this card
                card.querySelectorAll('.tab').forEach(t => t.classList.remove('active'));
                card.querySelectorAll('.tab-content').forEach(c => c.classList.remove('active'));

                // Add active class to clicked tab and corresponding content
                this.classList.add('active');
                document.getElementById(tabId).classList.add('active');
            });
        });

        // Export modal logic (global scope)
        function openExportModal(game) {
            window.__exportGame = game;
            const gameNameMap = {
                'nfl': 'NFL',
                'ncaaf': 'NCAAF',
                'ncaab': 'NCAAB'
            };
            const gameNameEl = document.getElementById('exportModalGameName');
            if (gameNameEl) {
                gameNameEl.textContent = gameNameMap[game] || game.toUpperCase();
            }
            const et = document.getElementById('exportType');
            if (et) et.value = 'results';
            fetchIndicesAndRender(game, 'results');
            const em = document.getElementById('exportModal');
            if (em) em.classList.add('active');
        }

        function closeExportModal() {
            const em = document.getElementById('exportModal');
            if (em) em.classList.remove('active');
        }

        document.addEventListener('DOMContentLoaded', function () {
            const et = document.getElementById('exportType');
            if (et) {
                et.addEventListener('change', function () {
                    const type = this.value;
                    fetchIndicesAndRender(window.__exportGame || 'nfl', type);
                });
            }
        });

        function fetchIndicesAndRender(game, type) {
            const container = document.getElementById('exportIndices');
            if (!container) return;
            container.innerHTML = 'Loading...';
            fetch('/data/indices?game=' + encodeURIComponent(game) + '&type=' + encodeURIComponent(type))
                .then(r => r.json())
                .then(data => {
                    if (!Array.isArray(data)) {
                        container.innerHTML = '<div style="color:#ffb4b4">No indices found</div>';
                        return;
                    }
                    container.innerHTML = '';
                    const checkAll = document.createElement('div');
                    checkAll.style.marginBottom = '8px';
                    checkAll.style.paddingBottom = '8px';
                    checkAll.style.borderBottom = '1px solid rgba(255,255,255,0.08)';
                    checkAll.innerHTML = '<label style="color:#cbd5e1;font-weight:600;cursor:pointer"><input type="checkbox" id="export_check_all" style="margin-right:6px"> Check all</label>';
                    container.appendChild(checkAll);
                    const cacb = document.getElementById('export_check_all');
                    if (cacb) {
                        cacb.addEventListener('change', function () {
                            container.querySelectorAll('input[type=checkbox].idx-check').forEach(cb => cb.checked = this.checked);
                        });
                    }

                    data.forEach(item => {
                        const div = document.createElement('div');
                        div.style.marginBottom = '6px';
                        const label = document.createElement('label');
                        label.style.color = '#cbd5e1';
                        label.innerHTML = '<input class="idx-check" type="checkbox" value="' + item.id + '">  Dataset ID ' + item.id + ' — ' + (item.scraped_at ? item.scraped_at : '') + ' (' + (item.actual_rows !== undefined ? item.actual_rows : item.rows_found) + ' rows)';
                        div.appendChild(label);
                        container.appendChild(div);
                    });
                })
                .catch(() => {
                    container.innerHTML = '<div style="color:#ffb4b4">Failed to load indices</div>';
                });
        }

        function startExport() {
            const checks = Array.from(document.querySelectorAll('#exportIndices input.idx-check:checked')).map(n => n.value);
            const typeEl = document.getElementById('exportType');
            const type = typeEl ? typeEl.value : 'results';
            const game = window.__exportGame || 'nfl';
            const params = new URLSearchParams();
            params.append('game', game);
            params.append('type', type);
            checks.forEach(v => params.append('indices[]', v));
            showExportOverlay();
            requestAnimationFrame(function () {
                window.location.href = '/data/export?' + params.toString();
            });
        }

        function showScrapingOverlay() {
            const o = document.getElementById('scrapeOverlay');
            if (o) o.classList.add('active');
        }

        function showExportOverlay() {
            const o = document.getElementById('exportOverlay');
            if (o) o.classList.add('active');
        }

        // Client-side loader handlers
        // - Links: add `data-redirect` attribute to anchors that should show loader
        //   then navigate. Intercept click, show loader, then navigate inside
        //   requestAnimationFrame to allow the browser to paint the loader.
        document.addEventListener('click', function (e) {
            const a = e.target.closest && e.target.closest('a[data-redirect]');
            if (!a) return;
            e.preventDefault();
            showScrapingOverlay();
            requestAnimationFrame(function () {
                window.location.href = a.href;
            });
        });

        // Forms: add `data-loader` to forms that should show a loader on submit.
        // Intercept submit, show loader, then submit inside requestAnimationFrame.
        document.addEventListener('submit', function (e) {
            const form = e.target;
            if (!form || !form.matches) return;
            if (!form.matches('[data-loader]')) return;
            e.preventDefault();
            const type = form.getAttribute('data-loader') || 'scrape';
            if (type === 'export') showExportOverlay(); else showScrapingOverlay();
            requestAnimationFrame(function () { form.submit(); });
        });

    </script>
    <div class="loading-overlay" id="scrapeOverlay">
        <div class="loading-card">
            <div class="spinner" aria-hidden="true"></div>
            <div class="loading-text">
                <div class="loading-title">Scraping</div>
                <div class="loading-note">Do not refresh or close this page</div>
            </div>
        </div>
    </div>

    <div class="loading-overlay" id="exportOverlay">
        <div class="loading-card">
            <div class="spinner" aria-hidden="true"></div>
            <div class="loading-text">
                <div class="loading-title">Generating Export File</div>
                <div class="loading-note">Do not refresh or close this page</div>
            </div>
        </div>
    </div>
</body>

</html>