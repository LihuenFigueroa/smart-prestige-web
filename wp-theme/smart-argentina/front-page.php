<?php /* Template Name: Home */ ?>
<?php get_header(); ?>
<?php get_template_part('partials/header'); ?>
  <!-- ================================================================
       HERO + NAV  (scroll-pinned video)
  ================================================================ -->
  <div id="heroPin">
  <section class="sticky top-0 relative w-full overflow-hidden" style="height:100vh; height:100dvh; min-height:640px; background:#141413;">
    <canvas id="heroCanvas" class="absolute inset-0 w-full h-full" style="z-index:2;"></canvas>
    <video
      id="heroVideo"
      muted playsinline preload="auto"
      class="absolute inset-0 w-full h-full object-cover object-center"
      style="z-index:1; opacity:0; pointer-events:none;"
    ></video>

    <div class="absolute top-0 left-0 right-0 pointer-events-none" style="height:170px; z-index:5; background: linear-gradient(to bottom, rgba(20,20,19,0.65) 0%, rgba(20,20,19,0.57) 35%, rgba(20,20,19,0) 100%);"></div>
    <div class="absolute bottom-0 left-0 right-0 pointer-events-none" style="height:261px; z-index:5; background: linear-gradient(to bottom, rgba(0,0,0,0) 0%, rgba(0,0,0,0.58) 60%, rgba(0,0,0,0.85) 100%);"></div>

    <!-- Navigation -->
    <nav class="absolute top-0 left-0 right-0 z-20 px-5 md:px-14 h-14 md:h-16 flex items-center justify-between">
      <div class="flex items-center gap-8">
        <button onclick="openNavMenu()" class="flex flex-col gap-[5px] cursor-pointer" aria-label="Menú">
          <span class="w-5 h-px bg-white block"></span>
          <span class="w-5 h-px bg-white block"></span>
          <span class="w-5 h-px bg-white block"></span>
        </button>
        <div class="relative hidden md:block" id="modelos-dropdown">
          <button onclick="toggleModelosDropdown()" class="flex items-center gap-1 text-white text-sm font-normal uppercase tracking-wide leading-6">
            MODELOS
            <svg id="modelos-chevron" class="w-3 h-3 ml-1" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
          </button>
          <div id="modelos-menu" class="absolute top-full left-0 mt-1 min-w-[152px] z-50">
            <a href="<?php echo home_url('/smart-1/'); ?>" class="block px-6 py-4 text-[#141413] text-base font-smart-sans border-b border-neutral-200 hover:bg-neutral-100 transition-colors">smart #1</a>
            <a href="<?php echo home_url('/smart-3/'); ?>" class="block px-6 py-4 text-[#141413] text-base font-smart-sans hover:bg-neutral-100 transition-colors">smart #3</a>
          </div>
        </div>
      </div>
      <div class="absolute left-1/2 -translate-x-1/2">
        <img src="<?php echo get_template_directory_uri(); ?>/assets/img/Logonavbar.svg" alt="smart" class="h-4 md:h-8 w-auto" />
      </div>
    </nav>

    <!-- Hero content -->
    <div class="absolute bottom-0 left-0 right-0 px-5 md:px-14 pb-8 md:pb-14 flex flex-col md:flex-row md:justify-between md:items-end gap-4 z-10">
      <div class="max-w-xl">
        <h1 class="text-white text-3xl font-normal leading-10 mb-2 font-smart-next">
          Electrizante por naturaleza.
        </h1>
        <p class="text-white text-lg font-normal leading-6 mb-5 md:mb-8 font-smart-sans">
          El SUV 100% eléctrico que redefine lo que significa conducir bien.
        </p>
        <div class="flex gap-3 items-center flex-wrap">
          <a href="#contacto" class="h-10 px-6 bg-white rounded-full text-sm font-bold tracking-tight text-neutral-900 flex items-center hover:bg-neutral-100 transition-colors">
            Contactanos
          </a>
        </div>
      </div>
    </div>
  </section>
  </div>

  <!-- ================================================================
       ELEGÍ TU SMART
  ================================================================ -->
  <style>
    #elegir-modelo          { aspect-ratio:375/465; }
    #elegi-img-wrap         { left:5.07%; top:10.75%; width:89.6%; height:71%; }
    #elegi-title-area       { top:4%; gap:0.5rem; width:80%; }
    #model-cta              { top:89%; }
    @media (min-width:768px) {
      #elegir-modelo        { aspect-ratio:1440/891; }
      #elegi-img-wrap       { left:3.19%; top:5.39%; width:93.54%; height:81.93%; }
      #elegi-title-area     { top:9%; gap:1rem; width:auto; }
      #model-cta            { top:93%; }
    }
    @media (min-width:768px) and (max-width:1024px) {
      #elegi-title-area     { top:4%; gap:0.5rem; }
      #elegi-title-area h2  { font-size:1.4rem; }
      #tab-smart1, #tab-smart3 { height:26px; font-size:12px; padding-left:14px; padding-right:14px; }
    }
  </style>
  <section id="elegir-modelo" style="position:relative; width:100%; overflow:hidden; background:#fff;">

    <!-- Wrapper de imagen: posición exacta del Figma, clipea el slide -->
    <div id="elegi-img-wrap" style="position:absolute; overflow:hidden;">

      <!-- Gradiente blanco en el top -->
      <div style="position:absolute; top:0; left:0; right:0; height:25%; background:linear-gradient(to bottom,rgba(255,255,255,0.95) 0%,rgba(255,255,255,0) 100%); z-index:2; pointer-events:none;"></div>

      <img
        id="model-img-1"
        src="<?php echo get_template_directory_uri(); ?>/assets/img/home/elegiTuSmart/smart1_home.jpeg"
        alt="smart #1"
        style="position:absolute; inset:0; width:100%; height:100%; object-fit:cover; object-position:center; transform:translateX(0); opacity:1; transition:transform 0.5s ease,opacity 0.5s ease;"
      />
      <img
        id="model-img-3"
        src="<?php echo get_template_directory_uri(); ?>/assets/img/home/elegiTuSmart/smart3_home.jpeg"
        alt="smart #3"
        style="position:absolute; inset:0; width:100%; height:100%; object-fit:cover; object-position:center; transform:translateX(100%); opacity:0; transition:transform 0.5s ease,opacity 0.5s ease;"
      />
    </div>

    <!-- Título + tabs: centrados -->
    <div id="elegi-title-area" style="position:absolute; left:50%; transform:translateX(-50%); z-index:10; display:flex; flex-direction:column; align-items:center;">
      <h2 class="font-smart-next font-bold text-neutral-900 text-center" style="font-size:clamp(2rem,3.5vw,2.8rem); white-space:nowrap;">Elegí tu smart.</h2>
      <div id="model-switcher" class="flex items-center bg-white border border-neutral-900 rounded-full" style="position:relative; overflow:hidden;">
        <!-- Píldora deslizante -->
        <div id="model-slider-indicator" style="position:absolute; top:0; left:0; height:100%; border-radius:9999px; background:#171717; z-index:0; transition:transform 0.4s cubic-bezier(0.4,0,0.2,1), width 0.4s cubic-bezier(0.4,0,0.2,1);"></div>
        <button
          id="tab-smart1"
          onclick="switchModel(1)"
          class="h-[30px] md:h-8 px-5 md:px-5 text-[13px] md:text-base font-normal whitespace-nowrap"
          style="position:relative; z-index:1; color:#fff; transition:color 0.4s ease;"
        >smart #1</button>
        <button
          id="tab-smart3"
          onclick="switchModel(3)"
          class="h-[30px] md:h-8 px-5 md:px-5 text-[13px] md:text-base font-normal whitespace-nowrap"
          style="position:relative; z-index:1; color:#171717; transition:color 0.4s ease;"
        >smart #3</button>
      </div>
    </div>

    <!-- CTA: centrado debajo de la imagen -->
    <a
      id="model-cta"
      href="<?php echo home_url('/smart-1/'); ?>"
      style="position:absolute; left:50%; transform:translate(-50%,-50%); white-space:nowrap; z-index:10;"
      class="h-10 md:h-12 px-6 md:px-8 rounded-full border border-neutral-900 text-sm md:text-sm font-bold text-neutral-900 inline-flex items-center hover:bg-neutral-900 hover:text-white transition-colors"
    >
      Descubrí más sobre el smart #1
    </a>

  </section>

  <!-- ================================================================
       FEATURE CARDS — carrusel drag-to-scroll
  ================================================================ -->
  <section id="modelos" class="w-full bg-white py-10 md:py-16" style="overflow:hidden;">
    <div
      id="cards-track"
      class="flex select-none"
      style="overflow-x:scroll; scrollbar-width:none; -ms-overflow-style:none;"
    >

      <div class="flex flex-col cards-item">
        <img src="<?php echo get_template_directory_uri(); ?>/assets/img/home/carrusel/1.jpg" alt="smart X BRABUS" class="w-full aspect-[4/3] object-cover" style="pointer-events:none;" />
        <div class="flex flex-col flex-1" style="gap:0.75rem;">
          <h3 class="font-smart-next text-3xl md:text-4xl font-normal text-black leading-tight">smart X BRABUS.</h3>
          <p class="text-base text-black leading-6 flex-1 font-smart-sans">Rendimiento extremo. Diseño inconfundible. La versión más potente de smart lleva cada detalle al límite.</p>
          <a href="<?php echo home_url('/brabus/'); ?>" class="self-start h-10 px-5 rounded-full border border-black text-sm font-bold text-neutral-900 flex items-center hover:bg-black hover:text-white transition-colors">Descubrilo</a>
        </div>
      </div>

      <div class="flex flex-col cards-item">
        <img src="<?php echo get_template_directory_uri(); ?>/assets/img/home/carrusel/2.jpg" alt="Conectividad" class="w-full aspect-[4/3] object-cover" style="pointer-events:none;" />
        <div class="flex flex-col flex-1" style="gap:0.75rem;">
          <h3 class="font-smart-next text-3xl md:text-4xl font-normal text-black leading-tight">Conectividad.</h3>
          <p class="text-base text-black leading-6 flex-1 font-smart-sans">Pantalla central de 12,8'', Apple CarPlay® y Android Auto® inalámbrico. El camino y tu mundo, integrados.</p>
          <a href="<?php echo home_url('/conectividad/'); ?>" class="self-start h-10 px-5 rounded-full border border-black text-sm font-bold text-neutral-900 flex items-center hover:bg-black hover:text-white transition-colors">Conocé más</a>
        </div>
      </div>

      <div class="flex flex-col cards-item">
        <img src="<?php echo get_template_directory_uri(); ?>/assets/img/home/carrusel/3.jpg" alt="Movilidad eléctrica" class="w-full aspect-[4/3] object-cover" style="pointer-events:none;" />
        <div class="flex flex-col flex-1" style="gap:0.75rem;">
          <h3 class="font-smart-next text-3xl md:text-4xl font-normal text-black leading-tight">Movilidad eléctrica.</h3>
          <p class="text-base text-black leading-6 flex-1 font-smart-sans">Hasta 597 km de autonomía y carga rápida en menos de 30 minutos. El futuro de la movilidad ya tiene forma.</p>
          <p class="text-xs text-black/30 leading-4 font-smart-sans">Autonomía sujeta a tipo de conducción, condiciones del terreno y condiciones del clima.</p>
          <a href="<?php echo home_url('/movilidad-electrica/'); ?>" class="self-start h-10 px-5 rounded-full border border-black text-sm font-bold text-neutral-900 flex items-center hover:bg-black hover:text-white transition-colors">Explorá</a>
        </div>
      </div>

      <div class="flex flex-col cards-item">
        <img src="<?php echo get_template_directory_uri(); ?>/assets/img/home/carrusel/4.png" alt="Servicios al cliente" class="w-full aspect-[4/3] object-cover" style="pointer-events:none;" />
        <div class="flex flex-col flex-1" style="gap:0.75rem;">
          <h3 class="font-smart-next text-3xl md:text-4xl font-normal text-black leading-tight">Servicios al cliente.</h3>
          <p class="text-base text-black leading-6 flex-1 font-smart-sans">Respaldo completo en cada kilómetro. Porque smart no termina en la compra.</p>
          <a href="<?php echo home_url('/servicios/'); ?>" class="self-start h-10 px-5 rounded-full border border-black text-sm font-bold text-neutral-900 flex items-center hover:bg-black hover:text-white transition-colors">Ver servicios</a>
        </div>
      </div>

    </div>
  </section>

  <!-- ================================================================
       BUSCADOR DE CONCESIONARIOS
  ================================================================ -->
  <section id="concesionarios" class="w-full bg-white py-10 md:py-16 px-5 md:px-14">
    <div class="max-w-[1320px] mx-auto flex flex-col md:flex-row gap-10 md:gap-16 md:items-start">

      <div class="flex flex-col gap-6 md:gap-8 md:w-[480px] md:flex-shrink-0">
        <div class="flex flex-col gap-3">
          <h2 class="font-smart-next text-3xl md:text-4xl font-normal text-neutral-900 leading-tight">
            Buscador de concesionarios.
          </h2>
          <p class="text-base text-neutral-900 leading-6 font-smart-sans">
            Desde la primera consulta hasta cada nuevo destino, la red de concesionarios smart te acompaña en cada paso de tu experiencia.
          </p>
        </div>
        <a href="<?php echo home_url('/buscador/'); ?>" class="self-start h-12 px-6 bg-neutral-900 rounded-full text-sm font-bold text-white inline-flex items-center hover:bg-neutral-700 transition-colors">
          Encontrá tu concesionario
        </a>
      </div>

      <div class="flex-1 overflow-hidden">
        <img src="<?php echo get_template_directory_uri(); ?>/assets/img/home/encontraTuSucursal/presenciaEnArgentina.png" alt="Concesionarios Argentina" class="w-full" />
      </div>

    </div>
  </section>

  <!-- ================================================================
       QUIENES SOMOS
  ================================================================ -->
  <section id="quienes-somos" class="w-full bg-white py-10 md:py-16 px-5 md:px-14">
    <div class="bg-[#141413] max-w-[1320px] mx-auto flex flex-col md:flex-row md:items-stretch">

      <div class="flex flex-col justify-center gap-6 md:w-1/2 px-10 md:px-14 py-10 md:py-16">
        <h2 class="font-smart-next text-3xl md:text-4xl font-normal text-white leading-tight">
          smart: tres décadas reinventando el auto urbano.
        </h2>
        <a href="<?php echo home_url('/sobre-smart/'); ?>" class="self-start h-12 px-6 bg-white rounded-full text-sm font-bold text-neutral-900 inline-flex items-center hover:bg-neutral-100 transition-colors">
          Descubrí quiénes somos
        </a>
      </div>

      <div id="quienes-img" class="md:w-1/2">
        <img src="<?php echo get_template_directory_uri(); ?>/assets/img/tres-decadas.png" alt="smart lifestyle"
             style="display:block; width:100%;" />
      </div>

    </div>
  </section>

  <!-- ================================================================
       FORMULARIO DE CONTACTO
  ================================================================ -->
  <?php get_template_part('partials/form-contacto'); ?>

  <!-- ================================================================
       FOOTER
  ================================================================ -->
  <?php get_template_part('partials/footer'); ?>

  <script>
    // Precargá las imágenes para que el slide no tenga delay
    new Image().src = 'assets/img/home/elegiTuSmart/smart1_home.jpeg';
    new Image().src = 'assets/img/home/elegiTuSmart/smart3_home.jpeg';

    let currentModel = 1;

    function initModelSwitcher() {
      const tab1      = document.getElementById('tab-smart1');
      const indicator = document.getElementById('model-slider-indicator');
      indicator.style.width     = tab1.offsetWidth + 'px';
      indicator.style.transform = `translateX(${tab1.offsetLeft}px)`;
    }

    function switchModel(num) {
      if (num === currentModel) return;

      const img1      = document.getElementById('model-img-1');
      const img3      = document.getElementById('model-img-3');
      const cta       = document.getElementById('model-cta');
      const tab1      = document.getElementById('tab-smart1');
      const tab3      = document.getElementById('tab-smart3');
      const indicator = document.getElementById('model-slider-indicator');

      const goingRight = num > currentModel; // #1→#3: derecha, #3→#1: izquierda
      const incoming   = num === 1 ? img1 : img3;
      const outgoing   = num === 1 ? img3 : img1;

      // Slide de imagen
      incoming.style.transition = 'none';
      incoming.style.transform  = goingRight ? 'translateX(100%)' : 'translateX(-100%)';
      incoming.style.opacity    = '0';
      incoming.getBoundingClientRect(); // forzar reflow
      incoming.style.transition = 'transform 0.5s ease, opacity 0.5s ease';
      outgoing.style.transition = 'transform 0.5s ease, opacity 0.5s ease';
      incoming.style.transform  = 'translateX(0)';
      incoming.style.opacity    = '1';
      outgoing.style.transform  = goingRight ? 'translateX(-100%)' : 'translateX(100%)';
      outgoing.style.opacity    = '0';

      // CTA
      cta.textContent = num === 1
        ? 'Descubrí más sobre el smart #1'
        : 'Descubrí más sobre el smart #3';
      cta.href = num === 1 ? 'smart1.html' : 'smart3.html';

      // Píldora deslizante: mover y redimensionar
      const activeTab = num === 1 ? tab1 : tab3;
      indicator.style.width     = activeTab.offsetWidth + 'px';
      indicator.style.transform = `translateX(${activeTab.offsetLeft}px)`;

      // Colores de texto (sincronizados con la transición del indicator)
      tab1.style.color = num === 1 ? '#fff' : '#171717';
      tab3.style.color = num === 3 ? '#fff' : '#171717';

      currentModel = num;
    }

    requestAnimationFrame(initModelSwitcher);

    // ── Carrusel de feature cards — drag-to-scroll con inercia ─────────
    (function () {
      const track = document.getElementById('cards-track');
      if (!track) return;

      let isDragging  = false;
      let hasMoved    = false;
      let startX      = 0;
      let startScroll = 0;
      let lastX       = 0;
      let lastTime    = 0;
      let velocity    = 0;
      let rafId       = null;

      // Inercia post-release
      function momentum() {
        velocity *= 0.97;          // fricción: 0.97 = desaceleración muy suave
        track.scrollLeft -= velocity;
        if (Math.abs(velocity) > 0.15) {
          rafId = requestAnimationFrame(momentum);
        }
      }

      track.addEventListener('mousedown', function (e) {
        cancelAnimationFrame(rafId);
        isDragging  = true;
        hasMoved    = false;
        startX      = e.clientX;
        startScroll = track.scrollLeft;
        lastX       = e.clientX;
        lastTime    = performance.now();
        velocity    = 0;
        track.style.cursor = 'grabbing';
      });

      window.addEventListener('mouseup', function () {
        if (!isDragging) return;
        isDragging = false;
        track.style.cursor = 'grab';
        rafId = requestAnimationFrame(momentum);
      });

      track.addEventListener('mousemove', function (e) {
        if (!isDragging) return;
        const delta = e.clientX - startX;
        if (Math.abs(delta) > 4) hasMoved = true;

        // Velocidad: píxeles por ms, normalizado a frame de 16ms
        const now = performance.now();
        const dt  = now - lastTime || 1;
        velocity  = ((e.clientX - lastX) / dt) * 16;
        lastX     = e.clientX;
        lastTime  = now;

        track.scrollLeft = startScroll - delta;
      });

      // Bloquear clicks en links si hubo arrastre real
      track.addEventListener('click', function (e) {
        if (hasMoved) e.preventDefault();
      }, true);
    })();
  </script>

  <script src="<?php echo get_template_directory_uri(); ?>/assets/js/main.js"></script>

  <script>
    function toggleModelosDropdown() {
      const menu    = document.getElementById('modelos-menu');
      const chevron = document.getElementById('modelos-chevron');
      menu.classList.toggle('is-open');
      chevron.style.transform = menu.classList.contains('is-open') ? 'rotate(180deg)' : '';
    }
    document.addEventListener('click', function(e) {
      const dd = document.getElementById('modelos-dropdown');
      if (dd && !dd.contains(e.target)) {
        const menu    = document.getElementById('modelos-menu');
        const chevron = document.getElementById('modelos-chevron');
        if (menu)    menu.classList.remove('is-open');
        if (chevron) chevron.style.transform = '';
      }
    });
    function openNavMenu() {
      const menu     = document.getElementById('nav-menu');
      const drawer   = document.getElementById('nav-drawer');
      const backdrop = document.getElementById('nav-backdrop');
      menu.classList.remove('pointer-events-none');
      backdrop.classList.remove('opacity-0');
      backdrop.classList.add('opacity-100');
      drawer.classList.remove('-translate-x-full');
      document.body.style.overflow = 'hidden';
    }
    function closeNavMenu() {
      const menu     = document.getElementById('nav-menu');
      const drawer   = document.getElementById('nav-drawer');
      const backdrop = document.getElementById('nav-backdrop');
      backdrop.classList.remove('opacity-100');
      backdrop.classList.add('opacity-0');
      drawer.classList.add('-translate-x-full');
      setTimeout(() => {
        menu.classList.add('pointer-events-none');
        document.body.style.overflow = '';
      }, 300);
    }
  </script>
  <script>
    document.addEventListener('click', function(e) {
      var a = e.target.closest('a[href^="#"]');
      if (!a) return;
      var id = a.getAttribute('href');
      var target = document.querySelector(id);
      if (!target) return;
      e.preventDefault();
      var start = window.pageYOffset;
      var end = target.getBoundingClientRect().top + start;
      var duration = 480;
      var startTime = null;
      function step(ts) {
        if (!startTime) startTime = ts;
        var progress = Math.min((ts - startTime) / duration, 1);
        var ease = progress < 0.5 ? 2*progress*progress : -1+(4-2*progress)*progress;
        window.scrollTo(0, start + (end - start) * ease);
        if (progress < 1) requestAnimationFrame(step);
      }
      requestAnimationFrame(step);
    });
  </script>
<?php wp_footer(); ?>
</body>
</html>
