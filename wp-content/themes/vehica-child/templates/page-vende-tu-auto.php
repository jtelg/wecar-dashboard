<?php
/**
 * Template Name: WeCar - Vendé tu auto
 * Template Post Type: page
 */

if (!defined('ABSPATH')) {
    exit;
}

$vende_asset_uri = get_stylesheet_directory_uri() . '/assets/images/vende-tu-auto/';
$cotiza_url = home_url('/cotiza/');
$faq_tabs = [
    'process' => [
        'label' => 'Sobre el proceso',
        'items' => [
            ['¿Cómo funciona la concesión virtual?', 'Nos traés los datos de tu auto, un asesor lo perita, y si pasa el control técnico lo publicamos nosotros en la plataforma. Vos seguís usando el auto con normalidad mientras nosotros nos encargamos de encontrar al comprador, gestionar las consultas у cerrar la venta.'],
            ['¿Cuánto tiempo tarda en venderse?', 'Depende del modelo, el precio y el estado del vehículo, pero trabajamos para que cada publicación tenga el máximo alcance desde el primer día. En la consulta con tu asesor te vamos a dar una estimación realista según el mercado actual.'],
            ['¿Qué pasa si mi auto no supera el peritaje?', 'Te lo comunicamos con transparencia y te explicamos exactamente qué observaciones encontramos. En muchos casos son detalles menores que podés resolver antes de volver a presentarlo. El peritaje no es un obstáculo: es lo que garantiza que tu auto se venda bien y al precio justo.'],
            ['¿Tengo que hacer algo durante el proceso o se encargan ustedes de todo?', 'Prácticamente todo lo manejamos nosotros: la publicación, las fotos, las consultas de compradores interesados y la negociación. Tu participación es mínima: completar el formulario, asistir al peritaje y estar disponible cuando tengamos un comprador listo para cerrar.'],
        ],
    ],
    'car' => [
        'label' => 'Sobre el auto',
        'items' => [
            ['¿Puedo seguir usando mi auto mientras lo venden?', 'Si. Ese es justamente el modelo. No entregás el auto hasta que la venta esté cerrada. Podés seguir usándolo con total normalidad durante todo el proceso.'],
            ['¿Qué tipos de autos aceptan?', 'Trabajamos con autos particulares de todas las marcas y modelos. El peritaje técnico es el que determina si el vehículo está en condiciones de ser publicado. Completá el formulario y un asesor te va a confirmar si tu auto califica.'],
            ['¿El auto tiene que estar en perfecto estado?', 'No necesariamente. Evaluamos cada caso de forma individual. Lo importante es que el estado del vehículo sea consistente con el precio de publicación. Si hay detalles a resolver, tu asesor te lo va a indicar antes del peritaje.'],
            ['¿Qué pasa con la documentación del auto?', 'No necesitás tener todo resuelto antes de empezar. En la primera conversación con tu asesor revisamos juntos qué tenés y qué falta, y te orientamos sobre los pasos a seguir si hay algo pendiente.'],
        ],
    ],
    'price' => [
        'label' => 'Sobre el precio',
        'items' => [
            ['¿Quién fija el precio de venta?', 'Lo definimos juntos. Nuestro equipo hace una valuación basada en el mercado actual y las condiciones del vehículo, y te presentamos una propuesta. El precio final se acuerda con vos antes de publicar.'],
            ['¿Me van a ofrecer menos de lo que vale mi auto?', 'Nuestra valuación está basada en datos reales del mercado, no en un número arbitrario. El objetivo es que tu auto se venda al mejor precio posible en el menor tiempo posible. Un precio demasiado alto o demasiado bajo no le conviene a nadie.'],
            ['¿Cuándo y cómo recibo el dinero de la venta?', 'El pago se gestiona al momento del cierre de la operación. Tu asesor te va a explicar los detalles del proceso según el medio de pago acordado con el comprador.'],
        ],
    ],
    'brand' => [
        'label' => 'Sobre WeCar y Le Parc',
        'items' => [
            ['¿Por qué confiarles mi auto?', 'WeCar es el ecosistema digital de compra y venta de autos de Le Parc, una de las empresas con mayor trayectoria en el mercado automotor. Cada operación tiene el respaldo de esa experiencia, su red de compradores calificados y sus estándares técnicos. No somos un clasificado: somos el intermediario que se hace cargo del proceso completo.'],
            ['¿Tienen presencia física o es todo online?', 'Tenemos presencia tanto digital como física. El peritaje se realiza en persona, у podés contactarte con un asesor real en cualquier momento del proceso. La tecnología está para hacer el proceso más ágil, no para reemplazar la atención humana.'],
        ],
    ],
    'first' => [
        'label' => 'Nuca vendí un auto',
        'items' => [
            ['Nunca vendí un auto. ¿Por dónde empiezo?', 'Por el formulario de cotización. Te lleva menos de dos minutos completarlo y no necesitás saber nada de precios ni de trámites. Una vez que lo envias, un asesor te contacta y te explica todo paso a paso. No hay compromiso de ningún tipo al consultar.'],
            ['¿Qué pasa si me arrepiento y quiero cancelar?', 'Podés bajarte del proceso en cualquier momento antes del cierre de la venta. No hay penalidades ni compromisos atados al hecho de consultar o incluso de publicar.'],
        ],
    ],
];

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

    <section class="wecar-sell__section wecar-sell__faq" aria-labelledby="wecar-faq-title">
        <div class="wecar-sell__container">
            <h2 id="wecar-faq-title">Consultas que suelen hacernos</h2>
            <div class="wecar-sell__tabs" role="tablist" aria-label="Categorías de consultas">
                <?php foreach ($faq_tabs as $faq_key => $faq_tab) : ?>
                    <?php $faq_selected = $faq_key === 'process'; ?>
                    <button
                        type="button"
                        role="tab"
                        id="<?php echo esc_attr('wecar-tab-' . $faq_key); ?>"
                        aria-selected="<?php echo $faq_selected ? 'true' : 'false'; ?>"
                        aria-controls="<?php echo esc_attr('wecar-panel-' . $faq_key); ?>"
                        tabindex="<?php echo $faq_selected ? '0' : '-1'; ?>"
                    ><?php echo esc_html($faq_tab['label']); ?></button>
                <?php endforeach; ?>
            </div>
            <?php foreach ($faq_tabs as $faq_key => $faq_tab) : ?>
                <?php $faq_selected = $faq_key === 'process'; ?>
                <div
                    id="<?php echo esc_attr('wecar-panel-' . $faq_key); ?>"
                    class="wecar-sell__tabpanel"
                    role="tabpanel"
                    aria-labelledby="<?php echo esc_attr('wecar-tab-' . $faq_key); ?>"
                    aria-hidden="<?php echo $faq_selected ? 'false' : 'true'; ?>"
                    <?php echo $faq_selected ? '' : 'hidden'; ?>
                >
                    <div class="wecar-sell__accordion">
                        <?php foreach ($faq_tab['items'] as $faq_index => $faq_item) : ?>
                            <?php
                            $faq_id = 'wecar-faq-' . $faq_key . '-' . ($faq_index + 1);
                            $faq_question_id = $faq_id . '-question';
                            $faq_answer_id = $faq_id . '-answer';
                            ?>
                            <article class="wecar-sell__faq-item">
                                <h3>
                                    <button type="button" aria-expanded="true" aria-controls="<?php echo esc_attr($faq_answer_id); ?>" id="<?php echo esc_attr($faq_question_id); ?>">
                                        <span><?php echo esc_html($faq_item[0]); ?></span>
                                        <img src="<?php echo esc_url($vende_asset_uri . 'icon-chevron-violet.svg'); ?>" alt="">
                                    </button>
                                </h3>
                                <div
                                    id="<?php echo esc_attr($faq_answer_id); ?>"
                                    class="wecar-sell__faq-panel"
                                    role="region"
                                    aria-labelledby="<?php echo esc_attr($faq_question_id); ?>"
                                    aria-hidden="false"
                                >
                                    <p><?php echo esc_html($faq_item[1]); ?></p>
                                </div>
                            </article>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </section>

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
