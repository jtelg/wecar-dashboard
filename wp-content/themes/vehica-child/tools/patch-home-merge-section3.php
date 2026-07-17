<?php
/**
 * Patch to merge features (h05a001) and partners (h06a001) into ONE section
 * matching Figma node 28:624 (section-3 as a single frame).
 *
 * Run with:
 * wp eval-file patch-home-merge-section3.php --path=/home/u2131-yaziskitlmmv/www/test.wecar.com.ar/public_html --allow-root
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

function wecar_section3_merged() {
    $image_box = function ($column_id, $widget_id, $asset, $title, $modifier) {
        return array(
            'id' => $column_id,
            'elType' => 'column',
            'settings' => array('_column_size' => 33, '_inline_size' => null),
            'elements' => array(array(
                'id' => $widget_id,
                'elType' => 'widget',
                'widgetType' => 'image-box',
                'settings' => array(
                    'image' => array('url' => '/wp-content/themes/vehica-child/assets/images/' . $asset, 'id' => '', 'size' => 'full', 'alt' => ''),
                    'title_text' => $title,
                    'description_text' => '',
                    'title_size' => 'h3',
                    'position' => 'left',
                    'image_size' => array('unit' => 'px', 'size' => 42),
                    'text_align' => 'left',
                    '_css_classes' => 'wecar-features__card wecar-features__card--' . $modifier,
                ),
            )),
        );
    };

    $logo = function ($column_id, $widget_id, $asset, $alt, $width) {
        return array(
            'id' => $column_id,
            'elType' => 'column',
            'settings' => array('_column_size' => 33, '_inline_size' => null),
            'elements' => array(array(
                'id' => $widget_id,
                'elType' => 'widget',
                'widgetType' => 'image',
                'settings' => array(
                    'image' => array('url' => '/wp-content/themes/vehica-child/assets/images/' . $asset, 'id' => '', 'size' => 'full', 'alt' => $alt),
                    'align' => 'center',
                    'width' => array('unit' => 'px', 'size' => $width),
                    '_css_classes' => 'wecar-partners__logo',
                ),
            )),
        );
    };

    return array(
        'id' => 'h05a001',
        'elType' => 'section',
        'settings' => array(
            'stretch_section' => 'section-stretched',
            'background_background' => 'classic',
            'padding' => array('unit' => 'px', 'top' => '0', 'right' => '0', 'bottom' => '0', 'left' => '0', 'isLinked' => true),
            '_element_id' => 'wecar-section3',
            'css_classes' => 'wecar-features-section wecar-figma-28-624-v2',
            'background_color' => 'transparent',
        ),
        'elements' => array(
            // Features title
            array(
                'id' => 'h05c001', 'elType' => 'column',
                'settings' => array('_column_size' => 100, '_inline_size' => null),
                'elements' => array(array(
                    'id' => 'h05w001', 'elType' => 'widget', 'widgetType' => 'heading',
                    'settings' => array('title' => 'Elegí Wecar', 'align' => 'center', 'header_size' => 'h2', 'title_color' => '#0F172A', '_css_classes' => 'wecar-features__title'),
                )),
            ),
            // Feature cards
            $image_box('h05c002', 'h05w003', 'wecar-benefit-users-v2.svg', 'Nuestro equipo de expertos te asesora', 'purple'),
            $image_box('h05c003', 'h05w004', 'wecar-benefit-search-v2.svg', 'Peritajes profesionales para asegurar su calidad', 'blue'),
            $image_box('h05c004', 'h05w005', 'wecar-benefit-handshake-v2.svg', 'Múltiples posibilidades de financiación', 'cyan'),
            // Partners title
            array(
                'id' => 'h06c001', 'elType' => 'column',
                'settings' => array('_column_size' => 100, '_inline_size' => null),
                'elements' => array(array(
                    'id' => 'h06w001', 'elType' => 'widget', 'widgetType' => 'heading',
                    'settings' => array('title' => 'Respaldado por grandes marcas', 'align' => 'center', 'header_size' => 'h2', 'title_color' => '#0F172A', '_css_classes' => 'wecar-partners__title'),
                )),
            ),
            // Partner logos
            $logo('h06c002', 'h06w002', 'wecar-partner-multicars-v2.svg', 'Multicars', 244),
            $logo('h06c003', 'h06w003', 'wecar-partner-peugeot-v2.svg', 'Le Parc Peugeot', 268),
            $logo('h06c004', 'h06w004', 'wecar-partner-citroen-v2.svg', 'Le Parc Citroën', 220),
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
        wecar_fail('Could not find Elementor data.');
    }

    $document = json_decode($original_raw, true, 512, JSON_THROW_ON_ERROR);
    if (!is_array($document)) {
        wecar_fail('Elementor document is not an array.');
    }

    // Find both sections
    $feature_index = null;
    $partner_index = null;
    foreach ($document as $index => $section) {
        if (isset($section['id'])) {
            if ($section['id'] === 'h05a001') $feature_index = $index;
            if ($section['id'] === 'h06a001') $partner_index = $index;
        }
    }

    if ($feature_index === null || $partner_index === null) {
        wecar_fail('Could not find sections h05a001 or h06a001.');
    }

    // Replace features section with merged section
    $document[$feature_index] = wecar_section3_merged();

    // Remove partners section (now merged into features)
    array_splice($document, $partner_index, 1);

    $new_raw = wecar_json($document);

    $updated = $wpdb->update(
        $wpdb->postmeta,
        array('meta_value' => $new_raw),
        array('post_id' => $post_id, 'meta_key' => '_elementor_data')
    );

    if ($updated === false) {
        wecar_fail('Failed to update Elementor data.');
    }

    clean_post_cache($post_id);
    delete_post_meta($post_id, '_elementor_element_cache');

    WP_CLI::success('Merged features + partners into one section (h05a001).');

} catch (Throwable $error) {
    WP_CLI::error($error->getMessage());
}
