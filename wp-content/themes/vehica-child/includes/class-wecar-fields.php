<?php
/**
 * WeCar — Hooks de automatización
 *
 * Auto-set de fecha de publicación y fecha de baja.
 * Campos creados via sistema Vehica (IDs: 41298-41302).
 */

defined('ABSPATH') || exit;

class WeCar_Fields {
    public static $origen_tax    = 'vehica_41298';
    public static $estado_tax    = 'vehica_41301';
    public static $partner_meta  = 'vehica_41299';
    public static $fecha_pub     = 'vehica_41300';
    public static $fecha_baja    = 'vehica_41302';

    public static function init() {
        add_action('wp_insert_post', array(self::class, 'set_fecha_publicacion'), 10, 3);
        add_action('set_object_terms', array(self::class, 'set_fecha_baja'), 10, 4);
    }

    public static function set_fecha_publicacion($post_id, $post, $update) {
        if ($post->post_type !== 'vehica_car') {
            return;
        }
        if ($update) {
            return;
        }
        if (empty(self::$fecha_pub)) {
            return;
        }
        if (get_post_meta($post_id, self::$fecha_pub, true)) {
            return;
        }
        update_post_meta($post_id, self::$fecha_pub, current_time('Y-m-d'));
    }

    public static function set_fecha_baja($post_id, $terms, $tt_ids, $taxonomy) {
        if (empty(self::$estado_tax) || empty(self::$fecha_baja)) {
            return;
        }
        if ($taxonomy !== self::$estado_tax) {
            return;
        }

        $assigned = wp_get_post_terms($post_id, self::$estado_tax, array('fields' => 'slugs'));
        if (empty($assigned)) {
            return;
        }

        $terminal = array('vendido', 'retirado');
        if (!empty(array_intersect($assigned, $terminal))) {
            update_post_meta($post_id, self::$fecha_baja, current_time('Y-m-d'));
        } else {
            delete_post_meta($post_id, self::$fecha_baja);
        }
    }
}

WeCar_Fields::init();
