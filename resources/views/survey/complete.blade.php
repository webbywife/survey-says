<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/><meta name="viewport" content="width=device-width,initial-scale=1"/>
  <title>Thank You — {{ $survey->title }}</title>
  <link rel="manifest" href="/manifest.json">
  <meta name="theme-color" content="#550D0E">
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700&family=Lato:wght@400;700&display=swap" rel="stylesheet">
  <style>
    body{font-family:'Lato',sans-serif;background:#f5f5f7;min-height:100vh;display:flex;flex-direction:column;align-items:center;justify-content:center;padding:20px}
    .card{background:#fff;border-radius:10px;padding:48px 40px;text-align:center;max-width:460px;width:100%;box-shadow:0 4px 20px rgba(0,0,0,.08)}
    .icon{width:64px;height:64px;border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 24px}
    .icon-ok{background:#d4edda;}.icon-ok svg{stroke:#155724}
    .icon-off{background:#fff3cd;}.icon-off svg{stroke:#856404}
    .icon svg{width:32px;height:32px;fill:none;stroke-width:2.5}
    h1{font-family:'Playfair Display',serif;font-size:26px;color:#222;margin-bottom:10px}
    p{font-size:15px;color:#666;line-height:1.6}
    .sync-box{margin-top:24px;border:1px solid #e0e0e0;border-radius:8px;padding:16px;text-align:left}
    .sync-box h3{font-size:13px;font-weight:700;text-transform:uppercase;letter-spacing:.05em;color:#555;margin-bottom:10px}
    .sync-row{display:flex;align-items:center;justify-content:space-between;gap:8px;flex-wrap:wrap}
    .sync-count{font-size:22px;font-weight:700;color:#1e3a5f}
    .sync-label{font-size:12px;color:#888;margin-top:2px}
    .btn-sync{background:#1e3a5f;color:#fff;border:none;padding:9px 20px;border-radius:6px;font-size:13px;font-weight:700;cursor:pointer;font-family:'Lato',sans-serif}
    .btn-sync:disabled{background:#aaa;cursor:not-allowed}
    .btn-new{display:inline-block;margin-top:20px;background:#7B1213;color:#fff;border:none;padding:10px 24px;border-radius:6px;font-size:14px;font-weight:700;cursor:pointer;text-decoration:none;font-family:'Lato',sans-serif}
    #sync-status{font-size:12px;margin-top:8px;min-height:18px}
    #sync-status.ok{color:#155724}#sync-status.err{color:#721c24}
  </style>
</head>
<body>
<div class="card" id="card">
  <div class="icon" id="card-icon">
    <svg viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg>
  </div>
  <h1 id="card-title">Thank You!</h1>
  <p id="card-msg">Your response to <strong style="color:#7B1213">{{ $survey->title }}</strong> has been recorded successfully.</p>
  <p style="margin-top:12px;font-size:13px;color:#aaa" id="card-sub">You may now close this window.</p>

  {{-- Sync queue box (shown when there are pending offline responses) --}}
  <div class="sync-box" id="sync-box" style="display:none">
    <h3>Pending Offline Responses</h3>
    <div class="sync-row">
      <div>
        <div class="sync-count" id="pending-count">0</div>
        <div class="sync-label">response(s) saved on this device</div>
      </div>
      <button class="btn-sync" id="sync-btn" onclick="syncAll()">Upload Now</button>
    </div>
    <div id="sync-status"></div>
  </div>

  <a href="/s/{{ $survey->public_token }}" class="btn-new" id="new-btn" style="display:none">
    New Response
  </a>
</div>

<script>
const SURVEY_TOKEN = '{{ $survey->public_token }}';
const SYNC_URL     = '/s/' + SURVEY_TOKEN + '/sync';
const IS_OFFLINE   = new URLSearchParams(location.search).get('offline') === '1';

// Show offline-saved state if redirected from offline submit
if (IS_OFFLINE) {
  document.getElementById('card-icon').className = 'icon icon-off';
  document.getElementById('card-icon').innerHTML = '<svg viewBox="0 0 24 24"><path d="M1 1l22 22M16.72 11.06A10.94 10.94 0 0 1 19 12.55M5 12.55a10.94 10.94 0 0 1 5.17-2.39M10.71 5.05A16 16 0 0 1 22.56 9M1.42 9a15.91 15.91 0 0 1 4.7-2.88M8.53 16.11a6 6 0 0 1 6.95 0M12 20h.01"/></svg>';
  document.getElementById('card-title').textContent = 'Saved Offline';
  document.getElementById('card-msg').innerHTML = 'Response saved to this device. It will be <strong>uploaded automatically</strong> when you reconnect to the internet.';
  document.getElementById('card-sub').textContent = '';
}

// ─── IndexedDB ────────────────────────────────────────────────────────────────
const IDB = {
  _db: null,
  open() {
    if (this._db) return Promise.resolve(this._db);
    return new Promise((resolve, reject) => {
      const req = indexedDB.open('surveysays-offline', 1);
      req.onupgradeneeded = e => {
        const db = e.target.result;
        if (!db.objectStoreNames.contains('pending')) {
          db.createObjectStore('pending', { keyPath: 'id', autoIncrement: true });
        }
      };
      req.onsuccess = e => { this._db = e.target.result; resolve(this._db); };
      req.onerror   = () => reject(req.error);
    });
  },
  async getAll() {
    const db = await this.open();
    return new Promise((resolve, reject) => {
      const tx = db.transaction('pending', 'readonly');
      const req = tx.objectStore('pending').getAll();
      req.onsuccess = () => resolve(req.result);
      tx.onerror = () => reject(tx.error);
    });
  },
  async delete(id) {
    const db = await this.open();
    return new Promise((resolve, reject) => {
      const tx = db.transaction('pending', 'readwrite');
      tx.objectStore('pending').delete(id);
      tx.oncomplete = resolve;
      tx.onerror = () => reject(tx.error);
    });
  }
};

async function refreshCount() {
  const all  = await IDB.getAll();
  const mine = all.filter(r => r.survey_token === SURVEY_TOKEN && r.status === 'pending');
  const box  = document.getElementById('sync-box');
  document.getElementById('pending-count').textContent = mine.length;
  box.style.display = mine.length > 0 ? '' : 'none';
  document.getElementById('new-btn').style.display = '';
  return mine;
}

async function syncAll() {
  if (!navigator.onLine) {
    showStatus('You are currently offline. Connect to the internet and try again.', false);
    return;
  }
  const btn  = document.getElementById('sync-btn');
  btn.disabled = true; btn.textContent = 'Uploading…';

  const pending = await refreshCount();
  let synced = 0, failed = 0;

  for (const record of pending) {
    try {
      const res = await fetch(SYNC_URL, {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: record.form_data,
      });
      if (res.ok) { await IDB.delete(record.id); synced++; }
      else failed++;
    } catch { failed++; }
  }

  btn.disabled = false; btn.textContent = 'Upload Now';
  if (synced > 0) showStatus('✓ ' + synced + ' response(s) uploaded successfully.', true);
  if (failed > 0) showStatus('✗ ' + failed + ' response(s) failed — check your connection and try again.', false);
  await refreshCount();
}

function showStatus(msg, ok) {
  const el = document.getElementById('sync-status');
  el.textContent = msg;
  el.className = ok ? 'ok' : 'err';
}

// Auto-sync when page loads and online
window.addEventListener('load', async () => {
  await refreshCount();
  if (navigator.onLine && !IS_OFFLINE) syncAll();
});

window.addEventListener('online', () => syncAll());

// Register service worker
if ('serviceWorker' in navigator) {
  navigator.serviceWorker.register('/sw.js').catch(() => {});
}
</script>
</body>
</html>
