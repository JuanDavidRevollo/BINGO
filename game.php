<?php
session_start();
if(!isset($_SESSION['player_id'])||!isset($_SESSION['room_code'])){header('Location: index.php');exit;}
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Jugando – BINGO</title>
<link href="https://fonts.googleapis.com/css2?family=Black+Han+Sans&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
<style>
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
:root{--bg:#0D1B2A;--accent:#00A7FF;--green:#2CE5C6;--yellow:#FFB800;--text:#E8E8E8;--surface:rgba(255,255,255,0.05);--border:rgba(255,255,255,0.1);--marked:rgba(255,184,0,0.85)}
html,body{min-height:100%;font-family:'DM Sans',sans-serif;color:var(--text);background:var(--bg)}
body{min-height:100vh;background-image:url('https://plus.unsplash.com/premium_photo-1717810866948-5f975ec7fa36');
  background-size:cover;background-position:center;background-attachment:fixed;}
body::before{content:'';position:fixed;inset:0;background:rgba(13,27,42,0.9);z-index:0}
.layout{position:relative;z-index:1;max-width:900px;margin:0 auto;padding:1rem}
header{display:flex;align-items:center;justify-content:space-between;margin-bottom:1rem;flex-wrap:wrap;gap:0.5rem}
.logo{font-family:'Black Han Sans',sans-serif;font-size:1.6rem;
  background:linear-gradient(135deg,var(--accent),var(--green));
  -webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text;}
.room-code{font-size:0.75rem;color:rgba(232,232,232,0.35);letter-spacing:0.1em}
.main-grid{display:grid;grid-template-columns:1fr 300px;gap:1rem}
@media(max-width:700px){.main-grid{grid-template-columns:1fr}}

/* Ball display */
.ball-section{background:var(--surface);border:1px solid var(--border);border-radius:18px;padding:1.5rem;backdrop-filter:blur(10px);margin-bottom:1rem}
.ball-label{font-size:0.7rem;letter-spacing:0.15em;text-transform:uppercase;color:rgba(232,232,232,0.35);margin-bottom:0.75rem}
.current-ball{display:flex;align-items:center;gap:1rem}
.ball-circle{width:80px;height:80px;border-radius:50%;display:flex;flex-direction:column;align-items:center;justify-content:center;
  border:3px solid;font-weight:500;flex-shrink:0;transition:all 0.4s;animation:popIn 0.4s ease}
@keyframes popIn{0%{transform:scale(0.5);opacity:0}60%{transform:scale(1.1)}100%{transform:scale(1);opacity:1}}
.ball-letter-disp{font-size:0.75rem;letter-spacing:0.1em}
.ball-num-disp{font-size:1.8rem;line-height:1}
.ball-info{flex:1}
.ball-info h2{font-size:1.6rem;font-weight:500}
.ball-info p{font-size:0.85rem;color:rgba(232,232,232,0.45);margin-top:0.25rem}
.no-ball{font-size:0.9rem;color:rgba(232,232,232,0.35);font-style:italic;padding:0.5rem 0}

/* History */
.history-strip{display:flex;flex-wrap:wrap;gap:5px;margin-top:0.75rem}
.mini-ball{width:32px;height:32px;border-radius:50%;display:flex;align-items:center;justify-content:center;
  font-size:0.75rem;font-weight:500;border:1.5px solid}

/* Cartón */
.carton-section{background:var(--surface);border:1px solid var(--border);border-radius:18px;padding:1.25rem;backdrop-filter:blur(10px)}
.carton-title{font-size:0.7rem;letter-spacing:0.15em;text-transform:uppercase;color:rgba(232,232,232,0.35);margin-bottom:0.75rem}
.carton-grid{display:grid;grid-template-columns:repeat(5,1fr);gap:5px}
.col-header{text-align:center;font-family:'Black Han Sans',sans-serif;font-size:1rem;
  padding:0.4rem;border-radius:8px;}
.A{color:#00A7FF}.B{color:#2CE5C6}.C{color:#FFB800}.D{color:#ff6eb4}.E{color:#c084fc}
.cell{aspect-ratio:1;display:flex;align-items:center;justify-content:center;
  background:rgba(0,0,0,0.25);border:1px solid rgba(255,255,255,0.08);border-radius:9px;
  font-size:0.85rem;font-weight:500;cursor:pointer;transition:all 0.18s;user-select:none;position:relative}
.cell:hover:not(.marked):not(.blocked){background:rgba(255,255,255,0.07);border-color:rgba(255,255,255,0.2)}
.cell.marked{background:rgba(255,184,0,0.25);border-color:rgba(255,184,0,0.6);color:var(--yellow)}
.cell.marked::after{content:'✓';position:absolute;top:2px;right:4px;font-size:0.55rem;color:var(--yellow)}
.cell.shake{animation:shake 0.35s ease}
@keyframes shake{0%,100%{transform:translateX(0)}25%{transform:translateX(-4px)}75%{transform:translateX(4px)}}

/* Sidebar */
.sidebar{}
.info-card{background:var(--surface);border:1px solid var(--border);border-radius:16px;padding:1.25rem;backdrop-filter:blur(10px);margin-bottom:1rem}
.players-mini{display:flex;flex-direction:column;gap:0.4rem}
.pm-row{display:flex;align-items:center;gap:0.5rem;font-size:0.85rem}
.pm-dot{width:7px;height:7px;border-radius:50%;background:var(--green)}
.btn-bingo{width:100%;padding:1rem;border:none;border-radius:14px;
  background:linear-gradient(135deg,var(--accent),var(--green));
  color:#0D1B2A;font-size:1.1rem;font-weight:500;cursor:pointer;
  font-family:'Black Han Sans',sans-serif;letter-spacing:0.05em;
  transition:all 0.2s;margin-top:0.5rem}
.btn-bingo:hover{transform:translateY(-2px);filter:brightness(1.1)}
.btn-bingo:active{transform:translateY(0)}
.warn-msg{font-size:0.8rem;color:#ff6b6b;text-align:center;margin-top:0.5rem;min-height:1.2em}
.timer-bar{height:4px;background:rgba(255,255,255,0.08);border-radius:2px;margin-top:0.75rem;overflow:hidden}
.timer-fill{height:100%;background:var(--accent);border-radius:2px;transition:width 1s linear}

/* Winner overlay */
.overlay{position:fixed;inset:0;background:rgba(13,27,42,0.96);z-index:100;
  display:flex;align-items:center;justify-content:center;display:none}
.winner-card{text-align:center;padding:3rem 2rem;background:var(--surface);
  border:1px solid var(--border);border-radius:24px;max-width:400px;width:90%;backdrop-filter:blur(20px);
  animation:slideUp 0.5s ease}
@keyframes slideUp{from{transform:translateY(40px);opacity:0}to{transform:none;opacity:1}}
.trophy{font-size:4rem;margin-bottom:1rem}
.winner-name{font-family:'Black Han Sans',sans-serif;font-size:2.5rem;
  background:linear-gradient(135deg,var(--yellow),#ff9d00);
  -webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text;}
.btn-home{display:inline-block;margin-top:1.5rem;padding:0.8rem 2rem;background:var(--accent);
  color:#fff;border:none;border-radius:12px;cursor:pointer;font-size:0.95rem;text-decoration:none}

/* Colors per letter */
.bc-A{border-color:#00A7FF;color:#00A7FF}.bc-B{border-color:#2CE5C6;color:#2CE5C6}
.bc-C{border-color:#FFB800;color:#FFB800}.bc-D{border-color:#ff6eb4;color:#ff6eb4}
.bc-E{border-color:#c084fc;color:#c084fc}
.bg-A{background:rgba(0,167,255,0.15)}.bg-B{background:rgba(44,229,198,0.12)}
.bg-C{background:rgba(255,184,0,0.15)}.bg-D{background:rgba(255,110,180,0.12)}.bg-E{background:rgba(192,132,252,0.12)}
</style>
</head>
<body>
<div class="layout">
  <header>
    <div>
      <div class="logo">BINGO</div>
      <div class="room-code">Sala: <?= htmlspecialchars($_SESSION['room_code']) ?></div>
    </div>
    <div id="header-info" style="text-align:right;font-size:0.85rem;color:rgba(232,232,232,0.45)"></div>
  </header>

  <div class="main-grid">
    <div>
      <!-- Ball section -->
      <div class="ball-section">
        <div class="ball-label">Última ficha generada</div>
        <div class="current-ball">
          <div class="ball-circle bc-A" id="main-ball" style="border-color:rgba(255,255,255,0.1)">
            <div class="ball-letter-disp" id="mb-letter" style="color:rgba(232,232,232,0.3)">-</div>
            <div class="ball-num-disp" id="mb-num" style="color:rgba(232,232,232,0.3)">--</div>
          </div>
          <div class="ball-info">
            <h2 id="ball-display">Esperando fichas...</h2>
            <p id="ball-sub">La primera ficha aparecerá en breve</p>
          </div>
        </div>
        <div class="timer-bar"><div class="timer-fill" id="timer-fill" style="width:100%"></div></div>
        <div class="ball-label" style="margin-top:0.75rem">Historial (<span id="hist-count">0</span>)</div>
        <div class="history-strip" id="history-strip"></div>
      </div>

      <!-- Cartón -->
      <div class="carton-section">
        <div class="carton-title">Tu cartón</div>
        <div class="carton-grid" id="carton-grid"></div>
      </div>
    </div>

    <!-- Sidebar -->
    <div class="sidebar">
      <div class="info-card">
        <div class="ball-label">Jugadores</div>
        <div class="players-mini" id="players-mini"></div>
      </div>
      <div class="info-card">
        <div class="ball-label">¿Tienes bingo?</div>
        <p style="font-size:0.82rem;color:rgba(232,232,232,0.4);margin-bottom:0.5rem">Marca tus casillas y presiona el botón cuando tengas una línea completa</p>
        <button class="btn-bingo" onclick="cantarBingo()">¡BINGO!</button>
        <div class="warn-msg" id="warn-msg"></div>
      </div>
    </div>
  </div>
</div>

<!-- Winner overlay -->
<div class="overlay" id="winner-overlay">
  <div class="winner-card">
    <div class="trophy">🏆</div>
    <p style="font-size:0.9rem;color:rgba(232,232,232,0.45);margin-bottom:0.5rem">¡Ganador!</p>
    <div class="winner-name" id="winner-name"></div>
    <p style="margin-top:1rem;color:rgba(232,232,232,0.45);font-size:0.85rem">La partida ha finalizado</p>
    <a href="index.php" class="btn-home" onclick="clearSession()">Volver al inicio</a>
  </div>
</div>

<script>
const myId='<?= $_SESSION['player_id'] ?>';
const roomCode='<?= $_SESSION['room_code'] ?>';
const LETTERS=['A','B','C','D','E'];
const COLORS={A:'#00A7FF',B:'#2CE5C6',C:'#FFB800',D:'#ff6eb4',E:'#c084fc'};
const BG={A:'rgba(0,167,255,0.15)',B:'rgba(44,229,198,0.12)',C:'rgba(255,184,0,0.15)',D:'rgba(255,110,180,0.12)',E:'rgba(192,132,252,0.12)'};

let carton=null;
let marked=[];
let history=[];
let timerInterval=null;
let nextBallIn=0;
let lastBallCount=0;

// Load carton from session or server
async function init(){
  const r=await fetch('api/game.php?action=carton&code='+roomCode);
  const d=await r.json();
  if(!d.ok){location.href='index.php';return}
  carton=d.carton;
  marked=d.marked||[];
  renderCarton();
  poll();
  setInterval(poll,3000);
}

function renderCarton(){
  const grid=document.getElementById('carton-grid');
  grid.innerHTML='';
  // Headers
  LETTERS.forEach(l=>{
    const h=document.createElement('div');
    h.className='col-header '+l;h.textContent=l;grid.appendChild(h);
  });
  // Cells - carton is array[5 cols][5 rows]
  for(let row=0;row<5;row++){
    for(let col=0;col<5;col++){
      const val=carton[col][row];
      const cell=document.createElement('div');
      cell.className='cell';cell.id=`cell-${col}-${row}`;cell.textContent=val;
      if(marked.includes(val))cell.classList.add('marked');
      cell.onclick=()=>markCell(val,col,row,cell);
      grid.appendChild(cell);
    }
  }
}

async function markCell(val,col,row,cell){
  if(cell.classList.contains('marked'))return;
  const r=await fetch('api/game.php',{method:'POST',headers:{'Content-Type':'application/json'},
    body:JSON.stringify({action:'mark',code:roomCode,value:val})});
  const d=await r.json();
  if(d.ok){
    cell.classList.add('marked');
    marked.push(val);
    clearWarn();
  }else{
    showWarn(d.error||'Esa ficha aún no ha salido');
    cell.classList.add('shake');
    setTimeout(()=>cell.classList.remove('shake'),400);
  }
}

async function cantarBingo(){
  const r=await fetch('api/game.php',{method:'POST',headers:{'Content-Type':'application/json'},
    body:JSON.stringify({action:'bingo',code:roomCode})});
  const d=await r.json();
  if(d.ok&&d.won){
    showWinner(d.winner_name);
  }else{
    showWarn(d.error||'Bingo inválido – revisa tu cartón');
  }
}

function showWinner(name){
  document.getElementById('winner-name').textContent=name;
  document.getElementById('winner-overlay').style.display='flex';
}

function clearSession(){sessionStorage.clear()}

async function poll(){
  try{
    const r=await fetch('api/game.php?action=state&code='+roomCode);
    const d=await r.json();
    if(!d.ok){location.href='index.php';return}
    const state=d.state;

    // Winner
    if(state.status==='finalizado'){showWinner(state.winner_name);return}

    // Players
    const pm=document.getElementById('players-mini');
    pm.innerHTML=state.players.map(p=>`<div class="pm-row"><div class="pm-dot"></div><span>${htmlEsc(p.name)}${p.id===myId?' (tú)':''}</span></div>`).join('');
    document.getElementById('header-info').textContent=`${state.history.length} fichas generadas`;

    // History
    if(state.history.length!==lastBallCount){
      lastBallCount=state.history.length;
      updateBall(state.history);
    }

    // Timer
    if(state.next_ball_at){
      const now=Date.now()/1000;
      const remaining=Math.max(0,state.next_ball_at-now);
      const total=state.ball_interval||12;
      updateTimer(remaining,total);
    }
  }catch(e){}
}

function updateBall(hist){
  if(!hist.length){document.getElementById('history-strip').innerHTML='';return}
  history=hist;
  const last=hist[hist.length-1];
  const letter=last[0],num=last.slice(1);
  const c=COLORS[letter];
  const bg=BG[letter];

  const mb=document.getElementById('main-ball');
  mb.className=`ball-circle bc-${letter}`;
  mb.style.borderColor=c;mb.style.background=bg;
  document.getElementById('mb-letter').style.color=c;document.getElementById('mb-letter').textContent=letter;
  document.getElementById('mb-num').style.color=c;document.getElementById('mb-num').textContent=num;
  document.getElementById('ball-display').textContent=last;
  document.getElementById('ball-sub').textContent=`Ficha #${hist.length} · Busca ${last} en tu cartón`;

  // History strip
  document.getElementById('hist-count').textContent=hist.length;
  const strip=document.getElementById('history-strip');
  strip.innerHTML=[...hist].reverse().slice(0,30).map(b=>{
    const l=b[0];return `<div class="mini-ball bc-${l}" style="background:${BG[l]}">${b.slice(1)}</div>`;
  }).join('');
}

function updateTimer(remaining,total){
  const pct=total>0?(remaining/total)*100:100;
  const fill=document.getElementById('timer-fill');
  fill.style.width=Math.round(pct)+'%';
  fill.style.background=pct<25?'#ff6b6b':pct<50?'var(--yellow)':'var(--accent)';
}

function showWarn(msg){
  const el=document.getElementById('warn-msg');el.textContent=msg;
  setTimeout(()=>el.textContent='',3500);
}
function clearWarn(){document.getElementById('warn-msg').textContent=''}
function htmlEsc(s){return s.replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;')}

init();
</script>
</body>
</html>
