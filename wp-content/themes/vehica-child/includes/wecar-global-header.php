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
      <a href="/cotiza/">Vender</a>
      <a href="/faq/">Nosotros</a>
      <a href="/blog/">Blog</a>
    </nav>
    <div class="wecar-header__cta">
      <a href="/contactanos/" class="wecar-header__button">Contactanos</a>
    </div>
  </div>
</header>
