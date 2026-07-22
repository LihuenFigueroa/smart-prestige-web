// ── Función genérica: convierte cualquier sección en video scroll-driven ──
function initScrollVideo({ videoId, canvasId, pinId, videoSrc, pxPerSecond, captureFps, lerp, pinHeight, textEl, textElMid, textElMidDesktop, textZonePx, holdZonePx, startOffset }) {
  startOffset = startOffset || 0;
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
  let logicalW = 0, logicalH = 0;

  // ── Canvas ───────────────────────────────────────────────────────────────
  // El buffer del canvas se dimensiona a resolución física (CSS px * devicePixelRatio)
  // para que no se vea pixelado/borroso en pantallas de alta densidad (Retina, escalado de Windows, etc.).
  // Todo el dibujo sigue usando coordenadas en CSS px gracias al ctx.setTransform.
  function resizeCanvas() {
    const dpr  = window.devicePixelRatio || 1;
    const newW = canvas.offsetWidth  || window.innerWidth;
    const newH = canvas.offsetHeight || (pinHeight || window.innerHeight);
    const newPxW = Math.round(newW * dpr);
    const newPxH = Math.round(newH * dpr);
    // Solo redimensionar si cambió de verdad (evita el flash por address bar en mobile)
    if (canvas.width === newPxW && canvas.height === newPxH) return;
    canvas.width  = newPxW;
    canvas.height = newPxH;
    logicalW = newW;
    logicalH = newH;
    ctx.setTransform(dpr, 0, 0, dpr, 0, 0);
    if (ready) {
      renderProgress(drawProgress);
    } else if (frames[0]) {
      drawBitmap(frames[0], 1);
      ctx.globalAlpha = 1;
    } else if (video.readyState >= 2 && video.videoWidth) {
      const cw = logicalW, ch = logicalH;
      const vw = video.videoWidth, vh = video.videoHeight;
      const scale = Math.max(cw / vw, ch / vh);
      ctx.drawImage(video, (cw - vw * scale) / 2, (ch - vh * scale) / 2, vw * scale, vh * scale);
    }
  }
  window.addEventListener('resize', resizeCanvas);
  resizeCanvas();

  // ── Render ───────────────────────────────────────────────────────────────
  function drawBitmap(bmp, alpha) {
    const cw = logicalW, ch = logicalH;
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
    let bmpA = frames[idxA];
    if (!bmpA) {
      for (let i = idxA - 1; i >= 0; i--) { if (frames[i]) { bmpA = frames[i]; break; } }
      if (!bmpA) return;
    }
    ctx.clearRect(0, 0, logicalW, logicalH);
    drawBitmap(bmpA, 1);
    if (blend > 0.01 && frames[idxB]) drawBitmap(frames[idxB], blend);
    ctx.globalAlpha = 1;
  }

  // ── RAF loop ─────────────────────────────────────────────────────────────
  function tick() {
    if (frames[0]) {
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
      const midFadeIn  = videoPx * 0.18;
      const midPeak    = videoPx * 0.32;
      const midFadeOut = midPeak + 350;
      const midGone    = midFadeOut + 350;

      if (rel < midFadeIn) {
        textElMid.style.opacity   = '0';
        textElMid.style.transform = 'translateY(40px)';
      } else if (rel < midPeak) {
        const t = (rel - midFadeIn) / (midPeak - midFadeIn);
        textElMid.style.opacity   = t.toFixed(3);
        textElMid.style.transform = `translateY(${(40 * (1 - t)).toFixed(1)}px)`;
      } else if (rel < midFadeOut) {
        textElMid.style.opacity   = '1';
        textElMid.style.transform = 'translateY(0)';
      } else if (rel < midGone) {
        const t = (rel - midFadeOut) / (midGone - midFadeOut);
        textElMid.style.opacity   = (1 - t).toFixed(3);
        textElMid.style.transform = 'translateY(0)';
      } else {
        textElMid.style.opacity   = '0';
        textElMid.style.transform = 'translateY(0)';
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
  function startBackgroundCapture(capVid, duration, offset) {
    offset = offset || 0;
    totalFrames = Math.round(duration * CAPTURE_FPS);
    frames.length = totalFrames;

    const vw = capVid.videoWidth  || 1280;
    const vh = capVid.videoHeight || 720;
    const os = new OffscreenCanvas(vw, vh);
    const oc = os.getContext('2d');

    let lastCapturedTime = -1;

    // Jugar desde 0 sin seek — más compatible con iOS Safari
    capVid.playbackRate = 4;
    const playPromise = capVid.play();
    if (playPromise) playPromise.catch(() => {});

    function captureLoop() {
      const t   = capVid.currentTime;

      // Ignorar frames antes del offset
      if (t >= offset) {
        const idx = Math.round(((t - offset) / duration) * (totalFrames - 1));

        if (t !== lastCapturedTime && idx >= 0 && idx < totalFrames && !frames[idx]) {
          oc.drawImage(capVid, 0, 0, vw, vh);
          frames[idx]      = os.transferToImageBitmap();
          lastCapturedTime = t;
        }

        if (capVid.ended || t >= offset + duration - 0.01) {
          // Forward-fill cualquier hueco
          let last = frames[0];
          for (let i = 0; i < totalFrames; i++) {
            if (frames[i]) { last = frames[i]; }
            else if (last) { frames[i] = last; }
          }
          onCaptureComplete();
          return;
        }
      }

      requestAnimationFrame(captureLoop);
    }

    requestAnimationFrame(captureLoop);
  }

  // ── Primer frame ─────────────────────────────────────────────────────────
  function showFirstFrame() {
    const tmp    = new OffscreenCanvas(video.videoWidth || 1280, video.videoHeight || 720);
    const tmpCtx = tmp.getContext('2d');
    tmpCtx.drawImage(video, 0, 0);
    frames[0] = tmp.transferToImageBitmap();
    // Solo renderizar frame 0 si el usuario está en o arriba del hero
    const rel = window.scrollY - pin.offsetTop;
    if (rel <= 0) renderProgress(0);
  }

  function onCaptureComplete() {
    ready = true;
    canvas.style.opacity = '1';
    renderProgress(drawProgress);
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
      if (window.scrollY > pin.offsetTop) return;
      // Pre-capturar frames[0] para que tick() tenga datos mientras llega el seek
      // No modificar canvas.style.opacity — lo revela tryFirstFrame con el frame correcto
      if (!frames[0]) {
        const tmp = new OffscreenCanvas(video.videoWidth, video.videoHeight);
        tmp.getContext('2d').drawImage(video, 0, 0);
        frames[0] = tmp.transferToImageBitmap();
      }
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
      videoDuration = videoDuration - startOffset;

      function tryFirstFrame() {
        const belowHero = window.scrollY > pin.offsetTop;
        const seek = () => {
          if (belowHero) {
            // Primero capturar el último frame para mostrarlo de inmediato
            video.currentTime = startOffset + videoDuration - 0.01;
            video.addEventListener('seeked', function() {
              const tmp = new OffscreenCanvas(video.videoWidth || 1280, video.videoHeight || 720);
              tmp.getContext('2d').drawImage(video, 0, 0);
              frames[totalFrames - 1] = tmp.transferToImageBitmap();
              drawProgress = targetProgress = 1;
              if (textEl) { textEl.style.opacity = '1'; textEl.style.pointerEvents = 'auto'; }
              canvas.style.opacity = '1';
              renderProgress(1);
              // Luego capturar el primer frame para que al scrollear para arriba no haya negro
              video.currentTime = startOffset;
              video.addEventListener('seeked', function() {
                const tmp2 = new OffscreenCanvas(video.videoWidth || 1280, video.videoHeight || 720);
                tmp2.getContext('2d').drawImage(video, 0, 0);
                frames[0] = tmp2.transferToImageBitmap();
              }, { once: true });
            }, { once: true });
          } else {
            video.currentTime = startOffset;
            video.addEventListener('seeked', function() {
              const tmp = new OffscreenCanvas(video.videoWidth || 1280, video.videoHeight || 720);
              tmp.getContext('2d').drawImage(video, 0, 0);
              frames[0] = tmp.transferToImageBitmap();
              drawProgress = targetProgress = 0;
              if (textEl) { textEl.style.opacity = '0'; textEl.style.pointerEvents = 'none'; }
              canvas.style.opacity = '1';
              renderProgress(0);
            }, { once: true });
          }
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

      const startCapture = () => startBackgroundCapture(capVid, videoDuration, startOffset);
      capVid.readyState >= 1 ? startCapture()
        : capVid.addEventListener('loadedmetadata', startCapture, { once: true });

      // Registrar scroll listener desde ya, sin esperar a que termine la captura
      window.addEventListener('scroll', onScroll, { passive: true });
      onScroll();
    }

    video.readyState >= 1 ? setup()
      : video.addEventListener('loadedmetadata', setup, { once: true });
  }

  // Ocultar canvas siempre hasta que tryFirstFrame lo revele con el frame correcto
  canvas.style.opacity = '0';

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
  videoSrc:   isMobile ? (window.THEME_URL || '') + '/assets/video/videoHeroMobile.mp4' : (window.THEME_URL || '') + '/assets/video/videoHero.mp4',
  pxPerSecond: isMobile ? 480 : 840,
  captureFps:  isMobile ? 30  : 15,
  lerp:        0.06,
  pinHeight:   null,
  textZonePx:  400,
  startOffset: 0
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
  videoSrc:    isMobile ? (window.THEME_URL || '') + '/assets/video/videoSmartXBRABUSMobile.mp4' : (window.THEME_URL || '') + '/assets/video/videoSmartXBRABUS.mp4',
  pxPerSecond: isMobile ? 400 : 800,
  captureFps:  60,
  lerp:        0.07,
  pinHeight:   null,
  textEl:            isMobile ? document.getElementById('brabusTextMobile') : document.getElementById('brabusText'),
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

// ── Form Custom Dropdowns ─────────────────────────────────────────────────
var _fddOpen = null;

function toggleFdd(id) {
  if (_fddOpen && _fddOpen !== id) closeFdd(_fddOpen);
  var panel = document.getElementById('fdd-' + id + '-panel');
  if (!panel) return;
  var isOpen = panel.style.maxHeight && panel.style.maxHeight !== '0px';
  isOpen ? closeFdd(id) : openFdd(id);
}

function openFdd(id) {
  var panel   = document.getElementById('fdd-' + id + '-panel');
  var chevron = document.getElementById('fdd-' + id + '-chevron');
  if (!panel) return;
  panel.style.maxHeight = panel.scrollHeight + 'px';
  if (chevron) chevron.style.transform = 'rotate(180deg)';
  _fddOpen = id;
}

function closeFdd(id) {
  var panel   = document.getElementById('fdd-' + id + '-panel');
  var chevron = document.getElementById('fdd-' + id + '-chevron');
  if (!panel) return;
  panel.style.maxHeight = '0';
  if (chevron) chevron.style.transform = '';
  if (_fddOpen === id) _fddOpen = null;
}

function selectFdd(id, value, label) {
  var valEl   = document.getElementById('fdd-' + id + '-val');
  var labelEl = document.getElementById('fdd-' + id + '-label');
  var wrap    = document.getElementById('fdd-' + id);
  if (valEl)   valEl.value = value;
  if (labelEl) {
    labelEl.textContent          = label;
    labelEl.style.color          = '#111827';
    labelEl.style.letterSpacing  = 'normal';
    labelEl.style.textTransform  = 'none';
  }
  if (wrap) wrap.style.borderBottomColor = '';
  closeFdd(id);
}

function setFddOptions(id, options) {
  var panel = document.getElementById('fdd-' + id + '-panel');
  if (!panel) return;
  var inner = panel.querySelector('[data-fdd-items]');
  if (!inner) return;
  inner.innerHTML = '';
  options.forEach(function(opt) {
    var btn = document.createElement('button');
    btn.type = 'button';
    btn.className = 'fdd-item font-smart-sans';
    btn.textContent = opt[1];
    (function(v, l) {
      btn.addEventListener('click', function() { selectFdd(id, v, l); });
    })(opt[0], opt[1]);
    inner.appendChild(btn);
  });
}

document.addEventListener('click', function(e) {
  if (!_fddOpen) return;
  var wrap = document.getElementById('fdd-' + _fddOpen);
  if (wrap && !wrap.contains(e.target)) closeFdd(_fddOpen);
});

// ── Envío del formulario de contacto ─────────────────────────────────────
function submitContactForm(e) {
  e.preventDefault();
  var form = document.getElementById('form-contacto');
  var btn  = document.getElementById('btn-enviar');
  var errorMsg = document.getElementById('form-error-msg');

  // ── Validación ───────────────────────────────────────────────────────────
  var valid = true;

  // Inputs requeridos (data-req)
  form.querySelectorAll('input[data-req]').forEach(function(inp) {
    var wrap = inp.closest('.border-b');
    if (!inp.value.trim()) {
      valid = false;
      if (wrap) wrap.style.borderBottomColor = 'rgba(239,68,68,0.22)';
      inp.addEventListener('input', function clear() {
        if (wrap) wrap.style.borderBottomColor = '';
        inp.removeEventListener('input', clear);
      });
    } else {
      if (wrap) wrap.style.borderBottomColor = '';
    }
  });

  // Dropdowns requeridos
  ['concesionario', 'modelo'].forEach(function(id) {
    var val  = document.getElementById('fdd-' + id + '-val');
    var wrap = document.getElementById('fdd-' + id);
    if (val && !val.value) {
      valid = false;
      if (wrap) wrap.style.borderBottomColor = 'rgba(239,68,68,0.22)';
    } else {
      if (wrap) wrap.style.borderBottomColor = '';
    }
  });

  if (!valid) {
    if (errorMsg) errorMsg.classList.remove('hidden');
    return;
  }
  if (errorMsg) errorMsg.classList.add('hidden');

  // ── Envío ────────────────────────────────────────────────────────────────
  var data = {
    nombre:        form.querySelector('input[placeholder="NOMBRE"]')?.value        || '',
    apellido:      form.querySelector('input[placeholder="APELLIDO"]')?.value      || '',
    ciudad:        form.querySelector('input[placeholder="CIUDAD"]')?.value        || '',
    email:         form.querySelector('input[type="email"]')?.value                || '',
    celular:       form.querySelector('input[type="tel"]')?.value                  || '',
    concesionario: document.getElementById('fdd-concesionario-label')?.textContent.trim() || document.getElementById('fdd-concesionario-val')?.value || '',
    modelo:        document.getElementById('fdd-modelo-label')?.textContent.trim()        || document.getElementById('fdd-modelo-val')?.value        || '',
    consulta:      form.querySelector('textarea')?.value                           || '',
  };

  if (btn) { btn.disabled = true; btn.textContent = 'Enviando...'; }

  var isWp = !!window.WP_AJAX_URL;
  var endpoint, fetchOpts;

  if (isWp) {
    // WordPress: admin-ajax.php con action + nonce, respuesta {success, data}
    data.action = 'smart_enviar_formulario';
    data.nonce  = window.WP_CONTACT_NONCE || '';
    var params  = new URLSearchParams();
    Object.keys(data).forEach(function(k) { params.append(k, data[k]); });
    endpoint  = window.WP_AJAX_URL;
    fetchOpts = {
      method:  'POST',
      headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
      body:    params.toString(),
    };
  } else {
    // Sitio estático: servidor Node local, respuesta {ok}
    endpoint = window.location.hostname === 'localhost'
      ? 'http://localhost:3001/enviar'
      : '/enviar';
    fetchOpts = {
      method:  'POST',
      headers: { 'Content-Type': 'application/json' },
      body:    JSON.stringify(data),
    };
  }

  fetch(endpoint, fetchOpts)
  .then(function(r) { return r.json(); })
  .then(function(res) {
    var ok = isWp ? res.success : res.ok;
    if (ok) {
      window.location.href = window.WP_GRACIAS_URL || 'gracias.html';
    } else {
      alert('Hubo un error al enviar. Por favor intentá de nuevo.');
      if (btn) { btn.disabled = false; btn.textContent = 'Enviar'; }
    }
  })
  .catch(function() {
    alert('Hubo un error al enviar. Por favor intentá de nuevo.');
    if (btn) { btn.disabled = false; btn.textContent = 'Enviar'; }
  });
}
