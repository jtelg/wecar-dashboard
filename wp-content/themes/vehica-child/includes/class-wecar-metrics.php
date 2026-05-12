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
     * Construir meta_query para filtrar por fecha de publicación
     */
    private static function build_fecha_meta_query($fecha_desde, $fecha_hasta) {
        if (empty($fecha_desde) && empty($fecha_hasta)) {
            return [];
        }
        return [[
            'key'     => self::fecha_pub(),
            'compare' => 'BETWEEN',
            'value'   => [
                $fecha_desde ?: '1970-01-01',
                $fecha_hasta ?: '2099-12-31',
            ],
            'type'    => 'DATE',
        ]];
    }

    /**
     * Stock por partner (concesionaria)
     * Los partners se gestionan via CPT wecar_partner
     */
    public static function get_partners($fecha_desde = null, $fecha_hasta = null) {
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
        $meta_fecha = self::build_fecha_meta_query($fecha_desde, $fecha_hasta);
        $query_args = [
            'post_type'      => 'vehica_car',
            'post_status'    => 'any',
            'posts_per_page' => -1,
            'fields'         => 'ids',
            'tax_query'      => [[
                'taxonomy' => self::origen_tax(),
                'field'    => 'slug',
                'terms'    => 'partner',
            ]],
        ];
        if (!empty($meta_fecha)) {
            $query_args['meta_query'] = $meta_fecha;
        }
        $query = new WP_Query($query_args);

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
     * Detalle por particular (similar a get_partners pero para particulares)
     * Cada particular con activos, vendidos, retirados, y días promedio
     */
    public static function get_particulares_detail($fecha_desde = null, $fecha_hasta = null) {
        $particulares = [];
        $particular_posts = WeCar_Particular::get_all();

        foreach ($particular_posts as $pp) {
            $particulares[$pp->post_title] = [
                'activos'    => 0,
                'vendidos'   => 0,
                'retirados'  => 0,
                'dias_total' => 0,
                'dias_count' => 0,
            ];
        }

        $meta_fecha = self::build_fecha_meta_query($fecha_desde, $fecha_hasta);
        $query_args = [
            'post_type'      => 'vehica_car',
            'post_status'    => 'any',
            'posts_per_page' => -1,
            'fields'         => 'ids',
            'tax_query'      => [[
                'taxonomy' => self::origen_tax(),
                'field'    => 'slug',
                'terms'    => 'particular',
            ]],
        ];
        if (!empty($meta_fecha)) {
            $query_args['meta_query'] = $meta_fecha;
        }
        $query = new WP_Query($query_args);

        foreach ($query->posts as $post_id) {
            $particular_id = get_post_meta($post_id, self::partner_meta(), true);

            if (empty($particular_id)) {
                $particular_name = 'Sin asignar';
            } else {
                $particular_name = WeCar_Particular::get_name($particular_id);
                if (empty($particular_name)) {
                    $particular_name = 'Sin asignar';
                }
            }

            if (!isset($particulares[$particular_name])) {
                $particulares[$particular_name] = [
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
                $particulares[$particular_name]['activos']++;
            } elseif ($estado === 'vendido') {
                $particulares[$particular_name]['vendidos']++;
                if ($fecha_pub && $fecha_baja) {
                    $dias = (int)diff_days($fecha_pub, $fecha_baja);
                    $particulares[$particular_name]['dias_total'] += $dias;
                    $particulares[$particular_name]['dias_count']++;
                }
            } elseif ($estado === 'retirado') {
                $particulares[$particular_name]['retirados']++;
            }
        }

        foreach ($particulares as $name => &$data) {
            $data['dias_promedio'] = $data['dias_count'] > 0
                ? round($data['dias_total'] / $data['dias_count'])
                : 0;

            $data['status'] = 'activo';
            if ($data['dias_promedio'] > 60) {
                $data['status'] = 'baja_rotacion';
            }

            unset($data['dias_total'], $data['dias_count']);
        }

        uasort($particulares, function ($a, $b) {
            return $b['activos'] - $a['activos'];
        });

        return $particulares;
    }

    /**
     * Stock por propio (concesionaria propia)
     * Los propios se gestionan via CPT wecar_propio
     */
    public static function get_propios($fecha_desde = null, $fecha_hasta = null) {
        $propios = [];
        $propio_posts = WeCar_Propio::get_all();

        // Inicializar estructura para cada propio registrado
        foreach ($propio_posts as $pp) {
            $propios[$pp->post_title] = [
                'activos'    => 0,
                'vendidos'   => 0,
                'retirados'  => 0,
                'dias_total' => 0,
                'dias_count' => 0,
            ];
        }

        // Consultar todos los vehículos con origen = PROPIO
        $meta_fecha = self::build_fecha_meta_query($fecha_desde, $fecha_hasta);
        $query_args = [
            'post_type'      => 'vehica_car',
            'post_status'    => 'any',
            'posts_per_page' => -1,
            'fields'         => 'ids',
            'tax_query'      => [[
                'taxonomy' => self::origen_tax(),
                'field'    => 'slug',
                'terms'    => 'propio',
            ]],
        ];
        if (!empty($meta_fecha)) {
            $query_args['meta_query'] = $meta_fecha;
        }
        $query = new WP_Query($query_args);

        foreach ($query->posts as $post_id) {
            $propio_id = get_post_meta($post_id, self::partner_meta(), true);

            if (empty($propio_id)) {
                $propio_name = 'Sin asignar';
            } else {
                $propio_name = WeCar_Propio::get_name($propio_id);
                if (empty($propio_name)) {
                    $propio_name = 'Sin asignar';
                }
            }

            if (!isset($propios[$propio_name])) {
                $propios[$propio_name] = [
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
                $propios[$propio_name]['activos']++;
            } elseif ($estado === 'vendido') {
                $propios[$propio_name]['vendidos']++;
                if ($fecha_pub && $fecha_baja) {
                    $dias = (int)diff_days($fecha_pub, $fecha_baja);
                    $propios[$propio_name]['dias_total'] += $dias;
                    $propios[$propio_name]['dias_count']++;
                }
            } elseif ($estado === 'retirado') {
                $propios[$propio_name]['retirados']++;
            }
        }

        foreach ($propios as $name => &$data) {
            $data['dias_promedio'] = $data['dias_count'] > 0
                ? round($data['dias_total'] / $data['dias_count'])
                : 0;

            $data['status'] = 'activo';
            if ($data['dias_promedio'] > 60) {
                $data['status'] = 'baja_rotacion';
            }

            unset($data['dias_total'], $data['dias_count']);
        }

        uasort($propios, function ($a, $b) {
            return $b['activos'] - $a['activos'];
        });

        return $propios;
    }

    /**
     * Obtener histórico paginado
     */
    public static function get_historico($limit = 30, $page = 1, $fecha_desde = null, $fecha_hasta = null) {
        global $wpdb;

        $table = self::SNAPSHOT_TABLE;
        $per_page = min(max((int)$limit, 10), 90);
        $current_page = max(1, (int)$page);
        $offset = ($current_page - 1) * $per_page;

        $where = '';
        $params = [];
        if ($fecha_desde && $fecha_hasta) {
            $where = 'WHERE fecha >= %s AND fecha <= %s';
            $params = [$fecha_desde, $fecha_hasta];
        } elseif ($fecha_desde) {
            $where = 'WHERE fecha >= %s';
            $params = [$fecha_desde];
        } elseif ($fecha_hasta) {
            $where = 'WHERE fecha <= %s';
            $params = [$fecha_hasta];
        }

        $count_sql = "SELECT COUNT(*) FROM {$table} {$where}";
        $total_rows = !empty($params)
            ? (int) $wpdb->get_var($wpdb->prepare($count_sql, $params))
            : (int) $wpdb->get_var($count_sql);
        $total_pages = max(1, (int)ceil($total_rows / $per_page));

        $data_sql = "SELECT * FROM {$table} {$where} ORDER BY fecha DESC LIMIT %d OFFSET %d";
        $data_params = array_merge($params, [$per_page, $offset]);
        $rows = $wpdb->get_results($wpdb->prepare($data_sql, $data_params));

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
