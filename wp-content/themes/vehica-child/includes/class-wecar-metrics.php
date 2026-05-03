<?php
/**
 * WeCar — Motor de Métricas
 *
 * Centraliza todas las consultas para el dashboard de NSM.
 */

defined('ABSPATH') || exit;

class WeCar_Metrics {
    const SNAPSHOT_TABLE = 'wp_wecar_snapshots';

    /**
     * Obtener IDs de campos desde WeCar_Fields
     */
    private static function origen_tax() {
        return WeCar_Fields::$origen_tax ?: 'vehica_41298';
    }
    private static function estado_tax() {
        return WeCar_Fields::$estado_tax ?: 'vehica_41301';
    }
    private static function partner_meta() {
        return WeCar_Fields::$partner_meta ?: 'vehica_41299';
    }
    private static function fecha_pub() {
        return WeCar_Fields::$fecha_pub ?: 'vehica_41300';
    }
    private static function fecha_baja() {
        return WeCar_Fields::$fecha_baja ?: 'vehica_41302';
    }

    /**
     * Obtener NSM actual
     * Formula: (Partners + Particulares activos) / Total activos x 100
     */
    public static function get_nsm() {
        $total    = self::count_activos();
        $terceros = self::count_activos_por_origen('partner')
                  + self::count_activos_por_origen('particular');

        if ($total === 0) {
            return 0;
        }

        return round(($terceros / $total) * 100, 1);
    }

    /**
     * Obtener mix completo del inventario
     */
    public static function get_mix() {
        $total = self::count_activos();

        $mix = [
            'propio'      => self::count_activos_por_origen('propio'),
            'partner'     => self::count_activos_por_origen('partner'),
            'particular'  => self::count_activos_por_origen('particular'),
            'total'       => $total,
        ];

        foreach (['propio', 'partner', 'particular'] as $key) {
            $mix[$key . '_pct'] = $total > 0
                ? round(($mix[$key] / $total) * 100, 1)
                : 0;
        }

        return $mix;
    }

    /**
     * Resumen del periodo (altas, bajas, tendencia)
     */
    public static function get_resumen($periodo = 'month') {
        $since = $periodo === 'week'
            ? date('Y-m-d', strtotime('-7 days'))
            : date('Y-m-d', strtotime('-30 days'));

        return [
            'altas'       => self::count_altas($since),
            'bajas'       => self::count_bajas($since),
            'nsm_anterior' => self::get_nsm_anterior(),
        ];
    }

    /**
     * Stock por partner (concesionaria)
     * Los partners se gestionan via CPT wecar_partner
     */
    public static function get_partners() {
        $partners = [];
        $partner_posts = WeCar_Partner::get_all();

        // Inicializar estructura para cada partner registrado
        foreach ($partner_posts as $pp) {
            $partners[$pp->post_title] = [
                'activos'    => 0,
                'vendidos'   => 0,
                'retirados'  => 0,
                'dias_total' => 0,
                'dias_count' => 0,
            ];
        }

        // Consultar todos los vehículos con origen = PARTNER
        $query = new WP_Query([
            'post_type'      => 'vehica_car',
            'post_status'    => 'any',
            'posts_per_page' => -1,
            'fields'         => 'ids',
            'tax_query'      => [[
                'taxonomy' => self::origen_tax(),
                'field'    => 'slug',
                'terms'    => 'partner',
            ]],
        ]);

        foreach ($query->posts as $post_id) {
            $partner_id = get_post_meta($post_id, self::partner_meta(), true);

            if (empty($partner_id)) {
                $partner_name = 'Sin asignar';
            } else {
                $partner_name = WeCar_Partner::get_name($partner_id);
                if (empty($partner_name)) {
                    $partner_name = 'Sin asignar';
                }
            }

            if (!isset($partners[$partner_name])) {
                $partners[$partner_name] = [
                    'activos'    => 0,
                    'vendidos'   => 0,
                    'retirados'  => 0,
                    'dias_total' => 0,
                    'dias_count' => 0,
                ];
            }

            $estado_terms = wp_get_post_terms($post_id, self::estado_tax(), ['fields' => 'slugs']);
            $estado = !empty($estado_terms) ? $estado_terms[0] : 'activo';

            $fecha_pub  = get_post_meta($post_id, self::fecha_pub(), true);
            $fecha_baja = get_post_meta($post_id, self::fecha_baja(), true);

            if ($estado === 'activo') {
                $partners[$partner_name]['activos']++;
            } elseif ($estado === 'vendido') {
                $partners[$partner_name]['vendidos']++;
                if ($fecha_pub && $fecha_baja) {
                    $dias = (int)diff_days($fecha_pub, $fecha_baja);
                    $partners[$partner_name]['dias_total'] += $dias;
                    $partners[$partner_name]['dias_count']++;
                }
            } elseif ($estado === 'retirado') {
                $partners[$partner_name]['retirados']++;
            }
        }

        foreach ($partners as $name => &$data) {
            $data['dias_promedio'] = $data['dias_count'] > 0
                ? round($data['dias_total'] / $data['dias_count'])
                : 0;

            $data['status'] = 'activo';
            if ($data['dias_promedio'] > 60) {
                $data['status'] = 'baja_rotacion';
            }

            unset($data['dias_total'], $data['dias_count']);
        }

        uasort($partners, function ($a, $b) {
            return $b['activos'] - $a['activos'];
        });

        return $partners;
    }

    /**
     * Metricas de particulares
     */
    public static function get_particulares() {
        $query = new WP_Query([
            'post_type'      => 'vehica_car',
            'post_status'    => 'any',
            'posts_per_page' => -1,
            'fields'         => 'ids',
            'tax_query'      => [[
                'taxonomy' => self::origen_tax(),
                'field'    => 'slug',
                'terms'    => 'particular',
            ]],
        ]);

        $total     = count($query->posts);
        $activos   = 0;
        $vendidos  = 0;
        $retirados = 0;

        foreach ($query->posts as $post_id) {
            $estado = wp_get_post_terms($post_id, self::estado_tax(), ['fields' => 'slugs']);
            $estado_slug = !empty($estado) ? $estado[0] : 'activo';

            switch ($estado_slug) {
                case 'activo':   $activos++;   break;
                case 'vendido':  $vendidos++;  break;
                case 'retirado': $retirados++; break;
            }
        }

        $conversion = ($vendidos + $retirados) > 0
            ? round(($vendidos / ($vendidos + $retirados)) * 100, 1)
            : 0;

        return [
            'total'      => $total,
            'activos'    => $activos,
            'vendidos'   => $vendidos,
            'retirados'  => $retirados,
            'conversion' => $conversion,
        ];
    }

    /**
     * Obtener histórico paginado
     */
    public static function get_historico($limit = 30, $page = 1) {
        global $wpdb;

        $table = self::SNAPSHOT_TABLE;
        $per_page = min(max((int)$limit, 10), 90);
        $current_page = max(1, (int)$page);
        $offset = ($current_page - 1) * $per_page;

        $total_rows = (int) $wpdb->get_var("SELECT COUNT(*) FROM {$table}");
        $total_pages = max(1, (int)ceil($total_rows / $per_page));

        $rows = $wpdb->get_results(
            $wpdb->prepare(
                "SELECT * FROM {$table} ORDER BY fecha DESC LIMIT %d OFFSET %d",
                $per_page,
                $offset
            )
        );

        return [
            'rows'         => array_reverse($rows),
            'page'         => $current_page,
            'per_page'     => $per_page,
            'total_rows'   => $total_rows,
            'total_pages'  => $total_pages,
        ];
    }

    // ─── Helper methods ──────────────────────────────────────────

    private static function count_activos() {
        $query = new WP_Query([
            'post_type'      => 'vehica_car',
            'post_status'    => 'publish',
            'posts_per_page' => -1,
            'fields'         => 'ids',
            'tax_query'      => [[
                'taxonomy' => self::estado_tax(),
                'field'    => 'slug',
                'terms'    => 'activo',
            ]],
        ]);
        return $query->post_count;
    }

    private static function count_activos_por_origen($origen) {
        $query = new WP_Query([
            'post_type'      => 'vehica_car',
            'post_status'    => 'publish',
            'posts_per_page' => -1,
            'fields'         => 'ids',
            'tax_query'      => [
                'relation' => 'AND',
                ['taxonomy' => self::estado_tax(), 'field' => 'slug', 'terms' => 'activo'],
                ['taxonomy' => self::origen_tax(), 'field' => 'slug', 'terms' => $origen],
            ],
        ]);
        return $query->post_count;
    }

    private static function count_altas($since) {
        $query = new WP_Query([
            'post_type'      => 'vehica_car',
            'post_status'    => 'publish',
            'posts_per_page' => -1,
            'fields'         => 'ids',
            'date_query'     => [['after' => $since, 'inclusive' => true]],
        ]);
        return $query->post_count;
    }

    private static function count_bajas($since) {
        $query = new WP_Query([
            'post_type'      => 'vehica_car',
            'post_status'    => 'any',
            'posts_per_page' => -1,
            'fields'         => 'ids',
            'meta_query'     => [[
                'key'     => self::fecha_baja(),
                'value'   => $since,
                'compare' => '>=',
                'type'    => 'DATE',
            ]],
        ]);
        return $query->post_count;
    }

    private static function get_nsm_anterior() {
        global $wpdb;
        $table = self::SNAPSHOT_TABLE;
        $row   = $wpdb->get_row("SELECT nsm FROM {$table} ORDER BY fecha DESC LIMIT 1");
        return $row ? (float) $row->nsm : null;
    }
}

if (!function_exists('diff_days')) {
    function diff_days($from, $to) {
        $from = new DateTime($from);
        $to   = new DateTime($to);
        return $to->diff($from)->days;
    }
}
