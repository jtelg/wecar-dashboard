/**
 * WeCar Home Hero - desktop horizontal accordion + mobile vertical accordion.
 * Mobile enhancement is opt-in: without JavaScript or its CSS marker, both
 * cards remain complete and actionable.
 */
(function () {
  'use strict';

  if (!document.body.classList.contains('home')) return;

  var ACTIVE = 'wecar-hero__column--active';
  var COLLAPSED = 'wecar-hero__column--collapsed';
  var MOBILE_ENHANCED = 'wecar-hero--mobile-enhanced';
  var MOBILE_CSS_MARKER = '--wecar-hero-mobile-css-ready';
  var HERO_SELECTOR = '#wecar-hero';
  var LEFT_SELECTOR = '.elementor-column[data-id="h02c001"]';
  var RIGHT_SELECTOR = '.elementor-column[data-id="h02c002"]';
  var mobileQuery = window.matchMedia('(max-width: 767px)');
  var currentInstance = null;
  var mutationObserver = null;
  var syncScheduled = false;
  var elementorWindow = null;

  function createHeroInstance(hero, leftCol, rightCol) {
    var savedAttributes = [];
    var mobileEnabled = false;
    var destroyed = false;

    function findSavedAttribute(element, name) {
      for (var index = 0; index < savedAttributes.length; index++) {
        if (savedAttributes[index].element === element && savedAttributes[index].name === name) {
          return savedAttributes[index];
        }
      }
      return null;
    }

    function rememberAttribute(element, name) {
      if (findSavedAttribute(element, name)) return;
      savedAttributes.push({
        element: element,
        name: name,
        present: element.hasAttribute(name),
        value: element.getAttribute(name)
      });
    }

    function restoreAttribute(element, name) {
      var saved = findSavedAttribute(element, name);
      if (!saved) return;
      if (saved.present) element.setAttribute(name, saved.value);
      else element.removeAttribute(name);
    }

    function restoreAllAttributes() {
      for (var index = 0; index < savedAttributes.length; index++) {
        var saved = savedAttributes[index];
        if (saved.present) saved.element.setAttribute(saved.name, saved.value);
        else saved.element.removeAttribute(saved.name);
      }
    }

    function getContentBlocks(column) {
      return column.querySelectorAll(
        '.elementor-widget-icon-box, .wecar-hero__card__badge-wrapper, ' +
        '.wecar-hero__card__image, .wecar-hero__card__cta'
      );
    }

    function setBlockAvailability(block, available) {
      var focusables = block.querySelectorAll('a, button, input, select, textarea, [tabindex]');
      var index;

      rememberAttribute(block, 'aria-hidden');
      rememberAttribute(block, 'inert');

      if (available) {
        restoreAttribute(block, 'aria-hidden');
        restoreAttribute(block, 'inert');
        if ('inert' in block) block.inert = block.hasAttribute('inert');
        for (index = 0; index < focusables.length; index++) {
          restoreAttribute(focusables[index], 'tabindex');
        }
        return;
      }

      block.setAttribute('aria-hidden', 'true');
      block.setAttribute('inert', '');
      if ('inert' in block) block.inert = true;
      for (index = 0; index < focusables.length; index++) {
        rememberAttribute(focusables[index], 'tabindex');
        focusables[index].setAttribute('tabindex', '-1');
      }
    }

    function setColumnAvailability(column, available) {
      var blocks = getContentBlocks(column);
      for (var index = 0; index < blocks.length; index++) {
        setBlockAvailability(blocks[index], available);
      }
    }

    function panelId(column) {
      return column === leftCol ? 'wecar-hero-panel-comprar' : 'wecar-hero-panel-vender';
    }

    function triggerLabel(column) {
      return column === leftCol ? 'Abrir opciones para comprar' : 'Abrir opciones para vender';
    }

    function widgetWrap(column) {
      return column.querySelector(':scope > .elementor-widget-wrap') ||
        column.querySelector('.elementor-widget-wrap');
    }

    function rememberMobileStructure(column) {
      var attributes = ['role', 'tabindex', 'aria-expanded', 'aria-controls', 'aria-label'];
      var wrap = widgetWrap(column);
      for (var index = 0; index < attributes.length; index++) {
        rememberAttribute(column, attributes[index]);
      }
      if (wrap) {
        rememberAttribute(wrap, 'id');
        wrap.id = panelId(column);
      }
    }

    function setActiveSemantics(column) {
      column.removeAttribute('role');
      column.removeAttribute('tabindex');
      column.removeAttribute('aria-expanded');
      column.removeAttribute('aria-controls');
      column.removeAttribute('aria-label');
    }

    function setCollapsedSemantics(column) {
      column.setAttribute('role', 'button');
      column.setAttribute('tabindex', '0');
      column.setAttribute('aria-expanded', 'false');
      column.setAttribute('aria-controls', panelId(column));
      column.setAttribute('aria-label', triggerLabel(column));
    }

    function resetDesktopState() {
      leftCol.classList.remove(ACTIVE, COLLAPSED);
      rightCol.classList.remove(ACTIVE, COLLAPSED);
    }

    function expandDesktop(column, otherColumn) {
      resetDesktopState();
      column.classList.add(ACTIVE);
      otherColumn.classList.add(COLLAPSED);
    }

    function setMobileState(activeColumn, collapsedColumn) {
      activeColumn.classList.add(ACTIVE);
      activeColumn.classList.remove(COLLAPSED);
      collapsedColumn.classList.add(COLLAPSED);
      collapsedColumn.classList.remove(ACTIVE);
      setActiveSemantics(activeColumn);
      setCollapsedSemantics(collapsedColumn);
      setColumnAvailability(activeColumn, true);
      setColumnAvailability(collapsedColumn, false);
    }

    function mobileCssReady() {
      return window.getComputedStyle(hero).getPropertyValue(MOBILE_CSS_MARKER).trim() === '1';
    }

    function enableMobileAccordion() {
      if (mobileEnabled || !mobileCssReady()) return;
      mobileEnabled = true;
      hero.classList.add(MOBILE_ENHANCED);
      rememberMobileStructure(leftCol);
      rememberMobileStructure(rightCol);
      setMobileState(leftCol, rightCol);
    }

    function disableMobileAccordion() {
      if (!mobileEnabled) {
        hero.classList.remove(MOBILE_ENHANCED);
        resetDesktopState();
        return;
      }
      mobileEnabled = false;
      hero.classList.remove(MOBILE_ENHANCED);
      resetDesktopState();
      restoreAllAttributes();
      setColumnAvailability(leftCol, true);
      setColumnAvailability(rightCol, true);
    }

    function isInteractiveTarget(target) {
      return Boolean(target.closest('a, button, input, select, textarea, label'));
    }

    function handleColumnClick(column, otherColumn, event) {
      if (mobileQuery.matches) {
        if (!mobileEnabled || isInteractiveTarget(event.target)) return;
        if (column.classList.contains(COLLAPSED)) {
          setMobileState(column, otherColumn);
        }
        return;
      }

      if (isInteractiveTarget(event.target)) return;
      if (column.classList.contains(ACTIVE)) resetDesktopState();
      else expandDesktop(column, otherColumn);
    }

    function handleColumnKeydown(column, otherColumn, event) {
      if (!mobileQuery.matches || !mobileEnabled || event.target !== column) return;
      if (!column.classList.contains(COLLAPSED)) return;
      if (event.key !== 'Enter' && event.key !== ' ') return;
      event.preventDefault();
      setMobileState(column, otherColumn);
    }

    function handleLeftClick(event) {
      handleColumnClick(leftCol, rightCol, event);
    }

    function handleRightClick(event) {
      handleColumnClick(rightCol, leftCol, event);
    }

    function handleLeftKeydown(event) {
      handleColumnKeydown(leftCol, rightCol, event);
    }

    function handleRightKeydown(event) {
      handleColumnKeydown(rightCol, leftCol, event);
    }

    function syncMode() {
      if (destroyed) return;
      if (mobileQuery.matches && mobileCssReady()) enableMobileAccordion();
      else disableMobileAccordion();
    }

    function destroy() {
      if (destroyed) return;
      destroyed = true;
      disableMobileAccordion();
      leftCol.removeEventListener('click', handleLeftClick);
      rightCol.removeEventListener('click', handleRightClick);
      leftCol.removeEventListener('keydown', handleLeftKeydown);
      rightCol.removeEventListener('keydown', handleRightKeydown);
    }

    var heroImages = hero.querySelectorAll('.wecar-hero__card__image img');
    for (var imageIndex = 0; imageIndex < heroImages.length; imageIndex++) {
      heroImages[imageIndex].setAttribute('loading', 'eager');
    }

    leftCol.addEventListener('click', handleLeftClick);
    rightCol.addEventListener('click', handleRightClick);
    leftCol.addEventListener('keydown', handleLeftKeydown);
    rightCol.addEventListener('keydown', handleRightKeydown);
    syncMode();

    return {
      hero: hero,
      leftCol: leftCol,
      rightCol: rightCol,
      syncMode: syncMode,
      destroy: destroy
    };
  }

  function findHeroNodes() {
    var hero = document.querySelector(HERO_SELECTOR);
    if (!hero) return null;
    var leftCol = hero.querySelector(LEFT_SELECTOR);
    var rightCol = hero.querySelector(RIGHT_SELECTOR);
    if (!leftCol || !rightCol) return null;
    return { hero: hero, leftCol: leftCol, rightCol: rightCol };
  }

  function syncHero() {
    syncScheduled = false;
    var nodes = findHeroNodes();
    var sameInstance = currentInstance && nodes &&
      currentInstance.hero === nodes.hero &&
      currentInstance.leftCol === nodes.leftCol &&
      currentInstance.rightCol === nodes.rightCol;

    if (sameInstance) {
      currentInstance.syncMode();
      return;
    }

    if (currentInstance) {
      currentInstance.destroy();
      currentInstance = null;
    }

    if (nodes) {
      currentInstance = createHeroInstance(nodes.hero, nodes.leftCol, nodes.rightCol);
    }
  }

  function scheduleSync() {
    if (syncScheduled) return;
    syncScheduled = true;
    window.requestAnimationFrame(syncHero);
  }

  function nodeTouchesHero(node) {
    if (!node || node.nodeType !== 1) return false;
    if (node.matches(HERO_SELECTOR + ', ' + LEFT_SELECTOR + ', ' + RIGHT_SELECTOR)) return true;
    return Boolean(node.querySelector(HERO_SELECTOR + ', ' + LEFT_SELECTOR + ', ' + RIGHT_SELECTOR));
  }

  function mutationTouchesHero(records) {
    if (currentInstance && (
      !currentInstance.hero.isConnected ||
      !currentInstance.leftCol.isConnected ||
      !currentInstance.rightCol.isConnected
    )) return true;

    for (var recordIndex = 0; recordIndex < records.length; recordIndex++) {
      var record = records[recordIndex];
      if (record.target.nodeType === 1 && record.target.closest(HERO_SELECTOR)) return true;
      for (var nodeIndex = 0; nodeIndex < record.addedNodes.length; nodeIndex++) {
        if (nodeTouchesHero(record.addedNodes[nodeIndex])) return true;
      }
      for (nodeIndex = 0; nodeIndex < record.removedNodes.length; nodeIndex++) {
        if (nodeTouchesHero(record.removedNodes[nodeIndex])) return true;
      }
    }
    return false;
  }

  function handleMutations(records) {
    if (mutationTouchesHero(records)) scheduleSync();
  }

  function handleResourceLoad(event) {
    if (event.target && (event.target.tagName === 'LINK' || event.target.tagName === 'STYLE')) {
      scheduleSync();
    }
  }

  function handlePageShow(event) {
    if (event.persisted) syncHero();
  }

  function shutdown(event) {
    if (event && event.persisted) return;
    if (mutationObserver) mutationObserver.disconnect();
    mobileQuery.removeEventListener ?
      mobileQuery.removeEventListener('change', scheduleSync) :
      mobileQuery.removeListener(scheduleSync);
    window.removeEventListener('load', scheduleSync);
    window.removeEventListener('pagehide', shutdown);
    window.removeEventListener('pageshow', handlePageShow);
    window.removeEventListener('elementor/frontend/init', scheduleSync);
    document.removeEventListener('load', handleResourceLoad, true);
    if (elementorWindow) elementorWindow.off('.wecarHero');
    if (currentInstance) {
      currentInstance.destroy();
      currentInstance = null;
    }
  }

  mutationObserver = new MutationObserver(handleMutations);
  mutationObserver.observe(document.body, { childList: true, subtree: true });
  if (mobileQuery.addEventListener) mobileQuery.addEventListener('change', scheduleSync);
  else mobileQuery.addListener(scheduleSync);
  window.addEventListener('load', scheduleSync);
  window.addEventListener('pagehide', shutdown);
  window.addEventListener('pageshow', handlePageShow);
  window.addEventListener('elementor/frontend/init', scheduleSync);
  document.addEventListener('load', handleResourceLoad, true);

  if (window.jQuery) {
    elementorWindow = window.jQuery(window);
    elementorWindow.on('elementor/frontend/init.wecarHero', scheduleSync);
  }

  syncHero();
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
    var IDLE_REVEAL_MS = 3000;
    var idleTimer = null;

    function cleanup() {
      debugLog('cleanup listeners', {
        scrollEventCount: scrollEventCount,
        revealed: section.classList.contains('wecar-steps--revealed')
      });
      if (idleTimer) {
        window.clearTimeout(idleTimer);
        idleTimer = null;
      }
      window.removeEventListener('scroll', handleScroll);
      window.removeEventListener('resize', handleResize);
    }

    // Idle fallback: if the section is on screen and the scroll stays still
    // for IDLE_REVEAL_MS, the reveal starts on its own so the cards never
    // sit as a blank gap waiting for a scroll event.
    function armIdleTimer() {
      if (section.classList.contains('wecar-steps--revealed')) return;
      if (idleTimer) window.clearTimeout(idleTimer);
      idleTimer = window.setTimeout(function () {
        idleTimer = null;
        var bounds = section.getBoundingClientRect();
        var anyPartVisible = bounds.top < window.innerHeight && bounds.bottom > 0;
        debugLog('idle timer fired', {
          scrollY: window.scrollY,
          anyPartVisible: anyPartVisible,
          revealed: section.classList.contains('wecar-steps--revealed')
        });
        if (!anyPartVisible) return;
        playStepsAnimation('idle-timeout');
        cleanup();
      }, IDLE_REVEAL_MS);
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
      armIdleTimer();
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
    // the user produces a real scroll event. The idle timer covers the case
    // where the section is visible but no scroll ever happens.
    window.addEventListener('scroll', handleScroll, { passive: true });
    window.addEventListener('resize', handleResize);
    armIdleTimer();
    debugLog('listeners registered', {
      scroll: 'passive',
      resize: true,
      initialRequestCheck: false,
      idleRevealMs: IDLE_REVEAL_MS
    });
  }

  // Init as early as possible: with the deferred footer script the DOM is
  // already parsed, so the motion-ready hidden state applies before first
  // paint instead of waiting for `load` (which caused a visible→hidden flash
  // of the step cards while page assets finished).
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initStepsReveal, { once: true });
  } else {
    initStepsReveal();
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
