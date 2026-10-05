<!DOCTYPE html>
<html lang="en" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Scalable URL Shortener - Production-Ready, High Performance URL Shortening Platform">
    <title>Scalable URL Shortener | High-Performance URL Engine</title>
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=JetBrains+Mono:wght@400;500;600&display=swap" rel="stylesheet">
    <!-- Chart.js for analytics -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <style>
        :root {
            --bg-primary: #090d16;
            --bg-secondary: #0f172a;
            --bg-card: rgba(15, 23, 42, 0.75);
            --border-color: rgba(255, 255, 255, 0.08);
            --border-hover: rgba(99, 102, 241, 0.4);
            --primary: #6366f1;
            --primary-hover: #4f46e5;
            --primary-glow: rgba(99, 102, 241, 0.25);
            --accent: #06b6d4;
            --accent-glow: rgba(6, 182, 212, 0.25);
            --success: #10b981;
            --warning: #f59e0b;
            --danger: #ef4444;
            --text-primary: #f8fafc;
            --text-secondary: #94a3b8;
            --text-muted: #64748b;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, sans-serif;
        }

        body {
            background-color: var(--bg-primary);
            color: var(--text-primary);
            min-height: 100vh;
            background-image: 
                radial-gradient(at 0% 0%, rgba(99, 102, 241, 0.12) 0px, transparent 50%),
                radial-gradient(at 100% 0%, rgba(6, 182, 212, 0.08) 0px, transparent 50%),
                radial-gradient(at 50% 100%, rgba(139, 92, 246, 0.1) 0px, transparent 50%);
            background-attachment: fixed;
            overflow-x: hidden;
        }

        .mono { font-family: 'JetBrains Mono', monospace; }

        /* Navigation */
        header {
            position: sticky;
            top: 0;
            z-index: 50;
            backdrop-filter: blur(16px);
            background: rgba(9, 13, 22, 0.8);
            border-bottom: 1px solid var(--border-color);
        }

        .nav-container {
            max-width: 1280px;
            margin: 0 auto;
            padding: 0.85rem 1.5rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .brand {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            text-decoration: none;
            color: var(--text-primary);
            font-weight: 700;
            font-size: 1.25rem;
        }

        .brand-icon {
            width: 38px;
            height: 38px;
            border-radius: 10px;
            background: linear-gradient(135deg, var(--primary), var(--accent));
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 0 20px var(--primary-glow);
        }

        .nav-links {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .nav-btn {
            background: rgba(255, 255, 255, 0.04);
            color: var(--text-secondary);
            border: 1px solid var(--border-color);
            padding: 0.5rem 1rem;
            border-radius: 8px;
            font-size: 0.875rem;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.2s ease;
            text-decoration: none;
        }

        .nav-btn:hover {
            color: var(--text-primary);
            border-color: var(--border-hover);
            background: rgba(255, 255, 255, 0.08);
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--primary), #4338ca);
            color: #fff;
            border: none;
            padding: 0.55rem 1.25rem;
            border-radius: 8px;
            font-weight: 600;
            font-size: 0.875rem;
            cursor: pointer;
            box-shadow: 0 4px 14px var(--primary-glow);
            transition: all 0.2s ease;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }

        .btn-primary:hover {
            opacity: 0.95;
            transform: translateY(-1px);
            box-shadow: 0 6px 20px var(--primary-glow);
        }

        /* Container & Layout */
        .main-container {
            max-width: 1280px;
            margin: 2.5rem auto;
            padding: 0 1.5rem;
        }

        /* Hero Section */
        .hero {
            text-align: center;
            max-width: 820px;
            margin: 0 auto 3rem auto;
        }

        .hero-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.35rem 0.85rem;
            border-radius: 9999px;
            background: rgba(99, 102, 241, 0.1);
            border: 1px solid rgba(99, 102, 241, 0.3);
            font-size: 0.775rem;
            font-weight: 600;
            color: #a5b4fc;
            margin-bottom: 1.25rem;
        }

        .hero h1 {
            font-size: 2.75rem;
            font-weight: 800;
            line-height: 1.15;
            letter-spacing: -0.025em;
            margin-bottom: 1rem;
            background: linear-gradient(135deg, #ffffff 30%, #94a3b8 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .hero p {
            color: var(--text-secondary);
            font-size: 1.1rem;
            line-height: 1.6;
        }

        /* Shortener Box Card */
        .glass-card {
            background: var(--bg-card);
            backdrop-filter: blur(20px);
            border: 1px solid var(--border-color);
            border-radius: 16px;
            padding: 2rem;
            box-shadow: 0 20px 40px -15px rgba(0, 0, 0, 0.5);
            transition: border-color 0.3s ease;
        }

        .glass-card:hover {
            border-color: rgba(255, 255, 255, 0.14);
        }

        .shorten-form {
            display: grid;
            grid-template-columns: 1fr;
            gap: 1.25rem;
        }

        .input-group {
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
        }

        .input-group label {
            font-size: 0.85rem;
            font-weight: 600;
            color: var(--text-secondary);
        }

        .input-field {
            background: rgba(0, 0, 0, 0.35);
            border: 1px solid var(--border-color);
            border-radius: 10px;
            padding: 0.85rem 1rem;
            color: var(--text-primary);
            font-size: 0.95rem;
            transition: all 0.2s ease;
            outline: none;
            width: 100%;
        }

        .input-field:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px var(--primary-glow);
        }

        .form-grid-3 {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 1rem;
        }

        /* Architecture Badges */
        .system-specs {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 1rem;
            margin-top: 2rem;
        }

        .spec-pill {
            background: rgba(255, 255, 255, 0.02);
            border: 1px solid var(--border-color);
            border-radius: 12px;
            padding: 1rem;
            display: flex;
            align-items: center;
            gap: 0.85rem;
        }

        .spec-icon {
            width: 36px;
            height: 36px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.1rem;
        }

        /* URL List Table */
        .section-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin: 3rem 0 1.25rem 0;
        }

        .section-title {
            font-size: 1.35rem;
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 0.6rem;
        }

        .table-wrap {
            overflow-x: auto;
            border-radius: 12px;
            border: 1px solid var(--border-color);
            background: rgba(15, 23, 42, 0.6);
        }

        table {
            width: 100%;
            border-collapse: collapse;
            text-align: left;
            font-size: 0.875rem;
        }

        th {
            background: rgba(255, 255, 255, 0.03);
            padding: 0.9rem 1.25rem;
            color: var(--text-muted);
            font-weight: 600;
            border-bottom: 1px solid var(--border-color);
            text-transform: uppercase;
            font-size: 0.75rem;
            letter-spacing: 0.05em;
        }

        td {
            padding: 1rem 1.25rem;
            border-bottom: 1px solid var(--border-color);
            vertical-align: middle;
        }

        tr:last-child td { border-bottom: none; }
        tr:hover td { background: rgba(255, 255, 255, 0.015); }

        .badge {
            display: inline-flex;
            align-items: center;
            gap: 0.35rem;
            padding: 0.25rem 0.6rem;
            border-radius: 6px;
            font-size: 0.75rem;
            font-weight: 600;
        }

        .badge-success { background: rgba(16, 185, 129, 0.15); color: #34d399; }
        .badge-warning { background: rgba(245, 158, 11, 0.15); color: #fbbf24; }
        .badge-danger { background: rgba(239, 68, 68, 0.15); color: #f87171; }
        .badge-primary { background: rgba(99, 102, 241, 0.15); color: #818cf8; }

        .action-btn {
            background: transparent;
            border: 1px solid var(--border-color);
            color: var(--text-secondary);
            padding: 0.35rem 0.65rem;
            border-radius: 6px;
            font-size: 0.775rem;
            cursor: pointer;
            transition: all 0.15s ease;
        }

        .action-btn:hover {
            color: var(--text-primary);
            border-color: var(--primary);
        }

        /* Modal styling */
        .modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.75);
            backdrop-filter: blur(8px);
            display: none;
            align-items: center;
            justify-content: center;
            z-index: 100;
            padding: 1.5rem;
        }

        .modal-overlay.active { display: flex; }

        .modal-content {
            background: #0f172a;
            border: 1px solid var(--border-color);
            border-radius: 16px;
            width: 100%;
            max-width: 780px;
            max-height: 90vh;
            overflow-y: auto;
            padding: 2rem;
            position: relative;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.8);
        }

        .modal-close {
            position: absolute;
            top: 1.25rem;
            right: 1.25rem;
            background: transparent;
            border: none;
            color: var(--text-muted);
            font-size: 1.5rem;
            cursor: pointer;
        }

        .modal-close:hover { color: var(--text-primary); }

        .chart-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 1.5rem;
            margin-top: 1.5rem;
        }

        .chart-box {
            background: rgba(0, 0, 0, 0.25);
            border: 1px solid var(--border-color);
            border-radius: 12px;
            padding: 1.25rem;
        }

        /* Notification Toast */
        .toast {
            position: fixed;
            bottom: 2rem;
            right: 2rem;
            background: #1e293b;
            color: #fff;
            padding: 0.85rem 1.25rem;
            border-radius: 10px;
            border: 1px solid var(--border-color);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.5);
            display: none;
            align-items: center;
            gap: 0.75rem;
            z-index: 999;
            font-size: 0.875rem;
            animation: slideIn 0.3s ease;
        }

        @keyframes slideIn {
            from { transform: translateY(20px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }
    </style>
</head>
<body>

    <!-- Header Navigation -->
    <header>
        <div class="nav-container">
            <a href="/" class="brand">
                <div class="brand-icon">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"/>
                        <path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"/>
                    </svg>
                </div>
                <span>Scalable URL Engine</span>
            </a>
            <div class="nav-links">
                <a href="/docs" class="nav-btn">API Docs (Swagger)</a>
                <a href="/health" target="_blank" class="nav-btn" style="display:flex;align-items:center;gap:0.4rem;">
                    <span style="width:8px;height:8px;border-radius:50%;background:#10b981;"></span>
                    Health: 200 OK
                </a>
                <button id="authBtn" class="btn-primary" onclick="openAuthModal()">
                    <span id="authBtnText">Sign In / Demo</span>
                </button>
            </div>
        </div>
    </header>

    <main class="main-container">
        <!-- Hero Title -->
        <section class="hero">
            <div class="hero-badge">
                <span>⚡ Built with Laravel 10 • Redis 7 • MySQL 8 • Clean Architecture</span>
            </div>
            <h1>Sub-Millisecond URL Shortener & Click Analytics Engine</h1>
            <p>Engineered for high throughput with Redis caching, asynchronous queue event streaming, collision-free short codes, and real-time observability.</p>
        </section>

        <!-- Main Shorten Box -->
        <section class="glass-card">
            <form id="shortenForm" class="shorten-form" onsubmit="handleShorten(event)">
                <div class="input-group">
                    <label for="originalUrl">Destination URL (http / https)</label>
                    <input type="url" id="originalUrl" class="input-field" placeholder="https://your-domain.com/path/to/long/resource?ref=engine" required>
                </div>
                <div class="form-grid-3">
                    <div class="input-group">
                        <label for="customAlias">Custom Alias (Optional)</label>
                        <input type="text" id="customAlias" class="input-field mono" placeholder="e.g. laravel-docs">
                    </div>
                    <div class="input-group">
                        <label for="urlTitle">Title / Tag (Optional)</label>
                        <input type="text" id="urlTitle" class="input-field" placeholder="e.g. Q4 Campaign Launch">
                    </div>
                    <div class="input-group">
                        <label for="expiresAt">Expiration Date (Optional)</label>
                        <input type="datetime-local" id="expiresAt" class="input-field">
                    </div>
                </div>
                <div style="display:flex; justify-content:space-between; align-items:center; margin-top:0.5rem;">
                    <span id="rateLimitBadge" class="badge badge-primary">Rate Limit: 60 req/min</span>
                    <button type="submit" class="btn-primary" style="padding:0.75rem 2rem; font-size:0.95rem;">
                        <span>Shorten URL</span>
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
                    </button>
                </div>
            </form>
        </section>

        <!-- System Architecture Features -->
        <section class="system-specs">
            <div class="spec-pill">
                <div class="spec-icon" style="background: rgba(239, 68, 68, 0.15); color:#ef4444;">⚡</div>
                <div>
                    <h4 style="font-size:0.9rem; font-weight:700;">Redis In-Memory Tier</h4>
                    <p style="font-size:0.775rem; color:var(--text-secondary);">Sub-ms redirects with cached payloads & auto invalidation.</p>
                </div>
            </div>
            <div class="spec-pill">
                <div class="spec-icon" style="background: rgba(99, 102, 241, 0.15); color:#818cf8;">🔀</div>
                <div>
                    <h4 style="font-size:0.9rem; font-weight:700;">Asynchronous Queues</h4>
                    <p style="font-size:0.775rem; color:var(--text-secondary);">Non-blocking click ingestion with retries and backoff.</p>
                </div>
            </div>
            <div class="spec-pill">
                <div class="spec-icon" style="background: rgba(6, 182, 212, 0.15); color:#06b6d4;">🛡️</div>
                <div>
                    <h4 style="font-size:0.9rem; font-weight:700;">Zero Collisions</h4>
                    <p style="font-size:0.775rem; color:var(--text-secondary);">Base62 cryptographically secure short-code generator.</p>
                </div>
            </div>
            <div class="spec-pill">
                <div class="spec-icon" style="background: rgba(16, 185, 129, 0.15); color:#10b981;">📊</div>
                <div>
                    <h4 style="font-size:0.9rem; font-weight:700;">Aggregated Analytics</h4>
                    <p style="font-size:0.775rem; color:var(--text-secondary);">Device, referer, hourly & daily breakdown reports.</p>
                </div>
            </div>
        </section>

        <!-- Recent / Demo URLs Table -->
        <section>
            <div class="section-header">
                <div class="section-title">
                    <span>Manage URLs</span>
                    <span id="userUrlCount" class="badge badge-primary">Demo Session</span>
                </div>
                <button class="nav-btn" onclick="fetchUrls()">Refresh List</button>
            </div>

            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>Short URL</th>
                            <th>Original Target</th>
                            <th>Status</th>
                            <th>Total Clicks</th>
                            <th>Expires</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="urlTableBody">
                        <tr>
                            <td colspan="6" style="text-align:center; padding: 2rem; color:var(--text-muted);">
                                Loading active short URLs...
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </section>
    </main>

    <!-- Analytics Modal -->
    <div id="analyticsModal" class="modal-overlay">
        <div class="modal-content">
            <button class="modal-close" onclick="closeAnalyticsModal()">&times;</button>
            <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:1rem;">
                <div>
                    <h2 id="modalShortCode" class="mono" style="font-size:1.5rem; font-weight:700; color:var(--primary);">Analytics</h2>
                    <p id="modalOriginalUrl" style="font-size:0.8rem; color:var(--text-muted); max-width:550px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;"></p>
                </div>
                <span id="modalTotalClicks" class="badge badge-success" style="font-size:1rem; padding:0.4rem 0.8rem;">0 Clicks</span>
            </div>

            <div class="chart-grid">
                <div class="chart-box">
                    <h4 style="font-size:0.85rem; font-weight:600; margin-bottom:1rem; color:var(--text-secondary);">Clicks by Day</h4>
                    <canvas id="dayChart"></canvas>
                </div>
                <div class="chart-box">
                    <h4 style="font-size:0.85rem; font-weight:600; margin-bottom:1rem; color:var(--text-secondary);">Device Breakdown</h4>
                    <canvas id="deviceChart"></canvas>
                </div>
            </div>

            <div style="margin-top:1.5rem; background:rgba(0,0,0,0.25); border-radius:12px; border:1px solid var(--border-color); padding:1.25rem;">
                <h4 style="font-size:0.85rem; font-weight:600; margin-bottom:0.75rem; color:var(--text-secondary);">Top Traffic Referrers</h4>
                <div id="referrersList" style="font-size:0.85rem; color:var(--text-secondary);">
                    No referrer data available yet.
                </div>
            </div>
        </div>
    </div>

    <!-- Auth Modal -->
    <div id="authModal" class="modal-overlay">
        <div class="modal-content" style="max-width:420px;">
            <button class="modal-close" onclick="closeAuthModal()">&times;</button>
            <h2 style="font-size:1.4rem; font-weight:700; margin-bottom:0.5rem;">API Authentication</h2>
            <p style="font-size:0.85rem; color:var(--text-muted); margin-bottom:1.5rem;">Authenticate with demo user credentials or your own account.</p>
            
            <form onsubmit="handleLogin(event)" style="display:flex; flex-direction:column; gap:1rem;">
                <div class="input-group">
                    <label for="loginEmail">Email Address</label>
                    <input type="email" id="loginEmail" class="input-field" value="user@example.com" required>
                </div>
                <div class="input-group">
                    <label for="loginPassword">Password</label>
                    <input type="password" id="loginPassword" class="input-field" value="password123" required>
                </div>
                <button type="submit" class="btn-primary" style="justify-content:center; padding:0.75rem; margin-top:0.5rem;">
                    Sign In via Sanctum
                </button>
                <div style="text-align:center; font-size:0.775rem; color:var(--text-muted); margin-top:0.5rem;">
                    Pre-seeded Demo: <code>user@example.com</code> / <code>password123</code><br>
                    Admin Demo: <code>admin@example.com</code> / <code>password123</code>
                </div>
            </form>
        </div>
    </div>

    <!-- Notification Toast -->
    <div id="toast" class="toast">
        <span id="toastMessage"></span>
    </div>

    <script>
        let currentToken = localStorage.getItem('auth_token') || '';
        let dayChartInstance = null;
        let deviceChartInstance = null;

        // Auto login with demo credentials if no token
        window.addEventListener('DOMContentLoaded', async () => {
            if (!currentToken) {
                await autoDemoLogin();
            } else {
                updateAuthButton(true);
            }
            fetchUrls();
        });

        async function autoDemoLogin() {
            try {
                const res = await fetch('/api/v1/auth/login', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
                    body: JSON.stringify({ email: 'user@example.com', password: 'password123' })
                });
                const data = await res.json();
                if (data.success) {
                    currentToken = data.data.token;
                    localStorage.setItem('auth_token', currentToken);
                    updateAuthButton(true);
                }
            } catch (e) {
                console.error('Demo login failed', e);
            }
        }

        function updateAuthButton(isLoggedIn) {
            const btnText = document.getElementById('authBtnText');
            if (isLoggedIn) {
                btnText.textContent = 'Active (user@example.com)';
            } else {
                btnText.textContent = 'Sign In / Demo';
            }
        }

        function openAuthModal() {
            document.getElementById('authModal').classList.add('active');
        }

        function closeAuthModal() {
            document.getElementById('authModal').classList.remove('active');
        }

        function openAnalyticsModal() {
            document.getElementById('analyticsModal').classList.add('active');
        }

        function closeAnalyticsModal() {
            document.getElementById('analyticsModal').classList.remove('active');
        }

        function showToast(msg) {
            const toast = document.getElementById('toast');
            document.getElementById('toastMessage').textContent = msg;
            toast.style.display = 'flex';
            setTimeout(() => { toast.style.display = 'none'; }, 3500);
        }

        async function handleLogin(e) {
            e.preventDefault();
            const email = document.getElementById('loginEmail').value;
            const password = document.getElementById('loginPassword').value;

            try {
                const res = await fetch('/api/v1/auth/login', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
                    body: JSON.stringify({ email, password })
                });
                const data = await res.json();
                if (data.success) {
                    currentToken = data.data.token;
                    localStorage.setItem('auth_token', currentToken);
                    closeAuthModal();
                    updateAuthButton(true);
                    showToast('Authenticated successfully');
                    fetchUrls();
                } else {
                    showToast(data.message || 'Login failed');
                }
            } catch (err) {
                showToast('Authentication network error');
            }
        }

        async function handleShorten(e) {
            e.preventDefault();
            const original_url = document.getElementById('originalUrl').value;
            const custom_alias = document.getElementById('customAlias').value || undefined;
            const title = document.getElementById('urlTitle').value || undefined;
            const expires_at = document.getElementById('expiresAt').value || undefined;

            try {
                const res = await fetch('/api/v1/urls', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'Authorization': `Bearer ${currentToken}`
                    },
                    body: JSON.stringify({ original_url, custom_alias, title, expires_at })
                });

                const data = await res.json();
                if (data.success) {
                    showToast('Short URL created successfully!');
                    document.getElementById('shortenForm').reset();
                    fetchUrls();
                } else {
                    const err = data.errors ? Object.values(data.errors).flat().join(', ') : data.message;
                    showToast(err || 'Failed to shorten URL');
                }
            } catch (err) {
                showToast('Error communicating with server');
            }
        }

        async function fetchUrls() {
            try {
                const res = await fetch('/api/v1/urls', {
                    headers: {
                        'Accept': 'application/json',
                        'Authorization': `Bearer ${currentToken}`
                    }
                });

                const data = await res.json();
                const tbody = document.getElementById('urlTableBody');

                if (data.success && data.data && data.data.items && data.data.items.length > 0) {
                    tbody.innerHTML = data.data.items.map(url => `
                        <tr>
                            <td>
                                <div style="display:flex; align-items:center; gap:0.5rem;">
                                    <a href="${url.short_url}" target="_blank" class="mono" style="color:var(--accent); text-decoration:none; font-weight:600;">
                                        ${url.short_url}
                                    </a>
                                    <button class="action-btn" onclick="copyToClipboard('${url.short_url}')" title="Copy URL">📋</button>
                                </div>
                            </td>
                            <td>
                                <div style="max-width:320px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap; color:var(--text-secondary);" title="${url.original_url}">
                                    ${url.title ? `<strong>${url.title}</strong><br>` : ''}${url.original_url}
                                </div>
                            </td>
                            <td>
                                <span class="badge ${url.is_active ? 'badge-success' : 'badge-danger'}">
                                    ${url.is_active ? 'Active' : 'Disabled'}
                                </span>
                            </td>
                            <td>
                                <span class="mono" style="font-weight:700; color:#fff;">${url.click_count}</span>
                            </td>
                            <td>
                                <span style="font-size:0.775rem; color:var(--text-muted);">
                                    ${url.expires_at ? new Date(url.expires_at).toLocaleDateString() : 'Never'}
                                </span>
                            </td>
                            <td>
                                <div style="display:flex; gap:0.4rem;">
                                    <button class="action-btn" onclick="viewAnalytics(${url.id})">📊 Stats</button>
                                    <button class="action-btn" onclick="deleteUrl(${url.id})" style="color:var(--danger); border-color:rgba(239,68,68,0.3);">🗑️</button>
                                </div>
                            </td>
                        </tr>
                    `).join('');
                } else {
                    tbody.innerHTML = `<tr><td colspan="6" style="text-align:center; padding:2rem; color:var(--text-muted);">No URLs created yet. Shorten your first link above!</td></tr>`;
                }
            } catch (err) {
                console.error(err);
            }
        }

        async function viewAnalytics(urlId) {
            try {
                const res = await fetch(`/api/v1/urls/${urlId}/analytics`, {
                    headers: {
                        'Accept': 'application/json',
                        'Authorization': `Bearer ${currentToken}`
                    }
                });

                const data = await res.json();
                if (!data.success) {
                    showToast(data.message || 'Error loading analytics');
                    return;
                }

                const report = data.data;
                document.getElementById('modalShortCode').textContent = report.short_code + (report.custom_alias ? ` (${report.custom_alias})` : '');
                document.getElementById('modalOriginalUrl').textContent = report.original_url;
                document.getElementById('modalTotalClicks').textContent = `${report.total_clicks} Clicks`;

                // Render Day Chart
                const dayCtx = document.getElementById('dayChart').getContext('2d');
                if (dayChartInstance) dayChartInstance.destroy();

                const dayLabels = report.clicks_by_day.map(d => d.date);
                const dayCounts = report.clicks_by_day.map(d => d.count);

                dayChartInstance = new Chart(dayCtx, {
                    type: 'line',
                    data: {
                        labels: dayLabels.length ? dayLabels : ['Today'],
                        datasets: [{
                            label: 'Clicks',
                            data: dayCounts.length ? dayCounts : [0],
                            borderColor: '#6366f1',
                            backgroundColor: 'rgba(99, 102, 241, 0.1)',
                            fill: true,
                            tension: 0.3
                        }]
                    },
                    options: {
                        responsive: true,
                        plugins: { legend: { display: false } },
                        scales: {
                            y: { beginAtZero: true, grid: { color: 'rgba(255,255,255,0.05)' } },
                            x: { grid: { color: 'rgba(255,255,255,0.05)' } }
                        }
                    }
                });

                // Render Device Chart
                const deviceCtx = document.getElementById('deviceChart').getContext('2d');
                if (deviceChartInstance) deviceChartInstance.destroy();

                const devLabels = report.device_breakdown.map(d => d.device_type);
                const devCounts = report.device_breakdown.map(d => d.count);

                deviceChartInstance = new Chart(deviceCtx, {
                    type: 'doughnut',
                    data: {
                        labels: devLabels.length ? devLabels : ['Desktop', 'Mobile'],
                        datasets: [{
                            data: devCounts.length ? devCounts : [1, 0],
                            backgroundColor: ['#6366f1', '#06b6d4', '#10b981', '#f59e0b']
                        }]
                    },
                    options: {
                        responsive: true,
                        plugins: { legend: { position: 'bottom', labels: { color: '#94a3b8' } } }
                    }
                });

                // Referrers
                const refDiv = document.getElementById('referrersList');
                if (report.top_referrers && report.top_referrers.length > 0) {
                    refDiv.innerHTML = report.top_referrers.map(r => `
                        <div style="display:flex; justify-content:space-between; padding:0.4rem 0; border-bottom:1px solid rgba(255,255,255,0.05);">
                            <span class="mono">${r.referer || 'Direct Traffic'}</span>
                            <span class="badge badge-primary">${r.count}</span>
                        </div>
                    `).join('');
                } else {
                    refDiv.innerHTML = 'Direct traffic only (no external referrers recorded)';
                }

                openAnalyticsModal();
            } catch (e) {
                showToast('Failed to load analytics');
            }
        }

        async function deleteUrl(urlId) {
            if (!confirm('Are you sure you want to delete this short URL?')) return;

            try {
                const res = await fetch(`/api/v1/urls/${urlId}`, {
                    method: 'DELETE',
                    headers: {
                        'Accept': 'application/json',
                        'Authorization': `Bearer ${currentToken}`
                    }
                });
                const data = await res.json();
                if (data.success) {
                    showToast('URL deleted');
                    fetchUrls();
                }
            } catch (e) {
                showToast('Failed to delete URL');
            }
        }

        function copyToClipboard(text) {
            navigator.clipboard.writeText(text).then(() => {
                showToast('Copied short URL to clipboard!');
            });
        }
    </script>
</body>
</html>
