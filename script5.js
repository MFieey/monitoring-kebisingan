// Element DOM
const dashboard = document.getElementById('dashboard');
const levelBar = document.getElementById('level');
const statusText = document.getElementById('status');
const toggleBtn = document.getElementById('toggleBtn');
const historyLog = document.getElementById('historyLog');
const profileUsername = document.getElementById('profileUsername');
const profileRole = document.getElementById('profileRole');
const logoutBtn = document.getElementById('logoutBtn');

let audioContext, analyser, dataArray, animationId;
let microphone;
let isRunning = false;
let noiseHistory = [];
let threshold = 10;
let lastSavedTime = 0;

function initThreshold() {
  fetch('get_threshold.php')
    .then(res => res.json())
    .then(data => {
      if (data.success) threshold = parseFloat(data.threshold);
      console.log("Threshold loaded:", threshold);
    })
    .catch(console.error);
}

function getNoiseStatus(volume) {
  if (volume < 10) return { text: 'Tenang', color: '#33cc33' };
  if (volume < 30) return { text: 'Sedang', color: '#ffae42' };
  return { text: 'Bising', color: '#ff3300' };
}

function updateMeter() {
  analyser.getByteFrequencyData(dataArray);
  const avg = dataArray.reduce((a, b) => a + b, 0) / dataArray.length;
  const volume = (avg / 255) * 100;
  const status = getNoiseStatus(volume);

  levelBar.style.width = volume + '%';
  levelBar.style.background = status.color;
  levelBar.style.boxShadow = `0 0 15px ${status.color}`;
  statusText.textContent = `Tingkat Kebisingan: ${status.text} (${volume.toFixed(1)}%)`;

  const now = new Date().toLocaleTimeString();
  addHistory(now, status.text, volume.toFixed(1));

  if (volume > threshold) {
    if (!window.lastSaveTime || Date.now() - window.lastSaveTime > 5000) {
      showNotification(`⚠️ Kebisingan: ${volume.toFixed(1)}% > batas ${threshold}%`);
      saveNoise(status.text, volume.toFixed(1));
      window.lastSaveTime = Date.now();
    }
  }

  animationId = requestAnimationFrame(updateMeter);
}

function saveNoise(status, volume) {
  const now = Date.now();
  if (now - lastSavedTime < 3000) return;
  lastSavedTime = now;

  fetch('save_noise.php', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({ status, volume })
  })
  .then(res => res.json())
  .then(data => {
    if (data.success && data.email_sent) {
      showEmailNotification();  // hanya jika email benar-benar dikirim
    }
  })
  .catch(console.error);
  
}

function addHistory(time, status, volume) {
  const now = Date.now();

  if (now - lastSavedTime >= 3000) {
    noiseHistory.push({ time, status, vol: volume, ts: now });
    if (noiseHistory.length > 5) noiseHistory.shift();
    renderHistory();
    saveNoise(status, volume);
    lastSavedTime = now;
  }
}

function renderHistory() {
  historyLog.innerHTML = '';
  noiseHistory.slice().reverse().forEach(item => {
    const div = document.createElement('div');
    div.className = 'history-item';
    div.textContent = `[${item.time}] ${item.status} (${item.vol}%)`;
    historyLog.appendChild(div);
  });
}

function clearHistory() {
  noiseHistory = [];
  renderHistory();
}

async function startDetection() {
  if (isRunning) return;
  try {
    audioContext = new (window.AudioContext || window.webkitAudioContext)();
    const stream = await navigator.mediaDevices.getUserMedia({ audio: true });
    analyser = audioContext.createAnalyser();
    microphone = audioContext.createMediaStreamSource(stream);
    analyser.fftSize = 256;
    dataArray = new Uint8Array(analyser.frequencyBinCount);
    microphone.connect(analyser);
    isRunning = true;
    toggleBtn.textContent = 'Stop';
    updateMeter();
  } catch (err) {
    console.error(err);
    statusText.textContent = 'Gagal mengakses mic.';
  }
}

function stopDetection() {
  if (!isRunning) return;
  cancelAnimationFrame(animationId);
  microphone.disconnect();
  audioContext.close();
  isRunning = false;
  toggleBtn.textContent = 'Mulai';
  levelBar.style.width = '0%';
  levelBar.style.background = '#ffd700';
  statusText.textContent = 'Klik “Mulai”';
}

// Notifikasi Kanan Atas
function showNotification(message) {
  const notif = document.createElement('div');
  notif.textContent = message;
  Object.assign(notif.style, {
    position: 'fixed',
    top: '20px',
    right: '20px',
    backgroundColor: '#f44336',
    color: 'white',
    padding: '15px',
    borderRadius: '5px',
    boxShadow: '0 0 10px rgba(0,0,0,0.3)',
    zIndex: '9999'
  });

  document.body.appendChild(notif);

  setTimeout(() => {
    notif.remove();
  }, 5000);
}

// Notifikasi Kiri Atas (Email)
function showEmailNotification() {
  const notif = document.createElement('div');
  notif.textContent = '📧 Email notifikasi berhasil dikirim!';
  Object.assign(notif.style, {
    position: 'fixed',
    top: '20px',
    left: '20px',
    backgroundColor: '#2196f3',
    color: 'white',
    padding: '12px 20px',
    borderRadius: '8px',
    boxShadow: '0 0 10px rgba(0,0,0,0.3)',
    zIndex: '9999'
  });

  document.body.appendChild(notif);

  setTimeout(() => {
    notif.remove();
  }, 4000);
}

// EVENTS
toggleBtn.addEventListener('click', () => {
  isRunning ? stopDetection() : startDetection();
});

logoutBtn.addEventListener('click', () => {
  window.location.href = 'login.html';
});

document.addEventListener('DOMContentLoaded', () => {
  fetch('session_check.php')
    .then(res => res.json())
    .then(data => {
      if (!data.logged_in) location.href = 'login.html';
      profileUsername.textContent = data.username;
      profileRole.textContent = data.role;
      dashboard.style.display = 'flex';
      initThreshold();
    })
    .catch(() => location.href = 'login.html');
});

if (data.role === 'admin') {
  document.getElementById("exportSection").style.display = "block";
}
