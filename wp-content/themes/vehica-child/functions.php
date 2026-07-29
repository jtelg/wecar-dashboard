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
    wp_enqueue_style('vehica-child', $stylesheet_uri . '/style.css', ['vehica'], filemtime($stylesheet_directory . '/style.css'));

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

    // ── Global header/footer CSS (loaded on all pages) ────────────
    $global_css = [
        'wecar-header' => 'home-header.css',
        'wecar-footer' => 'home-footer.css',
    ];

    foreach ($global_css as $handle => $file) {
        $path = $stylesheet_directory . '/assets/css/' . $file;
        if (file_exists($path)) {
            wp_enqueue_style(
                $handle,
                $stylesheet_uri . '/assets/css/' . $file,
                ['wecar-tokens'],
                filemtime($path)
            );
        }
    }

    // ── Home page only: section CSS + animations JS ──────────────
    if (is_front_page() || is_page(35463)) {
        $section_css = [
            'wecar-hero'         => 'home-hero.css',
            'wecar-steps'        => 'home-steps.css',
            'wecar-carousel'     => 'home-carousel.css',
            'wecar-features'     => 'home-features.css',
            'wecar-partners'     => 'home-partners.css',
        ];

        foreach ($section_css as $handle => $file) {
            $path = $stylesheet_directory . '/assets/css/' . $file;
            if (file_exists($path)) {
                wp_enqueue_style(
                    $handle,
                    $stylesheet_uri . '/assets/css/' . $file,
                    ['wecar-tokens'],
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

    // Dedicated "Vendé tu auto" page assets.
    $request_path = isset($_SERVER['REQUEST_URI'])
        ? trim((string) wp_parse_url(wp_unslash($_SERVER['REQUEST_URI']), PHP_URL_PATH), '/')
        : '';
    $is_sell_page = is_page('vende-tu-auto')
        || is_page_template('templates/page-vende-tu-auto.php')
        || $request_path === 'vende-tu-auto';

    if ($is_sell_page) {
        $sell_css_path = $stylesheet_directory . '/assets/css/vende-tu-auto.css';

        if (file_exists($sell_css_path)) {
            wp_enqueue_style(
                'wecar-vende-tu-auto',
                $stylesheet_uri . '/assets/css/vende-tu-auto.css',
                ['wecar-tokens', 'wecar-header', 'wecar-footer'],
                filemtime($sell_css_path)
            );
        }
    }

    // Dedicated Cotizador page assets.
    $is_quote_page = is_page('cotizador')
        || is_page_template('templates/page-cotizador.php')
        || $request_path === 'cotizador';

    if ($is_quote_page) {
        $quote_css_path = $stylesheet_directory . '/assets/css/cotizador.css';
        $quote_js_path = $stylesheet_directory . '/assets/js/cotizador.js';
        $modal_css_path = $stylesheet_directory . '/assets/css/cotizador-modal.css';
        $modal_js_path = $stylesheet_directory . '/assets/js/cotizador-modal.js';

        if (file_exists($quote_css_path)) {
            wp_enqueue_style(
                'wecar-cotizador',
                $stylesheet_uri . '/assets/css/cotizador.css',
                ['wecar-tokens', 'wecar-header', 'wecar-footer'],
                filemtime($quote_css_path)
            );
        }

        if (file_exists($quote_js_path)) {
            wp_enqueue_script(
                'wecar-cotizador',
                $stylesheet_uri . '/assets/js/cotizador.js',
                [],
                filemtime($quote_js_path),
                ['strategy' => 'defer', 'in_footer' => true]
            );
        }

        if (file_exists($modal_css_path)) {
            wp_enqueue_style(
                'wecar-cotizador-modal',
                $stylesheet_uri . '/assets/css/cotizador-modal.css',
                ['wecar-cotizador', 'wecar-tokens'],
                filemtime($modal_css_path)
            );
        }

        if (file_exists($modal_js_path)) {
            wp_enqueue_script(
                'wecar-cotizador-modal',
                $stylesheet_uri . '/assets/js/cotizador-modal.js',
                [],
                filemtime($modal_js_path),
                ['strategy' => 'defer', 'in_footer' => true]
            );

            wp_localize_script('wecar-cotizador-modal', 'wecarCotizador', [
                'ajaxUrl' => admin_url('admin-ajax.php'),
                'nonce'   => wp_create_nonce('wecar_cotizador_submit'),
            ]);
        }
    }

    if ($is_sell_page || $is_quote_page) {
        $faq_css_path = $stylesheet_directory . '/assets/css/components/wecar-faq.css';
        $faq_js_path = $stylesheet_directory . '/assets/js/components/wecar-faq.js';

        if (file_exists($faq_css_path)) {
            wp_enqueue_style(
                'wecar-faq',
                $stylesheet_uri . '/assets/css/components/wecar-faq.css',
                ['wecar-tokens'],
                filemtime($faq_css_path)
            );
        }

        if (file_exists($faq_js_path)) {
            wp_enqueue_script(
                'wecar-faq',
                $stylesheet_uri . '/assets/js/components/wecar-faq.js',
                [],
                filemtime($faq_js_path),
                ['strategy' => 'defer', 'in_footer' => true]
            );
        }
    }

    // ── Scroll reveal animations (home + vende-tu-auto + cotizador) ──
    if (is_front_page() || is_page(35463) || $is_sell_page || $is_quote_page) {
        $reveal_css_path = $stylesheet_directory . '/assets/css/scroll-animations.css';
        $reveal_js_path = $stylesheet_directory . '/assets/js/scroll-animations.js';

        if (file_exists($reveal_css_path)) {
            wp_enqueue_style(
                'wecar-scroll-animations',
                $stylesheet_uri . '/assets/css/scroll-animations.css',
                ['wecar-tokens'],
                filemtime($reveal_css_path)
            );
        }

        if (file_exists($reveal_js_path)) {
            wp_enqueue_script(
                'wecar-scroll-animations',
                $stylesheet_uri . '/assets/js/scroll-animations.js',
                [],
                filemtime($reveal_js_path),
                ['strategy' => 'defer', 'in_footer' => true]
            );
        }
    }
});

// Preempt WordPress' 404 handling so canonical/theme redirects cannot capture /cotizador.
add_filter('pre_handle_404', static function ($preempt, $wp_query) {
    $request_path = isset($_SERVER['REQUEST_URI'])
        ? trim((string) wp_parse_url(wp_unslash($_SERVER['REQUEST_URI']), PHP_URL_PATH), '/')
        : '';

    if ($request_path !== 'cotizador') {
        return $preempt;
    }

    $wp_query->is_404 = false;
    $wp_query->set('error', '');
    status_header(200);

    return true;
}, 10, 2);

// Serve the versioned page immediately, even before a matching WordPress page is created.
add_filter('template_include', static function ($template) {
    $request_path = isset($_SERVER['REQUEST_URI'])
        ? trim((string) wp_parse_url(wp_unslash($_SERVER['REQUEST_URI']), PHP_URL_PATH), '/')
        : '';

    if ($request_path !== 'vende-tu-auto' && !is_page('vende-tu-auto')) {
        return $template;
    }

    $sell_template = get_stylesheet_directory() . '/templates/page-vende-tu-auto.php';
    if (!file_exists($sell_template)) {
        return $template;
    }

    global $wp_query;
    if ($wp_query instanceof WP_Query) {
        $wp_query->is_404 = false;
    }
    status_header(200);

    return $sell_template;
}, 99);

// Serve Cotizador immediately, even before a matching WordPress page is created.
add_filter('template_include', static function ($template) {
    $request_path = isset($_SERVER['REQUEST_URI'])
        ? trim((string) wp_parse_url(wp_unslash($_SERVER['REQUEST_URI']), PHP_URL_PATH), '/')
        : '';

    if ($request_path !== 'cotizador' && !is_page('cotizador')) {
        return $template;
    }

    $quote_template = get_stylesheet_directory() . '/templates/page-cotizador.php';
    if (!file_exists($quote_template)) {
        return $template;
    }

    global $wp_query;
    if ($wp_query instanceof WP_Query) {
        $wp_query->is_404 = false;
    }
    status_header(200);

    return $quote_template;
}, 99);

add_filter('body_class', static function ($classes) {
    $request_path = isset($_SERVER['REQUEST_URI'])
        ? trim((string) wp_parse_url(wp_unslash($_SERVER['REQUEST_URI']), PHP_URL_PATH), '/')
        : '';

    if ($request_path === 'vende-tu-auto' || is_page('vende-tu-auto')) {
        $classes[] = 'wecar-vende-tu-auto-page';
        $classes = array_values(array_diff($classes, ['error404']));
    }

    if ($request_path === 'cotizador' || is_page('cotizador')) {
        $classes[] = 'wecar-cotizador-page';
        $classes = array_values(array_diff($classes, ['error404']));
    }

    return $classes;
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

// ─── Inject WeCar header globally ─────────────────────────────────
add_action('wp_body_open', static function () {
    $file = get_stylesheet_directory() . '/includes/wecar-global-header.php';
    if (file_exists($file)) {
        include $file;
    }
});

// ─── Inject WeCar footer globally ─────────────────────────────────
add_action('wp_footer', static function () {
    $file = get_stylesheet_directory() . '/includes/wecar-global-footer.php';
    if (file_exists($file)) {
        include $file;
    }
}, 5);

// ─── Hide Elementor header/footer on home via JavaScript (backup) ─
add_action('wp_footer', static function () {
    if (is_front_page() || is_page(35463)) {
        ?>
        <script>
        (function() {
            // Hide Elementor header/footer sections on home
            document.addEventListener('DOMContentLoaded', function() {
                var header = document.querySelector('body.home #wecar-header');
                var footer = document.querySelector('body.home #wecar-footer');
                if (header) header.style.display = 'none';
                if (footer) footer.style.display = 'none';
            });
            // Also hide immediately in case DOMContentLoaded already fired
            var header = document.querySelector('body.home #wecar-header');
            var footer = document.querySelector('body.home #wecar-footer');
            if (header) header.style.display = 'none';
            if (footer) footer.style.display = 'none';
        })();
        </script>
        <?php
    }
}, 99);

// ─── Hide Vehica global header/footer ──────────────────────────────
add_action('wp_head', static function () {
?>
<style id="wecar-hide-vehica-chrome">
  .vehica-header,
  .vehica-header__content,
  .vehica-header__top,
  .vehica-footer,
  .vehica-footer__content,
  .vehica-footer__bottom,
  .vehica-footer__widgets,
  .elementor-element-75cad954,
  .elementor-element-97f8a3c {
    display: none !important;
  }
</style>
<?php
});

// ─── Cotizador — AJAX submit ──────────────────────────────────────
define('WECAR_COTIZADOR_WEBHOOK', 'https://bot.custer.com.ar/webhook/wecar-cotiza');

add_action('wp_ajax_wecar_cotizador_submit', 'wecar_cotizador_handle_submit');
add_action('wp_ajax_nopriv_wecar_cotizador_submit', 'wecar_cotizador_handle_submit');

/**
 * Recibe los datos del formulario, sanitiza, y reenvía al webhook de n8n.
 */
function wecar_cotizador_handle_submit() {
    if (empty($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'wecar_cotizador_submit')) {
        wp_send_json_error(['code' => 'invalid_nonce'], 403);
    }

    if (empty($_POST['data'])) {
        wp_send_json_error(['code' => 'missing_data'], 400);
    }

    $data = json_decode(wp_unslash($_POST['data']), true);
    if (!$data || !is_array($data)) {
        wp_send_json_error(['code' => 'invalid_json'], 400);
    }

    $allowed = ['nombre', 'mail', 'telefono', 'localidad', 'anio', 'marca', 'modelo',
                'kilometros', 'gnc', 'dia', 'horario', 'id', 'timestamp'];

    $payload = [];
    foreach ($allowed as $key) {
        if (isset($data[$key])) {
            $payload[$key] = sanitize_text_field((string) $data[$key]);
        }
    }

    $response = wp_remote_post(WECAR_COTIZADOR_WEBHOOK, [
        'headers' => ['Content-Type' => 'application/json'],
        'body'    => wp_json_encode($payload),
        'timeout' => 15,
    ]);

    if (is_wp_error($response)) {
        wp_send_json_error(['code' => 'webhook_error', 'message' => $response->get_error_message()], 502);
    }

    $status = wp_remote_retrieve_response_code($response);
    if ($status < 200 || $status >= 300) {
        wp_send_json_error(['code' => 'webhook_status', 'http' => $status], 502);
    }

    wp_send_json_success(['forwarded' => true]);
}
