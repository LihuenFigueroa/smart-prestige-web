// ── Función genérica: convierte cualquier sección en video scroll-driven ──
function initScrollVideo({ videoId, canvasId, pinId, videoSrc, pxPerSecond, captureFps, lerp, pinHeight, textEl, textElMid, textElMidDesktop, textZonePx, holdZonePx }) {
  const video  = document.getElementById(videoId);
  const canvas = document.getElementById(canvasId);
  const pin    = document.getElementById(pinId);
  if (!video || !canvas || !pin) return;
  const ctx    = canvas.getContext('2d');

  const PX_PER_SECOND = pxPerSecond || 300;
  const CAPTURE_FPS   = captureFps  || 24;
  const LERP          = lerp        || 0.12;
  const HOLD_PX       = holdZonePx  || 0;

  const frames = [];
  let ready = false, totalFrames = 0;
  let targetProgress = 0, drawProgress = 0;
  let videoDuration = 0;

  // ── Canvas ───────────────────────────────────────────────────────────────
  function resizeCanvas() {
    const newW = canvas.offsetWidth  || window.innerWidth;
    const newH = canvas.offsetHeight || (pinHeight || window.innerHeight);
    // Solo redimensionar si cambió de verdad (evita el flash por address bar en mobile)
    if (canvas.width === newW && canvas.height === newH) return;
    canvas.width  = newW;
    canvas.height = newH;
    if (ready) renderProgress(drawProgress);
  }
  window.addEventListener('resize', resizeCanvas);
  resizeCanvas();

  // ── Render ───────────────────────────────────────────────────────────────
  function drawBitmap(bmp, alpha) {
    const cw = canvas.width, ch = canvas.height;
    const scale = Math.max(cw / bmp.width, ch / bmp.height);
    const sw = bmp.width * scale, sh = bmp.height * scale;
    ctx.globalAlpha = alpha;
    ctx.drawImage(bmp, (cw - sw) / 2, (ch - sh) / 2, sw, sh);
  }

  function renderProgress(p) {
    if (!frames.length) return;
    const exact = p * (frames.length - 1);
    const idxA  = Math.floor(exact);
    const idxB  = Math.min(idxA + 1, frames.length - 1);
    const blend = exact - idxA;
    if (!frames[idxA]) return;
    ctx.clearRect(0, 0, canvas.width, canvas.height);
    drawBitmap(frames[idxA], 1);
    if (blend > 0.01 && frames[idxB]) drawBitmap(frames[idxB], blend);
    ctx.globalAlpha = 1;
  }

  // ── RAF loop ─────────────────────────────────────────────────────────────
  function tick() {
    if (ready) {
      const diff = targetProgress - drawProgress;
      if (Math.abs(diff) > 0.0001) {
        drawProgress += diff * LERP;
        renderProgress(drawProgress);
      }
    }
    requestAnimationFrame(tick);
  }

  // ── Scroll ───────────────────────────────────────────────────────────────
  function onScroll() {
    const rel       = window.scrollY - pin.offsetTop;
    const videoPx   = videoDuration * PX_PER_SECOND;
    const extraPx   = textZonePx || 0;

    // Texto mid-video (bloque 1 mobile): fade in → hold → fade out
    if (textElMid) {
      const midFadeIn  = videoPx * 0.38;
      const midPeak    = videoPx * 0.58;
      const midFadeOut = midPeak + 350;
      const midGone    = midFadeOut + 350;

      if (rel < midFadeIn) {
        textElMid.style.opacity = '0';
      } else if (rel < midPeak) {
        textElMid.style.opacity = ((rel - midFadeIn) / (midPeak - midFadeIn)).toFixed(3);
      } else if (rel < midFadeOut) {
        textElMid.style.opacity = '1';
      } else if (rel < midGone) {
        textElMid.style.opacity = (1 - (rel - midFadeOut) / (midGone - midFadeOut)).toFixed(3);
      } else {
        textElMid.style.opacity = '0';
      }
    }

    if (textElMidDesktop) {
      const midFadeIn  = videoPx * 0.20;
      const midPeak    = videoPx * 0.38;
      const midFadeOut = midPeak + 350;
      const midGone    = midFadeOut + 350;

      if (rel < midFadeIn) {
        textElMidDesktop.style.opacity   = '0';
        textElMidDesktop.style.transform = 'translateY(40px)';
      } else if (rel < midPeak) {
        const t = (rel - midFadeIn) / (midPeak - midFadeIn);
        textElMidDesktop.style.opacity   = t.toFixed(3);
        textElMidDesktop.style.transform = `translateY(${(40 * (1 - t)).toFixed(1)}px)`;
      } else if (rel < midFadeOut) {
        textElMidDesktop.style.opacity   = '1';
        textElMidDesktop.style.transform = 'translateY(0)';
      } else if (rel < midGone) {
        const t = (rel - midFadeOut) / (midGone - midFadeOut);
        textElMidDesktop.style.opacity   = (1 - t).toFixed(3);
        textElMidDesktop.style.transform = 'translateY(0)';
      } else {
        textElMidDesktop.style.opacity   = '0';
        textElMidDesktop.style.transform = 'translateY(0)';
      }
    }

    if (rel <= videoPx) {
      // Fase 1 — avanza el video (lineal; el ease lo da el LERP)
      targetProgress = Math.max(0, rel / videoPx);
      if (textEl) { textEl.style.opacity = '0'; textEl.style.pointerEvents = 'none'; }
    } else if (rel <= videoPx + extraPx) {
      // Fase 2 — video en último frame, texto se revela con el scroll
      targetProgress = 1;
      if (textEl && extraPx > 0) {
        const t = Math.min(1, (rel - videoPx) / extraPx);
        textEl.style.opacity       = t.toFixed(3);
        textEl.style.pointerEvents = t > 0.5 ? 'auto' : 'none';
      }
    } else {
      // Fase 3 — hold zone: texto 100% visible, pin sigue activo
      targetProgress = 1;
      if (textEl) { textEl.style.opacity = '1'; textEl.style.pointerEvents = 'auto'; }
    }
  }

  // ── Captura en background ─────────────────────────────────────────────────
  function startBackgroundCapture(capVid, duration) {
    totalFrames = Math.round(duration * CAPTURE_FPS);
    frames.length = totalFrames;

    const vw = capVid.videoWidth  || 1280;
    const vh = capVid.videoHeight || 720;
    const os = new OffscreenCanvas(vw, vh);
    const oc = os.getContext('2d');

    let lastCapturedTime = -1;

    capVid.playbackRate = 4;
    const playPromise = capVid.play();
    if (playPromise) playPromise.catch(() => {});

    function captureLoop() {
      const t   = capVid.currentTime;
      const idx = Math.round((t / duration) * (totalFrames - 1));

      // Captura siempre antes de decidir si terminar
      if (t !== lastCapturedTime && idx >= 0 && idx < totalFrames && !frames[idx]) {
        oc.drawImage(capVid, 0, 0, vw, vh);
        frames[idx]      = os.transferToImageBitmap();
        lastCapturedTime = t;
      }

      if (capVid.ended || t >= duration - 0.01) {
        // Seek explícito al final para capturar el último frame real
        capVid.currentTime = duration - 0.001;
        capVid.addEventListener('seeked', function() {
          oc.drawImage(capVid, 0, 0, vw, vh);
          frames[totalFrames - 1] = os.transferToImageBitmap();
          // Forward-fill cualquier hueco
          let last = frames[0];
          for (let i = 0; i < totalFrames; i++) {
            if (frames[i]) { last = frames[i]; }
            else if (last) { frames[i] = last; }
          }
          onCaptureComplete();
        }, { once: true });
        return;
      }

      requestAnimationFrame(captureLoop);
    }

    captureLoop();
  }

  // ── Primer frame ─────────────────────────────────────────────────────────
  function showFirstFrame() {
    const tmp    = new OffscreenCanvas(video.videoWidth || 1280, video.videoHeight || 720);
    const tmpCtx = tmp.getContext('2d');
    tmpCtx.drawImage(video, 0, 0);
    frames[0] = tmp.transferToImageBitmap();
    renderProgress(0);
  }

  function onCaptureComplete() {
    ready = true;
    renderProgress(drawProgress);
    window.addEventListener('scroll', onScroll, { passive: true });
    onScroll(); // aplica el estado correcto según la posición actual de scroll
  }

  // ── Init ─────────────────────────────────────────────────────────────────
  function init() {
    // Setea la fuente correcta (desktop o mobile) antes de cualquier carga
    video.src = videoSrc;
    video.load(); // necesario en iOS Safari para arrancar la descarga sin interacción del usuario

    // Dibuja el primer frame directo al canvas en cuanto hay datos
    // Escucha tanto loadeddata como canplay para máxima compatibilidad (iOS vs otros)
    let firstFrameDrawn = false;
    function drawEarlyFrame() {
      if (firstFrameDrawn || !video.videoWidth || !canvas.width) return;
      firstFrameDrawn = true;
      const cw = canvas.width, ch = canvas.height;
      const vw = video.videoWidth, vh = video.videoHeight;
      const scale = Math.max(cw / vw, ch / vh);
      ctx.clearRect(0, 0, cw, ch);
      ctx.drawImage(video, (cw - vw * scale) / 2, (ch - vh * scale) / 2, vw * scale, vh * scale);
    }
    video.addEventListener('loadeddata', drawEarlyFrame, { once: true });
    video.addEventListener('canplay',    drawEarlyFrame, { once: true });

    function setup() {
      videoDuration = video.duration;
      const videoPx = videoDuration * PX_PER_SECOND;
      const totalPx = videoPx + (textZonePx || 0) + HOLD_PX;
      pin.style.height = pinHeight
        ? `calc(${pinHeight}px + ${totalPx}px)`
        : `calc(100vh + ${totalPx}px)`;

      // Frame 0
      function tryFirstFrame() {
        const seek = () => {
          video.currentTime = 0;
          video.addEventListener('seeked', showFirstFrame, { once: true });
        };
        video.readyState >= 2 ? seek()
          : video.addEventListener('canplay', seek, { once: true });
      }
      tryFirstFrame();

      // Capture video en background
      const capVid = document.createElement('video');
      capVid.src   = videoSrc;
      capVid.muted = true;
      capVid.setAttribute('playsinline', ''); // necesario en iOS Safari
      capVid.setAttribute('muted', '');
      capVid.preload     = 'auto';
      capVid.style.cssText = 'position:fixed;top:-9999px;left:-9999px;width:1px;height:1px;opacity:0.01;pointer-events:none;';
      document.body.appendChild(capVid);

      const startCapture = () => startBackgroundCapture(capVid, videoDuration);
      capVid.readyState >= 1 ? startCapture()
        : capVid.addEventListener('loadedmetadata', startCapture, { once: true });
    }

    video.readyState >= 1 ? setup()
      : video.addEventListener('loadedmetadata', setup, { once: true });
  }

  requestAnimationFrame(tick);
  init();
}

// ── Mobile detection ──────────────────────────────────────────────────────
const isMobile = window.innerWidth < 768;


// ── Hero ─────────────────────────────────────────────────────────────────
initScrollVideo({
  videoId:    'heroVideo',
  canvasId:   'heroCanvas',
  pinId:      'heroPin',
  videoSrc:   isMobile ? 'assets/video/videoHeroMobile.mp4' : 'assets/video/videoHero.mp4',
  pxPerSecond: isMobile ? 500 : 850,
  captureFps:  isMobile ? 30  : 15,
  lerp:        0.06,
  pinHeight:   null,
  textZonePx:  400
});

// ── Eléctrico de verdad — carrusel horizontal ────────────────────────────
(function () {
  const pin   = document.getElementById('electricoPin');
  const strip = document.getElementById('electricoStrip');
  if (!pin || !strip) return;
  const SLIDES = 4;

  function updateElectrico() {
    const scrolled = window.scrollY - pin.offsetTop;
    const totalScroll = pin.offsetHeight - window.innerHeight;
    const progress = Math.max(0, Math.min(1, scrolled / totalScroll));
    strip.style.transform = 'translateX(' + (-progress * (SLIDES - 1) * 100) + 'vw)';
  }

  window.addEventListener('scroll', updateElectrico, { passive: true });
  updateElectrico();
})();

// ── Tu auto piensa como vos — carrusel imágenes ─────────────────────────
(function () {
  const pin   = document.getElementById('tuAutoPin');
  const strip = document.getElementById('tuAutoStrip');
  if (!pin || !strip) return;
  const dots  = document.querySelectorAll('#tuAutoDots .dot');
  const SLIDES = 6;
  let lastActive = 0;

  function updateTuAuto() {
    const scrolled     = window.scrollY - pin.offsetTop;
    const totalScroll  = pin.offsetHeight - window.innerHeight;
    const progress     = Math.max(0, Math.min(1, scrolled / totalScroll));

    strip.style.transform = 'translateX(' + (-progress * (SLIDES - 1) * 100) + 'vw)';

    const active = Math.round(progress * (SLIDES - 1));
    if (active !== lastActive) {
      dots[lastActive].classList.remove('active');
      dots[active].classList.add('active');
      lastActive = active;
    }
  }

  window.addEventListener('scroll', updateTuAuto, { passive: true });
  updateTuAuto();
})();

// ── smart X BRABUS banner ─────────────────────────────────────────────────
initScrollVideo({
  videoId:     'brabusVideo',
  canvasId:    'brabusCanvas',
  pinId:       'brabusPin',
  videoSrc:    isMobile ? 'assets/video/videoSmartXBRABUSMobile.mp4' : 'assets/video/videoSmartXBRABUS.mp4',
  pxPerSecond: isMobile ? 400 : 800,
  captureFps:  60,
  lerp:        0.07,
  pinHeight:   null,
  textEl:            document.getElementById('brabusText'),
  textElMid:         isMobile ? document.getElementById('brabusTextMid') : null,
  textElMidDesktop:  !isMobile ? document.getElementById('brabusTextMidDesktop') : null,
  textZonePx:        isMobile ? 300 : 500,
  holdZonePx:  isMobile ? 400 : 600
});

// ── Brabus specs toggle ───────────────────────────────────────────────────────
let currentSpec = 1;

function switchSpec(num) {
  if (num === currentSpec) return;

  const bg1   = document.getElementById('spec-bg-1');
  const bg3   = document.getElementById('spec-bg-3');
  const tab1  = document.getElementById('spec-tab-1');
  const tab3  = document.getElementById('spec-tab-3');
  const ind   = document.getElementById('spec-slider-indicator');
  const accel = document.getElementById('spec-accel');
  const range = document.getElementById('spec-range');

  if (!bg1) return;

  const goingRight = num > currentSpec; // #1→#3: derecha, #3→#1: izquierda
  const incoming   = num === 1 ? bg1 : bg3;
  const outgoing   = num === 1 ? bg3 : bg1;

  // Posicionar incoming fuera de pantalla sin transición
  incoming.style.transition = 'none';
  incoming.style.transform  = goingRight ? 'translateX(100%)' : 'translateX(-100%)';
  incoming.style.opacity    = '0';
  incoming.getBoundingClientRect(); // forzar reflow
  // Animar ambas
  incoming.style.transition = 'transform 0.5s ease, opacity 0.5s ease';
  outgoing.style.transition = 'transform 0.5s ease, opacity 0.5s ease';
  incoming.style.transform  = 'translateX(0)';
  incoming.style.opacity    = '1';
  outgoing.style.transform  = goingRight ? 'translateX(-100%)' : 'translateX(100%)';
  outgoing.style.opacity    = '0';

  // Píldora
  const activeTab = num === 1 ? tab1 : tab3;
  ind.style.transform = `translateX(${activeTab.offsetLeft}px)`;
  tab1.style.color = num === 1 ? '#fff' : '#141413';
  tab3.style.color = num === 3 ? '#fff' : '#141413';

  // Stats variables
  if (accel) accel.textContent = num === 1 ? '3,9 seg' : '3,7 seg';
  if (range) range.textContent = num === 1 ? '400km'   : '415km';

  currentSpec = num;
}
