<?php
/**
 * Template Name: WeCar - Vendé tu auto
 * Template Post Type: page
 */

if (!defined('ABSPATH')) {
    exit;
}

$vende_asset_uri = get_stylesheet_directory_uri() . '/assets/images/vende-tu-auto/';
$cotiza_url = home_url('/cotizador/');

get_header();
?>

<main id="wecar-vende-tu-auto" class="wecar-sell" aria-labelledby="wecar-sell-title">
    <section class="wecar-sell__hero">
        <img class="wecar-sell__wave wecar-sell__wave--hero-tl" src="<?php echo esc_url($vende_asset_uri . 'wave-hero-tl.svg'); ?>" alt="" aria-hidden="true">
        <img class="wecar-sell__wave wecar-sell__wave--hero-br" src="<?php echo esc_url($vende_asset_uri . 'wave-hero-br.svg'); ?>" alt="" aria-hidden="true">
        <div class="wecar-sell__container wecar-sell__hero-content">
            <p class="wecar-sell__eyebrow">Vendé tu auto en Wecar</p>
            <h1 id="wecar-sell-title">COTIZÁ GRATIS Y LO VENDEMOS MIENTRAS LO SEGUÍS USANDO</h1>
            <p class="wecar-sell__hero-copy">Nos encargamos de todo el proceso para que vendas tu auto de forma segura y sin complicaciones</p>
            <a class="wecar-sell__button wecar-sell__button--primary" href="<?php echo esc_url($cotiza_url); ?>">Quiero vender mi auto</a>
        </div>
    </section>

    <section class="wecar-sell__section wecar-sell__process" aria-labelledby="wecar-process-title">
        <div class="wecar-sell__container">
            <h2 id="wecar-process-title">Así funciona, es muy simple</h2>
            <ol class="wecar-sell__timeline">
                <li class="wecar-sell__step">
                    <span class="wecar-sell__step-number">1</span>
                    <span class="wecar-sell__icon wecar-sell__icon--large"><img src="<?php echo esc_url($vende_asset_uri . 'icon-calculator.svg'); ?>" alt=""></span>
                    <h3>Cotizás gratis</h3>
                    <p>Completás el formulario online y recibis una cotización inicial sin compromiso.</p>
                </li>
                <li class="wecar-sell__step">
                    <span class="wecar-sell__step-number">2</span>
                    <span class="wecar-sell__icon wecar-sell__icon--large"><img src="<?php echo esc_url($vende_asset_uri . 'icon-search.svg'); ?>" alt=""></span>
                    <h3>Peritamos tu auto</h3>
                    <p>Revisamos tu vehículo para asegurarnos la mejor tasación posible y definir el precio ideal de venta.</p>
                </li>
                <li class="wecar-sell__step">
                    <span class="wecar-sell__step-number">3</span>
                    <span class="wecar-sell__icon wecar-sell__icon--large"><img src="<?php echo esc_url($vende_asset_uri . 'icon-car.svg'); ?>" alt=""></span>
                    <h3>Lo vendemos<br>mientras lo usás</h3>
                    <p>Publicamos tu auto, gestionamos las consultas y nos ocupamos de todo el proceso comercial.</p>
                </li>
                <li class="wecar-sell__step">
                    <span class="wecar-sell__step-number">4</span>
                    <span class="wecar-sell__icon wecar-sell__icon--large"><img src="<?php echo esc_url($vende_asset_uri . 'icon-banknote.svg'); ?>" alt=""></span>
                    <h3>Te lo pagamos en efectivo y cobramos con la comisión más baja del mercado: 7%</h3>
                </li>
            </ol>
            <a class="wecar-sell__button wecar-sell__button--secondary" href="<?php echo esc_url($cotiza_url); ?>">Empezar a cotizar</a>
        </div>
    </section>

    <section class="wecar-sell__section wecar-sell__situations" aria-labelledby="wecar-situations-title">
        <div class="wecar-sell__container">
            <div class="wecar-sell__heading">
                <h2 id="wecar-situations-title">¿Cual es tu situación?</h2>
                <p>Elegi la opcion que mas se adapte a vos y empeza hoy</p>
            </div>
            <div class="wecar-sell__situation-grid">
                <article class="wecar-sell__situation-card wecar-sell__situation-card--violet">
                    <div>
                        <span class="wecar-sell__icon wecar-sell__icon--large"><img src="<?php echo esc_url($vende_asset_uri . 'icon-zap.svg'); ?>" alt=""></span>
                        <h3>Tengo urgencia en vender</h3>
                        <p>Necesito vender rapido y al mejor precio posible</p>
                    </div>
                    <a class="wecar-sell__button wecar-sell__button--small" href="<?php echo esc_url($cotiza_url); ?>">Cotizar ahora</a>
                </article>
                <article class="wecar-sell__situation-card wecar-sell__situation-card--blue">
                    <div>
                        <span class="wecar-sell__icon wecar-sell__icon--large"><img src="<?php echo esc_url($vende_asset_uri . 'icon-repeat.svg'); ?>" alt=""></span>
                        <h3>Quiero cambiar por otro auto</h3>
                        <p>Quiero vender mi auto para comprarme el siguiente</p>
                    </div>
                    <a class="wecar-sell__button wecar-sell__button--small" href="<?php echo esc_url($cotiza_url); ?>">Cambiar Auto</a>
                </article>
                <article class="wecar-sell__situation-card wecar-sell__situation-card--cyan">
                    <div>
                        <span class="wecar-sell__icon wecar-sell__icon--large"><img src="<?php echo esc_url($vende_asset_uri . 'icon-shield-cyan.svg'); ?>" alt=""></span>
                        <h3>Es mi primera vez vendiendo</h3>
                        <p>Necesito asesoramiento y que me guien en el proceso</p>
                    </div>
                    <a class="wecar-sell__button wecar-sell__button--small" href="<?php echo esc_url($cotiza_url); ?>">Asesoramiento</a>
                </article>
            </div>
        </div>
    </section>

    <section class="wecar-sell__section wecar-sell__experience" aria-labelledby="wecar-experience-title">
        <div class="wecar-sell__container">
            <h2 id="wecar-experience-title">La experiencia y confianza de Grupo Le Parc</h2>
            <div class="wecar-sell__stats">
                <article class="wecar-sell__stat">
                    <span class="wecar-sell__icon wecar-sell__icon--large"><img src="<?php echo esc_url($vende_asset_uri . 'icon-medal.svg'); ?>" alt=""></span>
                    <strong>+25 años</strong>
                    <span>de experiencia</span>
                </article>
                <article class="wecar-sell__stat">
                    <span class="wecar-sell__icon wecar-sell__icon--large"><img src="<?php echo esc_url($vende_asset_uri . 'icon-car-stat.svg'); ?>" alt=""></span>
                    <strong>+80</strong>
                    <span>autos vendidos por mes</span>
                </article>
                <article class="wecar-sell__stat">
                    <span class="wecar-sell__icon wecar-sell__icon--large"><img src="<?php echo esc_url($vende_asset_uri . 'icon-chart.svg'); ?>" alt=""></span>
                    <strong>+3000</strong>
                    <span>consultas por mes</span>
                </article>
                <article class="wecar-sell__stat wecar-sell__stat--clients">
                    <span class="wecar-sell__icon wecar-sell__icon--large"><img src="<?php echo esc_url($vende_asset_uri . 'icon-heart.svg'); ?>" alt=""></span>
                    <strong>Miles de clientes</strong>
                    <span>Satisfechos</span>
                </article>
            </div>
        </div>
    </section>

    <section class="wecar-sell__section wecar-sell__comparison" aria-labelledby="wecar-comparison-title">
        <img class="wecar-sell__wave wecar-sell__wave--table-tl" src="<?php echo esc_url($vende_asset_uri . 'wave-table-tl.svg'); ?>" alt="" aria-hidden="true">
        <img class="wecar-sell__wave wecar-sell__wave--table-br" src="<?php echo esc_url($vende_asset_uri . 'wave-table-br.svg'); ?>" alt="" aria-hidden="true">
        <div class="wecar-sell__container">
            <h2 id="wecar-comparison-title">¿Qué incluye vender con Wecar?</h2>
            <div class="wecar-sell__table-scroll" tabindex="0" role="region" aria-label="Comparación de opciones de venta">
                <table class="wecar-sell__table">
                    <thead>
                        <tr>
                            <th scope="col">Características</th>
                            <th scope="col">
                                <span class="wecar-sell__table-logo" aria-label="WeCar">
                                    <img class="wecar-sell__table-mark" src="<?php echo esc_url($vende_asset_uri . 'logo-table-mark.svg'); ?>" alt="">
                                    <img class="wecar-sell__table-word" src="<?php echo esc_url($vende_asset_uri . 'logo-table-word.svg'); ?>" alt="">
                                </span>
                            </th>
                            <th scope="col">Venta particular</th>
                            <th scope="col">Concesionaria</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <th scope="row">Comisión del servicio</th>
                            <td>7% sobre venta final</td>
                            <td>Sin costo (tiempo propio)</td>
                            <td>15 -20% de descuento</td>
                        </tr>
                        <tr>
                            <th scope="row">Seguís usando tu auto</th>
                            <td><img src="<?php echo esc_url($vende_asset_uri . 'icon-check-violet.svg'); ?>" alt="">Sí, siempre</td>
                            <td><img src="<?php echo esc_url($vende_asset_uri . 'icon-check-dark.svg'); ?>" alt="">Sí</td>
                            <td><img src="<?php echo esc_url($vende_asset_uri . 'icon-x-dark.svg'); ?>" alt="">Lo dejás en consignación</td>
                        </tr>
                        <tr>
                            <th scope="row">Publicación y difusión</th>
                            <td><img src="<?php echo esc_url($vende_asset_uri . 'icon-check-violet.svg'); ?>" alt="">WeCar se encarga</td>
                            <td><img src="<?php echo esc_url($vende_asset_uri . 'icon-x-dark.svg'); ?>" alt="">A cargo del vendedor</td>
                            <td><img src="<?php echo esc_url($vende_asset_uri . 'icon-check-dark.svg'); ?>" alt="">La concesionaria</td>
                        </tr>
                        <tr>
                            <th scope="row">Peritaje estético/mecánico</th>
                            <td><img src="<?php echo esc_url($vende_asset_uri . 'icon-check-violet.svg'); ?>" alt="">Incluido • sin costo</td>
                            <td><img src="<?php echo esc_url($vende_asset_uri . 'icon-x-dark.svg'); ?>" alt="">No incluido</td>
                            <td><img src="<?php echo esc_url($vende_asset_uri . 'icon-check-dark.svg'); ?>" alt="">Sí</td>
                        </tr>
                        <tr>
                            <th scope="row">Seguridad del cobro</th>
                            <td><img src="<?php echo esc_url($vende_asset_uri . 'icon-check-violet.svg'); ?>" alt="">Garantizado</td>
                            <td><img src="<?php echo esc_url($vende_asset_uri . 'icon-x-dark.svg'); ?>" alt="">Riesgo de estafa</td>
                            <td><img src="<?php echo esc_url($vende_asset_uri . 'icon-check-dark.svg'); ?>" alt="">Sí</td>
                        </tr>
                        <tr>
                            <th scope="row">Gestión de papeles</th>
                            <td><img src="<?php echo esc_url($vende_asset_uri . 'icon-check-violet.svg'); ?>" alt="">WeCar se encarga</td>
                            <td><img src="<?php echo esc_url($vende_asset_uri . 'icon-x-dark.svg'); ?>" alt="">A cargo del vendedor</td>
                            <td><img src="<?php echo esc_url($vende_asset_uri . 'icon-check-dark.svg'); ?>" alt="">Sí</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </section>

    <section class="wecar-sell__section wecar-sell__benefits" aria-labelledby="wecar-benefits-title">
        <div class="wecar-sell__container wecar-sell__benefits-layout">
            <div class="wecar-sell__benefits-copy">
                <h2 id="wecar-benefits-title">Una forma más inteligente de vender tu auto</h2>
                <p>Publicá tu vehículo con respaldo profesional y seguí utilizándolo con normalidad mientras gestionamos todo el proceso de venta.</p>
            </div>
            <div class="wecar-sell__benefit-grid">
                <div class="wecar-sell__benefit"><span class="wecar-sell__icon"><img src="<?php echo esc_url($vende_asset_uri . 'icon-car.svg'); ?>" alt=""></span><p>Seguís usando tu auto hasta venderlo</p></div>
                <div class="wecar-sell__benefit"><span class="wecar-sell__icon"><img src="<?php echo esc_url($vende_asset_uri . 'icon-target.svg'); ?>" alt=""></span><p>Exposición a compradores reales</p></div>
                <div class="wecar-sell__benefit"><span class="wecar-sell__icon"><img src="<?php echo esc_url($vende_asset_uri . 'icon-dollar.svg'); ?>" alt=""></span><p>Tasación real de mercado</p></div>
                <div class="wecar-sell__benefit"><span class="wecar-sell__icon"><img src="<?php echo esc_url($vende_asset_uri . 'icon-handshake.svg'); ?>" alt=""></span><p>Gestión integral de consultas</p></div>
                <div class="wecar-sell__benefit"><span class="wecar-sell__icon"><img src="<?php echo esc_url($vende_asset_uri . 'icon-search.svg'); ?>" alt=""></span><p>Peritaje profesional</p></div>
                <div class="wecar-sell__benefit"><span class="wecar-sell__icon"><img src="<?php echo esc_url($vende_asset_uri . 'icon-shield-violet.svg'); ?>" alt=""></span><p>Proceso más seguro y acompañado</p></div>
                <div class="wecar-sell__benefit wecar-sell__benefit--wide"><span class="wecar-sell__icon"><img src="<?php echo esc_url($vende_asset_uri . 'icon-globe.svg'); ?>" alt=""></span><p>Publicación en la web oficial</p></div>
            </div>
        </div>
    </section>

    <?php get_template_part('template-parts/components/wecar-faq', null, ['id_prefix' => 'wecar-sell-faq']); ?>

    <section class="wecar-sell__section wecar-sell__final-cta" aria-labelledby="wecar-final-title">
        <div class="wecar-sell__container">
            <div class="wecar-sell__final-card">
                <h2 id="wecar-final-title">Completá el formulario<br>y cotiza tu usado</h2>
                <a class="wecar-sell__button wecar-sell__button--secondary" href="<?php echo esc_url($cotiza_url); ?>">Ir a cotizar</a>
            </div>
        </div>
    </section>
</main>

<?php
get_footer();
