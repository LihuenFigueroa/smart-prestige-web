<?php /* Template Name: Servicios */ ?>
<?php $smart_acordeon_servicios = smart_get_servicio_acordeon(); ?>
<?php get_header(); ?>
<?php get_template_part('partials/header'); ?>
  <!-- ================================================================
       HERO
  ================================================================ -->
  <section class="relative w-full h-screen min-h-[640px] overflow-hidden">
    <div class="absolute inset-0 bg-neutral-700">
      <img src="<?php echo get_template_directory_uri(); ?>/assets/img/servicios/hero.jpg" alt="Servicios al cliente" class="servicios-hero-img w-full h-full object-cover" style="object-position: center 85%;" />
    </div>
    <div class="absolute top-0 left-0 right-0 pointer-events-none" style="height:170px; z-index:5; background:linear-gradient(to bottom,rgba(20,20,19,0.65) 0%,rgba(20,20,19,0) 100%);"></div>
    <div class="absolute bottom-0 left-0 right-0 pointer-events-none" style="height:261px; z-index:5; background:linear-gradient(to bottom,rgba(0,0,0,0) 0%,rgba(0,0,0,0.85) 100%);"></div>

    <nav class="absolute top-0 left-0 right-0 z-20 px-5 md:px-14 h-14 md:h-16 flex items-center justify-between">
      <div class="flex items-center gap-6">
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
        <a href="<?php echo home_url('/'); ?>"><img src="<?php echo get_template_directory_uri(); ?>/assets/img/Logonavbar.svg" alt="smart" class="h-4 md:h-8 w-auto" /></a>
      </div>
    </nav>

    <div class="svc-hero-text-wrap absolute bottom-0 left-0 right-0 px-5 md:px-14 pb-8 md:pb-14 z-10">
      <h1 class="svc-hero-title font-smart-next font-normal text-white" style="font-size:45px; line-height:1.1;">Servicios al cliente.</h1>
    </div>
  </section>

  <!-- ================================================================
       INTRO + CARRUSEL  (scroll-pinned en desktop)
  ================================================================ -->
  <section id="svc-intro" class="svc-intro-section w-full bg-white overflow-hidden" style="height:480px;">
    <div class="flex flex-col md:flex-row" style="height:100%;">

      <!-- Texto izquierda -->
      <div class="svc-intro-text-wrap px-5 md:flex-shrink-0 flex flex-col justify-center py-10 md:py-0" style="width:471px; padding-left:56px; padding-right:48px;">
        <div class="svc-intro-inner" style="width:375px; max-width:100%; height:185px; display:flex; flex-direction:column; justify-content:center;">
          <div class="flex flex-col gap-2">
            <p class="svc-intro-label font-smart-sans font-normal" style="font-size:20px; line-height:120%; letter-spacing:-0.01em; color:#6B747B;">Servicios</p>
            <h2 class="svc-intro-headline font-smart-next font-normal" style="font-size:29px; line-height:36px; color:#141413;">
              Tu smart en las manos de expertos.
            </h2>
          </div>
          <p class="svc-intro-desc font-smart-sans font-normal" style="margin-top:24px; font-size:15px; line-height:130%; letter-spacing:0.02em; color:#000000;">
            En smart, nos esforzamos al máximo para ofrecerte servicios de excelencia para tu vehículo y mucho más.
          </p>
        </div>
      </div>

      <!-- Carrusel imágenes — drag-to-scroll -->
      <div id="svc-carousel-viewport" class="flex-1 min-w-0 flex items-center" style="padding-top:19px; padding-bottom:19px; overflow:hidden;">
        <div id="track-svc-intro" class="flex select-none" style="gap:19px; height:432px; will-change:transform; overflow-x:scroll; scrollbar-width:none; -ms-overflow-style:none; cursor:grab;">
          <div class="svc-intro-slide flex-shrink-0 h-full" style="width:clamp(260px,39.9vw,574px);">
            <img src="<?php echo get_template_directory_uri(); ?>/assets/img/servicios/intro-img1.jpg" alt="" class="w-full h-full object-cover" draggable="false" />
          </div>
          <div class="svc-intro-slide flex-shrink-0 h-full" style="width:clamp(260px,39.8vw,573px);">
            <img src="<?php echo get_template_directory_uri(); ?>/assets/img/servicios/intro-img2.jpg" alt="" class="w-full h-full object-cover" draggable="false" />
          </div>
          <div class="svc-intro-slide flex-shrink-0 h-full" style="width:clamp(260px,39.8vw,573px);">
            <img src="<?php echo get_template_directory_uri(); ?>/assets/img/servicios/intro-img3.jpg" alt="" class="w-full h-full object-cover" draggable="false" />
          </div>
        </div>
      </div>

    </div>
  </section>

  <!-- ================================================================
       FORMULARIO
  ================================================================ -->
  <section id="contacto" class="w-full bg-white py-12 md:pt-0 md:pb-16 px-5 md:px-14">
    <div class="max-w-[1320px] mx-auto flex flex-col md:flex-row gap-10 md:gap-16 md:items-start">

      <!-- Izquierda: título + subtítulo -->
      <div class="md:w-[440px] md:flex-shrink-0 md:pt-1 flex flex-col gap-6">
        <h2 class="contacto-title font-smart-next font-normal text-black">Agendá online un turno de servicio.</h2>
        <p class="contacto-subtitle text-base font-normal font-smart-sans">Contactá a tu concesionario más cercano. Agendá fácilmente turnos para el mantenimiento, las reparaciones y otros servicios en un taller autorizado cerca tuyo.</p>
      </div>

      <!-- Derecha: campos -->
      <div class="flex-1">
        <form id="form-contacto-servicios" novalidate>
          <div class="border-b border-neutral-200 py-3"><input type="text" placeholder="NOMBRE" class="font-smart-sans w-full text-sm text-black bg-transparent outline-none placeholder:text-xs placeholder:tracking-widest placeholder:text-neutral-400" /></div>
          <div class="border-b border-neutral-200 py-3"><input type="text" placeholder="APELLIDO" class="font-smart-sans w-full text-sm text-black bg-transparent outline-none placeholder:text-xs placeholder:tracking-widest placeholder:text-neutral-400" /></div>
          <div class="border-b border-neutral-200 py-3"><input type="text" placeholder="CIUDAD" class="font-smart-sans w-full text-sm text-black bg-transparent outline-none placeholder:text-xs placeholder:tracking-widest placeholder:text-neutral-400" /></div>
          <div class="border-b border-neutral-200 py-3"><input type="email" placeholder="CORREO ELECTRÓNICO" class="font-smart-sans w-full text-sm text-black bg-transparent outline-none placeholder:text-xs placeholder:tracking-widest placeholder:text-neutral-400" /></div>
          <div class="border-b border-neutral-200 py-3"><input type="tel" placeholder="CELULAR" class="font-smart-sans w-full text-sm text-black bg-transparent outline-none placeholder:text-xs placeholder:tracking-widest placeholder:text-neutral-400" /></div>
          <div class="border-b border-neutral-200 py-3 relative">
            <select name="modelo" class="font-smart-sans w-full text-xs bg-transparent appearance-none cursor-pointer outline-none" style="color:#9ca3af; letter-spacing:0.1em;" onchange="this.style.color=this.value?'#111827':'#9ca3af'; this.style.letterSpacing=this.value?'normal':'0.1em';">
              <option value="" disabled selected>SELECCIONE MODELO</option>
              <option value="smart1">smart #1</option>
              <option value="smart3">smart #3</option>
            </select>
            <span class="pointer-events-none absolute right-0 top-1/2 -translate-y-1/2 text-neutral-400 text-xs">&#8964;</span>
          </div>
          <div class="border-b border-neutral-200 py-3 relative">
            <select name="servicio" class="font-smart-sans w-full text-xs bg-transparent appearance-none cursor-pointer outline-none" style="color:#9ca3af; letter-spacing:0.1em;" onchange="this.style.color=this.value?'#111827':'#9ca3af'; this.style.letterSpacing=this.value?'normal':'0.1em';">
              <option value="" disabled selected>TIPO DE SERVICIO</option>
              <option value="mantenimiento">Mantenimiento y service</option>
              <option value="reparacion">Reparación</option>
              <option value="garantia">Garantía</option>
              <option value="carga">Soluciones de carga</option>
              <option value="otro">Otro</option>
            </select>
            <span class="pointer-events-none absolute right-0 top-1/2 -translate-y-1/2 text-neutral-400 text-xs">&#8964;</span>
          </div>
          <div class="py-3">
            <p class="font-smart-sans text-xs" style="letter-spacing:0.1em; color:#9ca3af; text-transform:uppercase; margin-bottom:8px;">CONSULTA</p>
            <textarea rows="5" class="font-smart-sans w-full text-sm text-black bg-transparent outline-none resize-none border border-neutral-200 p-3"></textarea>
          </div>
          <div class="pt-6">
            <button type="submit" id="btn-enviar" class="self-start h-10 px-8 bg-black rounded-full text-sm font-bold text-white hover:bg-neutral-800 transition-colors">Enviar</button>
          </div>
        </form>
      </div>

    </div>
  </section>

  <!-- ================================================================
       SÁBANA
  ================================================================ -->
  <section class="w-full bg-white px-5 md:px-14 pt-10 md:pt-16" style="padding-bottom:48px;">
    <div class="max-w-[1320px] mx-auto">

      <?php foreach ($smart_acordeon_servicios as $i => $item): $isLast = $i === count($smart_acordeon_servicios) - 1; ?>
      <div class="sabana-card border-t <?php echo $isLast ? 'border-b ' : ''; ?>border-neutral-200">
        <button class="sabana-btn w-full flex items-center justify-between text-left gap-6" style="height:71px;" onclick="toggleSabana(this)">
          <span class="font-smart-next font-normal text-black" style="font-size:18px;"><?php echo esc_html($item['titulo']); ?></span>
          <svg class="sabana-icon flex-shrink-0 text-neutral-400" width="16" height="16" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.2"><line x1="8" y1="1" x2="8" y2="15" class="sabana-vline"/><line x1="1" y1="8" x2="15" y2="8"/></svg>
        </button>
        <div class="sabana-content">
          <p class="font-smart-sans" style="color:#141413; font-size:15px; line-height:160%; padding-bottom:24px;"><?php echo nl2br(esc_html($item['contenido'])); ?></p>
        </div>
      </div>
      <?php endforeach; ?>

      <div style="margin-top:24px;">
        <a href="#contacto" class="inline-flex items-center h-10 px-6 rounded-full border border-neutral-800 font-smart-sans text-sm font-normal text-black hover:bg-neutral-100 transition-colors">Contact Support</a>
      </div>

    </div>
  </section>


  <!-- ================================================================
       FOOTER
  ================================================================ -->
  <?php get_template_part('partials/footer'); ?>

  <script>
    /* ── Carrusel servicios — drag-to-scroll con inercia ── */
    (function () {
      var track = document.getElementById('track-svc-intro');
      if (!track) return;
      var isDown = false, startX, scrollLeft, velocity = 0, lastX, rafId;
      track.addEventListener('mousedown', function (e) {
        isDown = true; track.style.cursor = 'grabbing';
        startX = e.pageX - track.offsetLeft; scrollLeft = track.scrollLeft;
        velocity = 0; lastX = e.pageX; cancelAnimationFrame(rafId);
      });
      track.addEventListener('mouseleave', function () { isDown = false; track.style.cursor = 'grab'; startInertia(); });
      track.addEventListener('mouseup',    function () { isDown = false; track.style.cursor = 'grab'; startInertia(); });
      track.addEventListener('mousemove',  function (e) {
        if (!isDown) return; e.preventDefault();
        velocity = e.pageX - lastX; lastX = e.pageX;
        track.scrollLeft = scrollLeft - (e.pageX - track.offsetLeft - startX);
      });
      function startInertia() {
        cancelAnimationFrame(rafId);
        (function loop() {
          if (Math.abs(velocity) < 0.5) return;
          track.scrollLeft -= velocity; velocity *= 0.92;
          rafId = requestAnimationFrame(loop);
        })();
      }
    })();
  </script>

  <style>
    .sabana-content { height: 0; overflow: hidden; transition: height 0.4s cubic-bezier(0.4,0,0.2,1); }
  </style>

  <script>
    function toggleSabana(btn) {
      var content = btn.nextElementSibling;
      var vline   = btn.querySelector('.sabana-vline');
      var isOpen  = content.classList.contains('is-open');

      /* cerrar todos */
      document.querySelectorAll('.sabana-content.is-open').forEach(function(el) {
        el.style.height = el.scrollHeight + 'px';
        el.offsetHeight; /* forzar reflow */
        el.style.height = '0';
        el.classList.remove('is-open');
      });
      document.querySelectorAll('.sabana-vline').forEach(function(el) { el.style.display = ''; });

      /* abrir el clickeado si estaba cerrado */
      if (!isOpen) {
        content.style.height = content.scrollHeight + 'px';
        content.classList.add('is-open');
        vline.style.display = 'none';
        content.addEventListener('transitionend', function handler() {
          if (content.classList.contains('is-open')) content.style.height = 'auto';
          content.removeEventListener('transitionend', handler);
        });
      }
    }
  </script>


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

  <!-- ── Carrusel intro mobile: navegación por flechas ── -->
  <script>
    (function () {
      if (window.innerWidth >= 768) return;

      var track = document.getElementById('track-svc-intro');
      if (!track) return;

      var SVG_PREV = '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M0.5 10.7949L7.14648 4.14844L7.85352 4.85547L1.20728 11.502H24V12.502H1.20703L7.85352 19.1484L7.14648 19.8555L0.5 13.2092C0.177734 12.887 0 12.4583 0 12.002C0 11.5457 0.177734 11.1169 0.5 10.7949Z" fill="#141413"/></svg>';
      var SVG_NEXT = '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><g transform="translate(24,0) scale(-1,1)"><path d="M0.5 10.7949L7.14648 4.14844L7.85352 4.85547L1.20728 11.502H24V12.502H1.20703L7.85352 19.1484L7.14648 19.8555L0.5 13.2092C0.177734 12.887 0 12.4583 0 12.002C0 11.5457 0.177734 11.1169 0.5 10.7949Z" fill="#141413"/></g></svg>';

      var idx = 0;
      var slides = track.querySelectorAll('.svc-intro-slide');

      track.style.overflow    = 'visible';
      track.style.touchAction = 'none';
      track.style.transition  = 'transform 0.32s cubic-bezier(0.4,0,0.2,1)';
      track.style.gap         = '4rem';

      function step() { return window.innerWidth + 24; }

      var btnPrev, btnNext;

      function goTo(i) {
        i = Math.max(0, Math.min(i, slides.length - 1));
        idx = i;
        track.style.transform = 'translateX(-' + (idx * step()) + 'px)';
        btnPrev.style.opacity      = idx === 0 ? '0.25' : '1';
        btnNext.style.opacity      = idx === slides.length - 1 ? '0.25' : '1';
        btnPrev.style.pointerEvents = idx === 0 ? 'none' : '';
        btnNext.style.pointerEvents = idx === slides.length - 1 ? 'none' : '';
      }

      var nav = document.createElement('div');
      nav.style.cssText = 'display:flex;justify-content:flex-end;gap:1.5rem;align-items:center;padding:0.75rem 1.25rem 0;';

      btnPrev = document.createElement('button');
      btnPrev.innerHTML = SVG_PREV;
      btnPrev.setAttribute('aria-label', 'Anterior');
      btnPrev.style.cssText = 'background:none;border:none;padding:0;cursor:pointer;line-height:0;opacity:0.25;transition:opacity 0.2s;pointer-events:none;';
      btnPrev.addEventListener('click', function () { goTo(idx - 1); });

      btnNext = document.createElement('button');
      btnNext.innerHTML = SVG_NEXT;
      btnNext.setAttribute('aria-label', 'Siguiente');
      btnNext.style.cssText = 'background:none;border:none;padding:0;cursor:pointer;line-height:0;transition:opacity 0.2s;';
      btnNext.addEventListener('click', function () { goTo(idx + 1); });

      nav.appendChild(btnPrev);
      nav.appendChild(btnNext);
      var viewport = document.getElementById('svc-carousel-viewport');
      viewport.parentNode.insertBefore(nav, viewport.nextSibling);
    })();
  </script>
<?php wp_footer(); ?>
</body>
</html>
