## Exploration: CSTR-33 — Filtro de Periodos de Fechas Persistente en Dashboard

### Current State

El dashboard de WeCar es un sistema **multi-página tradicional de WordPress admin** (no SPA). Cada sección es una página admin separada registrada vía `add_menu_page` / `add_submenu_page` en `class-wecar-dashboard.php`:

- `wecar-dashboard` → `view-main.php`
- `wecar-partners` → `view-partners.php`
- `wecar-particulares` → `view-particulares.php`
- `wecar-historica` → `view-historica.php`
- `wecar-ayuda` → `view-ayuda.php`
- `wecar-admin-datos` → `view-admin-datos.php` (no está registrado como submenú directo, existe el método `render_admin_datos()`)

**JS**: `dashboard.js` es un wrapper jQuery vacío (solo un `console.log`). No hay AJAX, ni state management, ni ninguna interacción dinámica.

**CSS**: `dashboard.css` es 100% custom, responsive con `clamp()`, sin estilos para date pickers.

**Métricas**: `WeCar_Metrics` usa métodos estáticos que consultan la base de datos vía `WP_Query` y `$wpdb`:
- `get_nsm()`, `get_mix()` — conteos de activos **actuales** (point-in-time).
- `get_partners()`, `get_particulares()`, `get_particulares_detail()` — agregan **TODO** el historial de vehículos; no aceptan filtros de fecha.
- `get_resumen($periodo)` — acepta `'week'` o `'month'` y calcula `altas` / `bajas` desde hoy; no acepta fechas arbitrarias.
- `get_historico($limit, $page)` — pagina la tabla `wp_wecar_snapshots` por cantidad de filas; **no filtra por rango de fechas** aunque la tabla tiene columna `fecha`.

**Histórica**: `view-historica.php` muestra datos de snapshots diarios paginados. No tiene UI de filtro de fechas, aunque `get_historico()` ya opera sobre datos con fecha.

**Navegación**: Al hacer clic en una pestaña del menú de admin, WordPress recarga la página completa. Los links del submenú son generados por WordPress (`admin.php?page=wecar-...`) y **no conservan query params** automáticamente.

---

### Affected Areas

- `includes/class-wecar-dashboard.php` — Necesita leer `$_GET['from']` / `$_GET['to']`, pasarlos a los métodos de métricas, y pre-cargar el filtro en cada vista.
- `includes/class-wecar-metrics.php` — Casi todos los métodos necesitan parámetros opcionales `$from` / `$to` y lógica de filtrado por fecha en `WP_Query` o `$wpdb`.
- `dashboard/views/view-main.php` — Incluir el filtro de fechas; mostrar métricas filtradas.
- `dashboard/views/view-partners.php` — Incluir el filtro; tabla filtrada por rango.
- `dashboard/views/view-particulares.php` — Incluir el filtro; agregados y detalle filtrados.
- `dashboard/views/view-historica.php` — Incluir el filtro; reemplazar paginación por cantidad de filas con paginación por rango de fechas.
- `dashboard/views/view-ayuda.php` — Incluir el filtro UI (aunque no afecte datos, para consistencia).
- `dashboard/views/view-admin-datos.php` — Incluir el filtro UI (consistencia visual).
- `dashboard/assets/dashboard.js` — Agregar lógica para preservar `from`/`to` en los links del menú de admin de WeCar, y posiblemente auto-submit del filtro.
- `dashboard/assets/dashboard.css` — Estilos para el componente de filtro de fechas (inputs, botón, layout).

---

### Approaches

#### 1. PHP-Céntrico: URL params + User Meta + JS para menú

**Descripción**: Se agrega un formulario de fechas en cada vista. Al enviar, se guarda el rango en `user_meta` y se recarga la misma página con `?from=YYYY-MM-DD&to=YYYY-MM-DD`. Un pequeño script JS lee los params de la URL actual y los agrega a todos los links del submenú de WeCar para que la navegación los conserve. Si no hay params en la URL, se usa el último valor guardado en `user_meta`.

- **Pros**:
  - URLs compartibles/bookmarkeables con un rango específico.
  - Persistencia robusta: sobrevive navegación por menú gracias al JS, y por recargas gracias al `user_meta`.
  - Degradación graceful: sin JS, el formulario sigue funcionando para la página actual.
  - No requiere AJAX ni endpoints nuevos.
- **Cons**:
  - Requiere modificar todos los métodos de métricas.
  - Requiere un snippet JS para inyectar params en los links del menú de WP.
  - Cada cambio de fecha recarga la página completa.
- **Effort**: Medium-High

#### 2. Híbrido PHP/JS: localStorage + AJAX

**Descripción**: El filtro se maneja con JS puro. Se guarda el rango en `localStorage`. Al cambiar de pestaña, JS lee `localStorage` y redirige a la nueva URL con los params, o bien usa AJAX para cargar solo los datos filtrados sin recargar la página.

- **Pros**:
  - UX más fluida si se usa AJAX.
  - No es necesario modificar los links del menú de WordPress (el JS intercepta o redirige).
- **Cons**:
  - Mucho más JS que el proyecto actual tiene (hoy es solo 14 líneas).
  - Requiere crear endpoints AJAX (`wp_ajax_wecar_metrics`) y manejar nonces.
  - No es shareable por URL a menos que también se sincronicen los params.
  - Mayor riesgo de bugs y mantenimiento.
- **Effort**: High

#### 3. Mínimo: URL params sin persistencia en servidor

**Descripción**: Solo se usan `$_GET` params. El formulario envía a la misma página. Los links internos dentro de cada vista se construyen con los params. Pero al hacer clic en el menú de admin de otra sección, los params se pierden.

- **Pros**:
  - Mínima cantidad de cambios.
- **Cons**:
  - **No cumple el requisito**: "Si se setea en una y cambia, debe permanecer en la otra sección."
  - Mala UX: el filtro se pierde al navegar por el menú.
- **Effort**: Medium

---

### Recommendation

**Usar la Opción 1 (PHP-Céntrico: URL params + User Meta + JS para menú).**

**Razones:**
1. **WordPress-native**: Aprovecha `user_meta` para persistencia sin inventar state management.
2. **URLs compartibles**: Un manager puede copiar `admin.php?page=wecar-partners&from=2024-06-01&to=2024-06-30` y mandarla por Slack.
3. **Mínima complejidad JS**: Solo necesitamos ~20 líneas de JS para parsear `location.search` e inyectar `from`/`to` en los `a[href*="page=wecar-"]` del menú lateral.
4. **Sin AJAX**: El proyecto no tiene infraestructura AJAX interna; agregarla sería overkill.
5. **Protección contra pérdida de params**: Si el usuario llega sin params (desde un favorito o el menú superior), `user_meta` recupera el último rango usado.

**Implementación resumida:**
1. **Crear partial** `dashboard/views/part-date-filter.php` con dos `<input type="date">`, un botón "Filtrar" y un botón "Limpiar" (que borra `user_meta` y quita los params).
2. **En `class-wecar-dashboard.php`**: En cada `render_*()`, leer `$_GET['from']` / `$_GET['to']`, validar/sanitizar, fallback a `get_user_meta()`, y pasar `$from` / `$to` a las vistas.
3. **En `class-wecar-metrics.php`**:
   - `get_historico($from, $to, $page)` — filtrar `wp_wecar_snapshots` con `WHERE fecha BETWEEN %s AND %s`.
   - `get_nsm($from, $to)`, `get_mix($from, $to)` — agregar params opcionales. Para mantener compatibilidad, defaults `null` = comportamiento actual.
   - `get_partners($from, $to)`, `get_particulares($from, $to)`, `get_particulares_detail($from, $to)` — agregar `meta_query` / `date_query` en `WP_Query` para filtrar vehículos según el rango.
   - `get_resumen($from, $to)` — reemplazar `$periodo` por fechas explícitas.
4. **En `dashboard.js`**:
   ```js
   const url = new URL(location.href);
   const from = url.searchParams.get('from');
   const to = url.searchParams.get('to');
   if (from || to) {
       document.querySelectorAll('a[href*="page=wecar-"]').forEach(a => {
           const u = new URL(a.href);
           if (from) u.searchParams.set('from', from);
           if (to) u.searchParams.set('to', to);
           a.href = u.toString();
       });
   }
   ```
5. **En `dashboard.css`**: Agregar clases para el filtro (`.wecar-date-filter`, `.wecar-date-input`, etc.) alineado con el diseño existente.

**Semántica del filtro (decisión crítica):**
El mayor riesgo técnico no es el mecanismo de persistencia, sino **qué significa "filtrar por fechas" para cada métrica**:
- **Altas**: vehículos con `fecha_publicacion` en el rango.
- **Bajas**: vehículos con `fecha_baja` en el rango.
- **Vendidos / Retirados**: vehículos con ese estado y `fecha_baja` en el rango.
- **Activos**: vehículos que estaban activos **al final del rango** (`fecha_publicacion <= $to` y (`estado = activo` o `fecha_baja > $to`)). Esto da una visión de stock al cierre del período.
- **NSM / Mix**: calculados sobre los "Activos al cierre" descritos arriba.

Esta semántica debe quedar documentada porque cambia el significado de los números actuales (que son acumulados históricos totales).

---

### Risks

- **Cambio semántico de métricas**: Hoy `get_partners()` suma TODOS los vehículos de un partner. Con filtro de fechas, los números cambiarán drásticamente. Los usuarios pueden confundirse si no se documenta claramente qué significa cada métrica filtrada.
- **Performance**: Agregar `date_query` y `meta_query` adicionales a `WP_Query` sobre `vehica_car` puede ralentizar las vistas si no hay índices adecuados en los post meta `vehica_41300` (fecha_pub) y `vehica_41302` (fecha_baja). Verificar índices o considerar un `meta_query` optimizado.
- **Snapshot vs. cálculo en vivo**: `view-historica` usa snapshots diarios; es natural filtrar por fecha. Pero `view-main`, `view-partners` y `view-particulares` calculan en vivo desde `vehica_car`. Si un usuario elige una fecha del pasado, los resultados representan "vehículos que existen HOY y cumplen condiciones de fecha", no necesariamente el estado exacto del pasado (porque un vehículo podría haber cambiado de estado después). Esto es una limitación aceptable para un dashboard operativo, pero debe ser conocida.
- **Scope creep en user meta**: Si en el futuro se agregan más filtros (por partner, por particular), `user_meta` puede volverse un almacenamiento disperso. Considerar guardar un array serializado `wecar_dashboard_filters` en lugar de metas sueltas.
- **Ayuda y Admin Datos**: El requisito dice "en todas las secciones". Esas dos vistas no tienen métricas sensibles a fechas. Incluir el filtro UI allí puede generar confusión si no se desactiva o se documenta como "sin efecto".

---

### Ready for Proposal

**Yes.** La arquitectura es clara y la Opción 1 es viable. Antes de pasar a `sdd-spec` o `sdd-design`, el orchestrator debería confirmar con el usuario:

> "¿La métrica 'Activos' filtrada por un rango de fechas debe mostrar los vehículos que estaban activos **al final** de ese rango (stock de cierre), o los que tuvieron actividad **durante** el rango?"

Esta respuesta define la semántica de las queries y afecta directamente la spec.
