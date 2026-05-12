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
        add_action('save_post', array(self::class, 'set_origen_desde_propietario'), 20, 1);
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

    /**
     * Auto-set Origen desde el Propietario seleccionado
     * Si el usuario selecciona un partner/particular/propio sin setear Origen,
     * lo inferimos del post_type del ID seleccionado.
     * Corre en save_post, priority alta para ir DESPUÉS de que Vehica ya guardó.
     */
    public static function set_origen_desde_propietario($post_id) {
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
        if (get_post_type($post_id) !== 'vehica_car') return;
        if (!current_user_can('edit_post', $post_id)) return;

        $partner_id = get_post_meta($post_id, self::$partner_meta, true);
        if (empty($partner_id)) return;

        // Si ya tiene Origen, no metemos mano
        $origen_terms = wp_get_post_terms($post_id, self::$origen_tax, array('fields' => 'slugs'));
        if (!empty($origen_terms) && !empty($origen_terms[0])) return;

        $entity_post = get_post((int)$partner_id);
        if (!$entity_post) return;

        switch ($entity_post->post_type) {
            case 'wecar_partner':
                $origen = 'partner';
                break;
            case 'wecar_particular':
                $origen = 'particular';
                break;
            case 'wecar_propio':
                $origen = 'propio';
                break;
            default:
                return; // No es una entidad nuestra, no tocamos
        }

        $term = term_exists($origen, self::$origen_tax);
        if ($term) {
            wp_set_post_terms($post_id, array((int)$term['term_id']), self::$origen_tax);
        }
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
