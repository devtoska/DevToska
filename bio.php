<?php require_once __DIR__ . '/config.php'; log_visit($_SERVER['REQUEST_URI']); ?>
<!DOCTYPE html>
<html lang="tr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
<title>Biyografi</title>
<style>
  * { margin:0; padding:0; box-sizing:border-box; }
  html, body {
    background:#000; color:#fff;
    font-family:'Inter', system-ui, -apple-system, 'Segoe UI', Roboto, sans-serif;
    overflow-x:hidden; min-height:100%; -webkit-tap-highlight-color:transparent;
  }
  #particles { position:fixed; inset:0; z-index:0; pointer-events:none; }
  #blood { position:fixed; inset:0; z-index:999; pointer-events:none; }
  .wrap { position:relative; z-index:1; display:flex; justify-content:center; padding:50px 16px 100px; }
  .bio-card {
    position:relative; width:100%; max-width:620px; border-radius:26px;
    padding:34px 30px 34px;
    background:rgba(15,0,0,0.75);
    border:1.5px solid rgba(255,43,43,0.55);
    box-shadow:0 0 0 1px rgba(255,40,40,0.08),0 0 70px rgba(255,0,0,0.22),inset 0 0 90px rgba(120,0,0,0.12);
    backdrop-filter:blur(10px); -webkit-backdrop-filter:blur(10px);
    overflow:hidden;
  }
  .close-btn {
    position:absolute; top:20px; right:20px;
    padding:8px 18px; font-size:12px; font-weight:700; letter-spacing:1px;
    color:#ff5b5b; background:rgba(120,0,0,0.18);
    border:1.2px solid rgba(255,60,60,0.55);
    border-radius:999px; cursor:pointer; transition:all .25s ease;
    text-transform:uppercase; z-index:5; font-family:inherit; text-decoration:none;
  }
  .close-btn:hover { background:rgba(200,20,20,0.28); color:#fff; box-shadow:0 0 20px rgba(255,40,40,0.5); }
  .avatar-wrap { display:flex; justify-content:center; margin-bottom:22px; }
  .avatar {
    width:118px; height:118px; border-radius:50%; padding:3px;
    background:linear-gradient(140deg,#ff2b2b,#4a0000 60%,#ff2b2b);
    box-shadow:0 0 30px rgba(255,30,30,0.45),0 0 60px rgba(150,0,0,0.35);
  }
  .avatar img {
    width:100%; height:100%; border-radius:50%; object-fit:cover;
    display:block; background:#111; filter:contrast(1.1) brightness(0.95);
  }
  .name {
    text-align:center; font-size:clamp(26px,6vw,38px); font-weight:900;
    letter-spacing:2px; color:#f2f2f2; text-transform:uppercase;
    text-shadow:0 0 24px rgba(255,40,40,0.35);
  }
  .name span { color:#ff2b2b; text-shadow:0 0 24px rgba(255,30,30,0.7); }
  .tagline {
    display:flex; justify-content:center; align-items:center; gap:10px;
    margin-top:8px; font-size:12px; letter-spacing:3px; color:#9a6a6a;
    text-transform:uppercase; font-weight:600;
  }
  .tagline .star { color:#ff2b2b; font-size:11px; text-shadow:0 0 10px rgba(255,40,40,0.9); }
  .divider { height:1px; margin:26px 0; background:linear-gradient(to right,transparent,rgba(255,50,50,0.55),transparent); }
  .lang-title {
    text-align:center; font-size:14px; font-weight:800; letter-spacing:1px;
    color:#ff4d4d; margin-bottom:14px; text-shadow:0 0 14px rgba(255,40,40,0.5);
  }
  .bio-text { text-align:center; font-size:14.5px; line-height:1.8; color:#d6d6d6; font-weight:400; }
  .bio-text strong { color:#ff3b3b; font-weight:800; }
  .bio-text em { color:#ff5b5b; font-style:italic; font-weight:700; }
  .tags { display:flex; flex-wrap:wrap; justify-content:center; gap:8px; margin:14px 0; }
  .tag {
    padding:5px 14px; font-size:12px; font-weight:600; color:#e0a0a0;
    background:rgba(255,40,40,0.08);
    border:1px solid rgba(255,60,60,0.45);
    border-radius:999px; transition:all .2s ease; cursor:default;
    display:inline-block;
  }
  .tag:hover { background:rgba(255,40,40,0.22); color:#fff; box-shadow:0 0 16px rgba(255,40,40,0.55); transform:translateY(-1px); }
  .github { text-align:center; margin-top:26px; }
  .github-title {
    font-size:14px; font-weight:800; color:#ff4d4d; letter-spacing:1px;
    margin-bottom:12px; text-shadow:0 0 14px rgba(255,40,40,0.5);
  }
  .github-title svg { vertical-align:-3px; margin-right:6px; fill:#ff2b2b; }
  .github a {
    display:inline-flex; align-items:center; gap:9px;
    padding:11px 24px; font-size:13.5px; font-weight:600;
    color:#f0c8c8; text-decoration:none;
    background:rgba(120,0,0,0.20);
    border:1.2px solid rgba(255,60,60,0.5);
    border-radius:999px; transition:all .25s ease;
  }
  .github a:hover { color:#fff; background:rgba(200,20,20,0.3); box-shadow:0 0 24px rgba(255,40,40,0.55); transform:translateY(-2px); }
  .github a svg { fill:#ff2b2b; }
  .quote {
    position:relative; margin-top:30px; padding:18px 20px 18px 42px;
    font-size:13.5px; font-style:italic; font-weight:600;
    text-align:center; color:#d0a0a0; line-height:1.6;
    border-left:3px solid #ff2b2b;
    background:rgba(255,30,30,0.05);
    border-radius:0 14px 14px 0;
    box-shadow:inset 0 0 24px rgba(180,0,0,0.08);
  }
  .quote::before {
    content:'\201C\201C'; position:absolute; left:12px; top:8px;
    font-size:26px; color:#ff2b2b; font-style:normal;
    font-weight:900; opacity:0.85; line-height:1; letter-spacing:-4px;
  }
  @media (max-width: 480px) {
    .bio-card { padding:28px 20px 28px; }
    .close-btn { top:14px; right:14px; padding:6px 14px; font-size:11px; }
    .avatar { width:96px; height:96px; }
    .bio-text { font-size:13.5px; }
  }
</style>
</head>
<body>

<canvas id="particles"></canvas>

<div class="wrap">
  <div class="bio-card">
    <a class="close-btn" href="index.php">Kapat</a>
    <div class="avatar-wrap">
      <div class="avatar"><img src="public/foto.jpg" alt="Avatar"></div>
    </div>
    <h1 class="name">Hakkımda?  <span>&<span> <span>About Me?</span></h1>

    <div class="tagline">
      <span class="star">★</span> 20 &nbsp;|&nbsp; DEVELOPER <span class="star">★</span>
    </div>
    <div class="divider"></div>

    <div class="lang-title"><span class="flag">🇬🇧</span> English</div>
    <p class="bio-text">Hello, I'm 20 years old, and passionate about software development. I have experience with</p>
    <div class="tags">
      <span class="tag">Java</span><span class="tag">JavaScript</span><span class="tag">Python</span>
      <span class="tag">C#</span><span class="tag">PHP</span>
    </div>
    <p class="bio-text">
      I enjoy building projects, developing tools, and continuously improving my skills in
      programming and technology. I speak <strong>English</strong> and <strong>German</strong>,
      and I use the name <em>"Juseff"</em> in Germany.
    </p>

    <div class="divider"></div>

    <div class="lang-title"><span class="flag">🇹🇷</span> Türkçe</div>
    <p class="bio-text">Merhaba, 20 yaşındayım ve yazılım geliştirmeye büyük bir tutku duyuyorum.</p>
    <div class="tags">
      <span class="tag">Java</span><span class="tag">JavaScript</span><span class="tag">Python</span>
      <span class="tag">C#</span><span class="tag">PHP</span>
    </div>
    <p class="bio-text">
      dillerinde deneyime sahibim. Projeler geliştirmeyi, araçlar oluşturmayı ve programlama ile
      teknoloji alanındaki becerilerimi sürekli olarak geliştirmeyi seviyorum. İngilizce ve
      Almanca konuşabiliyorum. Almanya'da <em>"Juseff"</em> ismini kullanıyorum.
    </p>

    <div class="divider"></div>

    <div class="github">
      <div class="github-title">
        <svg width="16" height="16" viewBox="0 0 24 24"><path d="M12 .5C5.7.5.5 5.7.5 12c0 5.1 3.3 9.4 7.9 10.9.6.1.8-.3.8-.6v-2c-3.2.7-3.9-1.5-3.9-1.5-.5-1.3-1.3-1.7-1.3-1.7-1.1-.7.1-.7.1-.7 1.2.1 1.8 1.2 1.8 1.2 1 1.8 2.8 1.3 3.5 1 .1-.8.4-1.3.7-1.6-2.6-.3-5.3-1.3-5.3-5.8 0-1.3.5-2.3 1.2-3.1-.1-.3-.5-1.5.1-3.1 0 0 1-.3 3.3 1.2a11.5 11.5 0 0 1 6 0C17.3 4.6 18.3 4.9 18.3 4.9c.6 1.6.2 2.8.1 3.1.8.8 1.2 1.8 1.2 3.1 0 4.5-2.7 5.5-5.3 5.8.4.4.8 1.1.8 2.2v3.3c0 .3.2.7.8.6 4.6-1.5 7.9-5.8 7.9-10.9C23.5 5.7 18.3.5 12 .5z"/></svg>
        GitHub:
      </div>
      <a href="https://github.com/Heckerandseher" target="_blank" rel="noopener">
        github.com/Heckerandseher
      </a>
    </div>

    <div class="quote">
      Technology, software development, and creating innovative projects are among my biggest interests.
    </div>
  </div>
</div>

<canvas id="blood"></canvas>
<audio id="bgMusic" src="public/tos.mp3" loop></audio>

<script>
const bgMusic = document.getElementById('bgMusic');
bgMusic.volume = 0.55;
function tryPlayMusic(){ bgMusic.play().catch(()=>{}); }
document.addEventListener('pointerdown', tryPlayMusic, { once: true });
document.addEventListener('touchstart', tryPlayMusic, { once: true });
document.addEventListener('click', tryPlayMusic, { once: true });

const pCanvas = document.getElementById('particles');
const pCtx = pCanvas.getContext('2d');
let PW=0, PH=0, DPR=Math.min(devicePixelRatio||1,2);
const particles = [];
function initParticles(){
  particles.length = 0;
  const count = innerWidth < 600 ? 60 : 110;
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
const bCanvas = document.getElementById('blood');
const bCtx = bCanvas.getContext('2d');
let BW=0, BH=0;
function drawSplat(x, y) {
  const base = 16 + Math.random() * 24;
  bCtx.save(); bCtx.filter='blur(1.6px)'; bCtx.fillStyle='rgba(148,0,0,0.88)';
  bCtx.beginPath();
  const blobs = 7 + Math.floor(Math.random()*5);
  for (let i=0;i<blobs;i++){
    const ang=Math.random()*Math.PI*2;
    const dist=Math.random()*base*0.65;
    const r=base*(0.35+Math.random()*0.65);
    const cx=x+Math.cos(ang)*dist, cy=y+Math.sin(ang)*dist;
    bCtx.moveTo(cx+r,cy); bCtx.arc(cx,cy,r,0,Math.PI*2);
  }
  bCtx.fill();
  bCtx.fillStyle='rgba(90,0,0,0.65)';
  bCtx.beginPath(); bCtx.arc(x,y,base*0.55,0,Math.PI*2); bCtx.fill();
  bCtx.restore();
  bCtx.save(); bCtx.filter='blur(0.5px)';
  const drops = 12+Math.floor(Math.random()*16);
  for (let i=0;i<drops;i++){
    const ang=Math.random()*Math.PI*2;
    const dist=base*(1+Math.random()*3.4);
    const r=Math.random()*3.6+0.7;
    const red=110+Math.floor(Math.random()*70);
    bCtx.fillStyle='rgba('+red+',0,0,'+(0.35+Math.random()*0.55).toFixed(2)+')';
    bCtx.beginPath(); bCtx.arc(x+Math.cos(ang)*dist,y+Math.sin(ang)*dist,r,0,Math.PI*2); bCtx.fill();
  }
  bCtx.restore();
  const drips = 1+Math.floor(Math.random()*3);
  for (let i=0;i<drips;i++){
    const dx=x+(Math.random()-0.5)*base*1.3;
    const dy=y+base*0.35;
    const len=30+Math.random()*90;
    const w=2+Math.random()*3.5;
    const g=bCtx.createLinearGradient(dx,dy,dx,dy+len);
    g.addColorStop(0,'rgba(140,0,0,0.75)');
    g.addColorStop(0.6,'rgba(110,0,0,0.35)');
    g.addColorStop(1,'rgba(80,0,0,0)');
    bCtx.save(); bCtx.filter='blur(1px)'; bCtx.fillStyle=g;
    bCtx.beginPath(); bCtx.ellipse(dx,dy+len/2,w/2,len/2,0,0,Math.PI*2); bCtx.fill(); bCtx.restore();
  }
}
let fadeTimer=0;
function fadeBlood(){
  fadeTimer++;
  if (fadeTimer%3===0){
    bCtx.save(); bCtx.globalCompositeOperation='destination-out';
    bCtx.fillStyle='rgba(0,0,0,0.010)';
    bCtx.fillRect(0,0,BW,BH); bCtx.restore();
  }
  requestAnimationFrame(fadeBlood);
}
function resize(){
  DPR=Math.min(devicePixelRatio||1,2);
  const w=innerWidth, h=innerHeight;
  PW=w; PH=h;
  pCanvas.width=w*DPR; pCanvas.height=h*DPR;
  pCanvas.style.width=w+'px'; pCanvas.style.height=h+'px';
  pCtx.setTransform(DPR,0,0,DPR,0,0);
  initParticles();
  BW=w; BH=h;
  bCanvas.width=w*DPR; bCanvas.height=h*DPR;
  bCanvas.style.width=w+'px'; bCanvas.style.height=h+'px';
  bCtx.setTransform(DPR,0,0,DPR,0,0);
}
addEventListener('resize',resize);
addEventListener('pointerdown', e => drawSplat(e.clientX, e.clientY), { passive:true });
addEventListener('touchstart', e => {
  for (let i=0;i<e.touches.length;i++) drawSplat(e.touches[i].clientX, e.touches[i].clientY);
}, { passive:true });
let lastMove=0;
addEventListener('pointermove', e => {
  if (e.pointerType==='mouse' && e.buttons===1){
    const now=Date.now();
    if (now-lastMove>120){ lastMove=now; drawSplat(e.clientX,e.clientY); }
  }
}, { passive:true });
resize(); drawParticles(); fadeBlood();
</script>
</body>
</html>