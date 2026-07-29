<?php
/**
 * Template Name: WeCar - Cotizador
 * Template Post Type: page
 */

if (!defined('ABSPATH')) {
    exit;
}

$asset_uri = get_stylesheet_directory_uri() . '/assets/images/cotizador/';
$whatsapp_url = esc_url_raw((string) apply_filters('wecar/cotizador/whatsapp_url', ''));

get_header();
?>

<main id="wecar-cotizador" class="wecar-cotizador" aria-labelledby="wecar-cotizador-title">
    <section class="wecar-cotizador__hero" aria-labelledby="wecar-cotizador-title">
        <img class="wecar-cotizador__texture wecar-cotizador__texture--hero-left" src="<?php echo esc_url($asset_uri . 'hero-texture-left.svg'); ?>" alt="" aria-hidden="true" width="669" height="316">
        <img class="wecar-cotizador__texture wecar-cotizador__texture--hero-right" src="<?php echo esc_url($asset_uri . 'hero-texture-right.svg'); ?>" alt="" aria-hidden="true" width="669" height="316">
        <div class="wecar-cotizador__container wecar-cotizador__hero-inner">
            <h1 id="wecar-cotizador-title">Estas muy cerca<br>de vender<br class="wecar-cotizador__br--mobile">tu auto</h1>
        </div>
    </section>

    <section class="wecar-cotizador__qualification" aria-labelledby="wecar-cotizador-qualification-title" data-wecar-qualification tabindex="0">
        <div class="wecar-cotizador__qualification-inner">
            <div class="wecar-cotizador__heading">
                <h2 id="wecar-cotizador-qualification-title">¿Tu auto califica para vender con Wecar?</h2>
                <p>Trabajamos con vehículos que cumplen estos criterios de calidad</p>
            </div>
            <div class="wecar-cotizador__qualification-cards">
                <article class="wecar-cotizador__qualification-card wecar-cotizador__qualification-card--violet">
                    <strong>2010</strong><h3>Año del vehiculo</h3><p>Aceptamos modelos del año 2010 en adelante</p>
                </article>
                <article class="wecar-cotizador__qualification-card wecar-cotizador__qualification-card--blue">
                    <span>Hasta</span><strong data-wecar-blue-km>120.000 km</strong><h3>Autos y SUVs</h3><p>Kilómetros máximos para autos de pasajeros</p>
                </article>
                <article class="wecar-cotizador__qualification-card wecar-cotizador__qualification-card--cyan">
                    <span>Hasta</span><strong>150.000 km</strong><h3>Pick-Ups</h3><p>Kilómetros máximos para camionetas y pickups</p>
                </article>
            </div>
            <p class="wecar-cotizador__qualification-copy">¿Tu auto no cumple estos requisitos? Puede que tengamos otras opciones para vos</p>
            <?php if ($whatsapp_url !== '') : ?>
                <a class="wecar-cotizador__qualification-cta" href="<?php echo esc_url($whatsapp_url); ?>" target="_blank" rel="noopener noreferrer">Escribinos por Whatsapp</a>
            <?php else : ?>
                <span class="wecar-cotizador__qualification-cta" aria-disabled="true">Escribinos por Whatsapp</span>
            <?php endif; ?>
        </div>
    </section>

    <section class="wecar-cotizador__video-section" aria-label="Conocé cómo funciona WeCar">
        <div class="wecar-cotizador__container">
            <div class="wecar-cotizador__video">
                <img class="wecar-cotizador__video-poster" src="<?php echo esc_url($asset_uri . 'video-poster.png'); ?>" alt="" width="1216" height="774">
                <span class="wecar-cotizador__video-overlay" aria-hidden="true"></span>
                <img class="wecar-cotizador__play" src="<?php echo esc_url($asset_uri . 'play.svg'); ?>" alt="" aria-hidden="true" width="60" height="60">
            </div>
        </div>
    </section>

    <section class="wecar-cotizador__steps" aria-labelledby="wecar-cotizador-steps-title">
        <img class="wecar-cotizador__texture wecar-cotizador__texture--steps-left" src="<?php echo esc_url($asset_uri . 'steps-texture-left.svg'); ?>" alt="" aria-hidden="true" width="669" height="316">
        <img class="wecar-cotizador__texture wecar-cotizador__texture--steps-right" src="<?php echo esc_url($asset_uri . 'steps-texture-right.svg'); ?>" alt="" aria-hidden="true" width="669" height="316">
        <div class="wecar-cotizador__steps-card">
            <div class="wecar-cotizador__heading">
                <h2 id="wecar-cotizador-steps-title">Cotizá tu auto en 3 pasos</h2>
                <p>Completá el formulario y te contactaremos con un asesor.</p>
            </div>
            <ol class="wecar-cotizador__step-list">
                <li class="wecar-cotizador__step">
                    <span class="wecar-cotizador__step-number">1</span>
                    <span class="wecar-cotizador__step-icon"><img src="<?php echo esc_url($asset_uri . 'icon-id-card.svg'); ?>" alt="" width="36" height="36"></span>
                    <h3>Tus datos</h3>
                    <img class="wecar-cotizador__step-divider" src="<?php echo esc_url($asset_uri . 'divider-1.svg'); ?>" alt="" aria-hidden="true" width="1" height="138">
                </li>
                <li class="wecar-cotizador__step">
                    <span class="wecar-cotizador__step-number">2</span>
                    <span class="wecar-cotizador__step-icon"><img src="<?php echo esc_url($asset_uri . 'icon-car.svg'); ?>" alt="" width="36" height="36"></span>
                    <h3>Datos del auto</h3>
                    <img class="wecar-cotizador__step-divider" src="<?php echo esc_url($asset_uri . 'divider-2.svg'); ?>" alt="" aria-hidden="true" width="1" height="138">
                </li>
                <li class="wecar-cotizador__step">
                    <span class="wecar-cotizador__step-number">3</span>
                    <span class="wecar-cotizador__step-icon"><img src="<?php echo esc_url($asset_uri . 'icon-search.svg'); ?>" alt="" width="36" height="36"></span>
                    <h3>Peritaje y sucursal</h3>
                </li>
            </ol>
            <button class="wecar-cotizador__primary" type="button" data-wecar-cotizador-open>Comenzar</button>
        </div>
    </section>

    <?php get_template_part('template-parts/components/wecar-faq', null, ['id_prefix' => 'wecar-quote-faq']); ?>
</main>

<div
    class="wecar-cotizador-modal"
    id="wecar-cotizador-modal"
    role="dialog"
    aria-modal="true"
    aria-labelledby="wecar-cotizador-step-title-1"
    hidden
>
    <div class="wecar-cotizador-modal__backdrop" data-wecar-cotizador-close></div>
    <button
        class="wecar-cotizador-modal__close"
        type="button"
        aria-label="Cerrar cotizador"
        data-wecar-cotizador-close
    >
        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
            <path d="M18 6L6 18M6 6L18 18" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
    </button>
    <div class="wecar-cotizador-modal__card">
        <div class="wecar-cotizador-modal__progress" role="progressbar" aria-valuemin="1" aria-valuemax="3" aria-valuenow="1" aria-label="Progreso del cotizador">
            <span class="wecar-cotizador-modal__progress-bar wecar-cotizador-modal__progress-bar--active" data-wecar-progress="1"></span>
            <span class="wecar-cotizador-modal__progress-bar" data-wecar-progress="2"></span>
            <span class="wecar-cotizador-modal__progress-bar" data-wecar-progress="3"></span>
        </div>

        <div class="wecar-cotizador-modal__steps">
            <div class="wecar-cotizador-modal__step wecar-cotizador-modal__step--active" data-wecar-step="1" aria-hidden="false">
                <h2 id="wecar-cotizador-step-title-1" class="wecar-cotizador-modal__title" aria-live="polite">Contanos sobre vos</h2>
                <div class="wecar-cotizador-modal__body">
                    <div class="wecar-cotizador-modal__grid wecar-cotizador-modal__grid--2x2">
                        <div class="wecar-cotizador-modal__field">
                            <label class="wecar-cotizador-modal__field-label" for="wecar-cotizador-nombre">Nombre</label>
                            <input class="wecar-cotizador-modal__field-input" id="wecar-cotizador-nombre" name="nombre" type="text" placeholder="Ingresá tu nombre" data-wecar-field="nombre" autocomplete="name" aria-describedby="wecar-cotizador-error-nombre">
                            <span class="wecar-cotizador-modal__field-error" id="wecar-cotizador-error-nombre" data-wecar-error="nombre"></span>
                        </div>
                        <div class="wecar-cotizador-modal__field">
                            <label class="wecar-cotizador-modal__field-label" for="wecar-cotizador-mail">Mail</label>
                            <input class="wecar-cotizador-modal__field-input" id="wecar-cotizador-mail" name="mail" type="email" placeholder="Ingresá tu mail" data-wecar-field="mail" autocomplete="email" aria-describedby="wecar-cotizador-error-mail">
                            <span class="wecar-cotizador-modal__field-error" id="wecar-cotizador-error-mail" data-wecar-error="mail"></span>
                        </div>
                        <div class="wecar-cotizador-modal__field">
                            <label class="wecar-cotizador-modal__field-label" for="wecar-cotizador-telefono">Teléfono</label>
                            <input class="wecar-cotizador-modal__field-input" id="wecar-cotizador-telefono" name="telefono" type="tel" placeholder="Ingresá tu teléfono sin el 15" data-wecar-field="telefono" autocomplete="tel" aria-describedby="wecar-cotizador-error-telefono">
                            <span class="wecar-cotizador-modal__field-error" id="wecar-cotizador-error-telefono" data-wecar-error="telefono"></span>
                        </div>
                        <div class="wecar-cotizador-modal__field">
                            <label class="wecar-cotizador-modal__field-label" for="wecar-cotizador-localidad">Localidad</label>
                            <input class="wecar-cotizador-modal__field-input" id="wecar-cotizador-localidad" name="localidad" type="text" placeholder="Ingresá tu localidad" data-wecar-field="localidad" autocomplete="address-level2" aria-describedby="wecar-cotizador-error-localidad">
                            <span class="wecar-cotizador-modal__field-error" id="wecar-cotizador-error-localidad" data-wecar-error="localidad"></span>
                        </div>
                    </div>
                    <div class="wecar-cotizador-modal__actions">
                        <button class="wecar-cotizador-modal__action-btn" type="button" data-wecar-action="next" disabled>Continuar</button>
                    </div>
                </div>
            </div>
            <div class="wecar-cotizador-modal__step" data-wecar-step="2" aria-hidden="true">
                <h2 id="wecar-cotizador-step-title-2" class="wecar-cotizador-modal__title" aria-live="polite">Datos del auto</h2>
                <div class="wecar-cotizador-modal__body">
                    <div class="wecar-cotizador-modal__grid wecar-cotizador-modal__grid--2x2">
                        <div class="wecar-cotizador-modal__field">
                            <label class="wecar-cotizador-modal__field-label" for="wecar-cotizador-anio">Año</label>
                            <input class="wecar-cotizador-modal__field-input" id="wecar-cotizador-anio" name="anio" type="text" inputmode="numeric" placeholder="Ingresá año" data-wecar-field="anio" aria-describedby="wecar-cotizador-error-anio">
                            <span class="wecar-cotizador-modal__field-error" id="wecar-cotizador-error-anio" data-wecar-error="anio"></span>
                        </div>
                        <div class="wecar-cotizador-modal__field">
                            <label class="wecar-cotizador-modal__field-label" for="wecar-cotizador-marca">Marca</label>
                            <input class="wecar-cotizador-modal__field-input" id="wecar-cotizador-marca" name="marca" type="text" placeholder="Ingresá la marca" data-wecar-field="marca" aria-describedby="wecar-cotizador-error-marca">
                            <span class="wecar-cotizador-modal__field-error" id="wecar-cotizador-error-marca" data-wecar-error="marca"></span>
                        </div>
                        <div class="wecar-cotizador-modal__field">
                            <label class="wecar-cotizador-modal__field-label" for="wecar-cotizador-modelo">Modelo</label>
                            <input class="wecar-cotizador-modal__field-input" id="wecar-cotizador-modelo" name="modelo" type="text" placeholder="Ingresá el modelo" data-wecar-field="modelo" aria-describedby="wecar-cotizador-error-modelo">
                            <span class="wecar-cotizador-modal__field-error" id="wecar-cotizador-error-modelo" data-wecar-error="modelo"></span>
                        </div>
                        <div class="wecar-cotizador-modal__field">
                            <label class="wecar-cotizador-modal__field-label" for="wecar-cotizador-kilometros">Kilómetros</label>
                            <input class="wecar-cotizador-modal__field-input" id="wecar-cotizador-kilometros" name="kilometros" type="text" inputmode="decimal" placeholder="Ingresá los kilometros" data-wecar-field="kilometros" aria-describedby="wecar-cotizador-error-kilometros">
                            <span class="wecar-cotizador-modal__field-error" id="wecar-cotizador-error-kilometros" data-wecar-error="kilometros"></span>
                        </div>
                    </div>
                    <div class="wecar-cotizador-modal__field">
                        <span class="wecar-cotizador-modal__field-label" id="wecar-cotizador-gnc-label">¿Tiene o tuvo GNC?</span>
                        <div class="wecar-cotizador-modal__radio-group" role="radiogroup" aria-labelledby="wecar-cotizador-gnc-label" aria-describedby="wecar-cotizador-error-gnc">
                            <button class="wecar-cotizador-modal__radio" type="button" role="radio" aria-checked="false" data-wecar-field="gnc" data-wecar-value="instalado">
                                <img class="wecar-cotizador-modal__radio-icon" src="<?php echo esc_url($asset_uri . 'icon-radio.svg'); ?>" data-wecar-unchecked-src="<?php echo esc_url($asset_uri . 'icon-radio.svg'); ?>" data-wecar-checked-src="<?php echo esc_url($asset_uri . 'icon-radio-checked.svg'); ?>" alt="" aria-hidden="true" width="20" height="20">
                                <span class="wecar-cotizador-modal__radio-label">Sí, tiene instalado GNC</span>
                            </button>
                            <button class="wecar-cotizador-modal__radio" type="button" role="radio" aria-checked="false" data-wecar-field="gnc" data-wecar-value="tuvo">
                                <img class="wecar-cotizador-modal__radio-icon" src="<?php echo esc_url($asset_uri . 'icon-radio.svg'); ?>" data-wecar-unchecked-src="<?php echo esc_url($asset_uri . 'icon-radio.svg'); ?>" data-wecar-checked-src="<?php echo esc_url($asset_uri . 'icon-radio-checked.svg'); ?>" alt="" aria-hidden="true" width="20" height="20">
                                <span class="wecar-cotizador-modal__radio-label">No, pero tuvo anteriormente</span>
                            </button>
                            <button class="wecar-cotizador-modal__radio" type="button" role="radio" aria-checked="false" data-wecar-field="gnc" data-wecar-value="nunca">
                                <img class="wecar-cotizador-modal__radio-icon" src="<?php echo esc_url($asset_uri . 'icon-radio.svg'); ?>" data-wecar-unchecked-src="<?php echo esc_url($asset_uri . 'icon-radio.svg'); ?>" data-wecar-checked-src="<?php echo esc_url($asset_uri . 'icon-radio-checked.svg'); ?>" alt="" aria-hidden="true" width="20" height="20">
                                <span class="wecar-cotizador-modal__radio-label">No, nunca tuvo GNC</span>
                            </button>
                        </div>
                        <span class="wecar-cotizador-modal__field-error" id="wecar-cotizador-error-gnc" data-wecar-error="gnc"></span>
                    </div>
                    <div class="wecar-cotizador-modal__actions">
                        <button class="wecar-cotizador-modal__back-btn" type="button" data-wecar-action="back">Volver</button>
                        <button class="wecar-cotizador-modal__action-btn" type="button" data-wecar-action="next" disabled>Continuar</button>
                    </div>
                </div>
            </div>
            <div class="wecar-cotizador-modal__step" data-wecar-step="3" aria-hidden="true">
                <h2 id="wecar-cotizador-step-title-3" class="wecar-cotizador-modal__title" aria-live="polite">Peritaje y sucursal</h2>
                <div class="wecar-cotizador-modal__body">
                    <p class="wecar-cotizador-modal__subtitle">Recordá que la inspección es <strong>gratuita y sin compromiso de venta</strong></p>
                    <div class="wecar-cotizador-modal__grid wecar-cotizador-modal__grid--2x2">
                        <div class="wecar-cotizador-modal__field">
                            <span class="wecar-cotizador-modal__field-label" id="wecar-cotizador-dia-label">Día</span>
                            <div class="wecar-cotizador-modal__pills" role="group" aria-labelledby="wecar-cotizador-dia-label" aria-describedby="wecar-cotizador-error-dia">
                                <button class="wecar-cotizador-modal__pill" aria-pressed="false" type="button" data-wecar-field="dia" data-wecar-value="Lun">Lun</button>
                                <button class="wecar-cotizador-modal__pill" aria-pressed="false" type="button" data-wecar-field="dia" data-wecar-value="Mar">Mar</button>
                                <button class="wecar-cotizador-modal__pill" aria-pressed="false" type="button" data-wecar-field="dia" data-wecar-value="Mie">Mie</button>
                                <button class="wecar-cotizador-modal__pill" aria-pressed="false" type="button" data-wecar-field="dia" data-wecar-value="Jue">Jue</button>
                                <button class="wecar-cotizador-modal__pill" aria-pressed="false" type="button" data-wecar-field="dia" data-wecar-value="Vie">Vie</button>
                            </div>
                            <span class="wecar-cotizador-modal__field-error" id="wecar-cotizador-error-dia" data-wecar-error="dia"></span>
                        </div>
                        <div class="wecar-cotizador-modal__field">
                            <span class="wecar-cotizador-modal__field-label" id="wecar-cotizador-horario-label">Horario</span>
                            <div class="wecar-cotizador-modal__pills" role="group" aria-labelledby="wecar-cotizador-horario-label" aria-describedby="wecar-cotizador-error-horario">
                                <button class="wecar-cotizador-modal__pill" aria-pressed="false" type="button" data-wecar-field="horario" data-wecar-value="Mañana">Mañana</button>
                                <button class="wecar-cotizador-modal__pill" aria-pressed="false" type="button" data-wecar-field="horario" data-wecar-value="Tarde">Tarde</button>
                            </div>
                            <span class="wecar-cotizador-modal__field-error" id="wecar-cotizador-error-horario" data-wecar-error="horario"></span>
                        </div>
                    </div>
                    <div class="wecar-cotizador-modal__location">
                        <span class="wecar-cotizador-modal__location-label">Peritá tu auto en</span>
                        <span class="wecar-cotizador-modal__location-address">RN9 Km 554, X5900 Villa María, Córdoba</span>
                    </div>
                    <div class="wecar-cotizador-modal__map">
                        <img class="wecar-cotizador-modal__map-image" src="<?php echo esc_url($asset_uri . 'map-villa-maria.png'); ?>" alt="Mapa de la sucursal WeCar en Villa María, Córdoba" width="600" height="240">
                        <div class="wecar-cotizador-modal__map-tooltip" aria-hidden="true">
                            <span class="wecar-cotizador-modal__map-tooltip-text">Este es el punto de peritaje</span>
                            <svg class="wecar-cotizador-modal__map-tooltip-arrow" width="10" height="10" viewBox="0 0 10 10" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                                <path d="M5 10L0 0h10L5 10z" fill="#9949FF"/>
                            </svg>
                        </div>
                        <span class="wecar-cotizador-modal__map-pin" aria-hidden="true">
                            <img class="wecar-cotizador-modal__map-pin-icon" src="<?php echo esc_url($asset_uri . 'icon-pin.svg'); ?>" alt="" width="20" height="20">
                        </span>
                    </div>
                    <div class="wecar-cotizador-modal__notice">
                        <img class="wecar-cotizador-modal__notice-icon" src="<?php echo esc_url($asset_uri . 'icon-info.svg'); ?>" alt="" aria-hidden="true" width="16" height="16">
                        <p class="wecar-cotizador-modal__notice-text">Un asesor se pondrá en contacto con vos, para ayudarte en el proceso y confirmar tu turno para el peritaje.</p>
                    </div>
                    <div class="wecar-cotizador-modal__actions">
                        <button class="wecar-cotizador-modal__back-btn" type="button" data-wecar-action="back">Volver</button>
                        <button class="wecar-cotizador-modal__action-btn" type="button" data-wecar-action="submit" disabled>Enviar</button>
                    </div>
                    <div class="wecar-cotizador-modal__inline-error" data-wecar-inline-error aria-hidden="true" role="alert"></div>
                </div>
            </div>
            <!-- QA 6.1: verify success announcement via aria-live="assertive" and auto-close after 3s -->
            <div class="wecar-cotizador-modal__success" data-wecar-success aria-hidden="true" aria-live="assertive" aria-atomic="true">
                <div class="wecar-cotizador-modal__success-icon" aria-hidden="true">
                    <svg viewBox="0 0 64 64" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <circle cx="32" cy="32" r="30" stroke="#9949FF" stroke-width="2"/>
                        <path d="M20 34L28 42L44 24" stroke="#9949FF" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </div>
                <h2 id="wecar-cotizador-success-title" class="wecar-cotizador-modal__title">¡Cotización enviada!</h2>
                <p class="wecar-cotizador-modal__success-text">Te contactaremos a la brevedad</p>
            </div>
        </div>
    </div>
</div>

<?php get_footer(); ?>
