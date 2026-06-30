/**
 * WeCar Home Animations — Scroll-Triggered 3-Step Process
 * =========================================================
 * - Intersection Observer with threshold 0.2
 * - Staggered entrance: 0ms, 150ms, 300ms based on CSS class (wecar-step--1/2/3)
 * - prefers-reduced-motion: reduce → immediate reveal, no transitions
 * - Scope: body.home only
 * - Graceful degradation: if JS fails, all content is visible by default
 * ========================================================= */

(function () {
  'use strict';

  // Only run on home page
  if (!document.body.classList.contains('home')) {
    return;
  }

  // Respect reduced motion
  const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

  // Find all step elements
  const steps = document.querySelectorAll('.wecar-step');

  if (!steps.length) {
    return;
  }

  // If reduced motion, immediately show all steps and exit
  if (prefersReducedMotion) {
    steps.forEach(function (step) {
      step.classList.add('wecar-step--visible');
    });
    return;
  }

  // Intersection Observer callback
  function onIntersect(entries, observer) {
    entries.forEach(function (entry) {
      if (!entry.isIntersecting) {
        return;
      }

      var step = entry.target;
      // Derive index from CSS class (wecar-step--1, wecar-step--2, etc.)
      var index = 0;
      var classes = step.classList;
      for (var i = 1; i <= 3; i++) {
        if (classes.contains('wecar-step--' + i)) {
          index = i;
          break;
        }
      }
      var delay = index * 150; // 0ms, 150ms, 300ms

      setTimeout(function () {
        step.classList.add('wecar-step--visible');
      }, delay);

      // Stop observing once revealed
      observer.unobserve(step);
    });
  }

  // Create observer
  var observer = new IntersectionObserver(onIntersect, {
    threshold: 0.2,
    rootMargin: '0px 0px -50px 0px'
  });

  // Observe each step
  steps.forEach(function (step) {
    // Start hidden
    step.classList.add('wecar-step--hidden');
    observer.observe(step);
  });
})();
