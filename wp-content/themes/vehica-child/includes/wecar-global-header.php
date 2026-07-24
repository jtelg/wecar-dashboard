<?php
if (!defined('ABSPATH')) exit;
?>
<header id="wecar-header-global" class="wecar-header-global">
  <div class="wecar-header__inner">
    <div class="wecar-header__logo">
      <a href="/"><img src="<?php echo esc_url(get_stylesheet_directory_uri() . '/assets/images/logo-wecar.svg'); ?>" alt="WeCar"></a>
    </div>
    <nav class="wecar-header__nav">
      <a href="/">Inicio</a>
      <a href="/autos/">Comprar</a>
      <a href="/vende-tu-auto/">Vender</a>
      <a href="/faq/">Nosotros</a>
      <a href="/blog/">Blog</a>
      <a href="/contactanos/" class="wecar-header__button">Contactanos</a>
    </nav>
  </div>
</header>
<script>
(function() {
  'use strict';
  var header = document.getElementById('wecar-header-global');
  if (!header) return;
  var THRESHOLD = 10;
  function onScroll() {
    if (window.scrollY > THRESHOLD) {
      header.classList.add('scrolled');
    } else {
      header.classList.remove('scrolled');
    }
  }
  onScroll();
  window.addEventListener('scroll', onScroll, { passive: true });
})();
</script>
