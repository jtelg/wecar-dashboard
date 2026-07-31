<?php
/**
 * Shared WeCar FAQ component.
 *
 * @var array $args {
 *     @type string $id_prefix Unique prefix for all component IDs.
 * }
 */

if (!defined('ABSPATH')) {
    exit;
}

$id_prefix = isset($args['id_prefix']) ? sanitize_html_class((string) $args['id_prefix']) : '';
if ($id_prefix === '') {
    $id_prefix = sanitize_html_class(wp_unique_id('wecar-faq-'));
}

$faq_tabs = [
    'process' => [
        'label' => 'Sobre el proceso',
        'items' => [
            ['¿Cómo funciona la concesión virtual?', 'Nos traés los datos de tu auto, un asesor lo perita, y si pasa el control técnico lo publicamos nosotros en la plataforma. Vos seguís usando el auto con normalidad mientras nosotros nos encargamos de encontrar al comprador, gestionar las consultas y cerrar la venta.'],
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
            ['¿Tienen presencia física o es todo online?', 'Tenemos presencia tanto digital como física. El peritaje se realiza en persona, y podés contactarte con un asesor real en cualquier momento del proceso. La tecnología está para hacer el proceso más ágil, no para reemplazar la atención humana.'],
        ],
    ],
    'first' => [
        'label' => 'Nunca vendí un auto',
        'items' => [
            ['Nunca vendí un auto. ¿Por dónde empiezo?', 'Por el formulario de cotización. Te lleva menos de dos minutos completarlo y no necesitás saber nada de precios ni de trámites. Una vez que lo envias, un asesor te contacta y te explica todo paso a paso. No hay compromiso de ningún tipo al consultar.'],
            ['¿Qué pasa si me arrepiento y quiero cancelar?', 'Podés bajarte del proceso en cualquier momento antes del cierre de la venta. No hay penalidades ni compromisos atados al hecho de consultar o incluso de publicar.'],
        ],
    ],
];

$heading_id = $id_prefix . '-heading';
$icon_path = get_stylesheet_directory() . '/assets/images/shared/icon-chevron-violet.svg';
$icon_uri = get_stylesheet_directory_uri() . '/assets/images/shared/icon-chevron-violet.svg';
$icon_version = file_exists($icon_path) ? filemtime($icon_path) : false;

if ($icon_version !== false) {
    $icon_uri = add_query_arg('ver', (string) $icon_version, $icon_uri);
}
?>
<section class="wecar-faq" data-wecar-faq aria-labelledby="<?php echo esc_attr($heading_id); ?>">
    <div class="wecar-faq__container">
        <h2 id="<?php echo esc_attr($heading_id); ?>">Consultas que suelen hacernos</h2>
        <div class="wecar-faq__tabs" role="tablist" aria-label="Categorías de consultas">
            <?php foreach ($faq_tabs as $faq_key => $faq_tab) : ?>
                <?php $is_selected = $faq_key === 'process'; ?>
                <button type="button" role="tab" id="<?php echo esc_attr($id_prefix . '-tab-' . $faq_key); ?>" aria-selected="<?php echo $is_selected ? 'true' : 'false'; ?>" aria-controls="<?php echo esc_attr($id_prefix . '-panel-' . $faq_key); ?>" tabindex="<?php echo $is_selected ? '0' : '-1'; ?>"><?php echo esc_html($faq_tab['label']); ?></button>
            <?php endforeach; ?>
        </div>
        <?php foreach ($faq_tabs as $faq_key => $faq_tab) : ?>
            <?php $is_selected = $faq_key === 'process'; ?>
            <div id="<?php echo esc_attr($id_prefix . '-panel-' . $faq_key); ?>" class="wecar-faq__tabpanel" role="tabpanel" aria-labelledby="<?php echo esc_attr($id_prefix . '-tab-' . $faq_key); ?>" aria-hidden="<?php echo $is_selected ? 'false' : 'true'; ?>" <?php echo $is_selected ? '' : 'hidden'; ?>>
                <div class="wecar-faq__accordion">
                    <?php foreach ($faq_tab['items'] as $faq_index => $faq_item) : ?>
                        <?php
                        $item_id = $id_prefix . '-' . $faq_key . '-' . ($faq_index + 1);
                        $question_id = $item_id . '-question';
                        $answer_id = $item_id . '-answer';
                        ?>
                        <article class="wecar-faq__item">
                            <h3><button type="button" id="<?php echo esc_attr($question_id); ?>" aria-expanded="false" aria-controls="<?php echo esc_attr($answer_id); ?>"><span><?php echo esc_html($faq_item[0]); ?></span><img src="<?php echo esc_url($icon_uri); ?>" alt="" width="22" height="22"></button></h3>
                            <div id="<?php echo esc_attr($answer_id); ?>" class="wecar-faq__panel" role="region" aria-labelledby="<?php echo esc_attr($question_id); ?>" aria-hidden="true" hidden><p><?php echo esc_html($faq_item[1]); ?></p></div>
                        </article>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</section>
