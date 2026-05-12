<?php
/**
 * WeCar — Partner CPT & Management
 *
 * Custom Post Type para gestionar concesionarias asociadas.
 * Reemplaza el campo texto "Partner" por un dropdown en el mismo lugar.
 */

defined('ABSPATH') || exit;

class WeCar_Partner {
    const POST_TYPE = 'wecar_partner';
    const META_KEY  = 'vehica_41299';

    public static function init() {
        add_action('init', [self::class, 'register_cpt']);
        add_action('admin_menu', [self::class, 'add_admin_menu'], 20);
        add_action('admin_enqueue_scripts', [self::class, 'admin_scripts']);
        add_action('admin_footer', [self::class, 'output_entity_data']);
        add_action('save_post', [self::class, 'save_partner_field'], 10, 2);
    }

    /**
     * Registrar CPT wecar_partner
     */
    public static function register_cpt() {
        register_post_type(self::POST_TYPE, [
            'labels' => [
                'name'               => 'Partners',
                'singular_name'      => 'Partner',
                'add_new'            => 'Agregar Partner',
                'add_new_item'       => 'Agregar nuevo Partner',
                'edit_item'          => 'Editar Partner',
                'new_item'           => 'Nuevo Partner',
                'view_item'          => 'Ver Partner',
                'search_items'       => 'Buscar Partners',
                'not_found'          => 'No se encontraron partners',
                'not_found_in_trash' => 'No hay partners en la papelera',
                'all_items'          => 'Todos los Partners',
                'menu_name'          => 'Partners',
            ],
            'public'       => false,
            'show_ui'      => true,
            'show_in_menu' => false,
            'supports'     => ['title'],
            'menu_icon'    => 'dashicons-groups',
            'capabilities' => [
                'edit_post'          => 'manage_options',
                'read_post'          => 'manage_options',
                'delete_post'        => 'manage_options',
                'edit_posts'         => 'manage_options',
                'edit_others_posts'  => 'manage_options',
                'publish_posts'      => 'manage_options',
                'read_private_posts' => 'manage_options',
            ],
        ]);
    }

    /**
     * Agregar submenú en WeCar NSM → "Administrar Partners"
     */
    public static function add_admin_menu() {
        add_submenu_page(
            'wecar-dashboard',
            'Administrar Partners — WeCar',
            'Administrar Partners',
            'manage_options',
            'edit.php?post_type=' . self::POST_TYPE,
            ''
        );
    }

    /**
     * Encolar JS y CSS del entity selector
     */
    public static function admin_scripts($hook) {
        $post_id = isset($_GET['post']) ? (int)$_GET['post'] : 0;
        $post_type = isset($_GET['post_type']) ? $_GET['post_type'] : '';

        if (!$post_id && !$post_type) return;
        if ($post_id) {
            $post = get_post($post_id);
            $post_type = $post ? $post->post_type : '';
        }
        if ($post_type !== 'vehica_car') return;

        wp_enqueue_style(
            'wecar-entity-select',
            get_stylesheet_directory_uri() . '/dashboard/assets/dashboard.css',
            [],
            '1.4.0'
        );

        wp_enqueue_script(
            'wecar-entity-select',
            get_stylesheet_directory_uri() . '/dashboard/assets/entity-select.js',
            ['jquery'],
            '1.4.0',
            true
        );
    }

    /**
     * Guardar el valor del partner cuando se guarda el post
     * Corre DESPUÉS de que Vehica ya guardó sus campos
     */
    public static function save_partner_field($post_id, $post) {
        if ($post->post_type !== 'vehica_car') return;
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
        if (!current_user_can('edit_post', $post_id)) return;

        // No guardamos acá porque Vehica ya lo maneja via $_POST[self::META_KEY]
        // Solo nos aseguramos de que si no viene el campo, se borre
        if (!isset($_POST[self::META_KEY])) {
            return;
        }
    }

    /**
     * Output entity data inline in admin footer
     * Uses same $_GET detection as admin_scripts() — get_current_screen() is unreliable with Vehica
     */
    public static function output_entity_data() {
        $post_id = isset($_GET['post']) ? (int)$_GET['post'] : 0;
        $post_type = isset($_GET['post_type']) ? $_GET['post_type'] : '';

        if (!$post_id && !$post_type) return;
        if ($post_id) {
            $post = get_post($post_id);
            $post_type = $post ? $post->post_type : '';
        }
        if ($post_type !== 'vehica_car') return;

        $current = $post_id ? get_post_meta($post_id, self::META_KEY, true) : '';

        $data = [
            'metaKey'      => self::META_KEY,
            'selected'     => $current,
            'partners'     => [],
            'particulares' => [],
            'propios'      => [],
        ];

        foreach (self::get_all() as $p) {
            $data['partners'][] = ['id' => $p->ID, 'title' => $p->post_title];
        }

        if (class_exists('WeCar_Particular')) {
            foreach (WeCar_Particular::get_all() as $p) {
                $data['particulares'][] = ['id' => $p->ID, 'title' => $p->post_title];
            }
        }

        if (class_exists('WeCar_Propio')) {
            foreach (WeCar_Propio::get_all() as $p) {
                $data['propios'][] = ['id' => $p->ID, 'title' => $p->post_title];
            }
        }

        echo '<script>window.wecarEntityData = ' . wp_json_encode($data) . ';console.log("[WeCar] Entity data loaded:", wecarEntityData);</script>';
    }

    /**
     * Obtener todos los partners
     */
    public static function get_all() {
        $partners = get_posts([
            'post_type'      => self::POST_TYPE,
            'posts_per_page' => -1,
            'post_status'    => 'any',
            'orderby'        => 'title',
            'order'          => 'ASC',
        ]);

        return $partners;
    }

    /**
     * Obtener nombre de partner por ID
     */
    public static function get_name($partner_id) {
        $partner = get_post((int)$partner_id);
        return $partner ? $partner->post_title : 'Sin asignar';
    }
}

WeCar_Partner::init();
