<?php
/**
 * WeCar — Propio CPT & Management
 *
 * Custom Post Type para gestionar concesionarias propias de WeCar.
 * Reutiliza el meta key vehica_41299 para asociar vehículos.
 */

defined('ABSPATH') || exit;

class WeCar_Propio {
    const POST_TYPE = 'wecar_propio';
    const META_KEY  = 'vehica_41299';

    public static function init() {
        add_action('init', [self::class, 'register_cpt']);
    }

    /**
     * Registrar CPT wecar_propio
     */
    public static function register_cpt() {
        register_post_type(self::POST_TYPE, [
            'labels' => [
                'name'               => 'Propios',
                'singular_name'      => 'Propio',
                'add_new'            => 'Agregar Propio',
                'add_new_item'       => 'Agregar nuevo Propio',
                'edit_item'          => 'Editar Propio',
                'new_item'           => 'Nuevo Propio',
                'view_item'          => 'Ver Propio',
                'search_items'       => 'Buscar Propios',
                'not_found'          => 'No se encontraron propios',
                'not_found_in_trash' => 'No hay propios en la papelera',
                'all_items'          => 'Todos los Propios',
                'menu_name'          => 'Propios',
            ],
            'public'       => false,
            'show_ui'      => true,
            'show_in_menu' => false,
            'supports'     => ['title'],
            'menu_icon'    => 'dashicons-store',
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
     * Obtener todos los propios
     */
    public static function get_all() {
        $propios = get_posts([
            'post_type'      => self::POST_TYPE,
            'posts_per_page' => -1,
            'post_status'    => 'any',
            'orderby'        => 'title',
            'order'          => 'ASC',
        ]);

        return $propios;
    }

    /**
     * Obtener nombre de propio por ID
     */
    public static function get_name($propio_id) {
        $propio = get_post((int)$propio_id);
        return $propio ? $propio->post_title : 'Sin asignar';
    }
}

WeCar_Propio::init();
