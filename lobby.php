<?php
session_start();
if(!isset($_SESSION['player_id'])||!isset($_SESSION['room_code'])){header('Location: index.php');exit;}
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Sala de espera – BINGO</title>
<link href="https://fonts.googleapis.com/css2?family=Black+Han+Sans&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
<style>
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
:root{--bg:#0D1B2A;--accent:#00A7FF;--green:#2CE5C6;--yellow:#FFB800;--text:#E8E8E8;--surface:rgba(255,255,255,0.05);--border:rgba(255,255,255,0.1)}
html,body{height:100%;font-family:'DM Sans',sans-serif;color:var(--text);background:var(--bg)}
body{min-height:100vh;background-image:url('https://plus.unsplash.com/premium_photo-1717810866948-5f975ec7fa36');
  background-size:cover;background-position:center;background-attachment:fixed;
  display:flex;align-items:center;justify-content:center;}
body::before{content:'';position:fixed;inset:0;background:rgba(13,27,42,0.88);z-index:0}
.page{position:relative;z-index:1;width:100%;max-width:540px;padding:2rem 1rem}
.logo{font-family:'Black Han Sans',sans-serif;font-size:2rem;letter-spacing:0.05em;
  background:linear-gradient(135deg,var(--accent),var(--green));
  -webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text;margin-bottom:0.25rem}
.card{background:var(--surface);border:1px solid var(--border);border-radius:20px;padding:2rem;backdrop-filter:blur(12px);margin-bottom:1rem}
.room-header{display:flex;align-items:center;justify-content:space-between;margin-bottom:1.5rem}
.room-title{font-size:1.3rem;font-weight:500}
.code-badge{background:rgba(0,167,255,0.15);border:1px solid rgba(0,167,255,0.3);
  border-radius:10px;padding:0.4rem 0.9rem;font-family:'Black Han Sans',sans-serif;
  font-size:1.4rem;letter-spacing:0.2em;color:var(--accent);cursor:pointer;position:relative}
.copy-tip{position:absolute;bottom:-24px;left:50%;transform:translateX(-50%);
  font-size:0.7rem;white-space:nowrap;color:var(--green);opacity:0;transition:opacity 0.3s}
.section-label{font-size:0.75rem;letter-spacing:0.12em;text-transform:uppercase;
  color:rgba(232,232,232,0.4);margin-bottom:0.75rem}
.players-list{display:flex;flex-direction:column;gap:0.5rem;min-height:60px}
.player-row{display:flex;align-items:center;gap:0.75rem;padding:0.6rem 0.8rem;
  background:rgba(0,0,0,0.2);border-radius:10px;animation:fadeIn 0.3s ease}
@keyframes fadeIn{from{opacity:0;transform:translateY(4px)}to{opacity:1;transform:none}}
.avatar{width:32px;height:32px;border-radius:50%;display:flex;align-items:center;justify-content:center;
  font-size:0.8rem;font-weight:500}
.av-host{background:rgba(255,184,0,0.2);color:var(--yellow);border:1px solid rgba(255,184,0,0.3)}
.av-player{background:rgba(0,167,255,0.15);color:var(--accent);border:1px solid rgba(0,167,255,0.2)}
.player-name{font-size:0.95rem;flex:1}
.host-tag{font-size:0.7rem;color:var(--yellow);background:rgba(255,184,0,0.12);
  border:1px solid rgba(255,184,0,0.25);border-radius:6px;padding:2px 8px}
.you-tag{font-size:0.7rem;color:var(--green);background:rgba(44,229,198,0.1);
  border:1px solid rgba(44,229,198,0.2);border-radius:6px;padding:2px 8px}
.waiting-msg{text-align:center;padding:1.5rem;color:rgba(232,232,232,0.45);font-size:0.9rem}
.dots::after{content:'';animation:dots 1.5s infinite}
@keyframes dots{0%{content:''}33%{content:'.'}66%{content:'..'}100%{content:'...'}}
.btn{width:100%;padding:0.85rem;border:none;border-radius:12px;font-size:1rem;
  font-weight:500;cursor:pointer;transition:all 0.2s;font-family:'DM Sans',sans-serif}
.btn-blue{background:var(--accent);color:#fff}
.btn-blue:hover{background:#0090dd;transform:translateY(-1px)}
.btn-blue:disabled{background:rgba(0,167,255,0.3);cursor:not-allowed;transform:none}
.btn-ghost{background:transparent;color:rgba(232,232,232,0.4);font-size:0.85rem;margin-top:0.5rem;
  border:1px solid var(--border)}
.btn-ghost:hover{color:var(--text);background:rgba(255,255,255,0.05)}
.status-dot{width:8px;height:8px;border-radius:50%;background:var(--green);
  animation:pulse 2s infinite;margin-right:6px}
@keyframes pulse{0%,100%{opacity:1}50%{opacity:0.3}}
@media(max-width:480px){.card{padding:1.25rem}}
</style>
</head>
<body>
<div class="page">
  <div class="logo">BINGO</div>
  <p style="font-size:0.8rem;color:rgba(232,232,232,0.35);letter-spacing:0.1em;text-transform:uppercase;margin-bottom:1.5rem">Sala de espera</p>

  <div class="card">
    <div class="room-header">
      <div>
        <div class="section-label">Sala</div>
        <div class="room-title" id="room-name">Cargando...</div>
      </div>
      <div>
        <div class="section-label" style="text-align:right">Código</div>
        <div class="code-badge" onclick="copyCode()" id="code-badge">
          <?= htmlspecialchars($_SESSION['room_code']) ?>
          <span class="copy-tip" id="copy-tip">¡Copiado!</span>
        </div>
      </div>
    </div>

    <div class="section-label"><span class="status-dot" style="display:inline-block"></span>Jugadores conectados (<span id="player-count">0</span>)</div>
    <div class="players-list" id="players-list">
      <div class="waiting-msg">Cargando...</div>
    </div>
  </div>

  <div id="host-controls" style="display:none">
    <button class="btn btn-blue" id="start-btn" onclick="startGame()" disabled>Iniciar partida</button>
    <p style="text-align:center;font-size:0.78rem;color:rgba(232,232,232,0.3);margin-top:0.5rem">Necesitas al menos 1 jugador</p>
  </div>
  <div id="guest-controls" style="display:none">
    <div style="text-align:center;padding:1rem;background:var(--surface);border:1px solid var(--border);border-radius:14px;backdrop-filter:blur(8px)">
      <p style="font-size:0.9rem;color:rgba(232,232,232,0.5)">Esperando que el host inicie<span class="dots"></span></p>
    </div>
  </div>

  <button class="btn btn-ghost" style="margin-top:0.75rem" onclick="leaveRoom()">Salir de la sala</button>
</div>
<script>
const myId='<?= $_SESSION['player_id'] ?>';
const roomCode='<?= $_SESSION['room_code'] ?>';
let isHost=false;

async function poll(){
  try{
    const r=await fetch('api/room.php?action=state&code='+roomCode);
    const d=await r.json();
    if(!d.ok){location.href='index.php';return}
    const room=d.room;
    if(room.status==='jugando'){location.href='game.php';return}
    document.getElementById('room-name').textContent=room.name;
    document.getElementById('player-count').textContent=room.players.length;
    renderPlayers(room.players,room.host_id);
    isHost=(room.host_id===myId);
    document.getElementById('host-controls').style.display=isHost?'block':'none';
    document.getElementById('guest-controls').style.display=isHost?'none':'block';
    if(isHost){
      document.getElementById('start-btn').disabled=room.players.length<1;
    }
  }catch(e){}
}

function renderPlayers(players,hostId){
  const el=document.getElementById('players-list');
  if(!players.length){el.innerHTML='<div class="waiting-msg">Sin jugadores aún...</div>';return}
  el.innerHTML=players.map(p=>{
    const isH=p.id===hostId, isMe=p.id===myId;
    const initials=p.name.slice(0,2).toUpperCase();
    return `<div class="player-row">
      <div class="avatar ${isH?'av-host':'av-player'}">${initials}</div>
      <span class="player-name">${htmlEsc(p.name)}</span>
      ${isH?'<span class="host-tag">Host</span>':''}
      ${isMe?'<span class="you-tag">Tú</span>':''}
    </div>`;
  }).join('');
}

function htmlEsc(s){return s.replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;')}

async function startGame(){
  const btn=document.getElementById('start-btn');
  btn.disabled=true;btn.textContent='Iniciando...';
  const r=await fetch('api/room.php',{method:'POST',headers:{'Content-Type':'application/json'},
    body:JSON.stringify({action:'start',code:roomCode})});
  const d=await r.json();
  if(d.ok){location.href='game.php'}else{btn.disabled=false;btn.textContent='Iniciar partida';alert(d.error)}
}

async function leaveRoom(){
  await fetch('api/room.php',{method:'POST',headers:{'Content-Type':'application/json'},
    body:JSON.stringify({action:'leave',code:roomCode})});
  location.href='index.php';
}

function copyCode(){
  navigator.clipboard.writeText(roomCode).then(()=>{
    const tip=document.getElementById('copy-tip');
    tip.style.opacity='1';setTimeout(()=>tip.style.opacity='0',2000);
  });
}

poll();setInterval(poll,2500);
</script>
</body>
</html>
