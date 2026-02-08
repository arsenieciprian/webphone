<?php
/* set the include path */
$conf = glob($_SERVER['DOCUMENT_ROOT'] . "/*/resources/config.php");
require_once dirname(dirname(__DIR__)) . "/resources/require.php";
require_once "resources/check_auth.php";

// Header-ul standard FusionPBX
require_once "resources/header.php";

// 1. Preluare date automate din FusionPBX
$user_name = $_SESSION['username'];
$domain_uuid = $_SESSION['domain_uuid'];
$domain_name = $_SESSION['domain_name'];
$wss_url = "wss://" . $domain_name . ":7443";

// 2. Query SQL
$sql = "SELECT e.extension, e.password ";
$sql .= "FROM v_extensions as e ";
$sql .= "JOIN v_extension_users as eu ON eu.extension_uuid = e.extension_uuid ";
$sql .= "JOIN v_users as u ON u.user_uuid = eu.user_uuid ";
$sql .= "WHERE e.domain_uuid = :domain_uuid ";
$sql .= "AND u.username = :username ";

$parameters['domain_uuid'] = $domain_uuid;
$parameters['username'] = $user_name;

$db = new database;
$row = $db->select($sql, $parameters, 'row');

if (!is_array($row) || sizeof($row) == 0) {
    echo "<div class='alert alert-danger'>Eroare: Nu am gasit extensia SIP pentru utilizatorul curent.</div>";
    require_once "resources/footer.php";
    exit;
}

$sip_user = $row['extension'];
$sip_pass = $row['password'];

// Ascundem elementele standard FusionPBX pentru aspect Full Screen
echo "<style>.action_bar { display: none; } #footer { display: none; }</style>\n";
?>

<script src="jssip.min.js"></script>

<style>
    /* --- LAYOUT GENERAL --- */
    body { overflow: hidden; background-color: #ecf0f1; } 
    
    .vox-app-container {
        display: flex;
        width: 99%;
        height: 88vh; 
        margin: 10px auto;
        background-color: #fff;
        border-radius: 8px;
        box-shadow: 0 0 20px rgba(0,0,0,0.1);
        overflow: hidden;
        border: 1px solid #bdc3c7;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }

    /* --- COLORS --- */
    :root {
        --col-side: #2c3e50;
        --col-main: #f7f9fa;
        --col-hist: #ffffff;
        --accent-blue: #3498db;
        --accent-green: #27ae60;
        --accent-red: #c0392b;
        --accent-yellow: #f1c40f;
        --text-color: #34495e;
    }

    /* --- COLOANA 1: SIDEBAR (STANGA) --- */
    .col-side {
        flex: 0 0 260px;
        background-color: var(--col-side);
        color: white;
        display: flex;
        flex-direction: column;
        border-right: 1px solid #1a252f;
    }
    
    .side-header { padding: 20px; background: rgba(0,0,0,0.2); border-bottom: 1px solid #34495e; }
    .op-status { display: flex; align-items: center; gap: 10px; margin-bottom: 10px; font-weight: bold; font-size: 1.1rem; }
    .status-dot { width: 12px; height: 12px; background: #95a5a6; border-radius: 50%; transition: 0.3s; }
    .status-dot.online { background: var(--accent-green); box-shadow: 0 0 8px var(--accent-green); }

    .btn-reg {
        width: 100%; padding: 8px; border: 1px solid #7f8c8d; background: transparent; color: #bdc3c7;
        border-radius: 4px; cursor: pointer; font-size: 0.8rem; transition: 0.2s;
    }
    .btn-reg:hover { background: rgba(255,255,255,0.1); color: white; }
    .btn-reg.active { border-color: var(--accent-green); color: var(--accent-green); }
    .btn-reg.inactive { border-color: var(--accent-red); color: var(--accent-red); }

    .call-list-header { padding: 10px 20px; font-size: 0.75rem; text-transform: uppercase; color: #7f8c8d; font-weight: bold; letter-spacing: 1px; }
    .active-calls-ul { list-style: none; padding: 10px; margin: 0; overflow-y: auto; flex: 1; }

    /* Card Apel (Mini) */
    .mini-card {
        background: rgba(255,255,255,0.1); padding: 10px; margin-bottom: 8px; border-radius: 4px;
        border-left: 3px solid #95a5a6; cursor: pointer; transition: 0.2s;
    }
    .mini-card:hover { background: rgba(255,255,255,0.15); }
    .mc-ringing { border-color: var(--accent-blue); animation: flash 1s infinite; }
    .mc-active { border-color: var(--accent-green); background: rgba(39, 174, 96, 0.2); }
    .mc-hold { border-color: var(--accent-yellow); opacity: 0.7; }
    
    .mc-info { display: flex; justify-content: space-between; align-items: center; }
    .mc-num { font-weight: bold; font-size: 0.95rem; }
    .mc-dur { font-size: 0.8rem; font-family: monospace; }
    .mc-status { font-size: 0.75rem; opacity: 0.8; display: block; margin-top: 4px; }
    
    /* STIL NOU PENTRU NOTA IN SIDEBAR */
    .mc-note { 
        font-size: 0.75rem; 
        color: var(--accent-yellow); 
        font-style: italic; 
        white-space: nowrap; 
        overflow: hidden; 
        text-overflow: ellipsis; 
        margin-top: 4px; 
        display: block; 
        border-top: 1px solid rgba(255,255,255,0.1);
        padding-top: 2px;
    }

    .mc-actions { margin-top: 8px; display: flex; gap: 5px; }
    .btn-mc { flex: 1; border: none; padding: 4px; border-radius: 3px; color: white; cursor: pointer; font-size: 0.75rem; }

    /* --- COLOANA 2: CENTRAL (DIALER) --- */
    .col-center {
        flex: 1; background-color: var(--col-main); display: flex; flex-direction: column;
        align-items: center; justify-content: center; padding: 20px; position: relative;
    }
    .dialer-container { width: 100%; max-width: 320px; display: flex; flex-direction: column; align-items: center; }

    .screen-display { width: 100%; margin-bottom: 20px; text-align: center; }
    .input-num {
        width: 100%; padding: 15px; font-size: 1.8rem; text-align: center; border: none;
        background: transparent; border-bottom: 2px solid #bdc3c7; outline: none;
        color: var(--text-color); font-weight: 300; letter-spacing: 2px;
    }
    .input-num:focus { border-color: var(--accent-blue); }

    .call-timer-big {
        font-size: 2.5rem; color: var(--accent-green); font-weight: 200; margin: 10px 0;
        font-family: 'Segoe UI Light', sans-serif;
    }

    /* DIALPAD GRID */
    .dialpad { display: grid; grid-template-columns: repeat(3, 1fr); gap: 15px; margin-bottom: 25px; width: 100%; }
    .d-btn {
        width: 65px; height: 65px; border-radius: 50%; border: 1px solid #e0e0e0; background: white;
        font-size: 1.5rem; color: var(--text-color); cursor: pointer; transition: 0.1s;
        box-shadow: 0 2px 5px rgba(0,0,0,0.05); display: flex; align-items: center; justify-content: center; margin: 0 auto; user-select: none;
    }
    .d-btn:active { background: #ecf0f1; transform: scale(0.95); }
    .d-btn:hover { border-color: var(--accent-blue); color: var(--accent-blue); }

    .controls-bar { width: 100%; display: flex; gap: 15px; }
    .btn-action {
        flex: 1; padding: 15px; border: none; border-radius: 30px; font-size: 1.1rem; font-weight: bold;
        color: white; cursor: pointer; box-shadow: 0 4px 10px rgba(0,0,0,0.1); transition: 0.2s;
        display: flex; align-items: center; justify-content: center; gap: 8px;
    }
    .btn-action:hover { transform: translateY(-2px); shadow: 0 6px 15px rgba(0,0,0,0.15); }
    .btn-action:active { transform: translateY(0); }
    
    .bg-green { background: var(--accent-green); }
    .bg-red { background: var(--accent-red); }
    .bg-yellow { background: var(--accent-yellow); color: #34495e; }

    .note-area { width: 100%; margin-top: 20px; display: none; }
    .note-txt { width: 100%; height: 80px; padding: 10px; border: 1px solid #f39c12; background: #fef9e7; border-radius: 6px; resize: none; }

    /* --- COLOANA 3: HISTORY (DREAPTA) --- */
    .col-hist { flex: 0 0 320px; background-color: var(--col-hist); border-left: 1px solid #e0e0e0; display: flex; flex-direction: column; }
    .hist-header { padding: 15px 20px; border-bottom: 1px solid #eee; display: flex; justify-content: space-between; align-items: center; background: #fafafa; }
    .hist-title { font-weight: bold; color: #7f8c8d; font-size: 0.9rem; }
    
    .hist-list { list-style: none; padding: 0; margin: 0; overflow-y: auto; flex: 1; }
    .h-item {
        padding: 12px 20px; border-bottom: 1px solid #f1f1f1; cursor: pointer; transition: 0.1s;
        display: flex; justify-content: space-between; align-items: center;
    }
    .h-item:hover { background: #f8f9fa; border-left: 3px solid var(--accent-blue); }
    
    .h-left { display: flex; flex-direction: column; overflow: hidden; }
    .h-num { font-weight: 600; color: var(--text-color); font-size: 0.95rem; }
    .h-meta { font-size: 0.75rem; color: #95a5a6; margin-top: 3px; }
    
    .hist-note-preview { font-size: 0.75rem; color: #f39c12; font-style: italic; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 150px; display: block; margin-top: 2px;}
    
    .h-right { text-align: right; min-width: 60px; }
    .icon-in { color: var(--accent-blue); }
    .icon-out { color: var(--accent-green); }
    .icon-miss { color: var(--accent-red); }

    /* Modal Styling */
    .modal-overlay { display: none; position: fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.6); z-index:999; justify-content:center; align-items:center; backdrop-filter: blur(3px); }
    .modal-box { background: white; width: 400px; padding: 25px; border-radius: 8px; box-shadow: 0 10px 40px rgba(0,0,0,0.3); }

    @keyframes flash { 0% { border-color: var(--accent-blue); } 50% { border-color: transparent; } 100% { border-color: var(--accent-blue); } }

</style>

<div class="vox-app-container">
    
    <div class="col-side">
        <div class="side-header">
            <div class="op-status">
                <div id="statusDot" class="status-dot"></div>
                <span id="statusText">Offline</span>
            </div>
            <button id="btnRegToggle" onclick="toggleRegister()" class="btn-reg">Conectează SIP</button>
        </div>
        <div class="call-list-header">Apeluri Active</div>
        <ul id="sidebarList" class="active-calls-ul"></ul>
    </div>

    <div class="col-center">
        <div class="dialer-container">
            <div class="screen-display">
                <input type="text" id="phoneInput" class="input-num" placeholder="Număr telefon..." autocomplete="off">
                <div id="bigTimer" class="call-timer-big" style="display:none;">00:00</div>
            </div>

            <div id="dialpad" class="dialpad">
                <button class="d-btn" onclick="addNumber('1')">1</button>
                <button class="d-btn" onclick="addNumber('2')">2</button>
                <button class="d-btn" onclick="addNumber('3')">3</button>
                <button class="d-btn" onclick="addNumber('4')">4</button>
                <button class="d-btn" onclick="addNumber('5')">5</button>
                <button class="d-btn" onclick="addNumber('6')">6</button>
                <button class="d-btn" onclick="addNumber('7')">7</button>
                <button class="d-btn" onclick="addNumber('8')">8</button>
                <button class="d-btn" onclick="addNumber('9')">9</button>
                <button class="d-btn" onclick="addNumber('*')">*</button>
                <button class="d-btn" onclick="addNumber('0')">0</button>
                <button class="d-btn" onclick="addNumber('#')">#</button>
            </div>

            <div id="mainControls" class="controls-bar">
                <button id="btnMakeCall" onclick="makeCall()" class="btn-action bg-green">
                    <span>📞</span> APELEAZĂ
                </button>
            </div>

            <div id="notesContainer" class="note-area">
                <textarea id="callNotes" class="note-txt" placeholder="Observații apel..."></textarea>
            </div>
        </div>
    </div>

    <div class="col-hist">
        <div class="hist-header">
            <span class="hist-title">ISTORIC RECENT</span>
            <div>
                <button onclick="clearHistory()" class="btn-reg" style="width:auto; padding:4px 8px; border:none; color:#e74c3c;">🗑️</button>
                <button onclick="exportCSV()" class="btn-reg" style="width:auto; padding:4px 8px; border:1px solid #ccc; color:#555;">📥</button>
            </div>
        </div>
        <ul id="historyList" class="hist-list"></ul>
    </div>

</div>

<div id="detailsModal" class="modal-overlay">
    <div class="modal-box">
        <h3 style="margin-top:0; color:var(--accent-blue);">Detalii & Acțiuni</h3>
        <div id="detContent"></div>
        <div style="margin-top:15px;">
            <label style="font-size:0.8rem; font-weight:bold; color:#7f8c8d;">Observații:</label>
            <textarea id="detNoteInput" class="note-txt" style="background:#fff; border:1px solid #ccc; height: 100px;"></textarea>
        </div>
        <div style="display:flex; gap:10px; margin-top:20px;">
            <button id="btnSaveNote" class="btn-action bg-green" style="font-size:0.9rem; padding:10px;">💾 Salvează</button>
            <button id="btnCallFromHist" class="btn-action bg-yellow" style="font-size:0.9rem; padding:10px;">📞 Apelează</button>
            <button onclick="document.getElementById('detailsModal').style.display='none'" class="btn-action bg-red" style="font-size:0.9rem; padding:10px;">Închide</button>
        </div>
    </div>
</div>

<audio id="remoteAudio" autoplay></audio>

<script>
    const SIP_CONFIG = {
        user: "<?php echo $sip_user; ?>",
        pass: "<?php echo $sip_pass; ?>",
        domain: "<?php echo $domain_name; ?>",
        wss: "<?php echo $wss_url; ?>"
    };

    let ua = null;
    let sessions = {}; 
    let activeSessionId = null; 
    let ringtoneInterval = null;
    let editingHistoryId = null;

    const UI = {
        statusText: document.getElementById('statusText'),
        statusDot: document.getElementById('statusDot'),
        btnReg: document.getElementById('btnRegToggle'),
        sidebar: document.getElementById('sidebarList'),
        input: document.getElementById('phoneInput'),
        timer: document.getElementById('bigTimer'),
        dialpad: document.getElementById('dialpad'),
        controls: document.getElementById('mainControls'),
        notesArea: document.getElementById('notesContainer'),
        notes: document.getElementById('callNotes'),
        histList: document.getElementById('historyList'),
        audio: document.getElementById('remoteAudio'),
        modal: document.getElementById('detailsModal'),
        detContent: document.getElementById('detContent'),
        detNote: document.getElementById('detNoteInput'),
        btnSaveNote: document.getElementById('btnSaveNote'),
        btnCallHist: document.getElementById('btnCallFromHist')
    };

    const audioCtx = new (window.AudioContext || window.webkitAudioContext)();
    function unlockAudio() { if(audioCtx.state === 'suspended') audioCtx.resume(); }

    function manageRingtone() {
        const ringing = Object.values(sessions).some(s => s.direction === 'incoming' && !s.isEstablished());
        const active = Object.values(sessions).some(s => s.isEstablished());
        if (ringing && !active) { if (!ringtoneInterval) startRing(); } else { stopRing(); }
    }

    function startRing() {
        const beep = () => {
            const o = audioCtx.createOscillator(); const g = audioCtx.createGain();
            o.frequency.value = 440; g.gain.value = 0.1;
            o.connect(g); g.connect(audioCtx.destination);
            o.start(); o.stop(audioCtx.currentTime + 1);
        };
        beep(); ringtoneInterval = setInterval(beep, 3000);
    }
    function stopRing() { if(ringtoneInterval) { clearInterval(ringtoneInterval); ringtoneInterval = null; } }

    function toggleRegister() {
        unlockAudio();
        if (ua && ua.isRegistered()) {
            ua.stop(); UI.btnReg.innerText = "Conectează SIP"; UI.btnReg.className = "btn-reg inactive";
        } else { initUA(); }
    }

    function initUA() {
        if(ua) ua.stop();
        try {
            const socket = new JsSIP.WebSocketInterface(SIP_CONFIG.wss);
            const cfg = {
                sockets: [socket], uri: `sip:${SIP_CONFIG.user}@${SIP_CONFIG.domain}`,
                password: SIP_CONFIG.pass, display_name: SIP_CONFIG.user, register: true
            };
            ua = new JsSIP.UA(cfg);

            ua.on('registered', () => {
                UI.statusText.innerText = `Online (${SIP_CONFIG.user})`; UI.statusDot.className = 'status-dot online';
                UI.btnReg.innerText = "Deconectează"; UI.btnReg.className = "btn-reg active";
            });
            ua.on('unregistered', () => { UI.statusText.innerText = "Offline"; UI.statusDot.className = 'status-dot'; });
            ua.on('registrationFailed', () => { UI.statusText.innerText = "Eroare Login"; UI.btnReg.className = "btn-reg inactive"; });

            ua.on('newRTCSession', (data) => {
                const s = data.session; s.data.note = ""; s.data.startTime = null; sessions[s.id] = s;
                if (s.direction === 'incoming') {
                    manageRingtone();
                    s.on('ended', () => { removeSession(s.id); manageRingtone(); });
                    s.on('failed', () => { addToHistory(s, 'Missed'); removeSession(s.id); manageRingtone(); });
                } else {
                    if(activeSessionId) holdSession(activeSessionId); setupConfirmed(s);
                }
                updateUI();
            });
            ua.start();
        } catch(e) { console.error(e); }
    }

    function answerCall(id) {
        unlockAudio(); const s = sessions[id]; if(!s) return;
        if(activeSessionId && sessions[activeSessionId]) holdSession(activeSessionId);
        s.answer({ mediaConstraints: {audio:true, video:false} }); setupConfirmed(s); manageRingtone();
    }
    function rejectCall(id) { if(sessions[id]) sessions[id].terminate(); }

    function setupConfirmed(s) {
        s.on('confirmed', () => { s.data.startTime = new Date(); setActive(s.id); manageRingtone(); });
        s.on('ended', () => { addToHistory(s, 'Ended', s.data.note); removeSession(s.id); manageRingtone(); });
        s.on('failed', () => { addToHistory(s, 'Failed', s.data.note); removeSession(s.id); manageRingtone(); });
        if(s.connection) s.connection.addEventListener('track', e => { UI.audio.srcObject = e.streams[0]; UI.audio.play(); });
        updateUI();
    }

    function setActive(id) { activeSessionId = id; updateUI(); renderMainArea(); }
    
    function switchTo(id) {
        if(activeSessionId === id) return;
        if(activeSessionId && sessions[activeSessionId]) holdSession(activeSessionId);
        const s = sessions[id]; if(s && s.isEstablished() && s.isOnHold().local) s.unhold();
        setActive(id);
    }
    function holdSession(id) { const s = sessions[id]; if(s && s.isEstablished() && !s.isOnHold().local) s.hold(); }
    
    function removeSession(id) {
        delete sessions[id]; if(activeSessionId === id) { activeSessionId = null; renderMainArea(); }
        updateUI();
    }

    // --- LOOP PRINCIPAL UI ---
    function updateUI() {
        renderSidebar(); 
        if (activeSessionId && sessions[activeSessionId]) {
            const s = sessions[activeSessionId];
            if (s.data.startTime) {
                const diff = (new Date() - s.data.startTime) / 1000;
                UI.timer.innerText = fmtTime(diff);
            }
        }
    }
    setInterval(updateUI, 1000);

    function renderSidebar() {
        UI.sidebar.innerHTML = ''; const ids = Object.keys(sessions);
        if(ids.length === 0) { UI.sidebar.innerHTML = '<li style="padding:15px; text-align:center; font-size:0.8rem; opacity:0.6;">Niciun apel activ</li>'; return; }

        ids.forEach(id => {
            const s = sessions[id];
            const isRing = (s.direction === 'incoming' && !s.isEstablished());
            const isHold = s.isOnHold().local;
            
            const li = document.createElement('li');
            let css = 'mini-card'; let status = ""; let icon = "";
            if (isRing) { css += ' mc-ringing'; status = "Se sună..."; icon="🔔"; }
            else if (isHold) { css += ' mc-hold'; status = "În așteptare"; icon="⏸️"; }
            else { css += ' mc-active'; status = "Conectat"; icon="🔊"; }
            
            let actions = isRing ? `<div class="mc-actions"><button class="btn-mc bg-green" onclick="event.stopPropagation(); answerCall('${id}')">Răspunde</button><button class="btn-mc bg-red" onclick="event.stopPropagation(); rejectCall('${id}')">Respinge</button></div>` : "";
            let dur = ""; if(s.data.startTime) dur = fmtTime(Math.floor((new Date() - s.data.startTime)/1000));

            // AICI ESTE NOUTATEA: NOTITA IN SIDEBAR
            let noteHtml = s.data.note ? `<span class="mc-note">📝 ${s.data.note}</span>` : '';

            li.className = css;
            li.innerHTML = `<div class="mc-info"><span class="mc-num">${icon} ${s.remote_identity.uri.user}</span><span class="mc-dur">${dur}</span></div><span class="mc-status">${status}</span>${noteHtml}${actions}`;
            if(!isRing) li.onclick = () => switchTo(id);
            UI.sidebar.appendChild(li);
        });
    }

    function renderMainArea() {
        const s = activeSessionId ? sessions[activeSessionId] : null;
        if (!s) {
            UI.input.value = ""; UI.input.disabled = false; UI.dialpad.style.display = 'grid';
            UI.timer.style.display = 'none'; UI.notesArea.style.display = 'none';
            UI.controls.innerHTML = `<button onclick="makeCall()" class="btn-action bg-green"><span>📞</span> APELEAZĂ</button>`;
        } else {
            UI.input.value = s.remote_identity.uri.user; UI.input.disabled = true; UI.dialpad.style.display = 'none';
            UI.timer.style.display = 'block'; 
            UI.timer.innerText = s.data.startTime ? fmtTime((new Date()-s.data.startTime)/1000) : "00:00";
            
            UI.notesArea.style.display = 'block'; UI.notes.value = s.data.note; 
            
            // UPDATE IN TIMP REAL LA SIDEBAR
            UI.notes.oninput = (e) => { 
                s.data.note = e.target.value; 
                renderSidebar(); // Actualizam imediat lista din stanga
            };
            
            const isHeld = s.isOnHold().local;
            UI.controls.innerHTML = `<button onclick="toggleHold('${s.id}')" class="btn-action bg-yellow"><span>${isHeld ? '▶️' : '⏸️'}</span> ${isHeld ? 'REIA' : 'HOLD'}</button><button onclick="endCall()" class="btn-action bg-red"><span>🔴</span> ÎNCHIDE</button>`;
        }
    }
    
    function toggleHold(id) { const s=sessions[id]; if(s) { s.isOnHold().local ? s.unhold() : s.hold(); updateUI(); renderMainArea(); } }
    function endCall() { if(activeSessionId && sessions[activeSessionId]) sessions[activeSessionId].terminate(); }

    function makeCall() {
        const dest = UI.input.value; if(!dest) return alert("Scrie un număr!");
        unlockAudio();
        navigator.mediaDevices.getUserMedia({audio:true}).then(() => {
            ua.call(dest, { mediaConstraints: {audio:true,video:false}, pcConfig: { iceServers: [{ urls: ['stun:stun.l.google.com:19302'] }] } });
        }).catch(()=>alert("Permisiune microfon refuzată!"));
    }

    function addNumber(val) { UI.input.value += val; UI.input.focus(); }

    document.addEventListener('keydown', (e) => {
        if (!activeSessionId) {
            if (document.activeElement === UI.input) return;
            const key = e.key;
            if (/^[0-9*#]$/.test(key)) {
                addNumber(key);
                const btns = Array.from(document.querySelectorAll('.d-btn'));
                const btn = btns.find(b => b.innerText === key);
                if(btn) { btn.style.borderColor = 'var(--accent-blue)'; btn.style.color = 'var(--accent-blue)'; setTimeout(() => { btn.style.borderColor=''; btn.style.color=''; }, 150); }
            } else if (key === 'Enter') { makeCall(); } else if (key === 'Backspace') { UI.input.value = UI.input.value.slice(0, -1); }
        }
    });

    function addToHistory(session, status, note) {
        const hist = JSON.parse(localStorage.getItem('vox_history') || '[]');
        hist.unshift({
            id: Date.now() + Math.random().toString(16).slice(2),
            num: session.remote_identity.uri.user,
            dir: session.direction === 'incoming' ? 'Intrare' : 'Ieșire',
            status: status,
            date: new Date().toLocaleDateString(),
            time: new Date().toLocaleTimeString(),
            duration: session.data.startTime ? fmtTime(Math.floor((new Date()-session.data.startTime)/1000)) : '00:00',
            note: note || ""
        });
        localStorage.setItem('vox_history', JSON.stringify(hist));
        renderHistory();
    }

    function renderHistory() {
        const hist = JSON.parse(localStorage.getItem('vox_history') || '[]');
        UI.histList.innerHTML = '';
        hist.forEach(item => {
            const li = document.createElement('li'); li.className = 'h-item';
            let iconClass = item.dir === 'Intrare' ? 'icon-in' : 'icon-out';
            let iconSym = item.dir === 'Intrare' ? '↙' : '↗';
            if (item.status === 'Missed' || item.status === 'Failed') { iconClass = 'icon-miss'; iconSym = '✕'; }
            
            let notePrev = item.note ? `<span class="hist-note-preview">📝 ${item.note}</span>` : '';

            li.innerHTML = `<div class="h-left"><span class="h-num">${item.num}</span><span class="h-meta">${item.date} ${item.time}</span>${notePrev}</div><div class="h-right"><span class="h-icon ${iconClass}">${iconSym} ${item.status}</span></div>`;
            li.onclick = () => openDetails(item);
            UI.histList.appendChild(li);
        });
    }

    function openDetails(item) {
        editingHistoryId = item.id;
        UI.detNote.value = item.note;
        UI.detContent.innerHTML = `<p><strong>Număr:</strong> ${item.num}</p><p><strong>Status:</strong> ${item.status}</p><p><strong>Data:</strong> ${item.date} ${item.time}</p><p><strong>Durată:</strong> ${item.duration}</p>`;
        
        UI.btnCallHist.onclick = () => { UI.modal.style.display = 'none'; UI.input.value = item.num; makeCall(); };
        
        UI.modal.style.display = 'flex';
    }

    UI.btnSaveNote.onclick = () => {
        if(!editingHistoryId) return;
        const hist = JSON.parse(localStorage.getItem('vox_history') || '[]');
        const idx = hist.findIndex(h => h.id === editingHistoryId);
        if(idx !== -1) {
            hist[idx].note = UI.detNote.value;
            localStorage.setItem('vox_history', JSON.stringify(hist));
            renderHistory();
            UI.modal.style.display = 'none';
        }
    };

    function clearHistory() { if(confirm("Ștergi tot istoricul?")) { localStorage.removeItem('vox_history'); renderHistory(); } }
    function exportCSV() {
        const hist = JSON.parse(localStorage.getItem('vox_history') || '[]');
        if(!hist.length) return alert("Istoric gol!");
        let csv = "Numar,Directie,Status,Data,Ora,Durata,Observatie\n";
        hist.forEach(r => { let n = r.note.replace(/(\r\n|\n|\r)/gm, " ").replace(/"/g, '""'); csv += `${r.num},${r.dir},${r.status},${r.date},${r.time},${r.duration},"${n}"\n`; });
        const blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
        const link = document.createElement("a"); link.href = URL.createObjectURL(blob); link.download = `Istoric_${new Date().toISOString().slice(0,10)}.csv`; link.click();
    }
    function fmtTime(s) { s=Math.floor(s); return (Math.floor(s/60)).toString().padStart(2,'0') + ':' + (s%60).toString().padStart(2,'0'); }

    window.onload = () => { initUA(); renderHistory(); };

</script>

<?php require_once "resources/footer.php"; ?>
