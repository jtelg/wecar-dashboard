# WeCar NSM Dashboard — Project Context for AI Agents

## Project Identity

- **Project**: WeCar NSM Dashboard — WordPress child theme for Vehica 1.0.87
- **Goal**: Measure North Star Metric (% stock de terceros) + manage partner dealerships
- **Target**: 75% NSM (stock de partners + particulares / total activos)
- **Language**: Spanish (Rioplatense, voseo) for team communication

## Critical Access Info

### SSH (both environments)
- **Host**: `ssh.wecar.com.ar` | **Port**: `18765` | **User**: `u2131-yaziskitlmmv`
- **SSH Key**: `~/.ssh/wecar-siteground-fixed` (ED25519, no passphrase)
- **SSH alias**: `ssh wecar` (configured in `~/.ssh/config`)

### Environments

| Env | URL | Webroot | DB |
|-----|-----|---------|-----|
| **Test** | https://test.wecar.com.ar | `~/www/test.wecar.com.ar/public_html/` | `dbijhrsz46exbp` |
| **Production** | https://wecar.com.ar | `~/www/wecar.com.ar/public_html/` | `dbbzno7a6rmoym` |

**For full details** (DB credentials, Elementor versions, paths, gotchas): see `openspec/specs/environments.md`.

### Workflow Rule

**NEVER work directly in production.** Always:
1. Make changes in `test.wecar.com.ar` first.
2. Validate (visual + console + network).
3. Only then apply to `wecar.com.ar`.
4. Exception: emergency hotfixes (must be followed by a retro).

### Other Rules

- **SCP for PHP files**: PowerShell heredoc breaks on `$variables`. Always use SCP.
- **Cache**: Always run `wp cache flush --path=~/www/wecar.com.ar/public_html` after changes.
- **Recovery source**: Full SQL backups are on the production server at `~/wecar-db-backup-YYYYMMDD.sql` (570 MB).

## Architecture Summary

```
vehica-child/
├── functions.php                    # Auto-load includes/*.php, Google Fonts Muli
├── style.css                        # Theme header (Template: vehica)
├── AGENTS.md                        # ← YOU ARE HERE
├── includes/
│   ├── class-wecar-fields.php          # Field constants + auto-set hooks + Origen fallback
│   ├── class-wecar-metrics.php         # Metrics engine (NSM, mix, partners, particulares, historica)
│   ├── class-wecar-dashboard.php       # Admin pages (7 tabs: main, partners, particulares, historica, admin-datos, ayuda)
│   ├── class-wecar-partner-cpt.php     # CPT wecar_partner + entity-select JS/CSS + entity data output
│   ├── class-wecar-particular-cpt.php  # CPT wecar_particular
│   └── class-wecar-propio-cpt.php      # CPT wecar_propio
├── dashboard/
│   ├── assets/
│   │   ├── dashboard.css            # v5 — Muli, 100% clamp(), responsive
│   │   ├── dashboard.js             # General dashboard JS
│   │   └── entity-select.js         # Dynamic "Propietario" dropdown: filters by Origen, auto-sets entity type
│   └── views/
│       ├── view-main.php            # Main panel: NSM, stock mix, partner summary
│       ├── view-partners.php        # Per-partner detail table
│       ├── view-particulares.php    # Private seller metrics + funnel + per-seller detail table
│       ├── view-historica.php       # Daily evolution (paginated)
│       ├── view-ayuda.php           # Team guide
│       └── view-admin-datos.php     # Unified management: partners, particulares, propios
├── docs/
│   ├── ARCHITECTURE.md              # Full architecture documentation
│   └── SETUP.md                     # SSH access & development setup
├── assets/
│   ├── css/
│   │   ├── tokens.css               # Home redesign: design tokens (colors, typography, spacing)
│   │   ├── home-header.css          # Home redesign: header section
│   │   ├── home-hero.css            # Home redesign: hero dual cards
│   │   ├── home-steps.css           # Home redesign: 3 pasos section
│   │   ├── home-carousel.css        # Home redesign: vehicle carousel
│   │   ├── home-features.css        # Home redesign: "Elegí Wecar" features
│   │   ├── home-partners.css        # Home redesign: "Marcas Asociadas"
│   │   └── home-footer.css          # Home redesign: footer
│   ├── js/
│   │   └── home-animations.js       # Home redesign: scroll-triggered animations
│   ├── images/
│   │   ├── logo-wecar.png           # Home redesign: WeCar logo
│   │   └── vehicle-placeholder.svg  # Home redesign: empty state for carousel
│   └── includes/
│       └── shortcodes/
│           └── wecar-vehicle-carousel.php  # Home redesign: [wecar_vehicle_carousel] shortcode
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
| `vehica_41299` — Propietario | Post Meta | Integer (CPT ID) | Hidden input, replaced by dynamic dropdown JS. Shows partners/particulares/propios based on Origen selection. Label changes to "Propietario" via JS. |
| `vehica_41300` — Fecha publicación | Meta (date) | — | Auto-set on creation |
| `vehica_41301` — Estado | Taxonomy | `activo`, `vendido`, `retirado` | Auto-set to `activo` if empty |
| `vehica_41302` — Fecha baja | Meta (date) | — | Auto-set on VENDIDO/RETIRADO, cleared on ACTIVO |

## Key Behaviors & Gotchas

1. **Vehica editor is Vue.js** — The Propietario field (vehica_41299) is rendered dynamically by Vue. `entity-select.js` injects a custom dropdown with search. Options depend on the selected Origen. When selecting an entity without Origen, the JS auto-sets Origen based on entity type. If Vue doesn't update visually, the PHP fallback `set_origen_desde_propietario()` in `WeCar_Fields` ensures data integrity on save.

2. **No extra HTML in Vehica wrappers** — Never insert `<p>`, notices, or any elements inside `.vehica-edit__section__inner`. It breaks Vue's layout.

3. **Días Prom. (avg days)** — Calculated ONLY from SOLD vehicles (`vendido`). Formula: `fecha_baja - fecha_publicacion`. Active cars do NOT count. Partners with 0 sales show 0 days.

4. **Estado badge logic** — `Activo` if avg days to sell ≤ 60. `Baja rotación` if > 60. Only applies to partners with at least one sale.

5. **Historical data** — `wp_wecar_snapshots` table populated by WP-Cron daily at 1:33 AM. 90 days retention. Plugin: `wecar-snapshot-cron`.

6. **Particulares page** — Shows aggregate metrics (activos, vendidos, retirados) + funnel visualization + per-seller detail table with días promedio and estado badges. Tasa de Conversión = `Vendidos / (Vendidos + Retirados) × 100`. Particulares CPT entries are managed via `WeCar NSM → Administrar Datos`.

7. **Origen auto-set fallback** — `WeCar_Fields::set_origen_desde_propietario()` runs on `save_post` at priority 20. If Propietario is set but Origen is empty, it determines the Origen from the entity's post type (`wecar_partner` → `partner`, `wecar_particular` → `particular`, `wecar_propio` → `propio`).

## Elementor Data — CRITICAL Gotchas

> **Read this BEFORE any Elementor work.** These rules come from the 2026-07-01 home-recovery incident.

### Rule 1: An Elementor page is 27 meta keys, not 1

If you restore only `_elementor_data`, the page renders without CSS. The full set of meta keys MUST be restored together. The 4 most critical for CSS rendering are:

- `_elementor_data` — page structure
- `_elementor_page_assets` — widget asset libraries
- `_elementor_controls_usage` — which controls are used
- `_elementor_css` — CSS generation state

### Rule 2: CSS file size is a quality gate

After any change to `_elementor_data`, the post-specific CSS file MUST be > 1 KB. A file < 1 KB means the page is broken. The home page (post 35463) is normally ~115 KB.

**Check the size:**

```bash
ssh wecar "wc -c ~/www/wecar.com.ar/public_html/wp-content/uploads/elementor/css/post-35463.css"
```

**If the file is < 1 KB, STOP and read the runbook:**

📖 **`openspec/specs/elementor-data-restoration.md`** — 5-step recovery procedure.

📖 **`openspec/specs/elementor-css-validation.md`** — CSS size validation rules.

### Rule 3: Local JSON backups are not enough

The `openspec/changes/{name}/backups/_elementor_data-{POST_ID}.json` files in the repo contain only ONE meta key. They are useful for diffing, not for restoring. To restore, use the SQL backup on the production server.

### Recovery quick reference

If a page renders without CSS:

```bash
# 1. Find the SQL backup
ssh wecar "ls -la ~/wecar-db-backup-*.sql"

# 2. Extract the 27 meta rows for the page (see runbook for the full script)
# 3. Import them
ssh wecar "wp db import /tmp/{POST_ID}-restore.sql --path=~/www/wecar.com.ar/public_html --allow-root"

# 4. Delete the stale CSS file
ssh wecar "rm ~/www/wecar.com.ar/public_html/wp-content/uploads/elementor/css/post-{POST_ID}.css"

# 5. Clear Elementor cache and trigger a page load
ssh wecar "wp eval 'Elementor\Plugin::instance()->files_manager->clear_cache();' --path=~/www/wecar.com.ar/public_html --allow-root"
ssh wecar "wp cache flush --path=~/www/wecar.com.ar/public_html --allow-root"
ssh wecar "curl -s -o /dev/null https://wecar.com.ar/"

# 6. Verify
ssh wecar "wc -c ~/www/wecar.com.ar/public_html/wp-content/uploads/elementor/css/post-{POST_ID}.css"
# Should be > 100000 for the home page
```

## Recent Changes

### `home-redesign` (2026-06-30) — Rediseño completo del home

Reemplazo del home legacy (14 secciones, branding Autokan) por un diseño moderno WeCar de 7 secciones con paleta purple/cyan/blue, animaciones scroll-triggered, carousel con vehículos reales desde la DB, y diseño responsive mobile-first.

**Artifacts**: `openspec/changes/home-redesign/`

**Branches creadas** (5 — listas para PR):
| Branch | Content |
|--------|---------|
| `feat/redesign` | Tracker PR (draft) |
| `feat/redesign-base` | Backup + Design System foundation |
| `feat/redesign-sections` | 7 section CSS files + Vehicle carousel shortcode |
| `feat/redesign-apply-test` | Elementor JSON + Apply + Validate on test |
| `feat/redesign-prod` | Production migration + Cleanup (current) |

**Stats**: 23 tasks | 25 commits | 4 chained PRs | 18 files

**Status**: PASS WITH WARNINGS

**Warnings conocidos**:
1. KM data no disponible (muestra "Consultar KM")
2. Logos de partners son placeholders (Multicars, Le Parc Peugeot, Le Parc Citroën)
3. Logo WeCar en PNG en vez de SVG (funcional, no óptimo para alta densidad)

**Rollback**: Ver `openspec/changes/home-redesign/apply-progress.md` sección "Phase 6: Recovery" para el procedimiento completo. NO restaurar solo desde `_elementor_data-35463.json` (es insuficiente). Usar el SQL backup del 1 de mayo (2026-05-01).

### `environments-and-recovery` (2026-07-01) — Documentación de environments y recuperación

**Motivación**: El incidente del 2026-07-01 mostró que restaurar solo `_elementor_data` rompe el CSS. Este cambio documenta:
- Los 3 environments (test, production, SQL backup).
- El runbook de restauración de páginas Elementor (5 pasos).
- El quality gate de CSS file size.
- Las lecciones aprendidas en Engram.

**Artifacts nuevos**:
- `openspec/specs/environments.md` — inventario permanente de environments
- `openspec/specs/elementor-data-restoration.md` — runbook de recuperación
- `openspec/specs/elementor-css-validation.md` — quality gate de CSS
- `openspec/changes/environments-and-recovery/` — change SDD completo

**Artifacts actualizados**:
- `openspec/config.yaml` — hosting info correcto, reglas de Elementor
- `AGENTS.md` (este archivo) — tabla de environments, sección de Elementor gotchas
- `openspec/changes/home-redesign/spec.md` — REQ-HOME-017 (CSS validation)
- `openspec/changes/home-redesign/apply-progress.md` — Phase 6: Recovery

**Lección en Engram**: topic key `sdd/elementor-restore-lesson`
