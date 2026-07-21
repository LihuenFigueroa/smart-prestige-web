<?php /* Template Name: Legales */ ?>
<?php get_header(); ?>
<?php get_template_part('partials/header'); ?>
  <style>
    main h1, main h2 { font-family: 'FOR_smart_Next', 'Helvetica Neue', Helvetica, Arial, sans-serif !important; }
    main p, main li  { font-family: 'FOR_smart_Sans', 'Helvetica Neue', Helvetica, Arial, sans-serif !important; }
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

    <section class="mb-10">
      <h2 class="font-smart-next font-normal text-[#141413] mb-4" style="font-size:22px; line-height:1.2;">Propiedad intelectual</h2>
      <p class="font-smart-sans text-[#141413]" style="font-size:16px; line-height:1.7;">
        En el marco del uso de esta página web se debe tener en cuenta la propiedad intelectual (en particular los derechos de autor, de marca, de nombre y de patentes) de Prestige Auto SAU, smart Argentina o de terceros. El acceso a la página web no confiere ningún derecho de licencia o de uso sobre la propiedad intelectual de smart o de terceros.
      </p>
    </section>

    <div class="border-t border-neutral-200 mb-10"></div>

    <section class="mb-10">
      <h2 class="font-smart-next font-normal text-[#141413] mb-4" style="font-size:22px; line-height:1.2;">Afirmaciones prospectivas</h2>
      <p class="font-smart-sans text-[#141413] mb-6" style="font-size:16px; line-height:1.7;">
        Esta página web contiene afirmaciones prospectivas, que se basan en nuestra estimación actual acerca de desarrollos futuros. Palabras como «anticipar», «asumir», «creer», «estimar», «prever», «pretender», «poder/podría», «planificar», «proyectar», «debería» y similares son características de estas afirmaciones. Estas afirmaciones están sujetas a diversos riesgos e incertidumbres. Algunos ejemplos de ello son:
      </p>
      <ul class="font-smart-sans text-[#141413] mb-6" style="font-size:16px; line-height:1.7; list-style:disc; padding-left:24px; display:flex; flex-direction:column; gap:8px;">
        <li>una evolución desfavorable de la situación económica mundial, especialmente a causa del retroceso de la demanda en nuestros principales mercados destinatarios,</li>
        <li>un empeoramiento de nuestras posibilidades de refinanciación en los mercados crediticios y financieros,</li>
        <li>eventos inevitables de fuerza mayor, como catástrofes naturales, pandemias, actos de terrorismo, disturbios políticos, conflictos armados, accidentes industriales y sus consecuencias para nuestras actividades de venta, compra, producción o financiación,</li>
        <li>modificaciones de los tipos de cambio, las disposiciones aduaneras y de comercio exterior,</li>
        <li>cambios en los hábitos de consumo en favor de vehículos más pequeños con menor margen de beneficios o una posible pérdida de aceptación de nuestros productos y servicios que influya negativamente en la aplicación de nuestros precios y en el aprovechamiento de nuestras capacidades de producción,</li>
        <li>aumentos de los precios de los combustibles, las materias primas y la energía,</li>
        <li>interrupciones de la producción debido a dificultades de aprovisionamiento de material o energía, huelgas del personal o insolvencia de proveedores,</li>
        <li>descenso en los precios de reventa de los vehículos usados,</li>
        <li>el éxito en la implementación de medidas de reducción de costes y aumento de la eficiencia,</li>
        <li>las perspectivas comerciales de las sociedades de las que tenemos participaciones significativas,</li>
        <li>el éxito en la implementación de cooperaciones estratégicas y Joint Ventures,</li>
        <li>enmiendas de leyes, disposiciones y directivas oficiales, especialmente las relativas a las emisiones de los vehículos, el consumo de combustible y la seguridad,</li>
        <li>así como la conclusión de investigaciones realizadas por autoridades o encargadas por ellas que deriven o puedan derivar en procesos legales vinculados,</li>
        <li>y otros riesgos y factores imponderables, algunos de los cuales figuran en la memoria actual de la empresa, en la rúbrica «Informe sobre riesgos y oportunidades».</li>
      </ul>
      <p class="font-smart-sans text-[#141413]" style="font-size:16px; line-height:1.7;">
        En caso de que se dé uno de estos factores de inseguridad u otras incertidumbres, o en caso de que las suposiciones en que se basan tales afirmaciones prospectivas demuestren ser incorrectas, los resultados reales podrían diferir notablemente de los resultados expresados, implícita o explícitamente, en dichas afirmaciones. No pretendemos ni nos comprometemos a actualizar estas afirmaciones sobre previsiones de futuro periódicamente, puesto que estas se basan exclusivamente en las circunstancias que imperan el día en que se publican.
      </p>
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
