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
