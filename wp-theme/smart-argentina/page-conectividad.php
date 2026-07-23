<?php /* Template Name: Conectividad */ ?>
<?php
$smart_cards_cc1 = smart_get_feature_cards('conectividad_1');
$smart_cards_cc2 = smart_get_feature_cards('conectividad_2');
$smart_hero_con   = smart_get_hero('conectividad');
?>
<?php get_header(); ?>
<?php get_template_part('partials/header'); ?>
  <!-- ================================================================
       HERO
  ================================================================ -->
  <section class="relative w-full h-screen min-h-[640px] overflow-hidden">
    <div class="absolute inset-0 bg-neutral-800">
      <img src="<?php echo esc_url($smart_hero_con['desktop']); ?>" alt="Conectividad smart" class="con-hero-img w-full h-full object-cover" />
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

    <div class="con-hero-text-wrap absolute bottom-0 left-0 right-0 z-10" style="padding-left:56px; padding-bottom:74px;">
      <p class="con-hero-label font-smart-sans text-white" style="font-size:18px; margin-bottom:6px; opacity:0.9;">Tecnología de integración</p>
      <h1 class="con-hero-title font-smart-next font-normal text-white" style="font-size:45px; line-height:1.1;">Tu auto piensa como vos.</h1>
    </div>
  </section>

  <!-- ================================================================
       INTRO TEXT
  ================================================================ -->
  <section class="con-intro-section" style="background:white; padding:64px 81px 80px;">
    <p class="con-intro-text font-smart-sans" style="font-size:26px; color:#141413; line-height:1.5; max-width:1176px;">
      El smart #1 y el #3 son vehículos diseñados desde cero en la era digital. El sistema de infoentretenimiento se integra con el ecosistema Hello smart: desde la pantalla central hasta tu teléfono, todo funciona en red. <strong class="font-smart-sans">Cada función está pensada para que la tecnología quede al servicio del manejo.</strong>
    </p>
  </section>

  <!-- ================================================================
       CARRUSELES DE CONECTIVIDAD
  ================================================================ -->
  <section class="w-full bg-white pb-10">

    <!-- ── Carrusel 1 ── -->
    <div class="pb-6" style="overflow:hidden;">
      <div id="track-cc1" class="flex select-none" style="overflow-x:scroll; scrollbar-width:none; -ms-overflow-style:none; padding-left:1.25rem; padding-right:1.25rem; gap:1rem; cursor:grab;">

        <?php foreach ($smart_cards_cc1 as $c): ?>
        <div class="c-card">
          <img src="<?php echo esc_url($c['imagen']); ?>" alt="<?php echo esc_attr($c['alt']); ?>" class="c-card__img" />
          <div class="c-card__gradient"></div>
          <div class="c-card__body">
            <div class="c-card__text">
              <p class="c-card__title"><?php echo esc_html($c['titulo']); ?></p>
              <p class="c-card__desc"><?php echo esc_html($c['descripcion']); ?></p>
            </div>
            <?php if (!empty($c['cta_texto'])): ?>
            <a href="<?php echo esc_url(smart_feature_card_link($c['cta_link'])); ?>" style="display:inline-flex; align-items:center; height:32px; padding:0 36px; background:#fff; border:none; border-radius:16px; font-size:11px; font-family:'FOR_smart_Sans','Helvetica Neue',Helvetica,Arial,sans-serif; color:#141413; text-decoration:none; margin-top:10px; white-space:nowrap;"><?php echo esc_html($c['cta_texto']); ?></a>
            <?php endif; ?>
          </div>
        </div>
        <?php endforeach; ?>

      </div>
    </div>

    <!-- ── Carrusel 2 ── -->
    <div class="pb-6" style="overflow:hidden;">
      <div id="track-cc2" class="flex select-none" style="overflow-x:scroll; scrollbar-width:none; -ms-overflow-style:none; padding-left:1.25rem; padding-right:1.25rem; gap:1rem; cursor:grab;">

        <?php foreach ($smart_cards_cc2 as $c): ?>
        <div class="c-card">
          <img src="<?php echo esc_url($c['imagen']); ?>" alt="<?php echo esc_attr($c['alt']); ?>" class="c-card__img" />
          <div class="c-card__gradient"></div>
          <div class="c-card__body">
            <div class="c-card__text">
              <p class="c-card__title"><?php echo esc_html($c['titulo']); ?></p>
              <p class="c-card__desc"><?php echo esc_html($c['descripcion']); ?></p>
            </div>
            <?php if (!empty($c['cta_texto'])): ?>
            <a href="<?php echo esc_url(smart_feature_card_link($c['cta_link'])); ?>" style="display:inline-flex; align-items:center; height:32px; padding:0 36px; background:#fff; border:none; border-radius:16px; font-size:11px; font-family:'FOR_smart_Sans','Helvetica Neue',Helvetica,Arial,sans-serif; color:#141413; text-decoration:none; margin-top:10px; white-space:nowrap;"><?php echo esc_html($c['cta_texto']); ?></a>
            <?php endif; ?>
          </div>
        </div>
        <?php endforeach; ?>

      </div>
    </div>

  </section>

  <!-- ================================================================
       FOOTER
  ================================================================ -->
  <?php get_template_part('partials/footer'); ?>


  <script>
    // ── Carruseles drag-to-scroll con inercia ──
    (function () {
      function initDragCarousel(trackId) {
        const track = document.getElementById(trackId);
        if (!track) return;
        track.style.willChange = 'scroll-position';
        track.style.webkitOverflowScrolling = 'touch';

        let isDragging  = false;
        let hasMoved    = false;
        let startX      = 0;
        let startScroll = 0;
        let lastX       = 0;
        let lastTime    = 0;
        let velocity    = 0;
        let rafId       = null;

        function momentum() {
          velocity *= 0.94;
          track.scrollLeft -= velocity;
          if (Math.abs(velocity) > 0.3) rafId = requestAnimationFrame(momentum);
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
          const now = performance.now();
          const dt  = now - lastTime || 1;
          velocity  = ((e.clientX - lastX) / dt) * 16;
          lastX     = e.clientX;
          lastTime  = now;
          track.scrollLeft = startScroll - delta;
        });

        track.addEventListener('click', function (e) {
          if (hasMoved) e.preventDefault();
        }, true);

        track.addEventListener('touchstart', function (e) {
          cancelAnimationFrame(rafId);
          isDragging  = true;
          hasMoved    = false;
          startX      = e.touches[0].clientX;
          startScroll = track.scrollLeft;
          lastX       = e.touches[0].clientX;
          lastTime    = performance.now();
          velocity    = 0;
        }, { passive: true });

        track.addEventListener('touchmove', function (e) {
          if (!isDragging) return;
          const delta = e.touches[0].clientX - startX;
          if (Math.abs(delta) > 4) hasMoved = true;
          const now = performance.now();
          const dt  = now - lastTime || 1;
          velocity  = ((e.touches[0].clientX - lastX) / dt) * 16;
          lastX     = e.touches[0].clientX;
          lastTime  = now;
          track.scrollLeft = startScroll - delta;
        }, { passive: true });

        track.addEventListener('touchend', function () {
          isDragging = false;
          rafId = requestAnimationFrame(momentum);
        }, { passive: true });
      }

      if (window.innerWidth >= 768) {
      ['track-cc1', 'track-cc2'].forEach(initDragCarousel);
    }
    })();

    // ── Cards conectividad — estilos mobile ──
    (function () {
      if (window.innerWidth >= 768) return;
      ['track-cc1', 'track-cc2'].forEach(function (id) {
        var t = document.getElementById(id);
        if (!t) return;
        t.style.paddingLeft = '16px';
        t.style.paddingRight = '16px';
      });
      document.querySelectorAll('#track-cc1 .c-card__title, #track-cc2 .c-card__title').forEach(function (el) {
        el.style.fontSize   = '18px';
        el.style.lineHeight = '1.25';
        el.style.marginBottom = '6px';
      });
      document.querySelectorAll('#track-cc1 .c-card__desc, #track-cc2 .c-card__desc').forEach(function (el) {
        el.style.fontSize   = '13px';
        el.style.lineHeight = '1.45';
      });
      document.querySelectorAll('#track-cc1 .c-card__text, #track-cc2 .c-card__text').forEach(function (el) {
        el.style.maxWidth = '88%';
        el.style.paddingLeft = '6px';
        el.style.paddingBottom = '8px';
      });
    })();

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
<?php wp_footer(); ?>
</body>
</html>
