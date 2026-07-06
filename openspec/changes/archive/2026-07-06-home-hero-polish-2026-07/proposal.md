# Proposal: Hero Polish 2026-07

## Intent

El hero actual tiene layout Figma pero las animaciones de transición, estado colapsado, posicionamiento de autos y texturas no coinciden con el diseño exacto. Este polish alinea el comportamiento del accordion horizontal con Figma sin tocar otros componentes del home.

## Scope

### In Scope
1. **Estado colapsado** — "Comprá"/"Vendé" centrado vertical+horizontal, fade-in retrasado hasta que el texto principal desvanezca.
2. **Cross-fade de texto** — fade-out título/subtítulo antes de collapse complete; slide-up (translateY 10→0) + fade-in para badges/CTA al expandir, ~0.3s ease-out.
3. **Posicionamiento de vehículos** — auto bottom-right anclado, `right: -10%` (left panel), overflow hidden en contenedor.
4. **Texturas de fondo** — reemplazar SVG único por radial-gradient CSS por panel según Figma.

### Out of Scope
- Otros componentes del home (header, steps, carousel, features, partners, footer).
- FontAwesome→SVG swap (deferred).
- Cambios a Elementor data o PHP.
- Producción (solo test.wecar.com.ar).

## Capabilities

### Modified Capabilities
- `home`: REQ-HOME-002 (Hero Dual Cards) se modifica para incluir transiciones collapsed/expandido, posicionamiento exacto de imágenes, y texturas radial-gradient.

### New Capabilities
- None.

## Approach

CSS-driven. Modificar `home-hero.css` con nuevas reglas de transición, collapsed label animation, car positioning, texture gradients. Mínimos cambios en `home-animations.js` si se necesita ajustar timing. Sin cambios en Elementor, PHP, o HTML.

## Decisions

| # | Decisión | Opción | Fundamento |
|---|----------|--------|------------|
| D-1 | Estado inicial | step-1 50/50 | Figma muestra ambos paneles iguales. Click expande. |
| D-2 | Car asset | 1 solo `hero-car.png` | Figma usa mismo imageRef. Diferente tamaño vía CSS. |
| D-3 | Texturas | CSS radial-gradient | Figma las modela como gradient, no SVGs. Más performante. |

## Affected Areas

| Area | Impact | Description |
|------|--------|-------------|
| `assets/css/home-hero.css` | Modified | Transiciones, collapsed label, car pos, textures |
| `assets/js/home-animations.js` | Modified (minimal) | Timing de clases si necesario |

## Risks

| Risk | Likelihood | Mitigation |
|------|------------|------------|
| Cross-fade timing conflict con JS toggle | Low | Ajustar CSS transition-delay para que coincida con el cambio de clase |
| Collapsed label overlap con badge residual | Low | Animar badge opacity antes del collapsed label |

## Rollback Plan

`git checkout HEAD -- wp-content/themes/vehica-child/assets/css/home-hero.css wp-content/themes/vehica-child/assets/js/home-animations.js`. Flush WP cache + Elementor clear_cache.

## Dependencies

- Verificar que `hero-car.png` existe del cambio anterior (`home-figma-exact-2026-07`).
- Figma radial-gradient params ya extraídos en brief.

## Success Criteria

- [ ] Colapsado: "Comprá"/"Vendé" centrado, fade-in después de que título se desvanece
- [ ] Cross-fade: título/subtítulo se desvanecen antes del collapse completo
- [ ] Badges + CTA: slide-up + fade-in ~0.3s al expandir
- [ ] Auto position: bottom-right, `right: -10%` left panel, no tapa CTA/texto
- [ ] Overflow hidden en contenedor (ya existe)
- [ ] Texturas: radial-gradient por panel, coordenadas Figma, opacity sutil
