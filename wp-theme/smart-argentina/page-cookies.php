<?php /* Template Name: Cookies */ ?>
<?php $smart_cookie_tipos = smart_get_cookie_tipos(); ?>
<?php get_header(); ?>
<?php get_template_part('partials/header'); ?>
  <style>
    main h1, main h2 { font-family: 'FOR_smart_Next', 'Helvetica Neue', Helvetica, Arial, sans-serif !important; }
    main p, main li  { font-family: 'FOR_smart_Sans', 'Helvetica Neue', Helvetica, Arial, sans-serif !important; }
  </style>

  <!-- ================================================================
       NAVBAR
  ================================================================ -->
  <nav class="w-full bg-[#141413] px-5 md:px-14 h-14 md:h-16 flex items-center justify-between relative">
    <div class="flex items-center gap-6">
      <button onclick="openNavMenu()" class="flex flex-col gap-[5px] cursor-pointer" aria-label="Menú">
        <span class="w-5 h-px bg-white block"></span>
        <span class="w-5 h-px bg-white block"></span>
        <span class="w-5 h-px bg-white block"></span>
      </button>
    </div>
    <div class="absolute left-1/2 -translate-x-1/2">
      <a href="<?php echo home_url('/'); ?>"><img src="<?php echo get_template_directory_uri(); ?>/assets/img/Logonavbar.svg" alt="smart" class="h-4 md:h-8 w-auto" /></a>
    </div>
  </nav>

  <!-- ================================================================
       CONTENIDO
  ================================================================ -->
  <main class="max-w-[800px] mx-auto px-5 md:px-8 pt-16 pb-16 md:pt-20 md:pb-24" style="margin-top: 48px;">

    <h1 class="font-smart-next font-normal text-[#141413] mb-12" style="font-size:40px; line-height:1.1;">Cookies.</h1>

    <section class="mb-10">
      <h2 class="font-smart-next font-normal text-[#141413] mb-4" style="font-size:22px; line-height:1.2;">Utilizamos cookies con diversos fines</h2>
      <p class="font-smart-sans text-[#141413]" style="font-size:16px; line-height:1.7;">
        Con ello, Prestige Auto SAU y smart, y los sub procesadores determinados, nos proponemos facilitarte en la medida de lo posible el uso de nuestra página web y mejorar constantemente su uso. Además, las cookies nos permiten mostrarte contenidos y publicidad adecuados a tus preferencias de uso. A tal efecto, colaboramos con socios seleccionados (entre otros, Google, Meta). A través de estos socios también recibirás publicidad en otras páginas web. Tu consentimiento es voluntario y puedes revocarlo en cualquier momento. Encontrarás más información (también sobre transferencias de datos) y posibilidades de ajuste en «Preferencias» y en nuestra Información sobre protección de datos.
      </p>
    </section>

    <div class="border-t border-neutral-200 mb-10"></div>

    <section class="mb-10">
      <h2 class="font-smart-next font-normal text-[#141413] mb-6" style="font-size:22px; line-height:1.2;">Tipos de cookies</h2>

      <div class="flex flex-col gap-0 border border-neutral-200">

        <?php foreach ($smart_cookie_tipos as $i => $t): $isLast = $i === count($smart_cookie_tipos) - 1; ?>
        <div class="flex items-start gap-6 p-6<?php echo $isLast ? '' : ' border-b border-neutral-200'; ?>">
          <div class="flex-shrink-0 mt-1">
            <?php if ($t['activo_por_defecto']): ?>
            <div class="w-5 h-5 rounded-full border-2 border-[#141413] flex items-center justify-center">
              <div class="w-2.5 h-2.5 rounded-full bg-[#141413]"></div>
            </div>
            <?php else: ?>
            <div class="w-5 h-5 rounded-full border-2 border-neutral-300"></div>
            <?php endif; ?>
          </div>
          <div>
            <p class="font-smart-next text-[#141413] mb-2" style="font-size:16px;"><?php echo esc_html($t['titulo']); ?></p>
            <p class="font-smart-sans text-neutral-500" style="font-size:14px; line-height:1.6;"><?php echo esc_html($t['descripcion']); ?></p>
          </div>
        </div>
        <?php endforeach; ?>

      </div>
    </section>

  </main>

  <!-- ================================================================
       FOOTER
  ================================================================ -->
  <?php get_template_part('partials/footer'); ?>

  <script>
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
