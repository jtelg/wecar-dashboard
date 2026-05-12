<?php
/**
 * WeCar — Particular CPT & Management
 *
 * Custom Post Type para gestionar vendedores particulares.
 * Reutiliza el meta key vehica_41299 para asociar vehículos.
 */

defined('ABSPATH') || exit;

class WeCar_Particular {
    const POST_TYPE = 'wecar_particular';
    const META_KEY  = 'vehica_41299';

    public static function init() {
        add_action('init', [self::class, 'register_cpt']);
    }

    /**
     * Registrar CPT wecar_particular
     */
    public static function register_cpt() {
        register_post_type(self::POST_TYPE, [
            'labels' => [
                'name'               => 'Particulares',
                'singular_name'      => 'Particular',
                'add_new'            => 'Agregar Particular',
                'add_new_item'       => 'Agregar nuevo Particular',
                'edit_item'          => 'Editar Particular',
                'new_item'           => 'Nuevo Particular',
                'view_item'          => 'Ver Particular',
                'search_items'       => 'Buscar Particulares',
                'not_found'          => 'No se encontraron particulares',
                'not_found_in_trash' => 'No hay particulares en la papelera',
                'all_items'          => 'Todos los Particulares',
                'menu_name'          => 'Particulares',
            ],
            'public'       => false,
            'show_ui'      => true,
            'show_in_menu' => false,
            'supports'     => ['title'],
            'menu_icon'    => 'dashicons-admin-users',
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
     * Obtener todos los particulares
     */
    public static function get_all() {
        $particulares = get_posts([
            'post_type'      => self::POST_TYPE,
            'posts_per_page' => -1,
            'post_status'    => 'any',
            'orderby'        => 'title',
            'order'          => 'ASC',
        ]);

        return $particulares;
    }

    /**
     * Obtener nombre de particular por ID
     */
    public static function get_name($particular_id) {
        $particular = get_post((int)$particular_id);
        return $particular ? $particular->post_title : 'Sin asignar';
    }
}

WeCar_Particular::init();
