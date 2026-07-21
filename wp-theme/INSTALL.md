# Instalación del tema Smart Argentina en WordPress

## Pasos

1. Copiar la carpeta `smart-argentina/` a `wp-content/themes/` en tu instalación de WordPress.

2. Copiar la carpeta `smart-argentina/assets/` con todo su contenido (ya incluida en el tema).

3. Activar el tema desde **Appearance → Themes** en el panel de WordPress.

4. Crear las siguientes páginas en **Pages → Add New**, asignando el template correcto desde **Page Attributes → Template**:

| Slug | Template |
|------|----------|
| `/` (front page) | Home |
| `smart-1` | Smart 1 |
| `smart-3` | Smart 3 |
| `brabus` | BRABUS |
| `servicios` | Servicios |
| `movilidad-electrica` | Movilidad Eléctrica |
| `conectividad` | Conectividad |
| `sobre-smart` | Sobre Smart |
| `buscador` | Buscador |

5. En **Settings → Reading**, establecer "A static page" y seleccionar la página Home como front page.

## Assets

Los assets (CSS, JS, imágenes, videos, PDFs, fuentes) están incluidos en `smart-argentina/assets/`.
Los videos son pesados — considerar un CDN o hosting de video externo para producción.
