# WeCar NSM Dashboard — Architecture

## Overview

WeCar NSM is a WordPress-based dashboard inside the **Vehica 1.0.87** child theme that measures the **North Star Metric**: the percentage of third-party inventory (partner dealerships + private sellers) over total active listings. Target: 75%.

It adds custom fields to vehicle listings (`vehica_car` post type), a management system for partner dealerships (custom post type `wecar_partner`), and 6 admin views.

---

## System Stack

| Layer | Technology |
|-------|-----------|
| CMS | WordPress (HostGator shared hosting) |
| Theme | Vehica 1.0.87 (parent) |
| Child Theme | `vehica-child` |
| Frontend | Vue.js (Vehica editor) + vanilla JS (dashboard) |
| CSS | Custom, 100% clamp(), no fixed sizes |
| Font | Muli / Mulish (Google Fonts) |
| Cron | WP-Cron (daily 1:33 AM snapshot) |
| DB Table | `wp_wecar_snapshots` (historical data) |

---

## Module Architecture

### 1. `class-wecar-fields.php` — Field Definitions & Auto-Save

Defines 5 custom fields as class constants. Hooks into `save_post` and `set_object_terms` to auto-set defaults:

- `WeCar_Fields::init()` — Registers all hooks
- `self::auto_set_origen()` — Sets Origen to "propio" if empty
- `self::auto_set_estado()` — Sets Estado to "activo" if empty
- `self::auto_set_fechas()` — Sets fecha_pub on create, fecha_baja on VENDIDO/RETIRADO, clears on ACTIVO
- `self::auto_set_partner_default()` — Reserved for future use

Hook list:
- `save_post_vehica_car` → `auto_set_origen`, `auto_set_estado`, `auto_set_fechas`
- `set_object_terms` → `auto_set_fechas` (triggers when Estado changes via quick-edit)

### 2. `class-wecar-metrics.php` — Metrics Engine

Central query engine. All dashboard views source data through this class.

**Public Methods:**

| Method | Returns | Description |
|--------|---------|-------------|
| `get_nsm()` | float | NSM = (Partner activos + Particular activos) / Total activos × 100 |
| `get_mix()` | array | `{propio, partner, particular, total, propio_pct, partner_pct, particular_pct}` |
| `get_resumen(period)` | array | `{altas, bajas, nsm_anterior}` for period ('month' or 'week') |
| `get_partners()` | array | Keyed by partner name: `{activos, vendidos, retirados, dias_promedio, status}` |
| `get_particulares()` | array | `{total, activos, vendidos, retirados, conversion}` |
| `get_historico(limit, page)` | array | Paginated rows from `wp_wecar_snapshots` |

**Helper function `diff_days($from, $to)`**: Calculates absolute days between two dates. Used for `dias_promedio`.

**Important**: `dias_promedio` only counts **sold** vehicles. For `activo` status, only `activos++` is incremented — no days are accumulated.

### 3. `class-wecar-partner-cpt.php` — Partner CPT & Dropdown

Registers `wecar_partner` post type:
- `public` → `false` (not visible on frontend)
- `show_ui` → `true` (admin management)
- `capabilities` → `manage_options` (admin-only)
- `show_in_menu` → `false` (added as submenu under WeCar NSM)

**Public Methods:**
- `WeCar_Partner::get_all()` — Returns all partners as `WP_Post[]`
- `WeCar_Partner::get_name($post_id)` — Returns partner title by post ID

**Key**: This class also enqueues `partner-select.js` and localizes the partner data to JavaScript.

### 4. `class-wecar-dashboard.php` — Admin Pages

Registers the "WeCar NSM" top-level menu and 6 submenu pages via `admin_menu` hook:

```php
add_menu_page('WeCar NSM', ...)           // view-main.php
add_submenu_page('wecar-nsm', 'Partners', ...)     // view-partners.php
add_submenu_page('wecar-nsm', 'Particulares', ...) // view-particulares.php
add_submenu_page('wecar-nsm', 'Histórica', ...)    // view-historica.php
add_submenu_page('wecar-nsm', 'Adm. Partners', ...) // (redirects to edit.php?post_type=wecar_partner)
add_submenu_page('wecar-nsm', 'Ayuda', ...)         // view-ayuda.php
```

Also enqueues `dashboard.css` and `dashboard.js`, and sets the icon.

### 5. `wecar-snapshot-cron` — Daily Snapshot Plugin

Standalone plugin at `wp-content/plugins/wecar-snapshot-cron/`. Registers:
- `wecar_daily_snapshot` cron hook, scheduled at 1:33 AM
- Creates `wp_wecar_snapshots` table on activation (columns: fecha, nsm, total, propios, partners, particulares, vendidos, retirados, conversion)
- `create_table()` and `seed_initial_snapshots()` run on plugin activation

---

## Data Flow

```
User edits listing →
  │
  ▼
Vehica Vue editor renders
  └─ Partner field (vehica_41299) hidden input rendered by Vue
     └─ MutationObserver in partner-select.js detects it
        └─ Replaces with <select>, hides original input
           └─ On change: syncs value back to hidden input
  │
  ▼
User saves (Publish/Update)
  │
  ▼
save_post hook fires
  ├─ WeCar_Fields auto-sets origen/estado/fechas
  └─ Post meta saved (including partner ID)
  │
  ▼
Dashboard refresh
  └─ class-wecar-metrics.php queries DB
     └─ get_nsm(), get_mix(), get_partners() etc.
```

---

## Metrics Calculation Details

### NSM Formula
```
NSM = (count_activos_por_origen('partner') + count_activos_por_origen('particular'))
      / count_activos()
      × 100
```

### Días Promedio (per partner)
```
dias_promedio = sum(fecha_baja - fecha_publicacion for each SOLD vehicle)
                / count(sold vehicles)

Only VENDIDO status counts. Active vehicles are excluded.
```

### Tasa de Conversión (Particulares)
```
conversion = vendidos / (vendidos + retirados) × 100
```

### Estado del Partner
- `Activo` → `dias_promedio <= 60`
- `Baja rotación` → `dias_promedio > 60`
- Partners with no sales: `Activo` with 0 days
