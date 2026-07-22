<?php /* Template Name: Gracias */ ?>
<?php get_header(); ?>
<?php get_template_part('partials/header'); ?>

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
  <main class="max-w-[800px] mx-auto px-5 md:px-8 pt-10 pb-12 md:pt-12 md:pb-16" style="margin-top: 24px;">

    <p class="font-smart-sans text-neutral-400 text-xs uppercase tracking-widest mb-6">Mensaje recibido</p>

    <h1 class="font-smart-next font-normal text-[#141413] mb-4" style="font-size: clamp(22px, 3vw, 32px); line-height: 1.1; letter-spacing: -0.02em;">
      Gracias por contactarte.
    </h1>

    <div class="border-t border-neutral-200 mb-4"></div>

    <div class="flex flex-row flex-wrap gap-2">
      <a href="<?php echo home_url('/'); ?>" class="font-smart-sans inline-flex items-center h-8 px-5 bg-[#141413] text-white rounded-full font-bold text-xs tracking-tight hover:bg-neutral-800 transition-colors whitespace-nowrap">
        Volver al inicio
      </a>
      <a href="<?php echo home_url('/smart-1/'); ?>" class="font-smart-sans inline-flex items-center h-8 px-5 border border-[#141413] text-[#141413] rounded-full font-normal text-xs tracking-tight hover:bg-neutral-100 transition-colors whitespace-nowrap">
        Ver modelos
      </a>
    </div>

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
