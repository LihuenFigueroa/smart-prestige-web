<div id="nav-menu" class="fixed inset-0 z-[100] pointer-events-none">
    <div id="nav-backdrop" class="absolute inset-0 bg-black/50 opacity-0 transition-opacity duration-300" onclick="closeNavMenu()"></div>
    <div id="nav-drawer" class="absolute top-0 left-0 h-full w-full max-w-sm bg-white flex flex-col -translate-x-full transition-transform duration-300 ease-in-out">
      <div class="px-8 h-16 flex items-center justify-between border-b border-neutral-200">
        <a href="<?php echo home_url('/'); ?>"><img src="<?php echo get_template_directory_uri(); ?>/assets/img/Logonavbar.svg" alt="smart" class="h-6 w-auto" /></a>
        <button onclick="closeNavMenu()" aria-label="Cerrar menú" class="text-[#141413] p-1">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
        </button>
      </div>
      <nav class="flex-1 overflow-y-auto px-8 py-8 flex flex-col">
        <p class="text-xs uppercase tracking-widest text-neutral-500 mb-4 font-smart-next">Modelos</p>
        <a href="<?php echo home_url('/smart-1/'); ?>" class="text-[#141413] text-xl font-smart-next font-normal py-3 border-b border-neutral-200 hover:text-neutral-400 transition-colors">smart #1</a>
        <a href="<?php echo home_url('/smart-3/'); ?>" class="text-[#141413] text-xl font-smart-next font-normal py-3 border-b border-neutral-200 hover:text-neutral-400 transition-colors">smart #3</a>
        <a href="<?php echo home_url('/brabus/'); ?>" class="text-[#141413] text-xl font-smart-next font-normal py-3 border-b border-neutral-200 hover:text-neutral-400 transition-colors">smart × BRABUS</a>
        <p class="text-xs uppercase tracking-widest text-neutral-500 mt-8 mb-4 font-smart-next">Descubrí</p>
        <a href="<?php echo home_url('/servicios/'); ?>" class="text-[#141413] text-xl font-smart-next font-normal py-3 border-b border-neutral-200 hover:text-neutral-400 transition-colors">Servicios al cliente</a>
        <a href="<?php echo home_url('/movilidad-electrica/'); ?>" class="text-[#141413] text-xl font-smart-next font-normal py-3 border-b border-neutral-200 hover:text-neutral-400 transition-colors">Movilidad eléctrica</a>
        <a href="<?php echo home_url('/conectividad/'); ?>" class="text-[#141413] text-xl font-smart-next font-normal py-3 border-b border-neutral-200 hover:text-neutral-400 transition-colors">Conectividad</a>
        <p class="text-xs uppercase tracking-widest text-neutral-500 mt-8 mb-4 font-smart-next">Sobre smart</p>
        <a href="<?php echo home_url('/buscador/'); ?>" class="text-[#141413] text-xl font-smart-next font-normal py-3 border-b border-neutral-200 hover:text-neutral-400 transition-colors">Buscador de concesionarios</a>
        <a href="<?php echo home_url('/sobre-smart/'); ?>" class="text-[#141413] text-xl font-smart-next font-normal py-3 border-b border-neutral-200 hover:text-neutral-400 transition-colors">Sobre nosotros</a>
      </nav>
      <div class="px-8 py-6 border-t border-neutral-200">
        <a href="index.html#contacto" class="text-sm text-neutral-500 hover:text-[#141413] transition-colors">Contáctanos</a>
      </div>
      <div class="px-8 pt-5 pb-8 border-t border-neutral-200 flex flex-col gap-3">
        <p class="text-xs text-neutral-400 font-smart-sans">
          <a href="legales.html" class="hover:text-[#141413] transition-colors">Legales</a>
          <span class="mx-2 text-neutral-300">|</span>
          <a href="cookies.html" class="hover:text-[#141413] transition-colors">Cookies</a>
        </p>
        <p class="text-xs text-neutral-400 font-smart-sans">© 2026 smart Argentina</p>
      </div>
    </div>
  </div>
