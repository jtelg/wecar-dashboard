<?php
/**
 * WeCar Vehicle Carousel — Shortcode
 *
 * Usage: [wecar_vehicle_carousel count="12"]
 *
 * Queries vehica_car posts that are published and active (taxonomy vehica_41301 = "activo").
 * Renders vehicle cards with: featured image, title, version, price, tags (year, transmission, fuel).
 * KM is not available in the data — shows "Consultar" placeholder.
 *
 * @package vehica-child
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Register the [wecar_vehicle_carousel] shortcode
 */
add_shortcode('wecar_vehicle_carousel', 'wecar_render_vehicle_carousel');

/**
 * Render the vehicle carousel
 *
 * @param array $atts Shortcode attributes
 * @return string HTML output
 */
function wecar_render_vehicle_carousel($atts = [])
{
    $atts = shortcode_atts([
        'count'  => 12,
        'status' => '', // Optional: filter by status taxonomy slug (e.g. 'activo')
    ], $atts, 'wecar_vehicle_carousel');

    $count = max(1, min(50, (int) $atts['count']));

    // Query vehica_car posts
    $args = [
        'post_type'      => 'vehica_car',
        'post_status'    => 'publish',
        'posts_per_page' => $count,
        'orderby'        => 'date',
        'order'          => 'DESC',
    ];

    // Optional status filter (taxonomy vehica_41301)
    $status_filter = trim($atts['status']);
    if (!empty($status_filter) && taxonomy_exists('vehica_41301')) {
        $args['tax_query'] = [
            [
                'taxonomy' => 'vehica_41301',
                'field'    => 'slug',
                'terms'    => $status_filter,
            ],
        ];
    }

    $query = new WP_Query($args);

    ob_start();

    if ($query->have_posts()) {
        echo '<div class="wecar-carousel">';
        while ($query->have_posts()) {
            $query->the_post();
            wecar_render_vehicle_card(get_the_ID());
        }
        echo '</div>';
        wp_reset_postdata();
    } else {
        // Empty state
        echo '<div class="wecar-carousel wecar-carousel--empty">';
        echo '<p class="wecar-carousel__empty-text">' . esc_html__('Próximamente verás los vehículos disponibles', 'vehica') . '</p>';
        echo '<a href="' . esc_url(home_url('/buscar/')) . '" class="wecar-carousel__empty-cta">' . esc_html__('Ver todos los autos', 'vehica') . '</a>';
        echo '</div>';
    }

    return ob_get_clean();
}

/**
 * Render a single vehicle card
 *
 * @param int $post_id Vehicle post ID
 */
function wecar_render_vehicle_card($post_id)
{
    $post_id = (int) $post_id;
    $title   = get_the_title($post_id);

    // Get first image from Vehica gallery meta (vehica_6673 = attachment IDs)
    $image_ids = get_post_meta($post_id, 'vehica_6673', true);
    if (!empty($image_ids)) {
        $ids = explode(',', $image_ids);
        $first_id = (int) trim($ids[0]);
        $image = wp_get_attachment_image_url($first_id, 'medium');
    } else {
        $image = get_the_post_thumbnail_url($post_id, 'medium');
    }

    if (!$image) {
        $image = esc_url(get_stylesheet_directory_uri() . '/assets/images/vehicle-placeholder.svg');
    }

    // Price from meta
    $price_raw = get_post_meta($post_id, 'vehica_currency_6656_2476', true);
    $price     = !empty($price_raw) ? 'ARS ' . number_format((int) $price_raw, 0, ',', '.') : '';

    // KM from meta
    $km_raw = get_post_meta($post_id, 'vehica_6664', true);
    $km     = !empty($km_raw) ? number_format((int) $km_raw, 0, ',', '.') . ' km' : 'Consultar KM';

    // Taxonomies
    $version       = wecar_get_term_name($post_id, 'vehica_19226');
    $year          = wecar_get_term_name($post_id, 'vehica_19270');
    $transmission  = wecar_get_term_name($post_id, 'vehica_6662');
    ?>
    <div class="wecar-vehicle-card">
        <div class="wecar-vehicle-card__image-wrap">
            <img
                class="wecar-vehicle-card__image"
                src="<?php echo esc_url($image); ?>"
                alt="<?php echo esc_attr($title); ?>"
                loading="lazy"
            />
        </div>

        <div class="wecar-vehicle-card__body">
            <h3 class="wecar-vehicle-card__title"><?php echo esc_html($title); ?></h3>

            <?php if ($version) : ?>
                <p class="wecar-vehicle-card__version"><?php echo esc_html($version); ?></p>
            <?php endif; ?>

            <?php if ($price) : ?>
                <p class="wecar-vehicle-card__price"><?php echo esc_html($price); ?></p>
            <?php endif; ?>

            <div class="wecar-vehicle-card__tags">
                <span class="wecar-vehicle-card__tag wecar-vehicle-card__tag--km">
                    <img src="<?php echo esc_url(get_stylesheet_directory_uri() . '/assets/images/ic-gauge.svg'); ?>" alt="" aria-hidden="true" class="wecar-vehicle-card__tag-icon">
                    <?php echo esc_html($km); ?>
                </span>

                <?php if ($year) : ?>
                    <span class="wecar-vehicle-card__tag wecar-vehicle-card__tag--year">
                        <img src="<?php echo esc_url(get_stylesheet_directory_uri() . '/assets/images/ic-calendar.svg'); ?>" alt="" aria-hidden="true" class="wecar-vehicle-card__tag-icon">
                        <?php echo esc_html($year); ?>
                    </span>
                <?php endif; ?>

                <?php if ($transmission) : ?>
                    <span class="wecar-vehicle-card__tag wecar-vehicle-card__tag--transmission">
                        <img src="<?php echo esc_url(get_stylesheet_directory_uri() . '/assets/images/ic-transmission.svg'); ?>" alt="" aria-hidden="true" class="wecar-vehicle-card__tag-icon">
                        <?php echo esc_html($transmission); ?>
                    </span>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <?php
}

/**
 * Get a single term name from a vehicle taxonomy
 *
 * @param int    $post_id  Post ID
 * @param string $taxonomy Taxonomy slug
 * @return string Term name or empty string
 */
function wecar_get_term_name($post_id, $taxonomy)
{
    $terms = wp_get_post_terms($post_id, $taxonomy, ['fields' => 'names']);
    if (!empty($terms) && !is_wp_error($terms)) {
        return $terms[0];
    }
    return '';
}
