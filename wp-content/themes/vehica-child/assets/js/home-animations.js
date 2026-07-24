/**
 * WeCar Header — Scroll-based background transition
 * Header is transparent at scroll position 0, becomes white when scrolled.
 */
(function () {
  'use strict';

  var header = document.querySelector('#wecar-header');
  var globalHeader = document.getElementById('wecar-header-global');
  var SCROLL_THRESHOLD = 10; // pixels

  function handleHeaderScroll() {
    var scrollY = window.scrollY || window.pageYOffset;
    var shouldAddScrolled = scrollY > SCROLL_THRESHOLD;

    if (header) {
      if (shouldAddScrolled) {
        header.classList.add('scrolled');
      } else {
        header.classList.remove('scrolled');
      }
    }

    if (globalHeader) {
      if (shouldAddScrolled) {
        globalHeader.classList.add('scrolled');
      } else {
        globalHeader.classList.remove('scrolled');
      }
    }
  }

  // Initial check
  handleHeaderScroll();

  // Listen for scroll
  window.addEventListener('scroll', handleHeaderScroll, { passive: true });
})();

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
 * WeCar Home Steps — Sequential viewport reveal
 * Step 1 rises into place, followed by steps 2 and 3 from left to right.
 */
(function () {
  'use strict';
  var DEBUG_PREFIX = '[WeCar Steps]';
  var isTestHost = window.location.hostname === 'test.wecar.com.ar';

  function debugLog(message, details) {
    if (!isTestHost) return;
    console.log(DEBUG_PREFIX + ' ' + message, details || {});
  }

  debugLog('script/init executed', {
    hostname: window.location.hostname,
    bodyHome: document.body.classList.contains('home'),
    readyState: document.readyState
  });

  if (!document.body.classList.contains('home')) return;

  // Figma component set 121:7265: SLOW smart-animate with a 1 ms handoff.
  // Keep the accumulated offsets derived from these two constants so the
  // normal reveal and the explicit TEST replay cannot drift apart.
  var FIGMA_STEP_DURATION_MS = 1250.1229047775269;
  var FIGMA_STEP_GAP_MS = 1.0000000474974513;
  var FIGMA_STEP_INTERVAL_MS = FIGMA_STEP_DURATION_MS + FIGMA_STEP_GAP_MS;
  var FIGMA_SLOW_EASING = 'cubic-bezier(0, 0, 0.3, 1)';

  function initStepsReveal() {
    var section = document.querySelector('#wecar-steps');
    debugLog('initStepsReveal executed', {
      sectionFound: Boolean(section),
      sectionDataset: section ? Object.assign({}, section.dataset) : null,
      sectionClasses: section ? Array.prototype.slice.call(section.classList) : []
    });
    if (!section || section.dataset.wecarStepsReveal === '1') return;

    section.dataset.wecarStepsReveal = '1';
    var prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

    debugLog('motion preference evaluated', {
      prefersReducedMotion: prefersReducedMotion,
      qaOverride: prefersReducedMotion && isTestHost,
      hostname: window.location.hostname
    });

    if (prefersReducedMotion && isTestHost) {
      // Apply the CSS QA override before motion-ready so TEST never paints the
      // reduced-motion visible state between those two classes.
      section.dataset.wecarReducedMotionQaOverride = '1';
      section.classList.add('wecar-steps--test-motion');
      debugLog('TEST reduced-motion override activated', {
        sectionClasses: Array.prototype.slice.call(section.classList),
        motionReady: section.classList.contains('wecar-steps--motion-ready')
      });
    }

    if (prefersReducedMotion && !isTestHost) {
      // Keep non-TEST hosts accessible and visible without ever entering the
      // hidden motion-ready state.
      section.dataset.wecarReducedMotion = '1';
      section.classList.add('wecar-steps--revealed');
      return;
    }

    section.classList.add('wecar-steps--motion-ready');

    // Commit the initial hidden state before scroll activation can reveal it.
    // Without this layout boundary, the browser may coalesce both class changes
    // and render the cards directly in their final state.
    section.getBoundingClientRect();

    function reveal() {
      debugLog('reveal invoked', {
        classesBefore: Array.prototype.slice.call(section.classList)
      });
      section.classList.add('wecar-steps--revealed');
      debugLog('reveal completed', {
        classesFinal: Array.prototype.slice.call(section.classList)
      });
    }

    var animationTargets = [
      { name: 'step-1', element: section.querySelector('.elementor-column[data-id="h03c002"]'), transform: 'translateY(36px)', index: 0 },
      { name: 'step-2', element: section.querySelector('.elementor-column[data-id="h03c003"]'), transform: 'translateX(-36px)', index: 1 },
      { name: 'step-3', element: section.querySelector('.elementor-column[data-id="h03c004"]'), transform: 'translateX(-36px)', index: 2 },
      { name: 'cta', element: section.querySelector('.wecar-steps__cta'), transform: 'translateY(36px)', index: 3 }
    ];

    function playStepsAnimation(source) {
      debugLog('playStepsAnimation invoked', {
        source: source,
        targets: animationTargets.map(function (target) {
          return {
            name: target.name,
            found: Boolean(target.element),
            duration: FIGMA_STEP_DURATION_MS,
            delay: target.index * FIGMA_STEP_INTERVAL_MS,
            easing: FIGMA_SLOW_EASING
          };
        }),
        classesBefore: Array.prototype.slice.call(section.classList)
      });

      animationTargets.forEach(function (target) {
        if (!target.element) return;
        target.element.getAnimations().forEach(function (animation) {
          animation.cancel();
        });
      });

      reveal();

      animationTargets.forEach(function (target) {
        if (!target.element) return;
        target.element.animate([
          { opacity: 0, transform: target.transform },
          { opacity: 1, transform: 'translate(0, 0)' }
        ], {
          duration: FIGMA_STEP_DURATION_MS,
          delay: target.index * FIGMA_STEP_INTERVAL_MS,
          easing: FIGMA_SLOW_EASING,
          fill: 'both'
        });
      });
    }

    function addTestReplayControl() {
      if (window.location.hostname !== 'test.wecar.com.ar') return;
      if (document.querySelector('.wecar-steps__test-replay')) return;

      var button = document.createElement('button');
      button.type = 'button';
      button.className = 'wecar-steps__test-replay';
      button.textContent = 'Replay Steps Animation (TEST)';
      button.addEventListener('click', function () {
        debugLog('Replay clicked', {});
        playStepsAnimation('replay');
      });
      document.body.appendChild(button);
    }

    addTestReplayControl();

    var ticking = false;
    var hasScrolled = false;
    var scrollEventCount = 0;

    function cleanup() {
      debugLog('cleanup listeners', {
        scrollEventCount: scrollEventCount,
        revealed: section.classList.contains('wecar-steps--revealed')
      });
      window.removeEventListener('scroll', handleScroll);
      window.removeEventListener('resize', handleResize);
    }

    function positionSnapshot() {
      var bounds = section.getBoundingClientRect();
      var triggerLine = window.innerHeight * 0.75;
      return {
        scrollY: window.scrollY,
        boundsTop: bounds.top,
        boundsBottom: bounds.bottom,
        innerHeight: window.innerHeight,
        triggerLine: triggerLine,
        hasScrolled: hasScrolled,
        shouldReveal: hasScrolled && bounds.top <= triggerLine && bounds.bottom > 0,
        revealed: section.classList.contains('wecar-steps--revealed'),
        sectionClasses: Array.prototype.slice.call(section.classList)
      };
    }

    function checkPosition() {
      ticking = false;
      var snapshot = positionSnapshot();
      debugLog('checkPosition', snapshot);

      if (snapshot.shouldReveal) {
        playStepsAnimation('viewport');
        cleanup();
      }
    }

    function requestCheck() {
      if (ticking) return;
      ticking = true;
      debugLog('requestAnimationFrame requested', {
        scrollEventCount: scrollEventCount,
        hasScrolled: hasScrolled
      });
      window.requestAnimationFrame(checkPosition);
    }

    function handleScroll() {
      scrollEventCount += 1;
      hasScrolled = true;
      debugLog('scroll event', {
        eventCount: scrollEventCount,
        scrollY: window.scrollY,
        ticking: ticking
      });
      requestCheck();
    }

    function handleResize() {
      debugLog('resize event', {
        hasScrolled: hasScrolled,
        innerHeight: window.innerHeight
      });
      if (!hasScrolled) return;
      requestCheck();
    }

    if (isTestHost) {
      window.wecarStepsDebug = function () {
        var snapshot = positionSnapshot();
        debugLog('manual debug check', snapshot);
        return snapshot;
      };
    }

    // No initial requestCheck: restored scroll positions remain hidden until
    // the user produces a real scroll event.
    window.addEventListener('scroll', handleScroll, { passive: true });
    window.addEventListener('resize', handleResize);
    debugLog('listeners registered', {
      scroll: 'passive',
      resize: true,
      initialRequestCheck: false
    });
  }

  if (document.readyState === 'complete') {
    initStepsReveal();
  } else {
    // Elementor can still shift the section while page assets finish loading.
    window.addEventListener('load', initStepsReveal, { once: true });
  }
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
