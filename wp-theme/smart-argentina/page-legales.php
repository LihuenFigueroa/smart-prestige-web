<?php /* Template Name: Legales */ ?>
<?php
$smart_legales = array_merge(
  smart_get_contenido('legal_propiedad_intelectual'),
  smart_get_contenido('legal_afirmaciones_prospectivas')
);
?>
<?php get_header(); ?>
<?php get_template_part('partials/header'); ?>
  <style>
    main h1, main h2 { font-family: 'FOR_smart_Sans', 'Helvetica Neue', Helvetica, Arial, sans-serif !important; }
    main p, main li  { font-family: 'FOR_smart_Next', 'Helvetica Neue', Helvetica, Arial, sans-serif !important; }
    .legal-content p, .legal-content ul { color:#141413; font-size:16px; line-height:1.7; margin:0 0 24px; }
    .legal-content ul { list-style:disc; padding-left:24px; }
    .legal-content li { margin-bottom:8px; }
    .legal-content p:last-child, .legal-content ul:last-child { margin-bottom:0; }
  </style>

  <!-- ================================================================
       NAVBAR
  ================================================================ -->
  <nav class="w-full bg-[#141413] px-5 md:px-14 h-14 md:h-16 flex items-center justify-between">
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

    <h1 class="font-smart-next font-normal text-[#141413] mb-12" style="font-size:40px; line-height:1.1;">Legales.</h1>

    <?php foreach ($smart_legales as $i => $bloque): ?>
    <?php if ($i > 0): ?>
    <div class="border-t border-neutral-200 mb-10"></div>
    <?php endif; ?>
    <section class="mb-10">
      <h2 class="font-smart-next font-normal text-[#141413] mb-4" style="font-size:22px; line-height:1.2;"><?php echo esc_html($bloque['titulo']); ?></h2>
      <div class="legal-content"><?php echo $bloque['contenido']; ?></div>
    </section>
    <?php endforeach; ?>

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
