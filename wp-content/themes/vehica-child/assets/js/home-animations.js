/**
 * WeCar Home Animations — State-Based Scroll Animation
 * =====================================================
 * - Single IntersectionObserver on .wecar-steps section
 * - 4 discrete frames based on intersectionRatio thresholds (25/50/75/100%)
 * - Line fill scale via CSS custom property (GPU-accelerated transform: scaleX)
 * - requestAnimationFrame for DOM class changes (avoids layout thrash)
 * - prefers-reduced-motion: reduce → immediate frame 4, no observer
 * - No IntersectionObserver support → immediate frame 4
 * - Scope: body.home only
 * ===================================================== */

(function () {
  'use strict';

  // Scope to home page only
  if (!document.body.classList.contains('home')) return;

  const section = document.querySelector('.wecar-steps');
  if (!section) return;

  // Graceful degradation: reduced motion or no IO support
  const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
  if (prefersReducedMotion || !('IntersectionObserver' in window)) {
    section.classList.add('wecar-steps--frame-4');
    return;
  }

  const observer = new IntersectionObserver(
    (entries) => {
      entries.forEach(entry => {
        const ratio = entry.intersectionRatio;
        const target = entry.target;
        let frame = 1;
        if (ratio >= 0.75) frame = 4;
        else if (ratio >= 0.5) frame = 3;
        else if (ratio >= 0.25) frame = 2;

        requestAnimationFrame(() => {
          // Update line fill scale
          let fillScale = 0;
          if (frame >= 4) fillScale = 1;
          else if (frame >= 3) fillScale = 2 / 3;
          else if (frame >= 2) fillScale = 1 / 3;
          target.style.setProperty('--wecar-line-fill-scale', fillScale);

          // Replace frame class
          target.className = target.className.replace(/wecar-steps--frame-\d+/g, '') + ' wecar-steps--frame-' + frame;
        });
      });
    },
    {
      threshold: [0, 0.25, 0.5, 0.75, 1.0],
      rootMargin: '0px',
    }
  );

  observer.observe(section);
})();
