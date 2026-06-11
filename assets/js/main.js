// ── Función genérica: convierte cualquier sección en video scroll-driven ──
function initScrollVideo({ videoId, canvasId, pinId, videoSrc, pxPerSecond, captureFps, lerp, pinHeight, textEl, textElMid, textZonePx, holdZonePx, easeOut }) {
  const video  = document.getElementById(videoId);
  const canvas = document.getElementById(canvasId);
  const ctx    = canvas.getContext('2d');
  const pin    = document.getElementById(pinId);

  const PX_PER_SECOND = pxPerSecond || 300;
  const CAPTURE_FPS   = captureFps  || 24;
  const LERP          = lerp        || 0.12;
  const HOLD_PX       = holdZonePx  || 0;

  const frames = [];
  let ready = false, totalFrames = 0;
  let targetProgress = 0, drawProgress = 0;
  let videoDuration = 0;

  // ── Feature detection ─────────────────────────────────────────────────────
  const hasCreateImageBitmap = typeof createImageBitmap === 'function';

  // ── Canvas ───────────────────────────────────────────────────────────────
  function resizeCanvas() {
    const newW = canvas.offsetWidth  || window.innerWidth;
    const newH = canvas.offsetHeight || (pinHeight || window.innerHeight);
    if (canvas.width === newW && canvas.height === newH) return;
    canvas.width  = newW;
    canvas.height = newH;
    if (ready) renderProgress(drawProgress);
  }
  window.addEventListener('resize', resizeCanvas);
  resizeCanvas();

  // ── Render ───────────────────────────────────────────────────────────────
  // Acepta ImageBitmap, HTMLCanvasElement o ImageData (fallback)
  function drawFrame(frame, alpha) {
    if (!frame) return;
    const cw = canvas.width, ch = canvas.height;
    ctx.globalAlpha = alpha;

    if (frame instanceof ImageData) {
      // Fallback para browsers sin createImageBitmap
      if (!drawFrame._tmp) drawFrame._tmp = document.createElement('canvas');
      const tmp = drawFrame._tmp;
      if (tmp.width !== frame.width || tmp.height !== frame.height) {
        tmp.width = frame.width;
        tmp.height = frame.height;
      }
      tmp.getContext('2d').putImageData(frame, 0, 0);
      const scale = Math.max(cw / frame.width, ch / frame.height);
      ctx.drawImage(tmp, (cw - frame.width * scale) / 2, (ch - frame.height * scale) / 2, frame.width * scale, frame.height * scale);
    } else {
      const scale = Math.max(cw / frame.width, ch / frame.height);
      ctx.drawImage(frame, (cw - frame.width * scale) / 2, (ch - frame.height * scale) / 2, frame.width * scale, frame.height * scale);
    }
  }

  function renderProgress(p) {
    if (!frames.length) return;
    const exact = p * (frames.length - 1);
    const idxA  = Math.floor(exact);
    const idxB  = Math.min(idxA + 1, frames.length - 1);
    const blend = exact - idxA;
    if (!frames[idxA]) return;
    ctx.clearRect(0, 0, canvas.width, canvas.height);
    drawFrame(frames[idxA], 1);
    if (blend > 0.01 && frames[idxB]) drawFrame(frames[idxB], blend);
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
    const rel     = window.scrollY - pin.offsetTop;
    const videoPx = videoDuration * PX_PER_SECOND;
    const extraPx = textZonePx || 0;

    if (textElMid) {
      const midFadeIn  = videoPx * 0.38;
      const midPeak    = videoPx * 0.58;
      const midFadeOut = midPeak + 350;
      const midGone    = midFadeOut + 350;
      if      (rel < midFadeIn)  textElMid.style.opacity = '0';
      else if (rel < midPeak)    textElMid.style.opacity = ((rel - midFadeIn) / (midPeak - midFadeIn)).toFixed(3);
      else if (rel < midFadeOut) textElMid.style.opacity = '1';
      else if (rel < midGone)    textElMid.style.opacity = (1 - (rel - midFadeOut) / (midGone - midFadeOut)).toFixed(3);
      else                       textElMid.style.opacity = '0';
    }

    if (rel <= videoPx) {
      const rawP = Math.max(0, rel / videoPx);
      targetProgress = easeOut ? rawP * (2 - rawP) : rawP;
      if (textEl) { textEl.style.opacity = '0'; textEl.style.pointerEvents = 'none'; }
    } else if (rel <= videoPx + extraPx) {
      targetProgress = 1;
      if (textEl && extraPx > 0) {
        const t = Math.min(1, (rel - videoPx) / extraPx);
        textEl.style.opacity       = t.toFixed(3);
        textEl.style.pointerEvents = t > 0.5 ? 'auto' : 'none';
      }
    } else {
      targetProgress = 1;
      if (textEl) { textEl.style.opacity = '1'; textEl.style.pointerEvents = 'auto'; }
    }
  }

  // ── Guardar un frame desde una fuente de video ────────────────────────────
  // Usa canvas regular en lugar de OffscreenCanvas (compatible con iOS Safari)
  function storeFrame(captureCanvas, captureCtx, srcVid, vw, vh, idx) {
    if (frames[idx]) return;
    captureCtx.drawImage(srcVid, 0, 0, vw, vh);
    if (hasCreateImageBitmap) {
      // createImageBitmap(canvas) soportado desde iOS 15 — captura snapshot del canvas en este momento
      createImageBitmap(captureCanvas).then(function(bmp) {
        if (!frames[idx]) frames[idx] = bmp;
      });
    } else {
      // Fallback: guardar ImageData directamente
      frames[idx] = captureCtx.getImageData(0, 0, vw, vh);
    }
  }

  // ── Captura seek-by-seek (fallback cuando autoplay está bloqueado en iOS) ─
  function seekCapture(capVid, captureCanvas, captureCtx, duration, vw, vh) {
    const step = duration / totalFrames;
    let frameIdx = 0;

    function seekNext() {
      if (frameIdx >= totalFrames) {
        completeCapture();
        return;
      }
      const t = Math.min(frameIdx * step, duration - 0.001);
      capVid.currentTime = t;
      capVid.addEventListener('seeked', function() {
        storeFrame(captureCanvas, captureCtx, capVid, vw, vh, frameIdx);
        frameIdx++;
        requestAnimationFrame(seekNext);
      }, { once: true });
    }
    seekNext();
  }

  // ── Captura play-based (más rápida, preferida en desktop / Android) ───────
  function playCapture(capVid, captureCanvas, captureCtx, duration, vw, vh) {
    capVid.playbackRate = 4;
    var playPromise = capVid.play();

    if (!playPromise) {
      // Browser sin soporte de Promise para play → seek fallback
      seekCapture(capVid, captureCanvas, captureCtx, duration, vw, vh);
      return;
    }

    playPromise.then(function() {
      var lastCapturedTime = -1;

      function captureLoop() {
        var t   = capVid.currentTime;
        var idx = Math.round((t / duration) * (totalFrames - 1));

        if (t !== lastCapturedTime && idx >= 0 && idx < totalFrames) {
          storeFrame(captureCanvas, captureCtx, capVid, vw, vh, idx);
          lastCapturedTime = t;
        }

        if (capVid.ended || t >= duration - 0.01) {
          capVid.currentTime = duration - 0.001;
          capVid.addEventListener('seeked', function() {
            storeFrame(captureCanvas, captureCtx, capVid, vw, vh, totalFrames - 1);
            // Pequeño delay para que se resuelvan los createImageBitmap pendientes
            setTimeout(completeCapture, 300);
          }, { once: true });
          return;
        }

        requestAnimationFrame(captureLoop);
      }
      captureLoop();

    }).catch(function() {
      // Autoplay bloqueado (típico en iOS Safari sin gesto) → seek fallback
      seekCapture(capVid, captureCanvas, captureCtx, duration, vw, vh);
    });
  }

  // ── Fin de captura ────────────────────────────────────────────────────────
  function completeCapture() {
    // Forward-fill huecos que puedan haber quedado
    var last = null;
    for (var i = 0; i < totalFrames; i++) {
      if (frames[i])      { last = frames[i]; }
      else if (last)      { frames[i] = last; }
    }
    onCaptureComplete();
  }

  function startBackgroundCapture(capVid, duration) {
    totalFrames = Math.round(duration * CAPTURE_FPS);
    frames.length = totalFrames;

    var vw = capVid.videoWidth  || 1280;
    var vh = capVid.videoHeight || 720;

    // Canvas regular en lugar de OffscreenCanvas — compatible con todos los browsers
    var captureCanvas = document.createElement('canvas');
    captureCanvas.width  = vw;
    captureCanvas.height = vh;
    var captureCtx = captureCanvas.getContext('2d');

    playCapture(capVid, captureCanvas, captureCtx, duration, vw, vh);
  }

  // ── Primer frame ─────────────────────────────────────────────────────────
  function showFirstFrame() {
    var vw = video.videoWidth  || 1280;
    var vh = video.videoHeight || 720;

    // Canvas regular en lugar de OffscreenCanvas
    var tmp    = document.createElement('canvas');
    tmp.width  = vw;
    tmp.height = vh;
    var tctx   = tmp.getContext('2d');
    tctx.drawImage(video, 0, 0, vw, vh);

    if (hasCreateImageBitmap) {
      createImageBitmap(tmp).then(function(bmp) {
        frames[0] = bmp;
        renderProgress(0);
      });
    } else {
      frames[0] = tctx.getImageData(0, 0, vw, vh);
      renderProgress(0);
    }
  }

  function onCaptureComplete() {
    ready = true;
    renderProgress(drawProgress);
    window.addEventListener('scroll', onScroll, { passive: true });
    onScroll();
  }

  // ── Init ─────────────────────────────────────────────────────────────────
  function init() {
    video.src = videoSrc;
    video.load();

    var firstFrameDrawn = false;
    function drawEarlyFrame() {
      if (firstFrameDrawn || !video.videoWidth || !canvas.width) return;
      firstFrameDrawn = true;
      var cw = canvas.width, ch = canvas.height;
      var vw = video.videoWidth, vh = video.videoHeight;
      var scale = Math.max(cw / vw, ch / vh);
      ctx.clearRect(0, 0, cw, ch);
      ctx.drawImage(video, (cw - vw * scale) / 2, (ch - vh * scale) / 2, vw * scale, vh * scale);
    }
    video.addEventListener('loadeddata', drawEarlyFrame, { once: true });
    video.addEventListener('canplay',    drawEarlyFrame, { once: true });

    function setup() {
      videoDuration = video.duration;
      var videoPx = videoDuration * PX_PER_SECOND;
      var totalPx = videoPx + (textZonePx || 0) + HOLD_PX;
      pin.style.height = pinHeight
        ? 'calc(' + pinHeight + 'px + ' + totalPx + 'px)'
        : 'calc(100vh + ' + totalPx + 'px)';

      function tryFirstFrame() {
        var seek = function() {
          video.currentTime = 0;
          video.addEventListener('seeked', showFirstFrame, { once: true });
        };
        video.readyState >= 2 ? seek()
          : video.addEventListener('canplay', seek, { once: true });
      }
      tryFirstFrame();

      var capVid = document.createElement('video');
      capVid.src   = videoSrc;
      capVid.muted = true;
      capVid.setAttribute('playsinline', '');
      capVid.setAttribute('muted', '');
      capVid.preload     = 'auto';
      capVid.style.cssText = 'position:fixed;top:-9999px;left:-9999px;width:1px;height:1px;opacity:0.01;pointer-events:none;';
      document.body.appendChild(capVid);

      var startCapture = function() { startBackgroundCapture(capVid, videoDuration); };
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
var isMobile = window.innerWidth < 768;

// ── Mobile model selector ─────────────────────────────────────────────────
function switchModel(n) {
  var img  = document.getElementById('mobileModelImg');
  var cta  = document.getElementById('mobileModelCta');
  var tab1 = document.getElementById('mobileTab1');
  var tab3 = document.getElementById('mobileTab3');
  var activeClass   = 'flex-1 flex items-center justify-center text-[10px] font-normal bg-neutral-900 text-white transition-all';
  var inactiveClass = 'flex-1 flex items-center justify-center text-[10px] font-normal text-neutral-900 transition-all';
  if (n === 1) {
    img.src         = 'assets/img/smartXBRABUS.png';
    img.alt         = 'smart #1';
    cta.textContent = 'Descubrí más sobre el smart #1';
    tab1.className  = activeClass;
    tab3.className  = inactiveClass;
  } else {
    img.src         = 'assets/img/SMART X BRABUS 1.png';
    img.alt         = 'smart #3';
    cta.textContent = 'Descubrí más sobre el smart #3';
    tab1.className  = inactiveClass;
    tab3.className  = activeClass;
  }
}

// ── Hero ──────────────────────────────────────────────────────────────────
initScrollVideo({
  videoId:     'heroVideo',
  canvasId:    'heroCanvas',
  pinId:       'heroPin',
  videoSrc:    isMobile ? 'assets/video/videoHeroMobile.mp4' : 'assets/video/videoHero.mp4',
  pxPerSecond: isMobile ? 350 : 600,
  captureFps:  isMobile ? 15  : 15,
  pinHeight:   null,
  textZonePx:  400,
  easeOut:     true
});

// ── Eléctrico de verdad — carrusel horizontal ─────────────────────────────
(function () {
  var pin   = document.getElementById('electricoPin');
  var strip = document.getElementById('electricoStrip');
  var SLIDES = 4;

  function updateElectrico() {
    var scrolled    = window.scrollY - pin.offsetTop;
    var totalScroll = pin.offsetHeight - window.innerHeight;
    var progress    = Math.max(0, Math.min(1, scrolled / totalScroll));
    strip.style.transform = 'translateX(' + (-progress * (SLIDES - 1) * 100) + 'vw)';
  }

  window.addEventListener('scroll', updateElectrico, { passive: true });
  updateElectrico();
})();

// ── Tu auto piensa como vos — carrusel imágenes ───────────────────────────
(function () {
  var pin    = document.getElementById('tuAutoPin');
  var strip  = document.getElementById('tuAutoStrip');
  var dots   = document.querySelectorAll('#tuAutoDots .dot');
  var SLIDES = 6;
  var lastActive = 0;

  function updateTuAuto() {
    var scrolled    = window.scrollY - pin.offsetTop;
    var totalScroll = pin.offsetHeight - window.innerHeight;
    var progress    = Math.max(0, Math.min(1, scrolled / totalScroll));

    strip.style.transform = 'translateX(' + (-progress * (SLIDES - 1) * 100) + 'vw)';

    var active = Math.round(progress * (SLIDES - 1));
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
  captureFps:  isMobile ? 15  : 60,
  lerp:        0.07,
  pinHeight:   null,
  textEl:      document.getElementById('brabusText'),
  textElMid:   isMobile ? document.getElementById('brabusTextMid') : null,
  textZonePx:  isMobile ? 300 : 500,
  holdZonePx:  isMobile ? 400 : 600
});
