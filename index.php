<?php
require_once __DIR__ . '/config.php';
log_visit($_SERVER['REQUEST_URI']);
?>
<!DOCTYPE html>
<html lang="tr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
<title>Hesaplarım</title>
<style>
  * { margin:0; padding:0; box-sizing:border-box; }
  html, body {
    background:#000; color:#fff;
    font-family:'Inter', system-ui, -apple-system, 'Segoe UI', Roboto, sans-serif;
    overflow-x:hidden; min-height:100%; -webkit-tap-highlight-color:transparent;
  }
  #particles { position:fixed; inset:0; z-index:0; pointer-events:none; }
  #blood { position:fixed; inset:0; z-index:999; pointer-events:none; }
  .wrap { position:relative; z-index:1; }
  .video-box {
    position:relative; width:100%; background:#000; line-height:0;
    border-bottom:1px solid rgba(255,0,0,0.35);
    box-shadow:0 0 60px rgba(255,0,0,0.12);
  }
  .video-box video {
    width:100%; max-height:78vh; object-fit:cover; display:block; background:#000;
  }
  .video-box::after {
    content:''; position:absolute; left:0; right:0; bottom:-1px; height:120px;
    background:linear-gradient(to bottom,transparent,#000); pointer-events:none;
  }
  .accounts { max-width:1100px; margin:0 auto; padding:70px 22px 120px; }
  .accounts h1 {
    font-size:clamp(28px,5vw,44px); font-weight:800; letter-spacing:-1px;
    text-align:center; margin-bottom:10px;
    text-shadow:0 0 30px rgba(255,0,0,0.45);
  }
  .accounts h1 span { color:#ff2b2b; }
  .subtitle {
    text-align:center; color:#7a7a7a; font-size:14px;
    letter-spacing:2px; text-transform:uppercase; margin-bottom:48px;
  }
  .grid {
    display:grid; grid-template-columns:repeat(auto-fit,minmax(230px,1fr)); gap:18px;
  }
  .card {
    position:relative; display:flex; align-items:center; gap:14px;
    padding:18px 20px; border-radius:16px;
    background:rgba(255,255,255,0.035);
    border:1px solid rgba(255,255,255,0.08);
    backdrop-filter:blur(8px); -webkit-backdrop-filter:blur(8px);
    text-decoration:none; color:#fff;
    transition:transform .25s ease,border-color .25s ease,box-shadow .25s ease,background .25s ease;
    overflow:hidden;
  }
  .card::before {
    content:''; position:absolute; inset:0;
    background:radial-gradient(circle at 20% 0%,rgba(255,0,0,0.18),transparent 70%);
    opacity:0; transition:opacity .3s ease; pointer-events:none;
  }
  .card:hover {
    transform:translateY(-5px);
    border-color:rgba(255,43,43,0.55);
    box-shadow:0 12px 40px rgba(255,0,0,0.22);
    background:rgba(255,255,255,0.06);
  }
  .card:hover::before { opacity:1; }
  .avatar {
    flex:0 0 46px; width:46px; height:46px; border-radius:50%;
    display:grid; place-items:center; font-weight:800; font-size:18px; color:#fff;
    background:linear-gradient(145deg,#1a1a1a,#0a0a0a);
    border:1px solid rgba(255,255,255,0.12);
    box-shadow:inset 0 0 12px rgba(255,0,0,0.25);
  }
  .card:hover .avatar {
    border-color:rgba(255,43,43,0.7);
    box-shadow:0 0 18px rgba(255,0,0,0.5);
  }
  .info { display:flex; flex-direction:column; gap:3px; min-width:0; }
  .info .name { font-weight:700; font-size:15px; letter-spacing:.2px; }
  .info .handle {
    font-size:12.5px; color:#8a8a8a;
    white-space:nowrap; overflow:hidden; text-overflow:ellipsis;
  }
  .hint {
    text-align:center; margin-top:60px; font-size:13px;
    color:#4d4d4d; letter-spacing:1.5px;
    animation:pulse 2.6s ease-in-out infinite;
  }
  @keyframes pulse { 0%,100%{opacity:.35;} 50%{opacity:.9;} }
</style>
</head>
<body>

<canvas id="particles"></canvas>

<div class="wrap">
  <div class="video-box">
    <video id="bgVideo" src="public/tos.mp4" autoplay muted loop playsinline></video>
  </div>

  <section class="accounts">
    <h1>Hesaplarım &<span>& Account</span></h1>
    <p class="subtitle">Social Media or &amp; Platform</p>

    <div class="grid">
      <a class="card" href="https://github.com/Heckerandseher" target="_blank" rel="noopener">
        <div class="avatar">G</div>
        <div class="info"><span class="name">GitHub</span><span class="handle">Heckerandseher</span></div>
      </a>
      <a class="card" href="https://discord.gg/jkGnndrbsB" target="_blank" rel="noopener">
        <div class="avatar">D</div>
        <div class="info"><span class="name">Discord</span><span class="handle">Sunucuya Katıl</span></div>
      </a>
      <a class="card" href="https://t.me/toskaorj" target="_blank" rel="noopener">
        <div class="avatar">T</div>
        <div class="info"><span class="name">Telegram</span><span class="handle">Biyografi & Bio</span></div>
      </a>
      <a class="card" href="https://t.me/refkefonay" target="_blank" rel="noopener">
        <div class="avatar">T</div>
        <div class="info"><span class="name">Telegram</span><span class="handle">Güvence Grubum</span></div>
      </a>
      <a class="card" href="https://t.me/botduyuruyeri" target="_blank" rel="noopener">
        <div class="avatar">T</div>
        <div class="info"><span class="name">Telegram</span><span class="handle">My Channel</span></div>
      </a>
      <a class="card" href="tg://openmessage?user_id=5562534115" target="_blank" rel="noopener">
        <div class="avatar">T</div>
        <div class="info"><span class="name">Telegram</span><span class="handle">Mesaj Gönder & Send Message.</span></div>
      </a>
      <a class="card" href="https://tiktok.com/@almanfilozof" target="_blank" rel="noopener">
        <div class="avatar">T</div>
        <div class="info"><span class="name">TikTok</span><span class="handle">@almanfilozof</span></div>
      </a>
      <a class="card" href="chat.php">
        <div class="avatar">AI</div>
        <div class="info"><span class="name">Tosmen AI </span><span class="handle">Sohbet et & Chat</span></div>
      </a>
      <a class="card" href="bio.php">
        <div class="avatar">B</div>
        <div class="info"><span class="name">Biyografi  & My Bio</span><span class="handle">Hakkımda</span></div>
      </a>
    </div>

    <p class="hint">touch the screen & ekrana dokun </p>
  </section>
</div>

<canvas id="blood"></canvas>

<script>
const pCanvas = document.getElementById('particles');
const pCtx = pCanvas.getContext('2d');
let PW = 0, PH = 0, DPR = Math.min(window.devicePixelRatio || 1, 2);
const particles = [];
const PARTICLE_COUNT = 110;

function initParticles() {
  particles.length = 0;
  const count = window.innerWidth < 600 ? 60 : PARTICLE_COUNT;
  for (let i = 0; i < count; i++) {
    particles.push({
      x: Math.random() * PW, y: Math.random() * PH,
      r: Math.random() * 1.8 + 0.4,
      vx: (Math.random() - 0.5) * 0.28,
      vy: (Math.random() - 0.5) * 0.28 - 0.08,
      a: Math.random() * 0.6 + 0.15,
      tw: Math.random() * Math.PI * 2,
      twSpeed: Math.random() * 0.02 + 0.005
    });
  }
}

function drawParticles() {
  pCtx.clearRect(0, 0, PW, PH);
  for (let i = 0; i < particles.length; i++) {
    const p = particles[i];
    p.x += p.vx; p.y += p.vy; p.tw += p.twSpeed;
    if (p.x < -10) p.x = PW + 10;
    if (p.x > PW + 10) p.x = -10;
    if (p.y < -10) p.y = PH + 10;
    if (p.y > PH + 10) p.y = -10;
    const alpha = p.a * (0.55 + 0.45 * Math.sin(p.tw));
    pCtx.beginPath();
    pCtx.arc(p.x, p.y, p.r, 0, Math.PI * 2);
    pCtx.fillStyle = 'rgba(255,255,255,' + alpha.toFixed(3) + ')';
    pCtx.shadowBlur = 8;
    pCtx.shadowColor = 'rgba(255,255,255,0.85)';
    pCtx.fill();
  }
  pCtx.shadowBlur = 0;
  requestAnimationFrame(drawParticles);
}

const bCanvas = document.getElementById('blood');
const bCtx = bCanvas.getContext('2d');
let BW = 0, BH = 0;

function drawSplat(x, y) {
  const base = 16 + Math.random() * 24;

  bCtx.save();
  bCtx.filter = 'blur(1.6px)';
  bCtx.fillStyle = 'rgba(148,0,0,0.88)';
  bCtx.beginPath();
  const blobs = 7 + Math.floor(Math.random() * 5);
  for (let i = 0; i < blobs; i++) {
    const ang = Math.random() * Math.PI * 2;
    const dist = Math.random() * base * 0.65;
    const r = base * (0.35 + Math.random() * 0.65);
    const cx = x + Math.cos(ang) * dist;
    const cy = y + Math.sin(ang) * dist;
    bCtx.moveTo(cx + r, cy);
    bCtx.arc(cx, cy, r, 0, Math.PI * 2);
  }
  bCtx.fill();

  bCtx.fillStyle = 'rgba(90,0,0,0.65)';
  bCtx.beginPath();
  bCtx.arc(x, y, base * 0.55, 0, Math.PI * 2);
  bCtx.fill();
  bCtx.restore();

  bCtx.save();
  bCtx.filter = 'blur(0.5px)';
  const drops = 12 + Math.floor(Math.random() * 16);
  for (let i = 0; i < drops; i++) {
    const ang = Math.random() * Math.PI * 2;
    const dist = base * (1 + Math.random() * 3.4);
    const r = Math.random() * 3.6 + 0.7;
    const red = 110 + Math.floor(Math.random() * 70);
    bCtx.fillStyle = 'rgba(' + red + ',0,0,' + (0.35 + Math.random() * 0.55).toFixed(2) + ')';
    bCtx.beginPath();
    bCtx.arc(x + Math.cos(ang) * dist, y + Math.sin(ang) * dist, r, 0, Math.PI * 2);
    bCtx.fill();
  }
  bCtx.restore();

  const drips = 1 + Math.floor(Math.random() * 3);
  for (let i = 0; i < drips; i++) {
    const dx = x + (Math.random() - 0.5) * base * 1.3;
    const dy = y + base * 0.35;
    const len = 30 + Math.random() * 90;
    const w = 2 + Math.random() * 3.5;
    const g = bCtx.createLinearGradient(dx, dy, dx, dy + len);
    g.addColorStop(0, 'rgba(140,0,0,0.75)');
    g.addColorStop(0.6, 'rgba(110,0,0,0.35)');
    g.addColorStop(1, 'rgba(80,0,0,0)');
    bCtx.save();
    bCtx.filter = 'blur(1px)';
    bCtx.fillStyle = g;
    bCtx.beginPath();
    bCtx.ellipse(dx, dy + len / 2, w / 2, len / 2, 0, 0, Math.PI * 2);
    bCtx.fill();
    bCtx.restore();
  }
}

let fadeTimer = 0;
function fadeBlood() {
  fadeTimer++;
  if (fadeTimer % 3 === 0) {
    bCtx.save();
    bCtx.globalCompositeOperation = 'destination-out';
    bCtx.fillStyle = 'rgba(0,0,0,0.010)';
    bCtx.fillRect(0, 0, BW, BH);
    bCtx.restore();
  }
  requestAnimationFrame(fadeBlood);
}

function resize() {
  DPR = Math.min(window.devicePixelRatio || 1, 2);
  const w = window.innerWidth, h = window.innerHeight;
  PW = w; PH = h;
  pCanvas.width = w * DPR; pCanvas.height = h * DPR;
  pCanvas.style.width = w + 'px'; pCanvas.style.height = h + 'px';
  pCtx.setTransform(DPR, 0, 0, DPR, 0, 0);
  initParticles();

  BW = w; BH = h;
  bCanvas.width = w * DPR; bCanvas.height = h * DPR;
  bCanvas.style.width = w + 'px'; bCanvas.style.height = h + 'px';
  bCtx.setTransform(DPR, 0, 0, DPR, 0, 0);
}

window.addEventListener('resize', resize);

function handlePoint(clientX, clientY, intensity) {
  const count = intensity || 1;
  for (let i = 0; i < count; i++) {
    const ox = i === 0 ? 0 : (Math.random() - 0.5) * 60;
    const oy = i === 0 ? 0 : (Math.random() - 0.5) * 60;
    drawSplat(clientX + ox, clientY + oy);
  }
}

window.addEventListener('pointerdown', e => handlePoint(e.clientX, e.clientY, 1), { passive: true });
window.addEventListener('touchstart', e => {
  for (let i = 0; i < e.touches.length; i++) {
    if (i > 0) handlePoint(e.touches[i].clientX, e.touches[i].clientY, 1);
  }
}, { passive: true });

let lastMove = 0;
window.addEventListener('pointermove', e => {
  if (e.pointerType === 'mouse' && e.buttons === 1) {
    const now = Date.now();
    if (now - lastMove > 120) { lastMove = now; drawSplat(e.clientX, e.clientY); }
  }
}, { passive: true });

const video = document.getElementById('bgVideo');
video.volume = 1.0;
function tryPlayVideo() { video.play().catch(() => {}); }
video.addEventListener('loadeddata', tryPlayVideo);
document.addEventListener('pointerdown', tryPlayVideo, { once: true });
document.addEventListener('touchstart', tryPlayVideo, { once: true });

resize(); drawParticles(); fadeBlood();
</script>
</body>
</html>