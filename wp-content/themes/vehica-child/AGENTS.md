# WeCar NSM Dashboard — Project Context for AI Agents

## Project Identity

- **Project**: WeCar NSM Dashboard — WordPress child theme for Vehica 1.0.87
- **Goal**: Measure North Star Metric (% stock de terceros) + manage partner dealerships
- **Target**: 75% NSM (stock de partners + particulares / total activos)
- **Language**: Spanish (Rioplatense, voseo) for team communication

## Critical Access Info

- **Host**: `ssh.wecar.com.ar` | **Port**: `18765` | **User**: `u2131-yaziskitlmmv`
- **SSH Key**: `C:\Users\Usuario\AppData\Local\Temp\wecar_key3` (temporary — regenerate from HostGator if lost)
- **Webroot**: `~/www/wecar.com.ar/public_html/`
- **Child Theme**: `~/www/wecar.com.ar/public_html/wp-content/themes/vehica-child/`
- **NO staging** — work directly in production
- **SCP for PHP files**: PowerShell heredoc breaks on `$variables`. Always use SCP.
- **Cache**: Always run `wp cache flush --path=~/www/wecar.com.ar/public_html` after changes

## Architecture Summary

```
vehica-child/
├── functions.php                    # Auto-load includes/*.php, Google Fonts Muli
├── style.css                        # Theme header (Template: vehica)
├── AGENTS.md                        # ← YOU ARE HERE
├── includes/
│   ├── class-wecar-fields.php          # Field constants + auto-set hooks
│   ├── class-wecar-metrics.php         # Metrics engine (NSM, mix, partners, historica)
│   ├── class-wecar-dashboard.php       # Admin pages (6 tabs)
│   ├── class-wecar-partner-cpt.php     # CPT wecar_partner + entity-select JS enqueue
│   ├── class-wecar-particular-cpt.php  # CPT wecar_particular
│   └── class-wecar-propio-cpt.php      # CPT wecar_propio
├── dashboard/
│   ├── assets/
│   │   ├── dashboard.css            # v5 — Muli, 100% clamp(), responsive
│   │   ├── dashboard.js             # General dashboard JS
│   │   └── entity-select.js         # Replaces Vehica input with dynamic dropdown (MutationObserver)
│   └── views/
│       ├── view-main.php            # Main panel: NSM, stock mix, partner summary
│       ├── view-partners.php        # Per-partner detail table
│       ├── view-particulares.php    # Private seller metrics
│       ├── view-historica.php       # Daily evolution (paginated)
│       └── view-ayuda.php           # Team guide
├── dashboard/
│   ├── assets/
│   │   ├── dashboard.css            # v5 — Muli, 100% clamp(), responsive
│   │   ├── dashboard.js             # General dashboard JS
│   │   └── entity-select.js         # Replaces Vehica input with dynamic dropdown (MutationObserver)
│   └── views/
│       ├── view-admin-datos.php     # Administrar Datos: partners, particulares, propios
│       ├── view-main.php            # Main panel: NSM, stock mix, partner summary
│       ├── view-partners.php        # Per-partner detail table
│       ├── view-particulares.php    # Private seller metrics
│       ├── view-historica.php       # Daily evolution (paginated)
│       └── view-ayuda.php           # Team guide
├── docs/
│   ├── ARCHITECTURE.md              # Full architecture documentation
│   └── SETUP.md                     # SSH access & development setup
```

## Menu Structure

WeCar NSM has 6 tabs: **WeCar NSM** | **Partners** | **Particulares** | **Histórica** | **Administrar Datos** | **Ayuda**

"Administrar Datos" reemplazó a "Administrar Partners". Es una página custom que agrupa gestión de Partners (vínculo al CPT), Particulares y Propios en un solo lugar.

"Orígenes" and "Estados" were removed — only 3 terms each, not worth separate pages.

## Custom Fields (Vehica)

Defined in `WeCar_Fields` constants, auto-set via `save_post` / `set_object_terms` hooks:

| Field | Type | Slugs | Behavior |
|-------|------|-------|----------|
| `vehica_41298` — Origen | Taxonomy | `propio`, `partner`, `particular` | Auto-set to `propio` if empty on save |
| `vehica_41299` — Entidad | Post Meta | Integer (CPT ID) | Hidden input, replaced by dynamic dropdown JS (partner / particular / propio) |
| `vehica_41300` — Fecha publicación | Meta (date) | — | Auto-set on creation |
| `vehica_41301` — Estado | Taxonomy | `activo`, `vendido`, `retirado` | Auto-set to `activo` if empty |
| `vehica_41302` — Fecha baja | Meta (date) | — | Auto-set on VENDIDO/RETIRADO, cleared on ACTIVO |

## Key Behaviors & Gotchas

1. **Vehica editor is Vue.js** — The Partner field (vehica_41299) is rendered dynamically by Vue. `entity-select.js` uses `MutationObserver` to detect when the input appears, then replaces it with a `<select>` whose options depend on the selected Origen (partner / particular / propio). The original input stays hidden (`hide()`) for Vue compatibility.

2. **No extra HTML in Vehica wrappers** — Never insert `<p>`, notices, or any elements inside `.vehica-edit__section__inner`. It breaks Vue's layout.

3. **Días Prom. (avg days)** — Calculated ONLY from SOLD vehicles (`vendido`). Formula: `fecha_baja - fecha_publicacion`. Active cars do NOT count. Partners with 0 sales show 0 days.

4. **Estado badge logic** — `Activo` if avg days to sell ≤ 60. `Baja rotación` if > 60. Only applies to partners with at least one sale.

5. **Historical data** — `wp_wecar_snapshots` table populated by WP-Cron daily at 1:33 AM. 90 days retention. Plugin: `wecar-snapshot-cron`.
