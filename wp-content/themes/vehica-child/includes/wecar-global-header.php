<?php
if (!defined('ABSPATH')) exit;

$request_uri = isset($_SERVER['REQUEST_URI']) ? wp_unslash($_SERVER['REQUEST_URI']) : '/';
$request_path = wp_parse_url($request_uri, PHP_URL_PATH);
$current_path = trailingslashit('/' . ltrim(is_string($request_path) ? $request_path : '/', '/'));
$nav_items = array(
  array('label' => 'Inicio', 'href' => '/'),
  array('label' => 'Comprar', 'href' => '/buscar/'),
  array('label' => 'Vender', 'href' => '/vende-tu-auto/'),
  array('label' => 'Nosotros', 'href' => '/acerca-de-nosotros/'),
  array('label' => 'Blog', 'href' => '/blog/'),
  array('label' => 'Contactanos', 'href' => '/contactanos/', 'class' => 'wecar-header__button'),
);
?>
<header id="wecar-header-global" class="wecar-header-global">
  <div class="wecar-header__inner">
    <div class="wecar-header__logo">
      <a href="/"><img src="<?php echo esc_url(get_stylesheet_directory_uri() . '/assets/images/logo-wecar.svg'); ?>" alt="WeCar"></a>
    </div>
    <button class="wecar-header__menu-toggle" type="button" aria-expanded="false" aria-controls="wecar-header-nav" aria-label="Abrir menú de navegación">
      <span class="wecar-header__menu-toggle-icon" aria-hidden="true"></span>
      <span class="screen-reader-text">Menú</span>
    </button>
    <nav id="wecar-header-nav" class="wecar-header__nav" aria-label="Navegación principal">
      <p class="wecar-header__nav-label">Navegación</p>
      <?php foreach ($nav_items as $item) : ?>
        <?php
        $item_path = trailingslashit('/' . ltrim($item['href'], '/'));
        $is_current = $current_path === $item_path;
        $classes = array_filter(array(
          isset($item['class']) ? $item['class'] : '',
          $is_current ? 'is-current' : '',
        ));
        ?>
        <a href="<?php echo esc_url($item['href']); ?>"<?php if ($classes) : ?> class="<?php echo esc_attr(implode(' ', $classes)); ?>"<?php endif; ?><?php if ($is_current) : ?> aria-current="page"<?php endif; ?>><?php echo esc_html($item['label']); ?></a>
      <?php endforeach; ?>
    </nav>
  </div>
</header>
<button class="wecar-header__backdrop" type="button" aria-label="Cerrar menú de navegación" tabindex="-1" aria-hidden="true"></button>
<script>
(function() {
  'use strict';

  var header = document.getElementById('wecar-header-global');
  if (!header) return;

  var THRESHOLD = 10;
  var CLOSE_FALLBACK_MS = 240;
  var toggle = header.querySelector('.wecar-header__menu-toggle');
  var nav = header.querySelector('.wecar-header__nav');
  var backdrop = document.querySelector('.wecar-header__backdrop');
  var mobileQuery = window.matchMedia('(max-width: 767px)');
  var reducedMotionQuery = window.matchMedia('(prefers-reduced-motion: reduce)');
  var isNavigating = false;
  var pendingNavigationHref = null;
  var navigationTimer = null;
  var navigationEndHandler = null;

  function onScroll() {
    header.classList.toggle('scrolled', window.scrollY > THRESHOLD);
  }

  function isMobile() {
    return mobileQuery.matches;
  }

  function prefersReducedMotion() {
    return reducedMotionQuery.matches;
  }

  function setNavAccessibility(open) {
    nav.setAttribute('aria-hidden', String(!open));
    if ('inert' in nav) nav.inert = !open;
  }

  function setBackdropState(open) {
    if (!backdrop) return;

    backdrop.classList.toggle('is-visible', open);
    backdrop.setAttribute('aria-hidden', String(!open));
    backdrop.tabIndex = -1;
  }

  function setMenuState(open, returnFocus) {
    if (!toggle || !nav || !isMobile()) return;

    header.classList.toggle('is-menu-open', open);
    document.body.classList.toggle('wecar-mobile-nav-is-open', open);
    toggle.setAttribute('aria-expanded', String(open));
    toggle.setAttribute('aria-label', open ? 'Cerrar men\u00fa de navegaci\u00f3n' : 'Abrir men\u00fa de navegaci\u00f3n');
    setNavAccessibility(open);
    setBackdropState(open);

    if (open) {
      var firstLink = nav.querySelector('a');
      if (firstLink) firstLink.focus();
    } else if (returnFocus) {
      toggle.focus();
    }
  }

  function clearPendingNavigation() {
    if (navigationTimer !== null) {
      window.clearTimeout(navigationTimer);
      navigationTimer = null;
    }

    if (navigationEndHandler) {
      nav.removeEventListener('transitionend', navigationEndHandler);
      nav.removeEventListener('transitioncancel', navigationEndHandler);
      navigationEndHandler = null;
    }

    pendingNavigationHref = null;
    isNavigating = false;
  }

  function completePendingNavigation(event) {
    if (
      event &&
      (event.target !== nav || (event.type === 'transitionend' && event.propertyName !== 'transform'))
    ) return;

    if (!pendingNavigationHref) return;

    var href = pendingNavigationHref;
    pendingNavigationHref = null;

    if (navigationTimer !== null) {
      window.clearTimeout(navigationTimer);
      navigationTimer = null;
    }

    if (navigationEndHandler) {
      nav.removeEventListener('transitionend', navigationEndHandler);
      nav.removeEventListener('transitioncancel', navigationEndHandler);
      navigationEndHandler = null;
    }

    isNavigating = false;
    window.location.assign(href);
  }

  function navigateAfterClose(href) {
    if (isNavigating) return;

    isNavigating = true;
    pendingNavigationHref = href;

    if (prefersReducedMotion()) {
      setMenuState(false, false);
      completePendingNavigation();
      return;
    }

    navigationEndHandler = completePendingNavigation;
    nav.addEventListener('transitionend', navigationEndHandler);
    nav.addEventListener('transitioncancel', navigationEndHandler);
    navigationTimer = window.setTimeout(completePendingNavigation, CLOSE_FALLBACK_MS);
    setMenuState(false, false);
  }

  function normalizePath(pathname) {
    return pathname.length > 1 ? pathname.replace(/\/+$/, '') : pathname;
  }

  function isCurrentPage(link, destination) {
    return link.getAttribute('aria-current') === 'page' || (
      destination.origin === window.location.origin &&
      normalizePath(destination.pathname) === normalizePath(window.location.pathname) &&
      destination.search === window.location.search
    );
  }

  function getNavigableDestination(link, event) {
    if (
      event.defaultPrevented ||
      event.button !== 0 ||
      event.metaKey || event.ctrlKey || event.shiftKey || event.altKey ||
      link.hasAttribute('download')
    ) return null;

    var target = link.getAttribute('target');
    if (target && target.toLowerCase() !== '_self') return null;

    try {
      var destination = new URL(link.href, window.location.href);
      return destination.origin === window.location.origin ? destination : null;
    } catch (error) {
      return null;
    }
  }

  function resetForViewport() {
    if (!toggle || !nav) return;

    if (isNavigating && pendingNavigationHref) {
      completePendingNavigation();
    } else {
      clearPendingNavigation();
    }

    if (isMobile()) {
      if (!header.classList.contains('is-menu-open')) {
        document.body.classList.remove('wecar-mobile-nav-is-open');
        toggle.setAttribute('aria-expanded', 'false');
        toggle.setAttribute('aria-label', 'Abrir men\u00fa de navegaci\u00f3n');
        setNavAccessibility(false);
        setBackdropState(false);
      }
      return;
    }

    header.classList.remove('is-menu-open');
    document.body.classList.remove('wecar-mobile-nav-is-open');
    toggle.setAttribute('aria-expanded', 'false');
    toggle.setAttribute('aria-label', 'Abrir men\u00fa de navegaci\u00f3n');
    setNavAccessibility(true);
    setBackdropState(false);
  }

  function trapFocus(event) {
    if (event.key !== 'Tab' || !isMobile() || !header.classList.contains('is-menu-open')) return;

    var focusable = [toggle].concat(Array.prototype.slice.call(nav.querySelectorAll('a[href], button:not([disabled]), [tabindex]:not([tabindex="-1"])')));
    var activeIndex = focusable.indexOf(document.activeElement);

    if (activeIndex === -1) {
      event.preventDefault();
      focusable[0].focus();
      return;
    }

    var nextIndex = event.shiftKey ? activeIndex - 1 : activeIndex + 1;
    if (nextIndex < 0) nextIndex = focusable.length - 1;
    if (nextIndex === focusable.length) nextIndex = 0;

    event.preventDefault();
    focusable[nextIndex].focus();
  }

  if (toggle && nav) {
    toggle.addEventListener('click', function () {
      if (isNavigating) return;
      var menuIsOpen = header.classList.contains('is-menu-open');
      setMenuState(!menuIsOpen, menuIsOpen);
    });

    nav.addEventListener('click', function (event) {
      var link = event.target.closest('a');
      if (!link || !nav.contains(link) || !isMobile() || !header.classList.contains('is-menu-open')) return;

      var destination = getNavigableDestination(link, event);
      if (!destination) return;

      event.preventDefault();

      if (isCurrentPage(link, destination)) {
        setMenuState(false, true);
        return;
      }

      navigateAfterClose(destination.href);
    });

    if (backdrop) {
      backdrop.addEventListener('click', function () {
        if (!isNavigating) setMenuState(false, true);
      });
    }

    document.addEventListener('keydown', function (event) {
      if (event.key === 'Escape' && header.classList.contains('is-menu-open')) {
        if (!isNavigating) setMenuState(false, true);
        return;
      }

      trapFocus(event);
    });

    if (mobileQuery.addEventListener) {
      mobileQuery.addEventListener('change', resetForViewport);
    } else {
      mobileQuery.addListener(resetForViewport);
    }

    resetForViewport();
    header.classList.add('is-nav-enhanced');
  }

  onScroll();
  window.addEventListener('scroll', onScroll, { passive: true });
})();
</script>
