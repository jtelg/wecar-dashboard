<?php
/**
 * Plugin Name: WeCar Snapshot Cron
 * Description: Registra métricas diarias de NSM en wp_wecar_snapshots.
 * Version: 1.0.0
 */

defined('ABSPATH') || exit;

class WeCar_Snapshot_Cron {
    const TABLE = 'wp_wecar_snapshots';

    public static function init() {
        register_activation_hook(__FILE__, [self::class, 'create_table']);

        add_filter('cron_schedules', [self::class, 'add_daily_schedule']);
        add_action('wecar_daily_snapshot', [self::class, 'take_snapshot']);
    }

    public static function create_table() {
        global $wpdb;

        $table = self::TABLE;
        $charset_collate = $wpdb->get_charset_collate();

        $sql = "CREATE TABLE IF NOT EXISTS {$table} (
            id          BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
            fecha       DATE            NOT NULL,
            nsm         DECIMAL(5,1)    NOT NULL DEFAULT 0,
            total       INT             NOT NULL DEFAULT 0,
            propios     INT             NOT NULL DEFAULT 0,
            partners    INT             NOT NULL DEFAULT 0,
            particulares INT            NOT NULL DEFAULT 0,
            vendidos    INT             NOT NULL DEFAULT 0,
            retirados   INT             NOT NULL DEFAULT 0,
            conversion  DECIMAL(5,1)    NOT NULL DEFAULT 0,
            UNIQUE KEY  fecha (fecha)
        ) {$charset_collate};";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    }

    public static function add_daily_schedule($schedules) {
        $schedules['wecar_daily'] = [
            'interval' => 86400,
            'display'  => 'Una vez por día',
        ];
        return $schedules;
    }

    public static function schedule_snapshot() {
        if (!wp_next_scheduled('wecar_daily_snapshot')) {
            wp_schedule_event(time(), 'wecar_daily', 'wecar_daily_snapshot');
        }
    }

    public static function unschedule_snapshot() {
        $timestamp = wp_next_scheduled('wecar_daily_snapshot');
        if ($timestamp) {
            wp_unschedule_event($timestamp, 'wecar_daily_snapshot');
        }
    }

    public static function take_snapshot() {
        global $wpdb;

        $nsm      = WeCar_Metrics::get_nsm();
        $mix      = WeCar_Metrics::get_mix();
        $partners = WeCar_Metrics::get_partners();

        $total = $mix['total'];
        $propios = $mix['propio'];
        $partners_count = $mix['partner'];
        $particulares = $mix['particular'];

        $vendidos = 0;
        $retirados = 0;

        foreach ($partners as $p) {
            $vendidos   += $p['vendidos'];
            $retirados += $p['retirados'];
        }

        $particulares_data = WeCar_Metrics::get_particulares();
        $vendidos   += $particulares_data['vendidos'];
        $retirados  += $particulares_data['retirados'];

        $total_baja = $vendidos + $retirados;
        $conversion = $total_baja > 0 ? round(($vendidos / $total_baja) * 100, 1) : 0;

        $fecha = date('Y-m-d');

        $wpdb->replace(
            self::TABLE,
            [
                'fecha'       => $fecha,
                'nsm'         => $nsm,
                'total'       => $total,
                'propios'     => $propios,
                'partners'    => $partners_count,
                'particulares'=> $particulares,
                'vendidos'    => $vendidos,
                'retirados'   => $retirados,
                'conversion'  => $conversion,
            ]
        );
    }

    public static function seed_initial_snapshots() {
        global $wpdb;

        $table = self::TABLE;
        $exists = $wpdb->get_var("SELECT COUNT(*) FROM {$table}");

        if ($exists > 0) {
            return;
        }

        $days = 90;
        for ($i = $days; $i >= 0; $i--) {
            $fecha = date('Y-m-d', strtotime("-{$i} days"));

            $nsm = round(mt_rand(250, 400) / 10, 1);
            $total = mt_rand(80, 120);
            $propios = (int) round($total * mt_rand(20, 45) / 100);
            $partners_count = (int) round($total * mt_rand(10, 30) / 100);
            $particulares = $total - $propios - $partners_count;
            $vendidos = mt_rand(2, 15);
            $retirados = mt_rand(1, 8);
            $conversion = $vendidos + $retirados > 0
                ? round(($vendidos / ($vendidos + $retirados)) * 100, 1)
                : 0;

            $wpdb->replace($table, [
                'fecha'        => $fecha,
                'nsm'          => $nsm,
                'total'        => $total,
                'propios'      => $propios,
                'partners'     => $partners_count,
                'particulares' => $particulares,
                'vendidos'     => $vendidos,
                'retirados'    => $retirados,
                'conversion'   => $conversion,
            ]);
        }
    }
}

WeCar_Snapshot_Cron::init();
