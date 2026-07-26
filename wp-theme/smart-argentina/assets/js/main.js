// ── Hero video: autoplay al entrar en pantalla completa, se congela en el
//    último frame al terminar, se reinicia al volver a verse por completo ──
function initHeroAutoplayVideo(videoId) {
  const video = document.getElementById(videoId);
  if (!video || !('IntersectionObserver' in window)) return;
  const section = video.closest('section');
  if (!section) return;

  new IntersectionObserver((entries) => {
    entries.forEach((entry) => {
      // 0.9 en vez de ~1: el hero tiene min-height:640px, que en ventanas más
      // bajas que eso puede superar el alto real del viewport y volver
      // inalcanzable un ratio cercano a 1 (el video nunca arrancaría).
      const isFullyVisible = entry.isIntersecting && entry.intersectionRatio >= 0.9;
      // Solo reinicia si terminó (o si todavía no arrancó nunca) — mientras
      // está reproduciéndose no se debe reiniciar aunque se pierda y recupere la visibilidad.
      const canRestart = video.ended || (video.paused && video.currentTime === 0);
      if (isFullyVisible && canRestart) {
        video.currentTime = 0;
        video.play().catch(() => {});
      }
    });
  }, { threshold: [0, 0.5, 0.9, 1] }).observe(section);
}

// ── Hero ─────────────────────────────────────────────────────────────────
initHeroAutoplayVideo('heroVideo');

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
initHeroAutoplayVideo('brabusVideo');

// ── smart X BRABUS banner: textos sincronizados con el tiempo del video ───
(function () {
  const video = document.getElementById('brabusVideo');
  if (!video) return;

  const midEls       = [document.getElementById('brabusTextMid'), document.getElementById('brabusTextMidDesktop')].filter(Boolean);
  const midDesktopEl = document.getElementById('brabusTextMidDesktop');
  const finalEls     = [document.getElementById('brabusText'), document.getElementById('brabusTextMobile')].filter(Boolean);

  function setOpacity(els, v) {
    els.forEach((el) => {
      el.style.opacity = v;
      el.style.pointerEvents = v > 0.5 ? 'auto' : 'none';
    });
  }

  // El texto mid desktop, además de fade, tenía un slide-up desde 40px (como antes con scroll)
  function setMid(v) {
    setOpacity(midEls, v);
    if (midDesktopEl) midDesktopEl.style.transform = `translateY(${(40 * (1 - v)).toFixed(1)}px)`;
  }

  function reset() {
    setMid(0);
    setOpacity(finalEls, 0);
  }
  reset();

  video.addEventListener('timeupdate', () => {
    const d = video.duration;
    if (!d || isNaN(d)) return;
    const p = video.currentTime / d;

    // Texto mid: aparece, se mantiene un rato, desaparece antes del final
    const midIn = 0.15, midHold = 0.35, midOut = 0.55, midGone = 0.65;
    let midOpacity;
    if (p < midIn) midOpacity = 0;
    else if (p < midHold) midOpacity = (p - midIn) / (midHold - midIn);
    else if (p < midOut) midOpacity = 1;
    else if (p < midGone) midOpacity = 1 - (p - midOut) / (midGone - midOut);
    else midOpacity = 0;
    setMid(midOpacity.toFixed(3));

    // Texto final: aparece cerca del final del video y queda visible
    const finalIn = 0.8;
    const finalOpacity = p < finalIn ? 0 : Math.min(1, (p - finalIn) / (1 - finalIn));
    setOpacity(finalEls, finalOpacity.toFixed(3));
  });

  video.addEventListener('ended', () => {
    setMid(0);
    setOpacity(finalEls, 1);
  });

  // Si el video se reinicia (ver initHeroAutoplayVideo), los textos vuelven a su estado inicial
  video.addEventListener('play', () => {
    if (video.currentTime < 0.15) reset();
  });
})();

// ── Brabus specs toggle ───────────────────────────────────────────────────────
let currentSpec = 1;

function switchSpec(num) {
  if (num === currentSpec) return;

  const bg1   = document.getElementById('spec-bg-1');
  const bg3   = document.getElementById('spec-bg-3');
  const tab1  = document.getElementById('spec-tab-1');
  const tab3  = document.getElementById('spec-tab-3');
  const ind   = document.getElementById('spec-slider-indicator');
  const accel    = document.getElementById('spec-accel');
  const range    = document.getElementById('spec-range');
  const bateria  = document.getElementById('spec-bateria');
  const traccion = document.getElementById('spec-traccion');
  const potencia = document.getElementById('spec-potencia');

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

  // Stats variables (vienen de wp_localize_script — ver page-brabus.php)
  const specs = window.brabusSpecs && window.brabusSpecs[num];
  if (specs) {
    if (accel)    accel.textContent    = specs.aceleracion;
    if (range)    range.textContent    = specs.autonomia;
    if (bateria)  bateria.textContent  = specs.bateria;
    if (traccion) traccion.textContent = specs.traccion;
    if (potencia) potencia.textContent = specs.potencia;
  }

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

// ── Scroll a sección vía ?scroll=id (ej: botón "Encontrá tu smart" de
//    movilidad eléctrica) — en mobile centra la sección en pantalla en vez
//    de alinearla arriba de todo, como hace el comportamiento nativo de un #hash ──
(function () {
  var params = new URLSearchParams(window.location.search);
  var targetId = params.get('scroll');
  if (!targetId) return;

  function init() {
    var el = document.getElementById(targetId);
    if (el) {
      var isMobile = window.innerWidth < 768;
      el.scrollIntoView({ behavior: 'auto', block: isMobile ? 'center' : 'start' });
    }
    history.replaceState(null, '', window.location.pathname + window.location.hash);
  }

  document.readyState === 'loading'
    ? document.addEventListener('DOMContentLoaded', init)
    : init();
})();

// ── Banner de cookies ───────────────────────────────────────────────────
(function () {
  var STORAGE_KEY = 'smart_cookie_consent';
  if (localStorage.getItem(STORAGE_KEY)) return;

  var cookiesUrl = window.WP_COOKIES_URL || 'cookies.html';

  var banner = document.createElement('div');
  banner.id = 'cookie-banner';
  banner.className = 'cookie-banner';
  banner.innerHTML =
    '<p class="cookie-banner__text font-smart-sans">Utilizamos cookies propias y de terceros para mejorar tu experiencia de navegación y analizar el uso del sitio. Podés aceptarlas o rechazarlas. <a href="' + cookiesUrl + '">Más información</a>.</p>' +
    '<div class="cookie-banner__actions">' +
      '<button type="button" class="cookie-banner__btn cookie-banner__btn--reject font-smart-sans">Rechazar</button>' +
      '<button type="button" class="cookie-banner__btn cookie-banner__btn--accept font-smart-sans">Aceptar</button>' +
    '</div>';

  function init() {
    document.body.appendChild(banner);
    requestAnimationFrame(function () {
      requestAnimationFrame(function () { banner.classList.add('is-visible'); });
    });

    function close(value) {
      localStorage.setItem(STORAGE_KEY, value);
      banner.classList.remove('is-visible');
      setTimeout(function () { banner.remove(); }, 400);
    }

    banner.querySelector('.cookie-banner__btn--accept').addEventListener('click', function () { close('accepted'); });
    banner.querySelector('.cookie-banner__btn--reject').addEventListener('click', function () { close('rejected'); });
  }

  document.readyState === 'loading'
    ? document.addEventListener('DOMContentLoaded', init)
    : init();
})();
