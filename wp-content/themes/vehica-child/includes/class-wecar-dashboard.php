<?php
/**
 * WeCar — Dashboard Admin Page
 *
 * Registra las páginas del dashboard en wp-admin y renderiza las vistas.
 */

defined('ABSPATH') || exit;

class WeCar_Dashboard {
    /**
     * Inicializar hooks del dashboard
     */
    public static function init() {
        add_action('admin_menu', [self::class, 'register_pages']);
        add_action('admin_enqueue_scripts', [self::class, 'enqueue_assets']);
    }

    /**
     * Registrar páginas en el menú de admin
     */
    public static function register_pages() {
        add_menu_page(
            'WeCar — Panel de Control Marketplace',
            'WeCar NSM',
            'edit_posts',
            'wecar-dashboard',
            [self::class, 'render_main'],
            'dashicons-chart-area',
            3
        );

        add_submenu_page(
            'wecar-dashboard',
            'Partners — WeCar',
            'Partners',
            'edit_posts',
            'wecar-partners',
            [self::class, 'render_partners']
        );

        add_submenu_page(
            'wecar-dashboard',
            'Particulares — WeCar',
            'Particulares',
            'edit_posts',
            'wecar-particulares',
            [self::class, 'render_particulares']
        );

        add_submenu_page(
            'wecar-dashboard',
            'Histórica — WeCar',
            'Histórica',
            'manage_options',
            'wecar-historica',
            [self::class, 'render_historica']
        );

        add_submenu_page(
            'wecar-dashboard',
            'Ayuda — WeCar',
            'Ayuda',
            'edit_posts',
            'wecar-ayuda',
            [self::class, 'render_ayuda']
        );
    }

    /**
     * Render: Vista Administrar Datos
     */
    public static function render_admin_datos() {
        if (!current_user_can('manage_options')) {
            wp_die('No tenés permisos para ver esta página.');
        }

        include get_stylesheet_directory() . '/dashboard/views/view-admin-datos.php';
    }

    /**
     * Encolar assets del dashboard
     */
    public static function enqueue_assets($hook) {
        if (strpos($hook, 'wecar-') === false) {
            return;
        }

        wp_enqueue_style(
            'wecar-dashboard',
            get_stylesheet_directory_uri() . '/dashboard/assets/dashboard.css',
            [],
            '1.0.5'
        );

        wp_enqueue_script(
            'wecar-dashboard',
            get_stylesheet_directory_uri() . '/dashboard/assets/dashboard.js',
            ['jquery'],
            '1.0.0',
            true
        );
    }

    /**
     * Render: Vista Principal
     */
    public static function render_main() {
        $nsm      = WeCar_Metrics::get_nsm();
        $mix      = WeCar_Metrics::get_mix();
        $resumen  = WeCar_Metrics::get_resumen();
        $partners = WeCar_Metrics::get_partners();

        include get_stylesheet_directory() . '/dashboard/views/view-main.php';
    }

    /**
     * Render: Vista Partners
     */
    public static function render_partners() {
        $partners = WeCar_Metrics::get_partners();
        include get_stylesheet_directory() . '/dashboard/views/view-partners.php';
    }

    /**
     * Render: Vista Particulares
     */
    public static function render_particulares() {
        $data = WeCar_Metrics::get_particulares();
        $particulares = WeCar_Metrics::get_particulares_detail();
        include get_stylesheet_directory() . '/dashboard/views/view-particulares.php';
    }

    /**
     * Render: Vista Histórica
     */
    public static function render_historica() {
        if (!current_user_can('manage_options')) {
            wp_die('No tenés permisos para ver esta página.');
        }

        $page = isset($_GET['p']) ? max(1, (int)$_GET['p']) : 1;
        $historico = WeCar_Metrics::get_historico(30, $page);
        include get_stylesheet_directory() . '/dashboard/views/view-historica.php';
    }

    /**
     * Render: Vista Ayuda
     */
    public static function render_ayuda() {
        include get_stylesheet_directory() . '/dashboard/views/view-ayuda.php';
    }
}
