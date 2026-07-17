<?php
/**
 * Patch to update home footer (section h07a001) to match Figma design.
 * 
 * Run with:
 * wp eval-file patch-home-footer-figma.php --path=/home/u2131-yaziskitlmmv/www/test.wecar.com.ar/public_html --allow-root
 */

if (!defined('ABSPATH')) {
    fwrite(STDERR, "WordPress must bootstrap this file.\n");
    exit(1);
}

$post_id = 35463;
$expected_home = 'https://test.wecar.com.ar';

function wecar_fail($message) {
    throw new RuntimeException($message);
}

function wecar_json($value) {
    $json = wp_json_encode($value, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    if (!is_string($json)) {
        wecar_fail('JSON serialization failed.');
    }
    return $json;
}

function wecar_footer_section() {
    return array(
        'id' => 'h07a001',
        'elType' => 'section',
        'settings' => array(
            'stretch_section' => 'section-stretched',
            'background_background' => 'classic',
            'background_color' => '#FFFFFF',
            'padding' => array('unit' => 'px', 'top' => '0', 'right' => '0', 'bottom' => '0', 'left' => '0', 'isLinked' => true),
            '_element_id' => 'wecar-footer',
            'css_classes' => 'wecar-footer',
        ),
        'elements' => array(
            // Row 1: Logo (left) + Description (right)
            array(
                'id' => 'h07c001',
                'elType' => 'column',
                'settings' => array('_column_size' => 50, '_inline_size' => null),
                'elements' => array(
                    array(
                        'id' => 'h07w001',
                        'elType' => 'widget',
                        'widgetType' => 'image',
                        'settings' => array(
                            'image' => array(
                                'url' => '/wp-content/themes/vehica-child/assets/images/wecar-isotype.svg',
                                'id' => '',
                                'size' => 'full',
                                'alt' => 'WeCar'
                            ),
                            'align' => 'left',
                            'width' => array('unit' => 'px', 'size' => 316),
                            '_css_classes' => 'wecar-footer__logo',
                        ),
                    ),
                ),
            ),
            array(
                'id' => 'h07c002',
                'elType' => 'column',
                'settings' => array('_column_size' => 50, '_inline_size' => null),
                'elements' => array(
                    array(
                        'id' => 'h07w002',
                        'elType' => 'widget',
                        'widgetType' => 'text-editor',
                        'settings' => array(
                            'editor' => '<p class="wecar-footer__description">Una plataforma moderna y segura para la compra y venta de vehículos nuevos y usados. Formamos parte de Grupo Le Parc.</p>',
                            'align' => 'right',
                            '_css_classes' => 'wecar-footer__description-wrapper',
                        ),
                    ),
                ),
            ),
            // Row 2: Copyright (left) + Phone (right)
            array(
                'id' => 'h07c003',
                'elType' => 'column',
                'settings' => array('_column_size' => 50, '_inline_size' => null),
                'elements' => array(
                    array(
                        'id' => 'h07w003',
                        'elType' => 'widget',
                        'widgetType' => 'text-editor',
                        'settings' => array(
                            'editor' => '<p class="wecar-footer__copyright">2026 Custer. All rights reserved.</p>',
                            'align' => 'left',
                        ),
                    ),
                ),
            ),
            array(
                'id' => 'h07c004',
                'elType' => 'column',
                'settings' => array('_column_size' => 50, '_inline_size' => null),
                'elements' => array(
                    array(
                        'id' => 'h07w004',
                        'elType' => 'widget',
                        'widgetType' => 'text-editor',
                        'settings' => array(
                            'editor' => '<p class="wecar-footer__phone"><a href="tel:+5493534413243">+54 9 3534 41-3243</a></p>',
                            'align' => 'right',
                        ),
                    ),
                ),
            ),
        ),
    );
}

try {
    if (untrailingslashit((string)get_option('home')) !== $expected_home) {
        wecar_fail('Refusing to run outside TEST.');
    }
    if ((int) get_option('page_on_front') !== $post_id) {
        wecar_fail('Post 35463 is not the configured TEST front page.');
    }

    global $wpdb;
    $original_raw = $wpdb->get_var($wpdb->prepare(
        "SELECT meta_value FROM {$wpdb->postmeta} WHERE post_id=%d AND meta_key=%s",
        $post_id,
        '_elementor_data'
    ));

    if (!$original_raw) {
        wecar_fail('Could not find Elementor data for post 35463.');
    }

    $document = json_decode($original_raw, true, 512, JSON_THROW_ON_ERROR);
    if (!is_array($document)) {
        wecar_fail('Elementor document is not an array.');
    }

    // Find footer section
    $footer_index = null;
    foreach ($document as $index => $section) {
        if (isset($section['id']) && $section['id'] === 'h07a001') {
            $footer_index = $index;
            break;
        }
    }

    if ($footer_index === null) {
        wecar_fail('Footer section h07a001 not found.');
    }

    // Replace footer section
    $document[$footer_index] = wecar_footer_section();
    $new_raw = wecar_json($document);

    $updated = $wpdb->update(
        $wpdb->postmeta,
        array('meta_value' => $new_raw),
        array('post_id' => $post_id, 'meta_key' => '_elementor_data')
    );

    if ($updated === false) {
        wecar_fail('Failed to update Elementor data.');
    }

    // Clear cache
    clean_post_cache($post_id);
    delete_post_meta($post_id, '_elementor_element_cache');

    WP_CLI::success('Footer section updated successfully.');

} catch (Throwable $error) {
    WP_CLI::error($error->getMessage());
}
