/**
 * WeCar Home Hero — Horizontal Accordion (Click)
 * ==============================================
 * Figma hero component (137:3003) has 3 states:
 *   step-1: both cards equal (50/50)
 *   step-2: left card expanded, right collapsed
 *   step-3: right card expanded, left collapsed
 *
 * Click a card to expand it (collapses the other).
 * Click the expanded card again to reset both to equal.
 * ==============================================
 */

(function () {
  'use strict';

  if (!document.body.classList.contains('home')) return;

  // ── Force eager loading on hero car images (lazy + hidden opacity = no load) ──
  var heroImages = document.querySelectorAll('#wecar-hero .wecar-hero__card__image img');
  for (var i = 0; i < heroImages.length; i++) {
    heroImages[i].setAttribute('loading', 'eager');
  }

  var leftCol = document.querySelector('#wecar-hero .elementor-column[data-id="h02c001"]');
  var rightCol = document.querySelector('#wecar-hero .elementor-column[data-id="h02c002"]');
  if (!leftCol || !rightCol) return;

  var ACTIVE = 'wecar-hero__column--active';
  var COLLAPSED = 'wecar-hero__column--collapsed';

  function reset() {
    leftCol.classList.remove(ACTIVE, COLLAPSED);
    rightCol.classList.remove(ACTIVE, COLLAPSED);
  }

  function expandLeft() {
    reset();
    leftCol.classList.add(ACTIVE);
    rightCol.classList.add(COLLAPSED);
  }

  function expandRight() {
    reset();
    rightCol.classList.add(ACTIVE);
    leftCol.classList.add(COLLAPSED);
  }

  leftCol.addEventListener('click', function () {
    if (window.innerWidth < 768) return;
    // If already active, reset to default. Otherwise expand left.
    if (leftCol.classList.contains(ACTIVE)) {
      reset();
    } else {
      expandLeft();
    }
  });

  rightCol.addEventListener('click', function () {
    if (window.innerWidth < 768) return;
    if (rightCol.classList.contains(ACTIVE)) {
      reset();
    } else {
      expandRight();
    }
  });
})();

/**
 * WeCar Home Carousel — Ping-Pong Auto-scroll + Manual Drag
 * ==========================================================
 * - Cards scroll right until the last card is visible, then reverse
 *   and scroll left until the first card is visible, then reverse again.
 * - Auto-scroll runs ONLY while the carousel is in the viewport (IntersectionObserver).
 * - Manual drag and trackpad/wheel supported; auto pauses during interaction
 *   and resumes after RESUME_DELAY ms of idle.
 */
(function () {
  'use strict';
  if (!document.body.classList.contains('home')) return;

  var SPEED = 1; // px per frame (~60px/s)

  function initCarousel() {
    var track = document.querySelector('body.home #wecar-carousel .wecar-carousel');
    if (!track || track.dataset.wecarMarquee === '1') return;
    if (track.classList.contains('wecar-carousel--empty')) return;

    var cards = Array.prototype.slice.call(track.children);
    if (cards.length < 2) return;

    // Wrap cards in a group (no clone — ping-pong, not infinite loop)
    var group = document.createElement('div');
    group.className = 'wecar-carousel__group';
    cards.forEach(function (card) { group.appendChild(card); });
    track.innerHTML = '';
    track.appendChild(group);
    track.classList.add('wecar-carousel--marquee');
    track.dataset.wecarMarquee = '1';

    var rafId = null;
    var lastTs = null;
    var inView = false;
    var interacting = false;
    var resumeTimer = null;
    var direction = 1; // 1 = scrolling right, -1 = scrolling left

    function maxScroll() {
      return track.scrollWidth - track.clientWidth;
    }

    function step(ts) {
      if (!inView || interacting) {
        rafId = null;
        lastTs = null;
        return;
      }
      if (lastTs === null) lastTs = ts;
      var dt = ts - lastTs;
      lastTs = ts;

      track.scrollLeft += direction * SPEED * (dt / 16.67);

      // Ping-pong: reverse at edges
      if (track.scrollLeft >= maxScroll()) {
        track.scrollLeft = maxScroll();
        direction = -1;
      } else if (track.scrollLeft <= 0) {
        track.scrollLeft = 0;
        direction = 1;
      }

      rafId = window.requestAnimationFrame(step);
    }

    function start() {
      if (rafId === null) {
        lastTs = null;
        rafId = window.requestAnimationFrame(step);
      }
    }
    function stop() {
      if (rafId !== null) {
        window.cancelAnimationFrame(rafId);
        rafId = null;
      }
    }

    // Start/pause based on viewport visibility
    if ('IntersectionObserver' in window) {
      var io = new IntersectionObserver(function (entries) {
        entries.forEach(function (e) {
          inView = e.isIntersecting;
          if (inView) start();
          else stop();
        });
      }, { threshold: 0.15 });
      io.observe(track);
    } else {
      inView = true;
      start();
    }

    var RESUME_DELAY = 3000;

    function pauseAuto() {
      interacting = true;
      stop();
      if (resumeTimer) clearTimeout(resumeTimer);
    }
    function resumeAuto() {
      if (resumeTimer) clearTimeout(resumeTimer);
      resumeTimer = setTimeout(function () {
        interacting = false;
        if (inView) start();
      }, RESUME_DELAY);
    }

    // Drag to scroll manually
    var isDown = false, startX = 0, startScroll = 0;
    function endDrag() {
      if (!isDown) return;
      isDown = false;
      track.classList.remove('wecar-carousel--dragging');
      resumeAuto();
    }
    track.addEventListener('pointerdown', function (e) {
      isDown = true;
      startX = e.clientX;
      startScroll = track.scrollLeft;
      pauseAuto();
      track.classList.add('wecar-carousel--dragging');
      try { track.setPointerCapture(e.pointerId); } catch (err) {}
      e.preventDefault();
    });
    track.addEventListener('pointermove', function (e) {
      if (!isDown) return;
      e.preventDefault();
      track.scrollLeft = startScroll - (e.clientX - startX);
    });
    track.addEventListener('pointerup', endDrag);
    track.addEventListener('pointercancel', endDrag);

    // Trackpad / wheel horizontal also counts as manual interaction
    track.addEventListener('wheel', function (e) {
      if (Math.abs(e.deltaX) > Math.abs(e.deltaY)) {
        pauseAuto();
        resumeAuto();
      }
    }, { passive: true });
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initCarousel);
  } else {
    initCarousel();
  }
})();
