<?php require_once __DIR__ . '/config.php'; ?>
<!DOCTYPE html>
<html lang="tr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Admin Panel</title>
<style>
  * { margin:0; padding:0; box-sizing:border-box; }
  html, body { background:#000; color:#fff; font-family:'Inter', system-ui, sans-serif; min-height:100%; }
  .wrap { max-width:1100px; margin:0 auto; padding:30px 18px 80px; }
  h1 { font-size:28px; margin-bottom:6px; text-shadow:0 0 24px rgba(255,40,40,0.5); }
  h1 span { color:#ff2b2b; }
  .sub { color:#7a7a7a; font-size:13px; margin-bottom:30px; letter-spacing:1px; }
  .login {
    max-width:380px; margin:80px auto;
    background:rgba(15,0,0,0.75);
    border:1.5px solid rgba(255,43,43,0.55);
    border-radius:20px; padding:34px 28px;
    box-shadow:0 0 70px rgba(255,0,0,0.2);
  }
  .login h2 { text-align:center; margin-bottom:20px; }
  .login input {
    width:100%; padding:13px 15px; margin-bottom:12px;
    background:rgba(255,255,255,0.04);
    border:1px solid rgba(255,60,60,0.4);
    border-radius:11px; color:#fff; font-size:14px;
    font-family:inherit; outline:none; transition:.25s;
  }
  .login input:focus { border-color:#ff2b2b; box-shadow:0 0 20px rgba(255,40,40,0.35); }
  .login button {
    width:100%; padding:13px; margin-top:6px;
    background:linear-gradient(140deg,#ff2b2b,#8a0000);
    border:none; border-radius:11px; color:#fff;
    font-size:14px; font-weight:700; letter-spacing:1px;
    cursor:pointer; font-family:inherit; transition:.25s;
  }
  .login button:hover { box-shadow:0 0 26px rgba(255,40,40,0.6); }
  .msg { text-align:center; font-size:13px; margin-top:10px; min-height:16px; color:#ff5b5b; }
  .stats { display:grid; grid-template-columns:repeat(auto-fit,minmax(180px,1fr)); gap:14px; margin-bottom:32px; }
  .stat {
    background:rgba(255,255,255,0.035);
    border:1px solid rgba(255,60,60,0.35);
    border-radius:14px; padding:18px 20px;
  }
  .stat .lbl { font-size:12px; color:#9a6a6a; letter-spacing:1px; text-transform:uppercase; }
  .stat .val { font-size:28px; font-weight:800; margin-top:6px; color:#ff4d4d; }
  .tabs { display:flex; gap:8px; margin-bottom:20px; flex-wrap:wrap; }
  .tab {
    padding:9px 20px; font-size:13px; font-weight:600;
    background:rgba(255,255,255,0.035);
    border:1px solid rgba(255,60,60,0.4);
    border-radius:999px; cursor:pointer; color:#e0a0a0;
    transition:.25s; font-family:inherit;
  }
  .tab.active, .tab:hover { background:rgba(200,20,20,0.25); color:#fff; }
  table { width:100%; border-collapse:collapse; font-size:13px; }
  th, td { text-align:left; padding:11px 12px; border-bottom:1px solid rgba(255,60,60,0.18); vertical-align:top; }
  th { color:#ff4d4d; font-weight:700; font-size:12px; text-transform:uppercase; letter-spacing:.5px; }
  td { color:#d0d0d0; word-break:break-word; }
  tr:hover td { background:rgba(255,40,40,0.05); }
  .logout {
    float:right; padding:7px 16px; font-size:12px;
    color:#ff5b5b; background:rgba(120,0,0,0.18);
    border:1px solid rgba(255,60,60,0.5);
    border-radius:999px; cursor:pointer; font-family:inherit;
    transition:.25s;
  }
  .logout:hover { background:rgba(200,20,20,0.3); color:#fff; }
  .hidden { display:none !important; }
</style>
</head>
<body>

<div class="login" id="loginBox">
  <h2>Admin <span style="color:#ff2b2b">Giriş</span></h2>
  <input type="text" id="userInput" placeholder="Kullanıcı adı">
  <input type="password" id="passInput" placeholder="Şifre">
  <button id="loginBtn">Giriş Yap</button>
  <div class="msg" id="loginMsg"></div>
</div>

<div class="wrap hidden" id="panelBox">
  <button class="logout" id="logoutBtn">Çıkış</button>
  <h1>Tosmen <span>Admin</span></h1>
  <p class="sub">Ziyaretçi &amp; Sohbet Kayıtları</p>
  <div class="stats" id="statsBox"></div>
  <div class="tabs">
    <button class="tab active" data-tab="visits">Ziyaretler</button>
    <button class="tab" data-tab="chats">Sohbetler</button>
    <button class="tab" data-tab="pages">Sayfa İstatistiği</button>
  </div>
  <div id="tableBox"></div>
</div>

<script>
const loginBox = document.getElementById('loginBox');
const panelBox = document.getElementById('panelBox');
const loginBtn = document.getElementById('loginBtn');
const logoutBtn = document.getElementById('logoutBtn');
const loginMsg = document.getElementById('loginMsg');
const userInput = document.getElementById('userInput');
const passInput = document.getElementById('passInput');
const statsBox = document.getElementById('statsBox');
const tableBox = document.getElementById('tableBox');

function esc(s){ return String(s).replace(/[&<>"']/g, c => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c])); }

async function api(action, body){
  const opts = body ? { method:'POST', headers:{'Content-Type':'application/json'}, body: JSON.stringify(body) } : {};
  const r = await fetch('api.php?action=' + action, opts);
  return { ok: r.ok, data: await r.json() };
}

loginBtn.addEventListener('click', async () => {
  loginMsg.textContent = '';
  const { ok, data } = await api('admin_login', { user: userInput.value, pass: passInput.value });
  if (ok) {
    loginBox.classList.add('hidden');
    panelBox.classList.remove('hidden');
    render('visits');
  } else {
    loginMsg.textContent = data.error || 'Hata';
  }
});

logoutBtn.addEventListener('click', async () => {
  await api('admin_logout', {});
  location.reload();
});

async function renderStats(){
  const { data } = await api('admin_stats');
  const uniqueIps = new Set();
  statsBox.innerHTML = `
    <div class="stat"><div class="lbl">Toplam Ziyaret</div><div class="val">${data.totalVisits}</div></div>
    <div class="stat"><div class="lbl">Kullanıcı</div><div class="val">${data.totalUsers}</div></div>
    <div class="stat"><div class="lbl">Doğrulanmış</div><div class="val">${data.verifiedUsers}</div></div>
    <div class="stat"><div class="lbl">Toplam Mesaj</div><div class="val">${data.totalChats}</div></div>
  `;
}

async function render(tab){
  renderStats();
  if (tab === 'visits') {
    const { data: rows } = await api('admin_visits');
    if (!rows.length) { tableBox.innerHTML = '<p style="color:#7a7a7a;text-align:center;padding:30px">Henüz ziyaret yok.</p>'; return; }
    tableBox.innerHTML = `
      <table>
        <thead><tr><th>#</th><th>IP</th><th>Sayfa</th><th>Tarayıcı</th><th>Zaman</th></tr></thead>
        <tbody>${rows.map(v => `
          <tr>
            <td>${v.id}</td>
            <td>${esc(v.ip)}</td>
            <td>${esc(v.page)}</td>
            <td>${esc(v.user_agent).slice(0,80)}</td>
            <td>${new Date(v.time).toLocaleString('tr-TR')}</td>
          </tr>`).join('')}</tbody>
      </table>`;
  }
  if (tab === 'chats') {
    const { data: rows } = await api('admin_chats');
    if (!rows.length) { tableBox.innerHTML = '<p style="color:#7a7a7a;text-align:center;padding:30px">Henüz sohbet yok.</p>'; return; }
    tableBox.innerHTML = `
      <table>
        <thead><tr><th>#</th><th>Email</th><th>Kim</th><th>Mesaj</th><th>Zaman</th></tr></thead>
        <tbody>${rows.map(c => `
          <tr>
            <td>${c.id}</td>
            <td>${esc(c.email)}</td>
            <td>${c.role === 'user' ? '👤' : '🤖'}</td>
            <td>${esc(c.message).slice(0,200)}</td>
            <td>${new Date(c.time).toLocaleString('tr-TR')}</td>
          </tr>`).join('')}</tbody>
      </table>`;
  }
  if (tab === 'pages') {
    const { data } = await api('admin_stats');
    const arr = data.pageStats || [];
    if (!arr.length) { tableBox.innerHTML = '<p style="color:#7a7a7a;text-align:center;padding:30px">Veri yok.</p>'; return; }
    tableBox.innerHTML = `
      <table>
        <thead><tr><th>Sayfa</th><th>Ziyaret</th></tr></thead>
        <tbody>${arr.map(p => `<tr><td>${esc(p.page)}</td><td>${p.c}</td></tr>`).join('')}</tbody>
      </table>`;
  }
}

document.querySelectorAll('.tab').forEach(t => {
  t.addEventListener('click', () => {
    document.querySelectorAll('.tab').forEach(x => x.classList.remove('active'));
    t.classList.add('active');
    render(t.dataset.tab);
  });
});

(async () => {
  const r = await fetch('api.php?action=admin_stats');
  if (r.ok) {
    loginBox.classList.add('hidden');
    panelBox.classList.remove('hidden');
    render('visits');
  }
})();
</script>
</body>
</html>