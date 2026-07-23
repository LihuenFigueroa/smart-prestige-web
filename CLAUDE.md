# smart Prestige Web — Contexto del proyecto

## Stack
- HTML estático + Tailwind CSS (`assets/css/tailwind.css`) + `assets/css/styles.css`
- JS vanilla en `assets/js/main.js` (scroll-video, carruseles horizontales, etc.)
- Sin bundler, sin framework. Todo se edita directamente en los `.html`.

## Fuentes
- `font-smart-next` — titulares
- `font-smart-sans` — cuerpo / UI

## Ramas
- `main` — producción
- `develop` — integración
- `vistaSmart1` — vista smart #1 (mergeada a develop)
- `vistaSmart3` — vista smart #3 (mergeada a develop)
- Rama activa actual: `vistaSmart3` / `develop`

## Páginas
| Archivo | Descripción |
|---------|-------------|
| `index.html` | Home con hero scroll-video, carrusel "Elegí tu smart", sección "Tu auto piensa como vos" |
| `smart1.html` | Vista smart #1 completa |
| `smart3.html` | Vista smart #3 completa |
| `brabus.html` | Vista smart x BRABUS |
| `servicios.html`, `movilidad-electrica.html`, `conectividad.html`, `sobre-smart.html`, `buscador.html` | Páginas secundarias |

## Commits de git — estilo
No incluir la línea `Co-Authored-By: Claude` en los mensajes de commit.

---

## smart1.html — arquitectura

### Secciones en orden
1. Nav overlay (drawer hamburguesa)
2. Hero con imagen estática
3. Carrusel 1–6 (drag-to-scroll horizontal, cards `.c-card`)
4. Sección "Elegí tu versión" (configurador)
5. Comparativa 3 columnas (Pure / Pro / BRABUS en smart1; Pro / Pro+ / BRABUS en smart3)
6. Formulario de contacto
7. Footer

### Cards carrusel
```css
.c-card        /* wrapper */
.c-card__img   /* imagen */
.c-card__gradient
.c-card__body
.c-card__text  /* etiqueta pequeña */
.c-card__title
.c-card__desc
```

### Configurador "Elegí tu versión"
- Toggle Exterior / Interior con `switchVis(view)`
- Botones `.vis-linea-btn` con `data-linea`
- Swatches `.vis-color-btn` con `data-img` (ruta a imagen exterior)
- `carFade(newSrc)` — crossfade entre colores con canvas back/front
- `ZOOM_SRCS` — array de imágenes que reciben `scale(1.14)` al mostrarse
- Interior carousel: `loadInteriorFor(linea)`, `lineaMap` con arrays de imágenes
- `getVisLinea()` — expone la línea activa globalmente

### Comparativa
```css
.comp-features / .comp-features--bold
.comp-model / .comp-version / .comp-divider
.comp-range-label / .comp-range-num / .comp-range-sub
```

---

## smart3.html — diferencias respecto a smart1

- 3 versiones (Pro / Pro+ / BRABUS) — sin Pure
- Comparativa de 3 columnas
- BRABUS: 7 colores exteriores (`vis-c1.png`–`vis-c7.png`)
  - c3 (naranja) es el **color por defecto** al cargar
  - `ZOOM_SRCS` = c1, c4, c5, c6, c7 + todos los Pro/Pro+ → `scale(1.14)`
- Pro / Pro+: 5 colores cada uno (mismas imágenes, `vis-pro-c*.png` / `vis-prop-c*.png`)
- Interiores BRABUS: `int-brabus-1..4.png`
- Interiores Pro/Pro+ (STD): `int-std-1..4.png`

---

## brabus.html — arquitectura

### Hero
- Scroll-pinned video (igual que index.html)
- IDs: `brabusPin`, `brabusCanvas`, `brabusVideo`, `brabusText`, `brabusTextMid`
- Inicializado en `main.js` con `initScrollVideo({ videoId: 'brabusVideo', ... })`
- Video src: `assets/video/videoSmartXBRABUS.mp4` (desktop) / `videoSmartXBRABUSMobile.mp4` (mobile)
- Texto final (`#brabusText`) aparece con fade al terminar la animación de scroll
- Texto mid (`#brabusTextMid`) solo mobile

### Resto de secciones
- Texto "smart x BRABUS." + párrafo descriptivo
- 2 columnas: texto + imagen (`assets/img/brabus/content.jpg`)
- Specs: 2 columnas oscuras (smart #1 BRABUS | smart #3 BRABUS)
- Footer idéntico al resto del sitio

---

## main.js — funciones clave

### `initScrollVideo({ ... })`
Convierte cualquier sección en scroll-driven video. Parámetros:
- `videoId`, `canvasId`, `pinId` — IDs de los elementos
- `videoSrc` — ruta al video (puede ser condicional mobile/desktop)
- `pxPerSecond` — cuántos px de scroll equivalen a 1 segundo de video
- `captureFps` — FPS de captura de frames
- `lerp` — suavizado del scroll
- `textEl` / `textElMid` — elementos de texto con fade
- `textZonePx` / `holdZonePx` — zonas de texto y retención

Instancias activas:
1. Hero home (`heroPin` / `heroCanvas` / `heroVideo`)
2. smart x BRABUS banner (`brabusPin` / `brabusCanvas` / `brabusVideo`)

---

## Assets — estructura de imágenes

```
assets/img/
  smart1/          vis-c1..c5.png, int-brabus-1..4.png, int-std-1..4.png, ...
  smart3/          vis-c1..c7.png, vis-pro-c1..5.png, vis-prop-c1..5.png,
                   int-brabus-1..4.png, int-std-1..4.png,
                   comp-pro.png, comp-prop.png, comp-brabus.png, ...
  brabus/          hero1.jpg, hero2.jpg, hero3.jpg, content.jpg, spec1.jpg, spec3.jpg
  Logonavbar.svg, favicon.svg

assets/video/
  videoSmartXBRABUS.mp4
  videoSmartXBRABUSMobile.mp4
```

---

## wp-theme/smart-argentina — port a WordPress

Theme WordPress generado a partir de los `.html` estáticos. Templates `page-*.php` + `front-page.php` + `partials/header.php` / `partials/footer.php` / `partials/form-contacto.php`.

### Reglas del port
- Todo `src="assets/...`, `href="assets/...`, `data-img="assets/...` (incluyendo dentro de `<script>` embebido, como los arrays `ZOOM_SRCS`/`STD`/`lineaMap` del configurador) debe ir prefijado con `<?php echo get_template_directory_uri(); ?>` — si no, 404 en producción.
- Los `href="*.html"` internos se reemplazan por `home_url('/slug/')`. Slugs: `/`, `/smart-1/`, `/smart-3/`, `/brabus/`, `/servicios/`, `/movilidad-electrica/`, `/conectividad/`, `/sobre-smart/`, `/buscador/`, `/legales/`, `/cookies/`.
- `main.js` se carga UNA sola vez, vía `wp_enqueue_script` en `functions.php` — nunca hardcodeado con `<script src="...">` en un template (rompe con doble ejecución).
- `functions.php` versiona los assets (`tailwind.css`, `styles.css`, `main.js`) con `filemtime()`, no con un string fijo — así el navegador invalida caché automáticamente en cada deploy, sin tocar nada a mano.
- `page-legales.php` / `page-cookies.php` son templates (`Template Name: Legales` / `Cookies`), sin hero, con la navbar simple oscura del `legales.html`/`cookies.html` original. Ya tienen sus páginas WP creadas con esos slugs y template asignado.

### Servidor (IIS + PHP en Windows)
- WordPress vive en `C:\inetpub\wwwroot\`. El theme desplegado en `C:\inetpub\wwwroot\wp-content\themes\smart-argentina\` **es una copia separada del repo, no un symlink** — todo cambio en `wp-theme/smart-argentina/` hay que copiarlo a mano al deploy vivo después de commitear.
- `C:\inetpub\wwwroot\web.config` tiene la regla de IIS URL Rewrite que manda todo a `index.php` (permalinks bonitos de WP). Requiere el módulo **URL Rewrite de IIS** instalado (`rewrite.dll` en `System32\inetsrv`) — si falta, todas las páginas menos la home dan 404.
- Permalink structure de WP: `/%postname%/` (limpio, sin `index.php`). Si se cambia esa opción, hay que forzar `$wp_rewrite->init()` antes de `flush_rewrite_rules()` — si no, quedan reglas viejas con prefijo `index.php/` que nunca matchean y todo 404 igual.
- `wp-theme/smart-argentina/web.config` (raíz del theme) declara MIME types de video/fuentes para IIS. Usa siempre `<remove fileExtension="..." />` antes de cada `<mimeMap>` — la mayoría ya están registrados a nivel global de IIS, y un `<mimeMap>` duplicado sin `<remove>` tira 500.19 (config error), no 404.

### `/buscador/` — mapa de concesionarios
- Leaflet 1.9.4 vía CDN (`unpkg.com`). Requiere `leaflet.css` + un `<style>` con reglas mobile y `.smart-marker`/`.smart-popup` — si falta ese bloque el mapa se ve completamente roto (contenedor colapsado, sin estilos de tiles/popups).

### Canvas del scroll-video (`initScrollVideo` en `main.js`)
- El canvas se dimensiona con `devicePixelRatio` (no solo `offsetWidth`/`offsetHeight`) y usa `ctx.setTransform(dpr,0,0,dpr,0,0)` para dibujar en coordenadas CSS — si no, el video se ve pixelado/borroso en pantallas de alta densidad (Retina, Windows con escalado >100%).
- Los assets de imagen del sitio (fotos) no pasan por ningún pipeline de resize — se sirven tal cual están en `assets/img/`. Si algo se ve pixelado, primero comparar tamaño/MD5 del archivo en repo vs. servidor antes de asumir que la imagen fuente es de baja calidad (y descartar compresión de RDP/VNC si se está revisando remoto).

### Formulario de contacto — envío de mail
- El sitio estático (`server.js` + Nodemailer) es solo una prueba local — **no se portó a WordPress**. En WP el envío es 100% nativo:
  - `functions.php` registra el handler AJAX `smart_enviar_formulario` (`wp_ajax_*` + `wp_ajax_nopriv_*`) que valida campos obligatorios y llama a `wp_mail()`.
  - `partials/header.php` expone `window.WP_AJAX_URL`, `window.WP_CONTACT_NONCE` y `window.WP_GRACIAS_URL` como globals PHP→JS.
  - `main.js` → `submitContactForm()` detecta `window.WP_AJAX_URL`: si existe, postea a `admin-ajax.php` con `action`/`nonce` (form-urlencoded, respuesta `{success}`); si no, usa el flujo viejo del servidor Node local (JSON, respuesta `{ok}`) — así el mismo `main.js` sirve para ambos sitios sin ramas de build.
  - Destinatario: `hola@prestige-auto.com.ar`. CCO fijo: `lifi.soluciones@gmail.com` y `clindstrom@prestige-auto.com.ar`.
  - El envío real usa el plugin **WP Mail SMTP** contra Gmail (cuenta `smart.arg.mailing@gmail.com` con App Password) — configurado con constantes `WPMS_*` en `C:\inetpub\wwwroot\wp-config.php` (fuera del repo, no versionado). Requiere la extensión `openssl` habilitada en `C:\PHP\php.ini` (estaba comentada por defecto) + reinicio de IIS para que FastCGI la tome.
  - `/gracias/` es una página WP nueva (`page-gracias.php`, sin hero) a la que redirige el JS tras un envío exitoso.

---

## Plan de implementación ACF (contenido editable desde wp-admin)

Relevamiento completo + detalle técnico en **`ACF-PLAN.md`** (raíz del repo). **Proyecto cerrado** — todas las fases hechas y en producción:

| Fase | Alcance | Estado |
|------|---------|--------|
| **1** | CPT `concesionario` + ACF (buscador de concesionarios) | ✅ Hecho y en producción (2026-07-22) |
| **2** | CPT `version_vehiculo` + `brabus_spec_modelo` (comparativa smart1/smart3 + specs BRABUS) | ✅ Hecho y en producción (2026-07-22) |
| **3a** | CPT `feature_card` + `servicio_acordeon` (carruseles de tarjetas + acordeón de servicios) | ✅ Hecho y en producción (2026-07-22) |
| ~~3b~~ | ~~Configurador de color/interior de smart1/smart3~~ | ❌ Sacado del proyecto — el cliente decidió que esta sección no debe ser editable desde wp-admin, queda hardcodeada permanentemente |
| **4** | CPT `cookie_tipo` + `contenido_wysiwyg` (cookies, legales, historia institucional) + copyright con `date('Y')` | ✅ Hecho y en producción (2026-07-23) |
| **5** | CPT `hero_pagina` + campos `imagen` reales (Biblioteca de Medios) en `version_vehiculo`/`feature_card` | ✅ Hecho y en producción (2026-07-23) |

9 CPTs en total (`concesionario`, `version_vehiculo`, `brabus_spec_modelo`, `feature_card`, `servicio_acordeon`, `cookie_tipo`, `contenido_wysiwyg`, `hero_pagina`), 61 posts migrados, mismo patrón en todos: ACF Free (sin repeater/options page), un helper `smart_get_*()` por CPT como única fuente de verdad, migración one-time idempotente hooked en `init`. El menú de navegación (header/footer) se dejó como está a propósito — no se migró a `wp_nav_menu()` para no cambiarle el flujo de trabajo al cliente.

### Fase 5 — detalle de lo implementado
- Los campos `imagen` de `version_vehiculo`/`feature_card` pasaron de texto (ruta relativa) a tipo **Imagen** real de ACF (Biblioteca de Medios) — 37 adjuntos importados desde `assets/img/` vía `smart_import_attachment_from_theme_path()`. Los helpers casi no cambiaron (`get_field('imagen', $id)` ya devuelve la URL completa), así que los templates que los consumen no se tocaron.
- CPT `hero_pagina` (9 posts) con campos `hero_desktop`/`hero_mobile` — cubre las 7 páginas con hero de imagen estática (smart1, smart3, conectividad, servicios, movilidad eléctrica, sobre-smart, buscador) **más** home y `/brabus/`, cuyos heroes eran video scroll-driven (`initScrollVideo`) y pasaron a imagen fija por pedido del cliente (se sacaron esas 2 llamadas de `assets/js/main.js`). Home y brabus quedaron con el campo de imagen **vacío a propósito** — no se pudo extraer un frame del video en este entorno (el Chrome de automatización no decodifica mp4), el cliente va a subir la foto real desde wp-admin.
- En `/brabus/`, los 2 textos que antes aparecían en secuencia durante el scroll ("Inconfundiblemente."/"BRABUS." a mitad de video, texto final al terminar) ahora quedan visibles **permanentemente, uno a cada lado** — pedido explícito del cliente.
- **Prerrequisitos de servidor** (fuera del repo): GD de PHP estaba deshabilitada (`C:\PHP\php.ini`, `extension=gd` descomentada + `iisreset`) y `WP_MAX_MEMORY_LIMIT` se subió a 512M en `wp-config.php` — sin esto, WordPress no puede generar miniaturas de fotos grandes y la migración de imágenes tira memory exhausted a mitad de camino.
- Bug de estética preexistente encontrado de paso: `md:hidden` de Tailwind nunca se compiló en `tailwind.css` (si existe `md:block`) — quedaba enmascarado porque el scroll-video ya manejaba la visibilidad por JS. Si se usa `md:hidden` en algún otro lugar del sitio, probablemente tampoco esté funcionando ahí.

### Fase 1 — detalle de lo implementado
- CPT `concesionario` (no público) registrado en `functions.php`, campos ACF vía `acf_add_local_field_group()` (versionado en código, no en base de datos): `nombre`, `direccion`, `localidad`, `provincia`, `telefono`, `latitud`, `longitud`, `tipo_servicio` (select), `horario` (textarea, nuevo — no existía en el sitio original, hoy vacío).
- Migración one-time de los 15 concesionarios hardcodeados, hooked en `init`, guardada con la opción `smart_concesionarios_migrados` — corre sola apenas ACF esté activo, no requiere WP-CLI ni acción manual además de activar el plugin.
- Helper `smart_get_concesionarios()` en `functions.php` — única fuente de verdad, usada por `page-buscador.php` (listado + `wp_localize_script('smart-buscador-data', 'smartConcesionarios', ...)` para el mapa Leaflet) y por `partials/form-contacto.php` (dropdown de concesionario). Antes estaban triplicados a mano en 3 formatos distintos.
- Los slugs de los 15 posts (`colcar-moreno`, `lonco-hue-libertador`, etc.) se mantuvieron iguales a los que ya usaba el dropdown del form, para no romper nada existente.
