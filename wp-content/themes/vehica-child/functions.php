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

    if (class_exists(\Elementor\Plugin::class)) {
        $deps[] = 'elementor-frontend';
    }

    wp_enqueue_style('vehica', get_template_directory_uri() . '/style.css', $deps, VEHICA_VERSION);
    wp_enqueue_style('vehica-child', get_stylesheet_directory_uri() . '/style.css', ['vehica']);
});

// ─── Text domain ────────────────────────────────────────────────
add_action('after_setup_theme', static function () {
    load_child_theme_textdomain('vehica', get_stylesheet_directory() . '/languages');
});

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
