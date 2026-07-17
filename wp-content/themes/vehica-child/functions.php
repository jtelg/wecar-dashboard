<?php
/**
 * Vehica Child Theme — Functions
 */

// ─── Auto-load includes ──────────────────────────────────────────
$includes = [
    'includes/class-wecar-fields.php',
    'includes/class-wecar-metrics.php',
    'includes/class-wecar-dashboard.php',
    'includes/class-wecar-partner-cpt.php',     // Partners CPT + dropdown
    'includes/class-wecar-particular-cpt.php',  // Particulares CPT
    'includes/class-wecar-propio-cpt.php',      // Propios CPT
];

foreach ($includes as $file) {
    $path = get_stylesheet_directory() . '/' . $file;
    if (file_exists($path)) {
        require_once $path;
    }
}

// ─── Init dashboard ─────────────────────────────────────────────
if (is_admin()) {
    WeCar_Dashboard::init();
}

// ─── Enqueue styles ─────────────────────────────────────────────
add_action('wp_enqueue_scripts', static function () {
    $deps = [];
    $stylesheet_directory = get_stylesheet_directory();
    $stylesheet_uri = get_stylesheet_directory_uri();

    if (class_exists(\Elementor\Plugin::class)) {
        $deps[] = 'elementor-frontend';
    }

    wp_enqueue_style('vehica', get_template_directory_uri() . '/style.css', $deps, VEHICA_VERSION);
    wp_enqueue_style('vehica-child', $stylesheet_uri . '/style.css', ['vehica']);

    // ── Design tokens (global, loaded after Elementor frontend) ──
    wp_enqueue_style(
        'wecar-tokens',
        $stylesheet_uri . '/assets/css/tokens.css',
        ['elementor-frontend'],
        filemtime($stylesheet_directory . '/assets/css/tokens.css')
    );

    // ── Google Fonts: Syne (display) + Exo 2 (body) from Figma spec ────
    wp_enqueue_style(
        'wecar-google-fonts',
        'https://fonts.googleapis.com/css2?family=Exo+2:wght@400;600;700&family=Syne:wght@400;700&display=swap',
        [],
        wp_get_theme()->get('Version')
    );

    // ── Home page only: section CSS + animations JS ──────────────
    if (is_front_page() || is_page(35463)) {
        $section_css = [
            'wecar-header'       => 'home-header.css',
            'wecar-hero'         => 'home-hero.css',
            'wecar-steps'        => 'home-steps.css',
            'wecar-carousel'     => 'home-carousel.css',
            'wecar-features'     => 'home-features.css',
            'wecar-partners'     => 'home-partners.css',
            'wecar-footer'       => 'home-footer.css',
        ];

        foreach ($section_css as $handle => $file) {
            $path = $stylesheet_directory . '/assets/css/' . $file;
            if (file_exists($path)) {
                wp_enqueue_style(
                    $handle,
                    $stylesheet_uri . '/assets/css/' . $file,
                    ['wecar-tokens'],
                    // Each Home asset changes URL only when that file changes.
                    filemtime($path)
                );
            }
        }

        // Animations JS (footer, deferred)
        $animations_path = $stylesheet_directory . '/assets/js/home-animations.js';
        if (file_exists($animations_path)) {
            wp_enqueue_script(
                'wecar-home-animations',
                $stylesheet_uri . '/assets/js/home-animations.js',
                [],
                filemtime($animations_path),
                ['strategy' => 'defer', 'in_footer' => true]
            );
        }
    }
});

// ─── Text domain ────────────────────────────────────────────────
add_action('after_setup_theme', static function () {
    load_child_theme_textdomain('vehica', get_stylesheet_directory() . '/languages');
});

// ─── Shortcodes ──────────────────────────────────────────────────
$shortcode_file = get_stylesheet_directory() . '/includes/shortcodes/wecar-vehicle-carousel.php';
if (file_exists($shortcode_file)) {
    require_once $shortcode_file;
}

// ─── Redirects ──────────────────────────────────────────────────
add_filter('vehica/socialAuth/redirectUrl', function () {
    return 'https://wecar.ar/';
});

add_filter('vehica/socialAuth/registered/redirectUrl', function () {
    return 'https://wecar.ar/';
});

add_filter('vehica/login/redirectUrl', function () {
    return 'https://wecar.ar/';
});
