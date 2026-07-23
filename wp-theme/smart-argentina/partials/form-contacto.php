<style>
  .fdd-item { display:block; width:100%; text-align:left; background:none; border:none; cursor:pointer; padding:10px 20px; font-size:0.8125rem; color:#111827; line-height:1.4; font-family:inherit; }
  .fdd-item:hover, .fdd-item:focus { background:#f9fafb; outline:none; }
  [data-fdd-items] { max-height:200px; overflow-y:auto; }
  [data-fdd-items]::-webkit-scrollbar { width:3px; }
  [data-fdd-items]::-webkit-scrollbar-track { background:transparent; }
  [data-fdd-items]::-webkit-scrollbar-thumb { background:#d1d5db; border-radius:2px; }
  .req-star { color:#9ca3af; font-size:0.6rem; line-height:1; margin-left:3px; flex-shrink:0; }
  #form-contacto input::placeholder { color:#9ca3af; font-size:0.75rem; letter-spacing:0.1em; }
</style>

<section id="contacto" class="w-full bg-white py-12 md:py-20 px-5 md:px-14">
  <div class="max-w-[1320px] mx-auto flex flex-col md:flex-row gap-10 md:gap-16 md:items-start">
    <div class="md:w-[440px] md:flex-shrink-0 md:pt-1 flex flex-col gap-6">
      <h2 class="contacto-title font-smart-next font-normal text-black">Realizar consulta.</h2>
      <p class="contacto-subtitle text-base font-normal font-smart-sans">Completá el formulario y te contactaremos a la brevedad.</p>
    </div>
    <div class="flex-1">
      <form id="form-contacto" novalidate onsubmit="submitContactForm(event)">

        <div class="border-b border-neutral-200 py-3">
          <input type="text" placeholder="NOMBRE *" data-req class="font-smart-sans w-full text-sm text-black bg-transparent outline-none" />
        </div>

        <div class="border-b border-neutral-200 py-3">
          <input type="text" placeholder="APELLIDO *" data-req class="font-smart-sans w-full text-sm text-black bg-transparent outline-none" />
        </div>

        <div class="border-b border-neutral-200 py-3">
          <input type="text" placeholder="CIUDAD *" data-req class="font-smart-sans w-full text-sm text-black bg-transparent outline-none" />
        </div>

        <div class="border-b border-neutral-200 py-3">
          <input type="email" placeholder="CORREO ELECTRÓNICO *" data-req class="font-smart-sans w-full text-sm text-black bg-transparent outline-none" />
        </div>

        <div class="border-b border-neutral-200 py-3">
          <input type="tel" placeholder="CELULAR *" data-req class="font-smart-sans w-full text-sm text-black bg-transparent outline-none" />
        </div>

        <!-- Dropdown: Concesionario -->
        <div class="border-b border-neutral-200 py-3 relative" id="fdd-concesionario">
          <input type="hidden" name="concesionario" id="fdd-concesionario-val" />
          <button type="button" class="font-smart-sans w-full text-left bg-transparent cursor-pointer outline-none flex items-center" style="border:none;padding:0;-webkit-appearance:none;appearance:none;height:1.3125rem;" onclick="toggleFdd('concesionario')">
            <span id="fdd-concesionario-label" style="color:#9ca3af;font-size:0.75rem;letter-spacing:0.1em;text-transform:uppercase;">CONCESIONARIO</span>
            <span class="req-star">*</span>
            <span class="flex-1"></span>
            <span id="fdd-concesionario-chevron" style="color:#9ca3af;font-size:0.75rem;display:inline-block;line-height:1;transition:transform 0.35s cubic-bezier(0.25,0,0,1);">&#8964;</span>
          </button>
          <div id="fdd-concesionario-panel" style="position:absolute;left:0;right:0;top:100%;background:#fff;border:1px solid #e5e7eb;border-top:none;max-height:0;overflow:hidden;transition:max-height 0.4s cubic-bezier(0.25,0,0,1);z-index:200;box-shadow:0 6px 20px rgba(0,0,0,0.07);">
            <div data-fdd-items style="padding:6px 0;">
              <?php foreach (smart_get_concesionarios() as $c): $fddLabel = $c['nombre'] . ' — ' . $c['direccion'] . ', ' . $c['localidad']; ?>
              <button type="button" class="fdd-item font-smart-sans" onclick="selectFdd('concesionario','<?php echo esc_js($c['slug']); ?>','<?php echo esc_js($fddLabel); ?>')"><?php echo esc_html($fddLabel); ?></button>
              <?php endforeach; ?>
            </div>
          </div>
        </div>

        <!-- Dropdown: Modelo -->
        <div class="border-b border-neutral-200 py-3 relative" id="fdd-modelo">
          <input type="hidden" name="modelo" id="fdd-modelo-val" />
          <button type="button" class="font-smart-sans w-full text-left bg-transparent cursor-pointer outline-none flex items-center" style="border:none;padding:0;-webkit-appearance:none;appearance:none;height:1.3125rem;" onclick="toggleFdd('modelo')">
            <span id="fdd-modelo-label" style="color:#9ca3af;font-size:0.75rem;letter-spacing:0.1em;text-transform:uppercase;">SELECCIONE MODELO</span>
            <span class="req-star">*</span>
            <span class="flex-1"></span>
            <span id="fdd-modelo-chevron" style="color:#9ca3af;font-size:0.75rem;display:inline-block;line-height:1;transition:transform 0.35s cubic-bezier(0.25,0,0,1);">&#8964;</span>
          </button>
          <div id="fdd-modelo-panel" style="position:absolute;left:0;right:0;top:100%;background:#fff;border:1px solid #e5e7eb;border-top:none;max-height:0;overflow:hidden;transition:max-height 0.4s cubic-bezier(0.25,0,0,1);z-index:200;box-shadow:0 6px 20px rgba(0,0,0,0.07);">
            <div data-fdd-items style="padding:6px 0;"></div>
          </div>
        </div>

        <div class="py-3">
          <p class="font-smart-sans" style="font-size:0.75rem;letter-spacing:0.1em;color:#9ca3af;text-transform:uppercase;margin-bottom:8px;">CONSULTA</p>
          <textarea rows="5" class="font-smart-sans w-full text-sm text-black bg-transparent outline-none resize-none border border-neutral-200 p-3"></textarea>
        </div>

        <div class="pt-6 flex items-center gap-4">
          <button type="submit" id="btn-enviar" class="self-start h-10 px-8 bg-black rounded-full text-sm font-bold text-white hover:bg-neutral-800 transition-colors">Enviar</button>
          <p id="form-error-msg" class="font-smart-sans text-xs hidden" style="color:rgba(239,68,68,0.6);">Completá todos los campos obligatorios (*).</p>
        </div>

      </form>
    </div>
  </div>
</section>
