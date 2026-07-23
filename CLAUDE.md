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
