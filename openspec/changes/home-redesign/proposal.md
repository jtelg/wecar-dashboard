# Home Redesign — Proposal

## Intent

Reemplazar el home actual (100% Elementor legacy, 14 secciones, branding Autokan) por un diseño moderno, mobile-responsive, con identidad WeCar propia que comunique los dos embudos clave: comprar y vender.

## Business Problem

El home actual tiene branding obsoleto (Autokan), estructura desordenada (14 secciones sin jerarquía clara), no está optimizado para conversión mobile, y carece de un storytelling visual que guíe al usuario hacia los embudos de compra/venta.

## Target Users

- Compradores: usuarios buscando su próximo auto, necesitan ver vehículos disponibles y sentir confianza.
- Vendedores: usuarios queriendo vender su usado, necesitan un proceso claro y sin fricción.
- Ambos: mobile-first, esperan una experiencia rápida y profesional.

## Product Outcome

Un home con header limpio, hero dual (comprar/vender), steps animados del proceso de venta, carousel de vehículos reales, features de confianza, marcas asociadas, y footer completo — todo responsive, con paleta purple/cyan/blue.

## Current State Gap

- Home 35463: 14 secciones, 111KB `_elementor_data`, branding Autokan.
- Sin template PHP child theme para home.
- Sin diseño responsive coherente.
- Sin conexión a vehículos reales de la DB en carousel.
- Cotizador existe pero en página separada (no se toca aquí).

## Scope

### In Scope

- Backup del `_elementor_data` de home 35463 (DB + JSON en repo).
- Nuevo diseño de 7 secciones: Header, Hero dual, 3 pasos animados, Vehicle carousel, Elegí Wecar, Marcas asociadas, Footer.
- Paleta de colores: purple #5E3BE0, cyan #36BFFA/#06B6D4, blue #2563EB.
- CSS global en child theme (variables + responsive).
- JS custom para animaciones scroll-triggered de los 3 pasos.
- Datos de vehículos reales (post type `vehica_car`) desde la DB para carousel.
- Logo extraído del sitio actual.
- Placeholders para logos de marcas (Multicars, Le Parc Peugeot, Le Parc Citroën).
- Aplicación en test.wecar.com.ar primero, luego producción.

### Out of Scope

- Cotizador (calculadora-préstamo 12464 / cotiza 21804).
- Página Vende-tu-auto (28121).
- Acerca de Nosotros (19625).
- Listing / single car pages.
- DB schema changes o nuevos CPTs.
- Backend (NSM dashboard, partners, admin).

## Approach

**DB-first**: construir el nuevo `_elementor_data` como JSON estructurado con las 7 secciones, aplicarlo via `wp-cli post meta update 35463 _elementor_data`. El child theme aporta CSS custom (variables, layout, responsive, animaciones) y JS para scroll-triggered steps. Los vehículos del carousel se traen con un shortcode PHP custom que consulta `vehica_car` posts activos.

**Fases**:
1. Backup: dump del meta `_elementor_data` del home + guardar JSON en repo.
2. Design system: CSS variables en `style.css`, animaciones en nuevo JS file.
3. Build: escribir JSON de `_elementor_data` con las 7 secciones, shortcode PHP para carousel.
4. Validación: capturas desktop + mobile en test.
5. Migración: backup de prod, aplicar, flushear cache.

## Affected Files

- `wp-content/themes/vehica-child/style.css` — CSS variables + estilos globales.
- `wp-content/themes/vehica-child/assets/js/home-animations.js` — scroll-triggered animations.
- `wp-content/themes/vehica-child/functions.php` — enqueue JS, registrar shortcode carousel.
- `openspec/changes/home-redesign/backups/_elementor_data-35463.json` — backup del JSON original.
- DB: `wp_postmeta` meta_key `_elementor_data` para post_id 35463.

## Risks

| Risk | Likelihood | Mitigation |
|------|------------|------------|
| Elementor regenera IDs en el JSON | Medium | Probar en test primero, comparar diff |
| Animaciones JS rotas en Safari mobile | Medium | Usar Intersection Observer con fallback |
| WPRocket cachea CSS/JS viejos | High | `wp cache flush` post-deploy + purgar CDN |
| Vehículos carousel vacío (sin autos activos) | Low | Mostrar estado vacío con CTA |

## Success Criteria

- [ ] Home 35463 muestra las 7 secciones correctamente en test.
- [ ] Carousel trae vehículos reales de la DB (post type `vehica_car`, activos y publicados).
- [ ] Animaciones de 3 pasos funcionan en Chrome, Firefox, Safari (desktop + mobile).
- [ ] Footer muestra "2026 Custer. All rights reserved." + teléfono.
- [ ] Header tiene logo WeCar extraído del sitio actual + nav + CTA.
- [ ] Sin regresiones visuales en otras páginas (Elementor global styles intactos).
- [ ] Rollback funcional: restaurar `_elementor_data` original desde backup JSON.

## Open Questions

- ¿El carousel se implementa con shortcode PHP que renderiza HTML (vía child theme) o con widget Elementor existente?
- ¿Header nav se extrae del menú actual de WP (wp_nav_menu) o se hardcodea en el JSON de Elementor?
- Validar si el logo extraído tiene la resolución suficiente para mobile @2x.
