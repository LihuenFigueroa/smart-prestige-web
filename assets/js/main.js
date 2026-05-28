/**
 * Smart Prestige — main.js
 *
 * Módulos:
 *  1. Navbar (hamburger + mobile menu)
 *  2. Video Scrubbing (Hero + BRABUS) con GSAP ScrollTrigger
 */

(function () {
    'use strict';

    // ── 1. Navbar ─────────────────────────────────────────────────────────────

    const hamburger = document.querySelector('.sp-navbar__hamburger');
    const mobileMenu = document.getElementById('sp-mobile-menu');
    const mobileClose = document.querySelector('.sp-mobile-menu__close');

    function openMobileMenu() {
        mobileMenu.classList.add('is-open');
        mobileMenu.setAttribute('aria-hidden', 'false');
        hamburger.setAttribute('aria-expanded', 'true');
        document.body.style.overflow = 'hidden';
    }

    function closeMobileMenu() {
        mobileMenu.classList.remove('is-open');
        mobileMenu.setAttribute('aria-hidden', 'true');
        hamburger.setAttribute('aria-expanded', 'false');
        document.body.style.overflow = '';
    }

    if (hamburger) hamburger.addEventListener('click', openMobileMenu);
    if (mobileClose) mobileClose.addEventListener('click', closeMobileMenu);

    // Cerrar con Escape
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') closeMobileMenu();
    });


    // ── 2. Video Scrubbing ────────────────────────────────────────────────────
    //
    // Cómo funciona:
    //  - ScrollTrigger pinea la sección (.sp-hero / .sp-brabus) al llegar al
    //    viewport. Mientras está pineada, el usuario scroll genera un valor
    //    "progress" de 0 a 1.
    //  - Ese progress se mapea a video.currentTime (0 → video.duration).
    //  - El scrubFactor controla cuántos px de scroll equivalen a la duración
    //    completa del video. A más pixels, más "lento" y detallado el efecto.
    //
    // IMPORTANTE: el video debe estar en /assets/video/ en el servidor.
    // Durante desarrollo local podés reemplazar las rutas con URLs absolutas
    // o archivos locales usando un servidor local (MAMP, Laragon, etc.)

    gsap.registerPlugin(ScrollTrigger);

    /**
     * Inicializa el scrubbing de video para una sección dada.
     *
     * @param {string} sectionSelector  - selector CSS de la sección contenedora
     * @param {string} videoSelector    - selector CSS del elemento <video>
     * @param {number} scrubSeconds     - duración en segundos de scroll para
     *                                    cubrir el video completo (default: 3)
     */
    function initVideoScrub(sectionSelector, videoSelector, scrubSeconds) {
        const section = document.querySelector(sectionSelector);
        const video   = document.querySelector(videoSelector);

        if (!section || !video) return;

        // Esperamos a que el video tenga metadata para conocer su duración.
        function setupScrub() {
            const duration = video.duration;
            if (!duration || isNaN(duration)) return; // por si el video no cargó

            // El "end" determina cuántos px scrolleamos con la sección pineada.
            // scrubSeconds * 200 px/s es un ratio razonable; ajustable.
            const scrollDistance = (scrubSeconds || 3) * 200;

            ScrollTrigger.create({
                trigger: section,
                start: 'top top',           // cuando el top de la sección llega al top del viewport
                end: `+=${scrollDistance}`, // pineada por scrollDistance px
                pin: true,                  // pinea la sección
                scrub: true,                // suaviza el movimiento (true = lag mínimo)
                anticipatePin: 1,
                onUpdate: function (self) {
                    // self.progress va de 0 a 1 mientras la sección está pineada
                    video.currentTime = self.progress * duration;
                },
            });
        }

        if (video.readyState >= 1) {
            // Metadata ya disponible
            setupScrub();
        } else {
            video.addEventListener('loadedmetadata', setupScrub, { once: true });
        }
    }

    // Hero — Smart #3
    initVideoScrub('.sp-hero', '#sp-hero-video', 4);

    // BRABUS — Smart #1 & #3
    initVideoScrub('.sp-brabus', '#sp-brabus-video', 4);

})();
