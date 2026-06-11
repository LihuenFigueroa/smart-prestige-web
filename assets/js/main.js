// ── Detección de iOS ──────────────────────────────────────────────────────
var isIOS = /iPad|iPhone|iPod/.test(navigator.userAgent) && !window.MSStream;
var hasCreateImageBitmap = typeof createImageBitmap === 'function';

// ── Función genérica: convierte cualquier sección en video scroll-driven ──
function initScrollVideo({ videoId, canvasId, pinId, videoSrc, pxPerSecond, captureFps, lerp, pinHeight, textEl, textElMid, textZonePx, holdZonePx, easeOut }) {
  var video  = document.getElementById(videoId);
  var canvas = document.getElementById(canvasId);
  var ctx    = canvas.getContext('2d');
  var pin    = document.getElementById(pinId);

  var PX_PER_SECOND = pxPerSecond || 300;
  // iOS: menos frames para ahorrar memoria
  var CAPTURE_FPS   = isIOS ? Math.min(captureFps || 24, 10) : (captureFps || 24);
  var LERP          = lerp || 0.12;
  var HOLD_PX       = holdZonePx || 0;

  var frames = [];
  var ready = false, totalFrames = 0;
  var targetProgress = 0, drawProgress = 0;
  var videoDuration = 0;

  // ── Canvas ───────────────────────────────────────────────────────────────
  function resizeCanvas() {
    var newW = canvas.offsetWidth  || window.innerWidth;
    var newH = canvas.offsetHeight || (pinHeight || window.innerHeight);
    if (canvas.width === newW && canvas.height === newH) return;
    canvas.width  = newW;
    canvas.height = newH;
    if (ready) renderProgress(drawProgress);
  }
  window.addEventListener('resize', resizeCanvas);
  resizeCanvas();

  // ── Render ───────────────────────────────────────────────────────────────
  function drawFrame(frame, alpha) {
    if (!frame) return;
    var cw = canvas.width, ch = canvas.height;
    ctx.globalAlpha = alpha;

    if (frame instanceof ImageData) {
      // Fallback sin createImageBitmap: usar canvas temporal estático
      if (!drawFrame._tmp) drawFrame._tmp = document.createElement('canvas');
      var tmp = drawFrame._tmp;
      if (tmp.width !== frame.width || tmp.height !== frame.height) {
        tmp.width  = frame.width;
        tmp.height = frame.height;
      }
      tmp.getContext('2d').putImageData(frame, 0, 0);
      var scale = Math.max(cw / frame.width, ch / frame.height);
      ctx.drawImage(tmp, (cw - frame.width * scale) / 2, (ch - frame.height * scale) / 2, frame.width * scale, frame.height * scale);
    } else {
      var scale = Math.max(cw / frame.width, ch / frame.height);
      ctx.drawImage(frame, (cw - frame.width * scale) / 2, (ch - frame.height * scale) / 2, frame.width * scale, frame.height * scale);
    }
  }

  function renderProgress(p) {
    if (!frames.length) return;
    var exact = p * (frames.length - 1);
    var idxA  = Math.floor(exact);
    var idxB  = Math.min(idxA + 1, frames.length - 1);
    var blend = exact - idxA;
    if (!frames[idxA]) return;
    ctx.clearRect(0, 0, canvas.width, canvas.height);
    drawFrame(frames[idxA], 1);
    if (blend > 0.01 && frames[idxB]) drawFrame(frames[idxB], blend);
    ctx.globalAlpha = 1;
  }

  // ── RAF loop ─────────────────────────────────────────────────────────────
  function tick() {
    if (ready) {
      var diff = targetProgress - drawProgress;
      if (Math.abs(diff) > 0.0001) {
        drawProgress += diff * LERP;
        renderProgress(drawProgress);
      }
    }
    requestAnimationFrame(tick);
  }

  // ── Scroll ───────────────────────────────────────────────────────────────
  function onScroll() {
    var rel     = window.scrollY - pin.offsetTop;
    var videoPx = videoDuration * PX_PER_SECOND;
    var extraPx = textZonePx || 0;

    if (textElMid) {
      var midFadeIn  = videoPx * 0.38;
      var midPeak    = videoPx * 0.58;
      var midFadeOut = midPeak + 350;
      var midGone    = midFadeOut + 350;
      if      (rel < midFadeIn)  textElMid.style.opacity = '0';
      else if (rel < midPeak)    textElMid.style.opacity = ((rel - midFadeIn) / (midPeak - midFadeIn)).toFixed(3);
      else if (rel < midFadeOut) textElMid.style.opacity = '1';
      else if (rel < midGone)    textElMid.style.opacity = (1 - (rel - midFadeOut) / (midGone - midFadeOut)).toFixed(3);
      else                       textElMid.style.opacity = '0';
    }

    if (rel <= videoPx) {
      var rawP = Math.max(0, rel / videoPx);
      targetProgress = easeOut ? rawP * (2 - rawP) : rawP;
      if (textEl) { textEl.style.opacity = '0'; textEl.style.pointerEvents = 'none'; }
    } else if (rel <= videoPx + extraPx) {
      targetProgress = 1;
      if (textEl && extraPx > 0) {
        var t = Math.min(1, (rel - videoPx) / extraPx);
        textEl.style.opacity       = t.toFixed(3);
        textEl.style.pointerEvents = t > 0.5 ? 'auto' : 'none';
      }
    } else {
      targetProgress = 1;
      if (textEl) { textEl.style.opacity = '1'; textEl.style.pointerEvents = 'auto'; }
    }
  }

  // ── Guardar frame desde canvas de captura ─────────────────────────────────
  function storeFrame(captureCanvas, captureCtx, srcVid, cw, ch, idx) {
    if (frames[idx]) return;
    captureCtx.drawImage(srcVid, 0, 0, cw, ch);
    if (hasCreateImageBitmap) {
      // Snapshot inmediato del canvas → ImageBitmap en GPU (eficiente en memoria)
      createImageBitmap(captureCanvas).then(function(bmp) {
        if (!frames[idx]) frames[idx] = bmp;
      });
    } else {
      // Fallback: ImageData (solo para iOS muy antiguo — baja resolución para no crashear)
      if (!frames[idx]) frames[idx] = captureCtx.getImageData(0, 0, cw, ch);
    }
  }

  // ── Fin de captura ────────────────────────────────────────────────────────
  function completeCapture() {
    // Esperar a que se resuelvan los createImageBitmap pendientes
    var delay = hasCreateImageBitmap ? 500 : 0;
    setTimeout(function() {
      var last = null;
      for (var i = 0; i < totalFrames; i++) {
        if (frames[i])   { last = frames[i]; }
        else if (last)   { frames[i] = last; }
      }
      onCaptureComplete();
    }, delay);
  }

  // ── Captura seek-by-seek (iOS y fallback) ─────────────────────────────────
  // En iOS, drawImage(video) durante playback puede devolver frames negros.
  // La única forma confiable es seek + esperar seeked + un rAF extra para
  // que WebKit termine de renderizar el frame antes de dibujar al canvas.
  function seekCapture(capVid, captureCanvas, captureCtx, duration, cw, ch) {
    var step = duration / totalFrames;
    var frameIdx = 0;

    function seekNext() {
      if (frameIdx >= totalFrames) {
        completeCapture();
        return;
      }
      var t = Math.min(frameIdx * step, duration - 0.001);
      capVid.currentTime = t;

      capVid.addEventListener('seeked', function() {
        // rAF extra: iOS Safari necesita un ciclo de render extra para que
        // el frame del video esté disponible para dibujar al canvas
        requestAnimationFrame(function() {
          storeFrame(captureCanvas, captureCtx, capVid, cw, ch, frameIdx);
          frameIdx++;
          requestAnimationFrame(seekNext);
        });
      }, { once: true });
    }

    // Activar el video con play+pause para que los seeks funcionen correctamente en iOS
    var initPlay = capVid.play();
    if (initPlay) {
      initPlay.then(function() {
        capVid.pause();
        seekNext();
      }).catch(function() {
        seekNext(); // Intentar seek directo si play falla
      });
    } else {
      seekNext();
    }
  }

  // ── Captura play-based (desktop y Android rápido) ────────────────────────
  function playCapture(capVid, captureCanvas, captureCtx, duration, cw, ch) {
    capVid.playbackRate = 4;
    var playPromise = capVid.play();

    if (!playPromise) {
      seekCapture(capVid, captureCanvas, captureCtx, duration, cw, ch);
      return;
    }

    playPromise.then(function() {
      var lastCapturedTime = -1;

      function captureLoop() {
        var t   = capVid.currentTime;
        var idx = Math.round((t / duration) * (totalFrames - 1));

        if (t !== lastCapturedTime && idx >= 0 && idx < totalFrames) {
          storeFrame(captureCanvas, captureCtx, capVid, cw, ch, idx);
          lastCapturedTime = t;
        }

        if (capVid.ended || t >= duration - 0.01) {
          capVid.currentTime = duration - 0.001;
          capVid.addEventListener('seeked', function() {
            storeFrame(captureCanvas, captureCtx, capVid, cw, ch, totalFrames - 1);
            completeCapture();
          }, { once: true });
          return;
        }

        requestAnimationFrame(captureLoop);
      }
      captureLoop();

    }).catch(function() {
      seekCapture(capVid, captureCanvas, captureCtx, duration, cw, ch);
    });
  }

  // ── startBackgroundCapture ────────────────────────────────────────────────
  function startBackgroundCapture(capVid, duration) {
    totalFrames = Math.round(duration * CAPTURE_FPS);
    frames.length = totalFrames;

    var vw = capVid.videoWidth  || 1280;
    var vh = capVid.videoHeight || 720;

    // En iOS reducir la resolución de captura a la mitad para ahorrar memoria
    var cw = isIOS ? Math.round(vw / 2) : vw;
    var ch = isIOS ? Math.round(vh / 2) : vh;

    // Canvas regular (compatible con todos los browsers, reemplaza OffscreenCanvas)
    var captureCanvas = document.createElement('canvas');
    captureCanvas.width  = cw;
    captureCanvas.height = ch;
    var captureCtx = captureCanvas.getContext('2d');

    // iOS: siempre seek-based (drawImage durante playback puede dar frames negros)
    if (isIOS) {
      seekCapture(capVid, captureCanvas, captureCtx, duration, cw, ch);
    } else {
      playCapture(capVid, captureCanvas, captureCtx, duration, cw, ch);
    }
  }

  // ── Primer frame ──────────────────────────────────────────────────────────
  function showFirstFrame() {
    var vw = video.videoWidth  || 1280;
    var vh = video.videoHeight || 720;

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
          video.addEventListener('seeked', function() {
            // rAF extra en iOS para que el frame esté listo
            requestAnimationFrame(showFirstFrame);
          }, { once: true });
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
  var pin    = document.getElementById('electricoPin');
  var strip  = document.getElementById('electricoStrip');
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
