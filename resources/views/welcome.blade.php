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
            transform: translateY(-6px);
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

        <div class="grid">
            <div class="card">
                <div>
                    <h3>NFL</h3>
                    <p>Results and bets for NFL.</p>
                </div>
                <div>
                    <div class="tabs">
                        <button class="tab active" data-tab="nfl-run">Run</button>
                        <button class="tab" data-tab="nfl-results">Results</button>
                    </div>
                    <div class="tab-content active" id="nfl-run">
                        <button class="btn btn-run" onclick="openNflModal()">Results Scraping</button>
                        <a class="btn btn-run" href="?page=run-nfl-bets">Bets Scraping</a>
                    </div>
                    <div class="tab-content" id="nfl-results">
                        <a class="btn btn-view" href="{{ route('data', ['game' => 'nfl', 'type' => 'results']) }}">Results</a>
                        <a class="btn btn-view" href="{{ route('data', ['game' => 'nfl', 'type' => 'bets']) }}">Bets</a>
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
                        <button class="tab" data-tab="ncaaf-results">Results</button>
                    </div>
                    <div class="tab-content active" id="ncaaf-run">
                        <button class="btn btn-run" onclick="openNcaafModal()">Results Scraping</button>
                        <a class="btn btn-run" href="?page=run-ncaaf-bets">Bets Scraping</a>
                    </div>
                    <div class="tab-content" id="ncaaf-results">
                        <a class="btn btn-view" href="{{ route('data', ['game' => 'ncaaf', 'type' => 'results']) }}">Results</a>
                        <a class="btn btn-view" href="{{ route('data', ['game' => 'ncaaf', 'type' => 'bets']) }}">Bets</a>
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
                        <button class="tab" data-tab="ncaab-results">Results</button>
                    </div>
                    <div class="tab-content active" id="ncaab-run">
                        <button class="btn btn-run" onclick="openDateModal('run-ncaab-results', 'NCAAB')">Results
                            Scraping</button>
                        <a class="btn btn-run" href="?page=run-ncaab-bets">Bets Scraping</a>
                    </div>
                    <div class="tab-content" id="ncaab-results">
                        <a class="btn btn-view" href="{{ route('data', ['game' => 'ncaab', 'type' => 'results']) }}">Results</a>
                        <a class="btn btn-view" href="{{ route('data', ['game' => 'ncaab', 'type' => 'bets']) }}">Bets</a>
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
                window.location.href = '?page=' + currentScraperPage + '&date=' + date;
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
                window.location.href = '?page=run-nfl-results&year=' + year + '&week=' + week;
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
                maxWeek = Math.min(18, Math.floor(daysDiff / 7) + 1);
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
                window.location.href = '?page=run-ncaaf-results&year=' + year + '&week=' + week;
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
                maxWeek = Math.min(15, Math.floor(daysDiff / 7) + 1);
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

    </script>
</body>

</html>