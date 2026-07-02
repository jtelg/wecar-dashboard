/**
 * WeCar Home Animations — Static No-op (Figma Exact 2026-07)
 * ==========================================================
 * Replaced the 4-frame scroll animation with a static no-op.
 * The Figma design shows a STATIC 3-step section with no
 * scroll-bound animation (per REQ-HOME-004/018 removal).
 *
 * All previous frame classes (wecar-steps--frame-*) are
 * harmless if present. This script ensures all content is
 * visible regardless of JS state.
 * ========================================================== */

(function () {
  'use strict';

  if (!document.body.classList.contains('home')) return;

  // Figma design is static. Scroll animation removed.
  // Ensure all content is visible regardless of JS state.
  var section = document.querySelector('.wecar-steps');
  if (section && !section.classList.contains('wecar-steps--frame-4')) {
    section.classList.add('wecar-steps--frame-4');
  }
})();
