# Plan de implementación ACF — smart-argentina (WordPress)

Relevamiento del theme `wp-theme/smart-argentina/` y plan de trabajo para que el cliente pueda editar contenido desde wp-admin sin tocar código. Última actualización: 2026-07-22.

---

## 1. Diagnóstico inicial

### ¿Qué está hardcodeado?
**Todo.** No había ningún mecanismo de edición de contenido antes de este plan: cero custom fields, cero opciones de theme, cero `wp_nav_menu()`. Cada texto, imagen y URL vivía escrito a mano en los 14 templates PHP del theme (~5.877 líneas).

### ¿ACF estaba instalado?
No. Verificado en su momento: sin plugin ACF activo, sin carpeta `acf-json/`, sin `acf_add_local_field_group`, `get_field`, `the_field` ni `acf_add_options_page` en ningún archivo del theme. `functions.php` solo registraba assets, el menú de nav y el handler AJAX del formulario de contacto.

### Precios
No existen en ningún lado del sitio (ni estático ni WP). El único texto con "precio" son párrafos legales genéricos de disclaimer (`page-legales.php`), no un campo de precio real. Si en algún momento se pide gestionar precios, hoy no hay ni el campo.

---

## 2. Inventario de contenido hardcodeado por archivo

### Páginas del configurador (`front-page.php`, `page-smart1.php`, `page-smart3.php`, `page-brabus.php`)

| Archivo | Hallazgos clave |
|---|---|
| `front-page.php` | Hero (título/subtítulo/CTA), 4 "feature cards" del carrusel (imagen+título+desc+CTA) — repeater ideal. Selector de modelo #1/#3 acoplado al JS (`switchModel`), no es repeater trivial. |
| `page-smart1.php` (1379 líneas) | 6 carruseles de características (28 tarjetas), tabla comparativa 4 versiones (Pure/Pro/Pro+/BRABUS) × 7 filas de specs, configurador de color (17 combinaciones color/línea en HTML plano), **interior carousel con imágenes dentro de un array JS embebido (`lineaMap`)** — esto es lo más costoso de migrar porque no es HTML. Nombres de versión repetidos 4-5 veces por archivo. |
| `page-smart3.php` (1317 líneas) | Mismo patrón que smart1 pero 3 versiones (sin "Pure"). `ZOOM_SRCS` duplica literalmente las mismas 17 rutas que ya están en los `data-img` de los botones — dato duplicado dentro del mismo archivo. |
| `page-brabus.php` (251 líneas) | Bloque de specs (3,9 seg / 66kWh / AWD / 400km / 428CV) **hardcodeado sin variar según el toggle #1/#3** — bug de contenido: los specs no cambian aunque el usuario cambie de modelo. Buena parte del "texto" está dentro de imágenes SVG/PNG (incluye un SVG con imagen base64 de ~161KB embebida directo en el PHP, el caso más extremo de hardcodeo del theme). |

### Páginas de contenido (`page-servicios.php`, `page-movilidad-electrica.php`, `page-conectividad.php`, `page-sobre-smart.php`, `page-buscador.php`, `page-legales.php`, `page-cookies.php`, `page-gracias.php`, `partials/*`)

**Hallazgo principal — concesionarios triplicados a mano** (antes de la Fase 1): los datos de los 15 concesionarios vivían en 3 formatos distintos y 2 archivos, sin fuente única de verdad:
1. HTML del listado (`page-buscador.php`, 15 `<div class="concesionario-item">`)
2. Array JS `sucursales` (`page-buscador.php`, para Leaflet)
3. Opciones del dropdown de concesionario (`partials/form-contacto.php`, con slugs propios que no coincidían con nada de los otros dos)

No existía campo de **horario de atención** en ningún lado. La localidad/provincia estaba embebida como texto libre dentro de "dirección", sin normalizar.

Otros hallazgos:
- `page-servicios.php`: acordeón de 9 ítems (garantía, mantenimiento, etc.) título+texto largo — repeater directo.
- `page-conectividad.php`: 2 carruseles de 4 cards cada uno (8 total), mismo patrón imagen+título+desc. Link roto (`href="#"`) en botón "Descargar app".
- `page-movilidad-electrica.php`: 3 cards de beneficios + 5 bloques tipo FAQ.
- `page-sobre-smart.php`: historia institucional (4 párrafos largos, candidato a WYSIWYG no repeater) + carrusel de 3 slides (bug de copy: 2 descripciones idénticas).
- `page-cookies.php`: 4 tipos de cookies, con el estado "activo por defecto" hoy hardcodeado en CSS en vez de ser un dato.
- `page-legales.php`: texto legal + lista de 13 bullets.
- `header.php` / `footer.php`: copyright "© 2026 smart Argentina" hardcodeado **por duplicado** en 2 archivos — hay que actualizarlo a mano cada año en ambos lugares. Menú de nav candidato a `wp_nav_menu()` nativo de WP, no necesariamente ACF.
- Formulario de servicios (`page-servicios.php`) no llama a `submitContactForm()` — probablemente no envía nada, a confirmar con el cliente.

---

## 3. Ranking de repeaters candidatos a ACF (por prioridad)

| # | Ubicación | Ítems | Prioridad |
|---|---|---|---|
| 1 | Concesionarios (`page-buscador.php` + `form-contacto.php`) | 15 | **Alta** — triplicado a mano, sin campo de horario |
| 2 | Acordeón "sábana" servicios | 9 | Alta |
| 3 | Cards conectividad (2 carruseles) | 8 (4+4) | Media-alta |
| 4 | Tipos de cookies | 4 | Media |
| 5 | Cards movilidad eléctrica | 3 | Media |
| 6 | Bloques FAQ movilidad eléctrica | 5 | Media |
| 7 | Carrusel "red de concesionarios" (sobre-smart) | 3 | Media |
| 8 | Menú mobile / footer | ~15 links | Baja — mejor `wp_nav_menu()` que ACF |
| 9 | Bullets legales | 13 | Baja — contenido legal estable |

---

## 4. Plan de implementación — 4 fases

| Fase | Alcance | Esfuerzo estimado | Estado |
|------|---------|--------------------|--------|
| **1** | CPT `concesionario` + ACF | ~4 días | ✅ **Implementado en código** (2026-07-22) — ver detalle abajo. Pendiente activar ACF en el servidor vivo. |
| **2** | Comparativa de versiones (smart1/smart3) + specs BRABUS por modelo | ~5 días | Pendiente |
| **3** | Carruseles de características + configurador de color/interior | ~5 días | Pendiente |
| **4** | Contenido institucional/legal/cookies/footer | ~2 días | Pendiente |

**Total estimado: ~13-16 días.**

### Fase 1 — Concesionarios (implementado)

Objetivo: eliminar la triplicación manual y darle al cliente un único lugar (wp-admin → Concesionarios) para dar de alta/baja sucursales, con horario de atención como campo nuevo.

**Qué se construyó:**
- **CPT `concesionario`** (no público, solo interno) registrado en `functions.php`.
- **Campos ACF** vía `acf_add_local_field_group()` (registrados en código/git, no en base de datos, para que queden versionados): `nombre` (text), `direccion` (text, solo calle+altura), `localidad` (text), `provincia` (text), `telefono` (text, vacío si no hay línea directa), `latitud` / `longitud` (number), `tipo_servicio` (select: Venta / Servicio / Venta y Servicio), `horario` (textarea — campo nuevo, hoy vacío en los 15 registros migrados, no existía en el sitio original).
- **Migración automática one-time**: los 15 concesionarios (con su dirección, teléfono, lat/lng y tipo de servicio originales) quedan como array en `functions.php` y se insertan solos vía `wp_insert_post()` + `update_field()` la primera vez que ACF esté activo — guardado con la opción `smart_concesionarios_migrados` para que no se dupliquen. No requiere WP-CLI ni acción manual, más allá de activar el plugin.
- **Helper `smart_get_concesionarios()`** en `functions.php` — única fuente de verdad, reemplaza los 3 formatos manuales. La usan:
  - `page-buscador.php` (listado HTML vía `WP_Query` + datos del mapa Leaflet vía `wp_localize_script('smart-buscador-data', 'smartConcesionarios', ...)`)
  - `partials/form-contacto.php` (dropdown de concesionario)
- Los slugs de los 15 posts (`colcar-moreno`, `lonco-hue-libertador`, etc.) se mantuvieron idénticos a los que ya usaba el dropdown del form original, para no romper nada existente.
- **ACF Free** se descargó de wordpress.org y se dejó instalado (sin activar) en `C:\inetpub\wwwroot\wp-content\plugins\advanced-custom-fields\` del servidor vivo. No se vendorizó dentro del theme (sería ~9MB de código de terceros a mantener aparte).

**Pendiente para que quede 100% operativo:**
1. Activar el plugin **Advanced Custom Fields** en `wp-admin/plugins.php` del servidor vivo (un clic). Hasta que esto pase, `/buscador/` se ve vacío en producción (sin lista, sin mapa, sin opciones en el dropdown) — el código no tira error, simplemente no hay datos todavía.
2. Una vez activado, la migración corre sola en el siguiente request. No hace falta tocar nada más.
3. Cargar el campo **horario** manualmente desde wp-admin para cada concesionario (no existía dato original que migrar).
4. Dato de calidad detectado durante la migración: el concesionario Besten de San Fernando tenía en el dato original un tag de búsqueda "victoria" inconsistente con su propia dirección (dice San Fernando). No se replicó ese alias suelto — si alguien buscaba "Victoria" antes lo encontraba, ahora no. Se puede agregar "Victoria" a la localidad si el cliente quiere preservar ese comportamiento.

### Fase 2 — Comparativa de versiones + specs BRABUS (pendiente, ~5 días)

Repeater "versión" (imagen, nombre, autonomía, mecánica, exterior, interior, tecnología, seguridad) reutilizable entre `page-smart1.php` (4 columnas: Pure/Pro/Pro+/BRABUS) y `page-smart3.php` (3 columnas: Pro/Pro+/BRABUS), con cantidad de columnas variable en vez de hardcodeada. El HTML actual es un `display:grid` secuencial, no una tabla — requiere reescribir el template para iterar el repeater. De paso, separar los specs de `page-brabus.php` (3,9 seg / 66kWh / 400km / 428CV) por modelo (#1/#3) para corregir el bug de que hoy no varían con el toggle visual.

### Fase 3 — Carruseles de características + configurador de color/interior (pendiente, ~5 días)

Repeater de tarjetas (imagen+título+descripción) para los 8+ carruseles del sitio (smart1, smart3, conectividad, servicios, sobre-smart, movilidad eléctrica, home) — estructura uniforme, sin lógica JS que tocar. El configurador de color/interior es más costoso: las imágenes de interior viven en un array JS embebido (`lineaMap`/`STD` en smart1/smart3, `ZOOM_SRCS` duplicando rutas ya presentes en HTML) — migrar a ACF exige exponer esos datos vía `wp_localize_script()`/`json_encode()` generados desde PHP en vez de arrays JS literales. Es refactor de JS, no solo de plantilla.

### Fase 4 — Contenido institucional/legal/cookies/footer (pendiente, ~2 días)

Repeaters simples para el acordeón de servicios/garantía (9 ítems) y los 4 tipos de cookies (con el flag "activo por defecto" que hoy es CSS y debería ser un dato). WYSIWYG para legales e historia institucional (contenido estable, no necesita repeater). `wp_nav_menu()` nativo de WP para header/footer en vez de ACF. `date('Y')` para el copyright en vez de mantenerlo hardcodeado en 2 archivos.

---

## 5. Notas transversales para las próximas fases

- **Nombres de versión** ("Pure"/"Pro"/"Pro+"/"BRABUS") aparecen repetidos literalmente 4-5 veces por archivo (nav, grid comparativo, botones `data-linea`, form de contacto) — con ACF esto pasa a tener una sola fuente de verdad, igual que se hizo con los concesionarios en la Fase 1.
- **Arrays JS embebidos** (`lineaMap`, `ZOOM_SRCS`, `STD`) son, en todo el theme, el contenido más caro de migrar — no es HTML, requiere `wp_localize_script()`/`json_encode()` generado desde PHP.
- **Duplicación de la misma imagen/dato en 2 lugares del mismo archivo** (comp-*.png en track mobile + grid desktop, manual PDF en 2 links) se resuelve automáticamente al migrar a repeater, con una sola fuente referenciada dos veces en el template.
- Bugs de contenido detectados de paso (no relacionados con ACF, corregir cuando se toque cada sección): link roto `href="#"` en botón "Descargar" app de conectividad; botón "Filtros" sin función en el buscador; 2 descripciones idénticas por copy-paste en el carrusel de sobre-smart; specs de BRABUS que no varían con el toggle de modelo; formulario de servicios que probablemente no envía nada.
