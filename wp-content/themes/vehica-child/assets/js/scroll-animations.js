/**
 * WeCar Scroll Animations — Reveal on scroll
 * ==========================================
 * Motor genérico de reveal con IntersectionObserver + configuración por página.
 * - Los elementos arrancan ocultos SOLO si este script corre (la clase
 *   .wecar-reveal la agrega el JS; sin JS el contenido se ve normal).
 * - Stagger por grupo via --wecar-reveal-delay.
 * - Respeta prefers-reduced-motion (no oculta nada).
 * - Los elementos visibles al cargar se revelan enseguida con stagger
 *   (entrada de hero), el resto aparece con el scroll.
 */
(function () {
  'use strict';

  if (!('IntersectionObserver' in window)) return;
  if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) return;

  var body = document.body;
  var groups = [];

  if (body.classList.contains('home')) {
    // ── Home (Elementor 35463). Hero y steps ya tienen animación propia. ──
    groups = [
      // Título + link del carousel
      {
        items: '.elementor-element-h04s001 .elementor-top-column',
        from: 'up',
        stagger: 140
      },
      // Carousel completo (las cards tienen marquee propio, compatible)
      {
        items: '#wecar-carousel',
        from: 'up'
      },
      // "Elegí Wecar" — título y 3 features
      {
        items: '.elementor-element-h05c001',
        from: 'up'
      },
      {
        items: '.elementor-element-h05c002, .elementor-element-h05c003, .elementor-element-h05c004',
        from: 'up',
        stagger: 160
      },
      // "Respaldado por grandes marcas" — título y logos
      {
        items: '.elementor-element-h06c001',
        from: 'up'
      },
      {
        items: '.elementor-element-h06c002, .elementor-element-h06c003, .elementor-element-h06c004',
        from: 'zoom',
        stagger: 160
      }
    ];
  } else if (body.classList.contains('wecar-vende-tu-auto-page')) {
    // ── Vendé tu auto ──
    groups = [
      // Hero (visible al cargar: entrada con stagger)
      {
        items: '.wecar-sell__hero-content > *',
        from: 'up',
        stagger: 130
      },
      // Proceso: título, pasos, CTA
      {
        items: '#wecar-process-title',
        from: 'up'
      },
      {
        items: '.wecar-sell__process .wecar-sell__step',
        from: 'up',
        stagger: 150
      },
      {
        items: '.wecar-sell__process .wecar-sell__button--secondary',
        from: 'up',
        delay: 150
      },
      // Situaciones
      {
        items: '.wecar-sell__situations .wecar-sell__heading',
        from: 'up'
      },
      {
        items: '.wecar-sell__situation-card',
        from: 'up',
        stagger: 150
      },
      // Experiencia / stats
      {
        items: '#wecar-experience-title',
        from: 'up'
      },
      {
        items: '.wecar-sell__stat',
        from: 'zoom',
        stagger: 130
      },
      // Comparación
      {
        items: '#wecar-comparison-title',
        from: 'up'
      },
      {
        items: '.wecar-sell__table-scroll',
        from: 'up',
        delay: 120
      },
      // Beneficios
      {
        items: '.wecar-sell__benefits-copy',
        from: 'left'
      },
      {
        items: '.wecar-sell__benefit',
        from: 'up',
        stagger: 100
      },
      // FAQ + CTA final
      {
        items: '.wecar-vende-tu-auto-page .wecar-faq',
        from: 'up'
      },
      {
        items: '.wecar-sell__final-card',
        from: 'zoom'
      }
    ];
  } else if (body.classList.contains('wecar-cotizador-page')) {
    // ── Cotizador ──
    groups = [
      // Hero
      {
        items: '.wecar-cotizador__hero-inner > *',
        from: 'up',
        stagger: 150
      },
      // Calificación: SOLO el heading. Las cards, copy y CTA ya tienen su
      // propia animación scroll-triggered con timing Figma (cotizador.js +
      // .wecar-cotizador__qualification.is-animating en cotizador.css).
      {
        items: '.wecar-cotizador__qualification .wecar-cotizador__heading',
        from: 'up'
      },
      // Video
      {
        items: '.wecar-cotizador__video',
        from: 'zoom'
      },
      // Steps
      {
        items: '.wecar-cotizador__steps .wecar-cotizador__heading',
        from: 'up'
      },
      {
        items: '.wecar-cotizador__step',
        from: 'up',
        stagger: 160
      },
      {
        items: '.wecar-cotizador__steps .wecar-cotizador__primary',
        from: 'up',
        delay: 160
      },
      // FAQ
      {
        items: '.wecar-cotizador-page .wecar-faq',
        from: 'up'
      }
    ];
  }

  if (!groups.length) return;

  // Al terminar el reveal se limpian clases, delay inline y data-attribute:
  // el elemento vuelve a sus estilos naturales (hovers y transiciones propias
  // no quedan con el delay ni el timing del reveal).
  var REVEAL_DURATION_MS = 1000;

  function cleanupRevealed(element) {
    element.classList.remove('wecar-reveal', 'wecar-reveal--visible');
    element.style.removeProperty('--wecar-reveal-delay');
    element.removeAttribute('data-wecar-reveal-from');
  }

  function scheduleCleanup(element) {
    var delay = parseInt(element.style.getPropertyValue('--wecar-reveal-delay'), 10) || 0;
    window.setTimeout(function () {
      cleanupRevealed(element);
    }, delay + REVEAL_DURATION_MS + 50);
  }

  var io = new IntersectionObserver(function (entries) {
    entries.forEach(function (entry) {
      if (!entry.isIntersecting) return;
      entry.target.classList.add('wecar-reveal--visible');
      io.unobserve(entry.target);
      scheduleCleanup(entry.target);
    });
  }, {
    // Dispara cuando el elemento ya está bien dentro del viewport (15% visible
    // y por encima del 20% inferior): la animación se ve completa, no en el borde.
    rootMargin: '0px 0px -20% 0px',
    threshold: 0.15
  });

  groups.forEach(function (group) {
    var items = document.querySelectorAll(group.items);
    for (var index = 0; index < items.length; index++) {
      var element = items[index];
      element.classList.add('wecar-reveal');
      element.style.setProperty(
        '--wecar-reveal-delay',
        ((group.delay || 0) + index * (group.stagger || 0)) + 'ms'
      );
      if (group.from && group.from !== 'up') {
        element.setAttribute('data-wecar-reveal-from', group.from);
      }
      io.observe(element);
    }
  });
})();
