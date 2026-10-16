<?php require_once __DIR__ . '/config.php'; log_visit($_SERVER['REQUEST_URI']); ?>
<!DOCTYPE html>
<html lang="tr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
<title>Tosmen AI</title>
<style>
  * { margin:0; padding:0; box-sizing:border-box; }
  html, body {
    background:#000; color:#fff;
    font-family:'Inter', system-ui, sans-serif;
    min-height:100%; -webkit-tap-highlight-color:transparent;
  }
  #particles { position:fixed; inset:0; z-index:0; pointer-events:none; }
  .wrap { position:relative; z-index:1; max-width:760px; margin:0 auto; padding:24px 16px 140px; }
  .topbar {
    display:flex; align-items:center; justify-content:space-between;
    padding:14px 18px; margin-bottom:20px;
    background:rgba(15,0,0,0.7);
    border:1.5px solid rgba(255,43,43,0.5);
    border-radius:16px;
    backdrop-filter:blur(10px);
  }
  .topbar h1 { font-size:18px; letter-spacing:1px; }
  .topbar h1 span { color:#ff2b2b; }
  .topbar .status { font-size:12px; color:#7a7a7a; }
  .topbar a {
    color:#ff5b5b; text-decoration:none; font-size:12px;
    border:1px solid rgba(255,60,60,0.5);
    padding:6px 14px; border-radius:999px; transition:.25s;
  }
  .topbar a:hover { background:rgba(200,20,20,0.25); color:#fff; }
  .auth {
    background:rgba(15,0,0,0.75);
    border:1.5px solid rgba(255,43,43,0.55);
    border-radius:22px; padding:36px 28px;
    box-shadow:0 0 70px rgba(255,0,0,0.2);
  }
  .auth h2 { text-align:center; font-size:22px; margin-bottom:8px; text-shadow:0 0 20px rgba(255,40,40,0.5); }
  .auth p { text-align:center; color:#9a6a6a; font-size:13px; margin-bottom:22px; }
  .auth input {
    width:100%; padding:14px 16px; margin-bottom:12px;
    background:rgba(255,255,255,0.04);
    border:1px solid rgba(255,60,60,0.4);
    border-radius:12px; color:#fff; font-size:15px;
    font-family:inherit; outline:none; transition:.25s;
  }
  .auth input:focus { border-color:#ff2b2b; box-shadow:0 0 20px rgba(255,40,40,0.35); }
  .auth input::placeholder { color:#5a4a4a; }
  .auth button {
    width:100%; padding:14px; margin-top:6px;
    background:linear-gradient(140deg,#ff2b2b,#8a0000);
    border:none; border-radius:12px; color:#fff;
    font-size:15px; font-weight:700; letter-spacing:1px;
    cursor:pointer; font-family:inherit; transition:.25s;
  }
  .auth button:hover { box-shadow:0 0 30px rgba(255,40,40,0.6); transform:translateY(-1px); }
  .auth button:disabled { opacity:.5; cursor:not-allowed; transform:none; }
  .msg { text-align:center; font-size:13px; margin-top:12px; min-height:18px; }
  .msg.err { color:#ff5b5b; }
  .msg.ok { color:#4dff88; }
  .hidden { display:none; }
  #chatBox { display:flex; flex-direction:column; gap:12px; margin-bottom:20px; }
  .bubble {
    max-width:80%; padding:12px 16px; border-radius:16px;
    font-size:14.5px; line-height:1.55; word-wrap:break-word;
    animation:pop .25s ease;
  }
  @keyframes pop { from { opacity:0; transform:translateY(8px);} to { opacity:1; transform:none; } }
  .bubble.user {
    align-self:flex-end;
    background:linear-gradient(140deg,#ff2b2b,#8a0000);
    border-bottom-right-radius:4px;
  }
  .bubble.ai {
    align-self:flex-start;
    background:rgba(255,255,255,0.06);
    border:1px solid rgba(255,60,60,0.35);
    border-bottom-left-radius:4px;
    color:#e8d0d0;
  }
  .bubble.err {
    align-self:flex-start;
    background:rgba(120,0,0,0.25);
    border:1px solid rgba(255,60,60,0.65);
    color:#ff9b9b;
    font-size:13px;
    font-family:monospace;
    white-space:pre-wrap;
  }
  .bubble.typing { color:#7a7a7a; font-style:italic; }
  .inputbar {
    position:fixed; left:0; right:0; bottom:0; z-index:10;
    padding:14px 16px calc(14px + env(safe-area-inset-bottom));
    background:linear-gradient(to top,#000 60%,transparent);
  }
  .inputbar .inner {
    max-width:760px; margin:0 auto;
    display:flex; gap:10px;
    background:rgba(15,0,0,0.85);
    border:1.5px solid rgba(255,43,43,0.55);
    border-radius:999px; padding:6px 6px 6px 20px;
    backdrop-filter:blur(10px);
  }
  .inputbar input {
    flex:1; background:transparent; border:none; outline:none;
    color:#fff; font-size:15px; font-family:inherit;
  }
  .inputbar input::placeholder { color:#5a4a4a; }
  .inputbar button {
    width:46px; height:46px; border-radius:50%;
    background:linear-gradient(140deg,#ff2b2b,#8a0000);
    border:none; color:#fff; cursor:pointer;
    display:grid; place-items:center; transition:.25s;
  }
  .inputbar button:hover { box-shadow:0 0 22px rgba(255,40,40,0.7); }
  .inputbar button:disabled { opacity:.4; cursor:not-allowed; }
  .inputbar svg { fill:#fff; }
</style>
</head>
<body>
<canvas id="particles"></canvas>

<div class="wrap">
  <div class="topbar">
    <h1>TOSMEN <span>AI</span></h1>
    <div class="status" id="statusText">Doğrulama gerekli</div>
    <a href="index.php">Geri</a>
  </div>

  <div class="auth" id="authBox">
    <h2>Email Doğrulama</h2>
    <p>Sohbete başlamak için emailini doğrula</p>

    <div id="step1">
      <input type="email" id="emailInput" placeholder="ornek@gmail.com" autocomplete="email">
      <button id="sendCodeBtn">Kod Gönder</button>
    </div>

    <div id="step2" class="hidden">
      <input type="text" id="codeInput" placeholder="6 haneli kod" maxlength="6" inputmode="numeric">
      <button id="verifyBtn">Doğrula</button>
    </div>

    <div class="msg" id="authMsg"></div>
  </div>

  <div id="chatScreen" class="hidden">
    <div id="chatBox"></div>
  </div>
</div>

<div class="inputbar hidden" id="inputBar">
  <div class="inner">
    <input type="text" id="msgInput" placeholder="Mesajını yaz..." autocomplete="off">
    <button id="sendBtn" aria-label="Gönder">
      <svg width="20" height="20" viewBox="0 0 24 24"><path d="M2 21l21-9L2 3v7l15 2-15 2z"/></svg>
    </button>
  </div>
</div>

<script>
const pCanvas = document.getElementById('particles');
const pCtx = pCanvas.getContext('2d');
let PW=0, PH=0, DPR=Math.min(devicePixelRatio||1,2);
const particles = [];
function initParticles(){
  particles.length = 0;
  const count = innerWidth < 600 ? 50 : 90;
  for (let i=0;i<count;i++){
    particles.push({
      x:Math.random()*PW, y:Math.random()*PH,
      r:Math.random()*1.8+0.4,
      vx:(Math.random()-0.5)*0.28,
      vy:(Math.random()-0.5)*0.28-0.08,
      a:Math.random()*0.6+0.15,
      tw:Math.random()*Math.PI*2,
      twSpeed:Math.random()*0.02+0.005
    });
  }
}
function drawParticles(){
  pCtx.clearRect(0,0,PW,PH);
  for (const p of particles){
    p.x+=p.vx; p.y+=p.vy; p.tw+=p.twSpeed;
    if(p.x<-10)p.x=PW+10; if(p.x>PW+10)p.x=-10;
    if(p.y<-10)p.y=PH+10; if(p.y>PH+10)p.y=-10;
    const alpha = p.a*(0.55+0.45*Math.sin(p.tw));
    pCtx.beginPath();
    pCtx.arc(p.x,p.y,p.r,0,Math.PI*2);
    pCtx.fillStyle='rgba(255,255,255,'+alpha.toFixed(3)+')';
    pCtx.shadowBlur=8; pCtx.shadowColor='rgba(255,255,255,0.85)';
    pCtx.fill();
  }
  pCtx.shadowBlur=0;
  requestAnimationFrame(drawParticles);
}
function resize(){
  DPR=Math.min(devicePixelRatio||1,2);
  const w=innerWidth, h=innerHeight;
  PW=w; PH=h;
  pCanvas.width=w*DPR; pCanvas.height=h*DPR;
  pCanvas.style.width=w+'px'; pCanvas.style.height=h+'px';
  pCtx.setTransform(DPR,0,0,DPR,0,0);
  initParticles();
}
addEventListener('resize',resize);
resize(); drawParticles();

const authBox = document.getElementById('authBox');
const chatScreen = document.getElementById('chatScreen');
const inputBar = document.getElementById('inputBar');
const statusText = document.getElementById('statusText');
const authMsg = document.getElementById('authMsg');
const step1 = document.getElementById('step1');
const step2 = document.getElementById('step2');
const emailInput = document.getElementById('emailInput');
const codeInput = document.getElementById('codeInput');
const sendCodeBtn = document.getElementById('sendCodeBtn');
const verifyBtn = document.getElementById('verifyBtn');
const chatBox = document.getElementById('chatBox');
const msgInput = document.getElementById('msgInput');
const sendBtn = document.getElementById('sendBtn');

let currentEmail = '';

function isValidEmail(e){ return /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/.test(e); }

function showChat(){
  authBox.classList.add('hidden');
  chatScreen.classList.remove('hidden');
  inputBar.classList.remove('hidden');
  statusText.textContent = currentEmail;
  statusText.style.color = '#4dff88';
}

function addBubble(text, who){
  const d = document.createElement('div');
  d.className = 'bubble ' + who;
  d.textContent = text;
  chatBox.appendChild(d);
  scrollDown();
  return d;
}
function scrollDown(){
  requestAnimationFrame(() => window.scrollTo({top: document.body.scrollHeight, behavior:'smooth'}));
}

async function apiCall(action, body){
  const opts = body
    ? { method:'POST', headers:{'Content-Type':'application/json'}, body: JSON.stringify(body) }
    : {};
  const r = await fetch('api.php?action=' + action, opts);
  const text = await r.text();
  let data;
  try { data = JSON.parse(text); }
  catch (e) { throw new Error('Sunucu JSON döndürmedi:\n' + text.slice(0, 400)); }
  if (!r.ok) {
    const err = new Error(data.error || 'Hata');
    err.detail = data;
    throw err;
  }
  return data;
}

sendCodeBtn.addEventListener('click', async () => {
  const email = emailInput.value.trim().toLowerCase();
  if (!isValidEmail(email)) { authMsg.textContent='Geçerli bir email gir'; authMsg.className='msg err'; return; }
  sendCodeBtn.disabled = true;
  authMsg.textContent = 'Gönderiliyor...';
  authMsg.className = 'msg';
  try {
    await apiCall('send_code', { email });
    currentEmail = email;
    step1.classList.add('hidden');
    step2.classList.remove('hidden');
    authMsg.textContent = 'Kod emailine gönderildi ✉️ Spam klasörünü de kontrol et';
    authMsg.className = 'msg ok';
  } catch(e) {
    authMsg.textContent = e.message;
    authMsg.className = 'msg err';
    sendCodeBtn.disabled = false;
  }
});

verifyBtn.addEventListener('click', async () => {
  const code = codeInput.value.trim();
  if (!code) { authMsg.textContent='Kodu gir'; authMsg.className='msg err'; return; }
  verifyBtn.disabled = true;
  try {
    await apiCall('verify_code', { email: currentEmail, code });
    showChat();
    addBubble('Merhaba! Ben Tosmen. Ne konuşmak istersin?', 'ai');
  } catch(e) {
    authMsg.textContent = e.message;
    authMsg.className = 'msg err';
    verifyBtn.disabled = false;
  }
});

async function sendMessage(){
  const text = msgInput.value.trim();
  if (!text) return;
  msgInput.value = '';
  addBubble(text, 'user');
  sendBtn.disabled = true;

  const typing = addBubble('Tosmen yazıyor...', 'ai typing');

  try {
    const data = await apiCall('chat', { message: text });
    typing.remove();
    addBubble(data.reply, 'ai');
  } catch(e) {
    typing.remove();
    let msg = e.message;
    if (e.detail) {
      msg += '\n\nDetay: ' + JSON.stringify(e.detail, null, 2);
    }
    addBubble(msg, 'err');
  } finally {
    sendBtn.disabled = false;
    msgInput.focus();
  }
}

sendBtn.addEventListener('click', sendMessage);
msgInput.addEventListener('keydown', e => { if (e.key === 'Enter') sendMessage(); });

apiCall('me').then(j => {
  if (j.email){ currentEmail = j.email; showChat(); }
}).catch(()=>{});
</script>
</body>
</html>