# Home Redesign — Archive Report

**Date**: 2026-06-30
**Status**: PASS WITH WARNINGS
**Stats**: 23 tasks | 25 commits | 4 chained PRs | 18 files

---

## Executive Summary (Resumen Ejecutivo)

El cambio **home-redesign** reemplazó por completo el home legacy de WeCar (post ID 35463), que tenía 14 secciones con branding Autokan y 111 KB de datos Elementor, por un diseño moderno de 7 secciones con identidad visual WeCar propia (paleta purple/cyan/blue), animaciones scroll-triggered, carousel con vehículos reales desde la DB, y diseño responsive mobile-first.

La implementación siguió un workflow **test-first**: los cambios se validaron en `test.wecar.com.ar` antes de migrar a producción. Se utilizaron 4 PRs encadenados (feat/redesign-base → feat/redesign-sections → feat/redesign-apply-test → feat/redesign-prod) con 25 commits en total, organizados en 5 fases y 23 tareas.

El home actualmente muestra las 7 secciones correctamente tanto en test como en producción: header sticky con logo WeCar, hero dual (comprar/vender), 3 pasos animados con Intersection Observer, carousel con datos reales de vehículos (Nissan Frontier, Citroen Basalt, Peugeot 208 en prod), sección "Elegí WeCar" con 3 features, "Marcas Asociadas" con placeholders, y footer con copyright 2026 Custer.

Se verificaron los 16 requerimientos funcionales y 8 no-funcionales del spec. No se encontraron issues críticos. Se documentaron 3 warnings conocidos que no bloquean el pase a producción pero deberían resolverse en iteraciones futuras. El rollback está documentado y probado: restaurar el `_elementor_data` original desde el backup JSON y flushear caché.

---

## Artifacts del Change

| Artifact | Path | Status |
|----------|------|--------|
| Proposal | `openspec/changes/home-redesign/proposal.md` | ✅ |
| Spec | `openspec/changes/home-redesign/spec.md` | ✅ |
| Design | `openspec/changes/home-redesign/design.md` | ✅ |
| Tasks | `openspec/changes/home-redesign/tasks.md` | ✅ (23/23) |
| Apply Progress | `openspec/changes/home-redesign/apply-progress.md` | ✅ |
| Verify Report | `openspec/changes/home-redesign/verify-report.md` | ✅ |
| Archive Report | `openspec/changes/home-redesign/archive-report.md` | ✅ (this file) |

### Backup Artifacts

| Artifact | Path |
|----------|------|
| Elementor Data (test) | `openspec/changes/home-redesign/backups/_elementor_data-35463.json` |
| Elementor Data (prod) | `openspec/changes/home-redesign/backups/prod/_elementor_data-35463.json` |
| Page Template (test) | `openspec/changes/home-redesign/backups/home-35463-page-template.txt` |
| Page Template (prod) | `openspec/changes/home-redesign/backups/prod/home-35463-page-template.txt` |
| Backup Script | `openspec/changes/home-redesign/backups/backup-home.sh` |
| Elementor JSON (new) | `openspec/changes/home-redesign/elementor/home-35463-new.json` |

---

## Stale Checkbox Reconciliation

During archive, tasks T018–T023 showed as unchecked (`- [ ]`) in `tasks.md` while the `verify-report.md` and `apply-progress.md` confirmed all 23/23 tasks were completed (Phase 5 — Production Migration fully executed). This was a stale checkbox state: the tasks artifact had not been updated after Phase 5 execution. Based on the evidence in apply-progress and verify-report, the checkboxes were reconciled to checked (`- [x]`). This is an exceptional archive-time repair — `sdd-apply` normally owns checkbox updates.

---

## Branches Creadas

| Branch | Purpose | Status |
|--------|---------|--------|
| `feat/redesign` | Tracker PR (draft, no-merge) | ✅ Local |
| `feat/redesign-base` | PR #1 — Backup + Design System | ✅ Local |
| `feat/redesign-sections` | PR #2 — Section CSS + Shortcode | ✅ Local |
| `feat/redesign-apply-test` | PR #3 — Elementor JSON + Apply + Validate | ✅ Local |
| `feat/redesign-prod` | PR #4 — Production Migration + Cleanup | ✅ Current |

---

## Stats Detalladas

| Métrica | Valor |
|---------|-------|
| Total tareas | 23 |
| Tareas completadas | 23 |
| Total commits | 25 |
| PRs encadenados | 4 |
| Archivos nuevos | ~18 (CSS, JS, PHP, JSON, assets) |
| Archivos modificados | 1 (functions.php) |
| Secciones en home nuevo | 7 |
| Widgets en JSON de Elementor | ~33 |
| Líneas en Elementor JSON | 739 |
| Líneas en shortcode PHP | 163 |
| Líneas en animation JS | 77 |
| Environments desplegados | 2 (test + production) |

---

## Warnings Conocidos (3)

### 1. KM Data No Disponible
**Impacto**: Las cards de vehículos muestran "Consultar KM" en lugar del kilometraje real.
**Causa**: El meta key para kilometraje no está disponible en el dataset actual — no se encuentra ni en meta keys ni en taxonomías de Vehica.
**Recomendación**: Identificar el meta key correcto para mileage en producción y actualizar el shortcode `wecar-vehicle-carousel.php`.
**Severidad**: Baja (UX gap, no bloqueante).

### 2. Logos de Partners son Placeholders
**Impacto**: La sección "Marcas Asociadas" muestra placeholders con bordes dashed en lugar de logos reales.
**Causa**: Los archivos SVG/PNG de Multicars, Le Parc Peugeot y Le Parc Citroën no fueron provistos.
**Recomendación**: Proveer los logos reales y reemplazar los archivos `partner-*.svg` en `assets/images/`.
**Severidad**: Media (sección visualmente incompleta).

### 3. Logo PNG en vez de SVG
**Impacto**: El logo WeCar está en formato PNG, no SVG como especificaba el spec original.
**Causa**: El logo se extrajo del sitio actual como PNG. Funcional pero no es resolution-independent.
**Recomendación**: Extraer o crear una versión SVG del logo WeCar para mejor calidad en displays de alta densidad.
**Severidad**: Baja (funcional, no óptimo).

---

## Suggestions (del Verify Report)

1. **Nav Menu Widget**: El header usa HTML hardcodeado en vez del widget Nav Menu de Elementor. Cambiar al widget permitiría administrar el menú desde WP Admin.
2. **Footer Phone**: Verificar que `+54 9 11 1234-5678` sea el número correcto de contacto.
3. **CSS `!important`**: Una instancia en `home-footer.css` (`.elementor-column { width: 100% !important }`) — monitorear por futuros conflictos de especificidad.

---

## Rollback Path

```bash
# Restore original Elementor data from backup
wp post meta update 35463 _elementor_data < openspec/changes/home-redesign/backups/_elementor_data-35463.json

# Clear all caches
wp cache flush

# Verify
# Visit https://wecar.com.ar/ — the original 14-section home should be restored
```

El backup original de `_elementor_data` está almacenado en `openspec/changes/home-redesign/backups/_elementor_data-35463.json` (test) y `backups/prod/_elementor_data-35463.json` (producción). Ambos tienen checksums SHA-256 verificados.

---

## Future Improvements

1. **SVG Logo**: Convertir el logo WeCar de PNG a SVG para resolución independiente.
2. **Real Partner Logos**: Obtener y reemplazar los placeholders de "Marcas Asociadas" con los SVG oficiales.
3. **KM Data**: Investigar y agregar el meta key correcto para kilometraje de vehículos.
4. **Nav Menu Widget**: Migrar de HTML hardcodeado al widget Nav Menu de Elementor para administración desde WP Admin.
5. **Accessibility Deep Audit**: Corregir contraste de cyan (#36BFFA) sobre blanco (2.7:1) — aunque es decorativo, idealmente mejorarlo.
6. **Focus Indicators**: Agregar estilos de focus explícitos (no solo default del browser).
7. **Performance Budget**: Establecer un presupuesto de performance y monitorear Core Web Vitals con Lighthouse.
8. **Animation Performance**: Evaluar si las animaciones scroll-triggered pueden migrarse a CSS `@view-timeline` cuando tenga soporte suficiente.
9. **Test Data Sync**: Documentar el proceso para mantener test.wecar.com.ar sincronizado con datos de producción reales.
10. **PR Merge + Branch Cleanup**: Hacer merge de los 4 PRs encadenados y eliminar las feature branches locales y remotas.

---

## SDD Cycle Summary

| Fase | Artifact | Key Decision / Outcome |
|------|----------|----------------------|
| **Proposal** | `proposal.md` | Defined scope: 7 sections replacing 14, WeCar branding, purple/cyan/blue palette |
| **Spec** | `spec.md` | 16 functional requirements, 8 NFRs, migration & rollback procedures |
| **Design** | `design.md` | DB-first Elementor JSON approach, child theme design system, shortcode for vehicle data |
| **Tasks** | `tasks.md` | 23 tasks across 5 phases, 4 chained PRs, ~2,877 LOC total |
| **Apply** | `apply-progress.md` | 25 commits, issues found & fixed (PHP TypeError, icon format, nav menu, etc.) |
| **Verify** | `verify-report.md` | PASS WITH WARNINGS — all REQs met, 3 warnings (KM data, partner logos, PNG logo) |
| **Archive** | `archive-report.md` | Change closed. All artifacts preserved in `openspec/changes/home-redesign/` |
