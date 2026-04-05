<?php session_start(); ?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>BINGO ONLINE</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Black+Han+Sans&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
<style>
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
:root{
  --bg:#0D1B2A;
  --accent:#00A7FF;
  --green:#2CE5C6;
  --yellow:#FFB800;
  --text:#E8E8E8;
  --surface:rgba(255,255,255,0.05);
  --border:rgba(255,255,255,0.1);
}
html,body{height:100%;font-family:'DM Sans',sans-serif;color:var(--text);background:var(--bg)}
body{
  min-height:100vh;
  background-image:url('https://plus.unsplash.com/premium_photo-1717810866948-5f975ec7fa36');
  background-size:cover;background-position:center;background-repeat:no-repeat;background-attachment:fixed;
  display:flex;align-items:center;justify-content:center;
}
body::before{content:'';position:fixed;inset:0;background:rgba(13,27,42,0.88);z-index:0}
.page{position:relative;z-index:1;width:100%;max-width:480px;padding:2rem 1rem}
.logo{text-align:center;margin-bottom:2.5rem}
.logo h1{font-family:'Black Han Sans',sans-serif;font-size:4rem;letter-spacing:0.05em;
  background:linear-gradient(135deg,var(--accent),var(--green));
  -webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text;
  filter:drop-shadow(0 0 30px rgba(0,167,255,0.4));
}
.logo p{font-size:0.85rem;letter-spacing:0.2em;text-transform:uppercase;color:rgba(232,232,232,0.45);margin-top:0.25rem}
.card{background:var(--surface);border:1px solid var(--border);border-radius:20px;padding:2rem;backdrop-filter:blur(12px)}
.tabs{display:flex;gap:0.5rem;margin-bottom:1.75rem;background:rgba(0,0,0,0.25);border-radius:12px;padding:4px}
.tab{flex:1;padding:0.6rem;border:none;background:transparent;color:rgba(232,232,232,0.5);
  border-radius:9px;cursor:pointer;font-family:'DM Sans',sans-serif;font-size:0.9rem;font-weight:500;transition:all 0.2s}
.tab.active{background:var(--accent);color:#fff}
.panel{display:none}.panel.active{display:block}
label{display:block;font-size:0.8rem;letter-spacing:0.1em;text-transform:uppercase;
  color:rgba(232,232,232,0.5);margin-bottom:0.4rem;margin-top:1rem}
label:first-child{margin-top:0}
input[type=text]{width:100%;padding:0.75rem 1rem;background:rgba(0,0,0,0.3);
  border:1px solid var(--border);border-radius:12px;color:var(--text);
  font-family:'DM Sans',sans-serif;font-size:1rem;outline:none;transition:border-color 0.2s}
input:focus{border-color:var(--accent)}
input[type=text]::placeholder{color:rgba(232,232,232,0.25)}
.btn{width:100%;padding:0.85rem;border:none;border-radius:12px;font-size:1rem;
  font-weight:500;cursor:pointer;margin-top:1.25rem;transition:all 0.2s;font-family:'DM Sans',sans-serif;letter-spacing:0.02em}
.btn-blue{background:var(--accent);color:#fff}
.btn-blue:hover{background:#0090dd;transform:translateY(-1px)}
.btn-green{background:var(--green);color:#0D1B2A}
.btn-green:hover{background:#25cdb0;transform:translateY(-1px)}
.btn:active{transform:translateY(0)}
.error{color:#ff6b6b;font-size:0.85rem;margin-top:0.75rem;text-align:center;display:none}
.divider{height:1px;background:var(--border);margin:1.5rem 0}
.hint{font-size:0.8rem;color:rgba(232,232,232,0.3);text-align:center;margin-top:1rem}
@media(max-width:480px){.logo h1{font-size:3rem}.card{padding:1.5rem}}
</style>
</head>
<body>
<div class="page">
  <div class="logo">
    <h1>BINGO</h1>
    <p>Online Multiplayer</p>
  </div>
  <div class="card">
    <div class="tabs">
      <button class="tab active" onclick="switchTab('create')">Crear sala</button>
      <button class="tab" onclick="switchTab('join')">Unirse</button>
    </div>

    <div id="panel-create" class="panel active">
      <label>Tu nombre</label>
      <input type="text" id="create-name" placeholder="ej. María" maxlength="20">
      <label>Nombre de la sala</label>
      <input type="text" id="create-room" placeholder="ej. Bingo Familiar" maxlength="30">
      <button class="btn btn-blue" onclick="createRoom()">Crear sala →</button>
      <div class="error" id="err-create"></div>
    </div>

    <div id="panel-join" class="panel">
      <label>Tu nombre</label>
      <input type="text" id="join-name" placeholder="ej. Carlos" maxlength="20">
      <label>Código de sala</label>
      <input type="text" id="join-code" placeholder="ej. A7X3" maxlength="6" style="text-transform:uppercase;letter-spacing:0.2em;font-size:1.3rem;text-align:center">
      <button class="btn btn-green" onclick="joinRoom()">Unirse →</button>
      <div class="error" id="err-join"></div>
    </div>

    <div class="divider"></div>
    <p class="hint">Comparte el código con tus amigos para jugar juntos</p>
  </div>
</div>
<script>
function switchTab(t){
  document.querySelectorAll('.tab').forEach((el,i)=>el.classList.toggle('active',['create','join'][i]===t));
  document.querySelectorAll('.panel').forEach(el=>el.classList.remove('active'));
  document.getElementById('panel-'+t).classList.add('active');
}
async function createRoom(){
  const name=document.getElementById('create-name').value.trim();
  const room=document.getElementById('create-room').value.trim();
  const err=document.getElementById('err-create');
  if(!name||!room){err.textContent='Completa todos los campos';err.style.display='block';return;}
  err.style.display='none';
  const r=await fetch('api/room.php',{method:'POST',headers:{'Content-Type':'application/json'},
    body:JSON.stringify({action:'create',name,room})});
  const d=await r.json();
  if(d.ok){location.href='lobby.php'}else{err.textContent=d.error;err.style.display='block'}
}
async function joinRoom(){
  const name=document.getElementById('join-name').value.trim();
  const code=document.getElementById('join-code').value.trim().toUpperCase();
  const err=document.getElementById('err-join');
  if(!name||!code){err.textContent='Completa todos los campos';err.style.display='block';return;}
  err.style.display='none';
  const r=await fetch('api/room.php',{method:'POST',headers:{'Content-Type':'application/json'},
    body:JSON.stringify({action:'join',name,code})});
  const d=await r.json();
  if(d.ok){location.href='lobby.php'}else{err.textContent=d.error;err.style.display='block'}
}
document.getElementById('join-code').addEventListener('input',function(){this.value=this.value.toUpperCase()});
</script>
</body>
</html>
