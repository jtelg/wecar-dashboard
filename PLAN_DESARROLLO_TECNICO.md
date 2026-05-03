# WeCar Dashboard — Plan de Desarrollo Técnico

> Documento para el equipo de desarrollo
> Basado en: "Plan de Medición y Arquitectura de Datos" (Custer Marketing, Marzo 2026)
> Objetivo: Implementar el dashboard de NSM y campos de origen de vehículos en WordPress + Vehica

---

## Índice

1. [Stack y Entorno Actual](#1-stack-y-entorno-actual)
2. [Arquitectura de la Solución](#2-arquitectura-de-la-solución)
3. [Fase 1: Capa de Datos — Campos Custom](#3-fase-1-capa-de-datos)
4. [Fase 2: Dashboard en wp-admin](#4-fase-2-dashboard)
5. [Fase 3: Historización de Métricas](#5-fase-3-historización)
6. [Fase 4: Migración del Stock Actual](#6-fase-4-migración)
7. [Fase 5: Capacitación y Deploy](#7-fase-5-capacitación-y-deploy)
8. [Anexos Técnicos](#8-anexos-técnicos)

---

## 1. Stack y Entorno Actual

### 1.1 Servidor

| Item | Valor |
|------|-------|
| Hosting | Compartido — Hosting Argentina |
| SSH | `ssh.wecar.com.ar:18765` — usuario `u2131-yaziskitlmmv` |
| PHP | 8.2.30 (ZTS) con OPcache |
| Disco | 9.8GB total — 7.1GB usado (77%) — **liberar backup de 14GB urgente** |
| Home | `/home/customer/` |
| Web root | `/home/customer/www/wecar.com.ar/public_html/` |
| WP-CLI | Disponible en `/usr/local/bin/wp` |

### 1.2 WordPress

| Item | Valor |
|------|-------|
| WordPress | Versión reciente (compatible Elementor 4.0.5) |
| Tema activo | `vehica-child` (child de `vehica`) |
| CPT Vehículos | `vehica_car` |
| Total vehículos | 125 (111 publicados, 8 draft, 6 pending) |
| Plugins clave | vehica-core 1.0.87, Elementor Pro, WP All Import/Export, Duplicator |

### 1.3 Estructura de Vehica

Vehica usa un sistema de custom fields con IDs numéricos:

- **Taxonomías** (select/dropdown): `vehica_{ID}` — ej. `vehica_6659` = Marca
- **Post meta** (texto/número): `vehica_{ID}` — ej. `vehica_6664` = KM
- **IDs existentes**: 6654 (Condición), 6655 (Carrocería), 6657 (Flags), 6659 (Marca), 6660 (Modelo), 6661 (Tracción), 6662 (Transmisión), 6663 (Combustible), 6666 (Color), 12770 (Puertas), 12974 (Cilindros), 19226 (Versión), 19270 (Año)

### 1.4 Lo que NO existe (y hay que crear)

| Componente | Estado |
|------------|--------|
| Campo "Origen" (Propio/Partner/Particular) | ❌ No existe |
| Campo "Partner" (nombre concesionaria) | ❌ No existe |
| Campo "Fecha de publicación" | ❌ No existe |
| Campo "Estado" (Activo/Vendido/Retirado) | ❌ Existe `vehica_6657` con "Vendido" pero es un flag, no un lifecycle |
| Campo "Fecha de baja" | ❌ No existe |
| Dashboard de métricas en wp-admin | ❌ No existe |
| Vista histórica de NSM | ❌ No existe |

---

## 2. Arquitectura de la Solución

### 2.1 Principios de Diseño

1. **Todo el código custom va en el child theme** (`vehica-child`) — nunca en el parent ni en Vehica Core
2. **Los campos custom se crean dentro del sistema de Vehica** (usando sus APIs nativas), no con ACF
3. **El dashboard es una página admin nativa de WordPress** usando `add_menu_page()`
4. **No tocar templates de Elementor** — los campos nuevos se agregan vía PHP en los templates override del child theme
5. **Backup obligatorio antes de cualquier cambio** (Duplicator ya está instalado)

### 2.2 Diagrama de Flujo de Datos

```
Vehica Panel (UI)
    │
    ▼
Nuevos campos en post meta/taxonomías
    │
    ▼
WP_Query con meta/tax queries
    │
    ├──► Dashboard wp-admin (métricas en vivo)
    │
    └──► Tabla wp_wecar_snapshots (histórico vía WP-Cron)
              │
              ▼
         Vista Histórica
```

### 2.3 Modelo de Roles y Permisos (del HTML)

| Vista | Rol | ¿Quién? |
|-------|-----|---------|
| Principal (NSM + Mix) | Administrador / Editor | Directorio / Gerencia |
| Partners (desglose por concesionaria) | Editor | Operaciones / Aylén |
| Particulares (conversión) | Editor | Comercial / Bruno |
| Histórica (tendencias) | Administrador | Directorio (reportes) |

Se implementa con `current_user_can()` en cada vista.

---

## 3. Fase 1: Capa de Datos

### 3.1 Backup Inicial

```bash
# Backup completo con Duplicante
wp duplicator package create --path=/home/customer/www/wecar.com.ar/public_html/

# O backup manual
cd /home/customer/www/wecar.com.ar/
wp db export backup-$(date +%Y%m%d).sql --path=public_html/
tar -czf backup-files-$(date +%Y%m%d).tar.gz public_html/
```

### 3.2 Creación de los 5 Campos Custom

#### Campo 1: Origen (Taxonomía — Select)

Se crea una nueva taxonomía Vehica para el origen del vehículo.

```
ID sugerido: 30000 (suficientemente alto para no colisionar)
Tipo: Taxonomy
Valores: PROPIo, PARTNER, PARTICULAR
Slug: vehica_30000
```

**Implementación**: A través del admin de Vehica (Vehica Panel > Custom Fields) o mediante código:

```php
// En functions.php del child theme — registrar taxonomía
add_action('init', function () {
    register_taxonomy('vehica_30000', 'vehica_car', [
        'label' => 'Origen del vehículo',
        'public' => true,
        'show_in_rest' => true,
        'hierarchical' => true,
    ]);

    // Crear términos si no existen
    $terms = ['PROPIO', 'PARTNER', 'PARTICULAR'];
    foreach ($terms as $term) {
        if (!term_exists($term, 'vehica_30000')) {
            wp_insert_term($term, 'vehica_30000');
        }
    }
});
```

#### Campo 2: Partner (Texto)

```
ID sugerido: 30001
Tipo: Text
Meta key: vehica_30001
```

**Condicional**: Solo visible cuando Origen = PARTNER. Se maneja vía las Field Connections de Vehica o mediante JS en el frontend.

#### Campo 3: Fecha de Publicación (Date)

```
ID sugerido: 30002
Tipo: Date
Meta key: vehica_30002
```

**Auto-set**: Al crear el vehículo, se completa automáticamente con la fecha actual:

```php
add_action('wp_insert_post', function ($post_id, $post, $update) {
    if ($post->post_type !== 'vehica_car') return;
    if ($update) return;

    update_post_meta($post_id, 'vehica_30002', current_time('Y-m-d'));
}, 10, 3);
```

#### Campo 4: Estado (Taxonomía — Select)

```
ID sugerido: 30003
Tipo: Taxonomy
Valores: ACTIVO, VENDIDO, RETIRADO
```

**Importante**: Este campo es el lifecycle del vehículo. Cuando cambia a VENDIDO o RETIRADO, se auto-completa la fecha de baja.

#### Campo 5: Fecha de Baja (Date)

```
ID sugerido: 30004
Tipo: Date
Meta key: vehica_30004
```

**Auto-set**: Cuando el Estado cambia a VENDIDO o RETIRADO:

```php
add_action('set_object_terms', function ($post_id, $terms, $tt_ids, $taxonomy) {
    if ($taxonomy !== 'vehica_30003') return;

    $term_list = wp_get_post_terms($post_id, 'vehica_30003', ['fields' => 'slugs']);
    if (in_array('vendido', $term_list) || in_array('retirado', $term_list)) {
        update_post_meta($post_id, 'vehica_30004', current_time('Y-m-d'));
    }
}, 10, 4);
```

### 3.3 Integración con el Panel de Usuario Vehica

Los campos deben aparecer en:

1. **Formulario de carga frontend** (Vehica User Panel) — usando los hooks/filters de Vehica o modificando los templates del child theme
2. **Single vehicle page** — agregar los campos al template via Elementor o PHP
3. **Tarjetas de vehículo** — si es necesario mostrar el origen (ej. badge "Partner")

### 3.4 Integración con Elementor

Los campos se agregan al template del Single Listing vía Elementor:

1. Ir a `Elementor > Templates > Theme Builder > Single Listing (Vehica)`
2. Agregar los widgets de "Vehica Field" para los nuevos campos
3. O bien, modificar los templates PHP del child theme en:
   - `templates/car/single/attributes.php`
   - `templates/card/car/card_v1.php` (y variants)

### 3.5 Validación de Datos

Checklist post-implementación:

- [ ] 125 vehículos tienen campo `vehica_30000` poblado (migración inicial = todos "PROPIO")
- [ ] Los nuevos vehículos creados tienen fecha de publicación automática
- [ ] El cambio de estado a VENDIDO/REtIRADO dispara la fecha de baja
- [ ] Los campos aparecen en el formulario de carga frontend
- [ ] Los campos se ven en la página del vehículo

---

## 4. Fase 2: Dashboard

### 4.1 Archivos a Crear

```
vehica-child/
├── dashboard/
│   ├── class-wecar-dashboard.php       ← Clase principal
│   ├── class-wecar-metrics.php         ← Motor de consultas
│   ├── views/
│   │   ├── view-main.php              ← Vista Principal (NSM)
│   │   ├── view-partners.php          ← Vista Partners
│   │   ├── view-particulares.php      ← Vista Particulares
│   │   └── view-historica.php         ← Vista Histórica
│   └── assets/
│       ├── dashboard.css
│       └── dashboard.js
└── functions.php                       ← Ya existe, agregar include
```

### 4.2 Estructura del Dashboard

#### 4.2.1 Registro de la Admin Page

```php
// En functions.php o en class-wecar-dashboard.php
add_action('admin_menu', function () {
    add_menu_page(
        'WeCar Dashboard',
        'WeCar NSM',
        'edit_posts',           // mínimo: editor
        'wecar-dashboard',
        'wecar_render_dashboard',
        'dashicons-chart-area',
        3                       // posición en el menú
    );

    // Submenú: Partners
    add_submenu_page(
        'wecar-dashboard',
        'Partners — WeCar',
        'Partners',
        'edit_posts',
        'wecar-partners',
        'wecar_render_partners'
    );

    // Submenú: Particulares
    add_submenu_page(
        'wecar-dashboard',
        'Particulares — WeCar',
        'Particulares',
        'edit_posts',
        'wecar-particulares',
        'wecar_render_particulares'
    );

    // Submenú: Histórica (solo admins)
    add_submenu_page(
        'wecar-dashboard',
        'Histórica — WeCar',
        'Histórica',
        'manage_options',
        'wecar-historica',
        'wecar_render_historica'
    );
});
```

#### 4.2.2 Motor de Métricas (class-wecar-metrics.php)

Todas las consultas se centralizan acá:

```php
class WeCar_Metrics {
    private $field_origen    = 'vehica_30000';
    private $field_partner   = 'vehica_30001';
    private $field_fecha_pub = 'vehica_30002';
    private $field_estado    = 'vehica_30003';
    private $field_fecha_baja= 'vehica_30004';

    /**
     * NSM: % de stock de terceros
     * Fórmula: (Partners + Particulares activos) / Total activos × 100
     */
    public function get_nsm() { /* ... */ }

    /**
     * Mix del inventario: % Propio / % Partner / % Particular
     */
    public function get_mix() { /* ... */ }

    /**
     * Resumen general: totales activos, altas/bajas del período
     */
    public function get_resumen($periodo = 'month') { /* ... */ }

    /**
     * Stock por partner: autos activos, vendidos, días promedio
     */
    public function get_partners() { /* ... */ }

    /**
     * Métricas de particulares: activos, conversión, funnel
     */
    public function get_particulares() { /* ... */ }
}
```

#### 4.2.3 Consultas Clave (WP_Query)

**NSM (stock de terceros):**

```php
// Total activos
$total = new WP_Query([
    'post_type'      => 'vehica_car',
    'post_status'    => 'publish',
    'posts_per_page' => -1,
    'fields'         => 'ids',
    'tax_query'      => [[
        'taxonomy' => 'vehica_30003',
        'field'    => 'slug',
        'terms'    => 'activo',
    ]],
]);

// Partners activos
$partners = new WP_Query([
    'post_type'      => 'vehica_car',
    'post_status'    => 'publish',
    'posts_per_page' => -1,
    'fields'         => 'ids',
    'tax_query'      => [
        'relation' => 'AND',
        ['taxonomy' => 'vehica_30003', 'field' => 'slug', 'terms' => 'activo'],
        ['taxonomy' => 'vehica_30000', 'field' => 'slug', 'terms' => 'partner'],
    ],
]);

// NSM = (partners + particulares) / total * 100
```

**Días promedio hasta venta:**

```php
$vendidos = get_posts([
    'post_type'      => 'vehica_car',
    'posts_per_page' => -1,
    'fields'         => 'ids',
    'tax_query'      => [[
        'taxonomy' => 'vehica_30003',
        'field'    => 'slug',
        'terms'    => 'vendido',
    ]],
]);

foreach ($vendidos as $id) {
    $fecha_pub  = get_post_meta($id, 'vehica_30002', true);
    $fecha_baja = get_post_meta($id, 'vehica_30004', true);
    // Calcular días de diferencia, promediar
}
```

### 4.3 UI del Dashboard

#### Vista Principal

Basada en el preview del HTML (Section 4). Layout:

```
┌─────────────────────────────────────────────┐
│  WeCar — Panel de Control Marketplace       │
│                               ┌──────────┐  │
│                               │  48%     │  │
│                               │  NSM     │  │
│                               └──────────┘  │
├──────────┬──────────┬──────────┬────────────┤
│ Stock    │ Stock    │ Stock    │ Período    │
│ Propio   │ Partners │ Partic.  │ Altas/Bajas│
│ 63 (52%) │ 57 (47%) │ 2 (1%)   │ +12 / -5   │
├──────────┴──────────┴──────────┴────────────┤
│ Partners │ Activos │ Vendidos │ Días │ Status│
│──────────┼─────────┼──────────┼──────┼──────│
│ Martínez │ 18      │ 3        │ 22   │ ✅   │
│ AutoSur  │ 14      │ 2        │ 35   │ ✅   │
│ ...      │         │          │      │      │
└──────────┴─────────┴──────────┴──────┴──────┘
```

#### Vistas Partners y Particulares

Tablas con filtros. Datos obtenidos del motor de métricas.

#### Vista Histórica

Requiere la tabla de snapshots (Fase 3). Muestra:

- Evolución de NSM semana a semana (gráfico de línea)
- Progreso hacia target 75%
- Tendencia del mix del inventario

### 4.4 CSS y Diseño

Se replican los estilos del HTML original — usar los mismos colores, tipografía (Syne + Exo 2 via Google Fonts) y componentes. Archivo `dashboard.css` en el child theme.

---

## 5. Fase 3: Historización

### 5.1 Tabla Custom en BD

```sql
CREATE TABLE wp_wecar_snapshots (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    fecha DATE NOT NULL,
    nsm DECIMAL(5,2),
    total_activos INT,
    propios INT,
    partners INT,
    particulares INT,
    altas_periodo INT,
    bajas_periodo INT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY idx_fecha (fecha)
);
```

### 5.2 WP-Cron Diario

```php
add_action('wecar_daily_snapshot', function () {
    $metrics = new WeCar_Metrics();
    $mix = $metrics->get_mix();

    global $wpdb;
    $wpdb->insert('wp_wecar_snapshots', [
        'fecha'          => current_time('Y-m-d'),
        'nsm'            => $metrics->get_nsm(),
        'total_activos'  => $mix['total'],
        'propios'        => $mix['propio'],
        'partners'       => $mix['partner'],
        'particulares'   => $mix['particular'],
        'altas_periodo'  => $metrics->get_altas('day'),
        'bajas_periodo'  => $metrics->get_bajas('day'),
    ]);
});

if (!wp_next_scheduled('wecar_daily_snapshot')) {
    wp_schedule_event(strtotime('today 23:59'), 'daily', 'wecar_daily_snapshot');
}
```

### 5.3 Migración de Datos Históricos

Para tener datos desde el día 1 del dashboard, se ejecuta un script único que toma el snapshot inicial y lo guarda como primer registro histórico.

---

## 6. Fase 4: Migración del Stock Actual

### 6.1 Script de Migración

Los 125 vehículos actuales no tienen origen ni estado. Script one-time:

```bash
wp eval-file migrate-stock.php --path=/home/customer/www/wecar.com.ar/public_html/
```

```php
<?php
// migrate-stock.php
$vehicles = get_posts([
    'post_type'      => 'vehica_car',
    'posts_per_page' => -1,
    'fields'         => 'ids',
]);

foreach ($vehicles as $id) {
    // Asignar origen = PROPIO por defecto
    wp_set_object_terms($id, 'PROPIO', 'vehica_30000');

    // Asignar estado = ACTIVO si está publicado
    $post = get_post($id);
    $estado = ($post->post_status === 'publish') ? 'ACTIVO' : 'RETIRADO';
    wp_set_object_terms($id, $estado, 'vehica_30003');

    // Fecha de publicación = post_date
    update_post_meta($id, 'vehica_30002', substr($post->post_date, 0, 10));
}

echo "Migrados {$total} vehículos.\n";
```

### 6.2 Validación Post-Migración

```bash
# Verificar que todos tengan origen
wp db query "SELECT COUNT(*) FROM wp_term_relationships WHERE term_taxonomy_id IN (SELECT term_taxonomy_id FROM wp_term_taxonomy WHERE taxonomy = 'vehica_30000')" --path=public_html/

# Verificar distribuciónd
wp db query "SELECT t.name, COUNT(*) FROM wp_term_relationships tr JOIN wp_term_taxonomy tt ON tr.term_taxonomy_id = tt.term_taxonomy_id JOIN wp_terms t ON tt.term_id = t.term_id WHERE tt.taxonomy = 'vehica_30000' GROUP BY t.name" --path=public_html/
```

---

## 7. Fase 5: Capacitación y Deploy

### 7.1 Cronograma Estimado

| Fase | Duración | Semana |
|------|----------|--------|
| Backup + Setup entorno | 1 día | Semana 1 |
| Creación campos custom + pruebas | 3 días | Semana 1 |
| Migración stock actual | 1 día | Semana 2 |
| Dashboard (vistas Principal + Partners) | 5 días | Semana 2-3 |
| Dashboard (vistas Particulares + Histórica) | 3 días | Semana 3 |
| WP-Cron + snapshots | 2 días | Semana 3 |
| Capacitación + docs | 2 días | Semana 4 |
| Go-live | 1 día | Semana 4 |

**Total estimado: 3-4 semanas** (alineado con el estimado de Custer)

### 7.2 Capacitación

Sesiones de 30 min con cada rol:

| Rol | Qué necesita saber |
|-----|-------------------|
| Operaciones (Aylén) | Cargar origen al crear vehículo, consultar dashboard de Partners |
| Comercial (Bruno) | Ver métricas de particulares, entender conversión |
| Directorio | Interpretar NSM, ver tendencia histórica |

### 7.3 Deploy

1. Hacer backup completo (Duplicator o wp db export + files tar)
2. Subir cambios del child theme (solo archivos nuevos/modificados en `vehica-child/`)
3. Ejecutar script de migración
4. Verificar WP-Cron schedule activo
5. Pruebas en producción (no hay staging disponible)
6. Capacitar al equipo

### 7.4 Post-Implementación (Opcional)

- Conectar con **Looker Studio** para reportes de directorio más elaborados
- Usar WP All Export para exportar datos de vehículos con origen a Sheets/CSV

---

## 8. Anexos Técnicos

### 8.1 Conexión SSH

```bash
ssh -i /ruta/a/la/llave -p 18765 u2131-yaziskitlmmv@ssh.wecar.com.ar
```

### 8.2 Comandos WP-CLI Útiles

```bash
# Listar vehículos
wp post list --post_type=vehica_car --fields=ID,post_title,post_status --path=public_html/

# Ver meta de un vehículo
wp post meta list {ID} --path=public_html/

# Backup DB
wp db export backup-{date}.sql --path=public_html/

# Ejecutar script de migración
wp eval-file migrate-stock.php --path=public_html/
```

### 8.3 IDs de Campos Sugeridos

| Campo | ID | Tipo | Meta Key / Taxonomy |
|-------|-----|------|---------------------|
| Origen | 30000 | Taxonomy | `vehica_30000` |
| Partner | 30001 | Text | `vehica_30001` |
| Fecha Publicación | 30002 | Date | `vehica_30002` |
| Estado | 30003 | Taxonomy | `vehica_30003` |
| Fecha Baja | 30004 | Date | `vehica_30004` |

> **Nota**: Estos IDs (30000+) están lo suficientemente lejos de los existentes (6654-19270) para evitar colisiones.

### 8.4 Estructura de Archivos Final

```
vehica-child/
├── dashboard/
│   ├── class-wecar-dashboard.php
│   ├── class-wecar-metrics.php
│   ├── views/
│   │   ├── view-main.php
│   │   ├── view-partners.php
│   │   ├── view-particulares.php
│   │   └── view-historica.php
│   └── assets/
│       ├── dashboard.css
│       └── dashboard.js
├── templates/                       ← Ya existe
│   └── car/
│       └── single/
│           └── attributes.php       ← Modificar para mostrar origen
├── functions.php                    ← Ya existe, agregar includes
├── style.css                        ← Ya existe
├── migrate-stock.php                ← Script one-time (borrar después)
└── README-dashboard.md              ← Documentación para el equipo
```

---

## Tabla de Referencia Rápida

| ¿Qué? | ¿Cómo? | ¿Dónde? |
|-------|--------|---------|
| Crear campo custom | Vehica Panel > Custom Fields o `register_taxonomy()` | Admin o `functions.php` |
| Consultar vehículos | `WP_Query` con `post_type=vehica_car + tax_query + meta_query` | `class-wecar-metrics.php` |
| Admin page | `add_menu_page()` + `add_submenu_page()` | `class-wecar-dashboard.php` |
| Estilos | CSS custom (misma identidad que el HTML) | `dashboard.css` |
| Snapshot histórico | Tabla `wp_wecar_snapshots` + WP-Cron diario | `class-wecar-metrics.php` |
| Migración | Script one-time `migrate-stock.php` | CLI via `wp eval-file` |
| Backup | Duplicator plugin o manual | Antes de tocar cualquier cosa |
