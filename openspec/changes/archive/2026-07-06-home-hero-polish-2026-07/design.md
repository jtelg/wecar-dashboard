# Design: Hero Polish 2026-07

## Technical Approach

CSS-only enhancement of the existing dual-card hero, leaving Elementor/PHP/HTML untouched. We keep the current column/card DOM and reuse the existing `.wecar-hero__column--active` / `--collapsed` state classes. The polish adds exact Figma dimensions, cross-fade text choreography, collapsed labels, bottom-right car anchoring, and per-panel CSS radial-gradient textures. JS changes are limited to toggling the same two classes and, if needed, a small `--expanded` modifier to coordinate animation timing.

## Architecture Decisions

| Decision | Choice | Alternatives | Rationale |
|---|---|---|---|
| DOM scope | Reuse `.wecar-hero__card` as panel | Add new `.wecar-hero__panel` wrapper | No HTML/Elementor changes allowed; the existing card already has `border-radius`, `overflow:hidden`, and option modifiers. |
| State classes | Keep `--active` / `--collapsed` on columns, add optional `--expanded` alias | Replace with step-1/2/3 classes | Existing JS already toggles these; adding step-N classes would increase JS risk for minimal gain. |
| Textures | CSS `::before` radial-gradient | SVG background | Matches Figma and decision D-3; no new asset requests. |
| Car asset | Single `hero-car.png`, sized by CSS | Two image files | Decision D-2; reduces assets and cache overhead. |
| Timing | `cubic-bezier(0.4,0,0.2,1)` for width; `ease-out` 0.3s for text | Linear / longer durations | Keeps motion snappy and within Figma feel; avoids jank on low-end devices. |

## Data Flow / State Machine

```
user click
   │
   ▼
home-animations.js toggles column classes
   │
   ├── left clicked  ──► left column ACTIVE, right column COLLAPSED  (step-2)
   ├── right clicked ──► right column ACTIVE, left column COLLAPSED (step-3)
   └── active clicked──► remove both modifiers                      (step-1)
   │
   ▼
CSS transitions react:
   • width/flex on columns
   • opacity/visibility of title, subtitle, badge, CTA, image
   • collapsed label opacity (delayed)
```

Initial load is step-1: neither column has ACTIVE nor COLLAPSED. A click on an inactive panel sets ACTIVE on it and COLLAPSED on the sibling. A click on the already ACTIVE panel clears both modifiers and returns to step-1.

On viewports < 768px, the JS toggle is a no-op; both panels remain in step-1 layout (stacked vertically via the existing CSS media query).

## Components / Selectors

| Selector | Purpose |
|---|---|
| `body.home #wecar-hero .elementor-container` | 1216×460 wrapper, `gap:20px`, `overflow:hidden` |
| `.wecar-hero__card` | Panel base (598px initial, `border-radius:40px`, `overflow:hidden`) |
| `.wecar-hero__card--left` / `--right` | Option-1/Option-2 background gradients and texture colors |
| `.wecar-hero__column--active .wecar-hero__card` | Expanded panel width target (976px via flex) |
| `.wecar-hero__column--collapsed .wecar-hero__card` | Collapsed panel (220px via flex, min-width guard) |
| `.elementor-icon-box-title`, `.elementor-icon-box-description` | Main text; fade out before width transition completes |
| `.wecar-hero__card__badge-wrapper`, `.wecar-hero__card__cta` | Slide-up + fade-in on active; hidden on collapsed |
| `.wecar-hero__card__image` | Bottom-right car; `right:-10%` for left panel, transitions width/height |
| `.elementor-column::after` | Collapsed label "Comprá"/"Vendé", centered, delayed fade |

## Animation Timing

- **Panel width**: `flex 0.5s cubic-bezier(0.4,0,0.2,1)`.
- **Collapse sequence**:
  - **Title/subtitle fade-out**: `opacity 0.2s ease-out` with no delay so it starts immediately.
  - **Badge/CTA fade-out**: `opacity 0.2s ease-out` concurrently with title.
  - **Car fade-out**: `opacity 0.2s ease-out` concurrently.
  - **Width transition**: starts at the same time, lasts 0.5s.
  - **Collapsed label fade-in**: `opacity 0.3s ease` delayed ~0.25s so it appears after main text is gone.
- **Expand sequence**:
  - **Width transition**: `flex 0.5s cubic-bezier(0.4,0,0.2,1)` starts immediately.
  - **Title/subtitle fade-in**: `opacity 0.2s ease-out` no delay (concurrent with width).
  - **Badge/CTA slide-up + fade-in**: `opacity 0.3s ease-out, transform 0.3s ease-out` with 0.1s delay; `translateY(10px) → 0`. Begins while title is still appearing (visual overlap acceptable per spec REQ-HOME-HP01 stagger tolerance).
  - **Car fade-in**: `opacity 0.3s ease-out` concurrent with width.
- **Rapid clicking**: CSS transitions restart gracefully. Potential visual flicker on collapsed label is acceptable for the target audience and device profile.

## Car Positioning

`.wecar-hero__card__image` is absolute `bottom:0; right:0`. For the left panel we apply `right:-10%` to match Figma overflow intent. Sizes are driven by the parent column state:

- step-1: `width:530px; height:270px;`
- step-2 (left active): left image `490×250`, right image hidden/kept inside 220px collapsed.
- step-3 (right active): right image `530×270`, left image hidden.

The panel/card `overflow:hidden` clips any negative offset.

## Textures

Replace the current `::before` SVG texture with layered radial-gradients on `.wecar-hero__card--left::before` and `.wecar-hero__card--right::before`. Implementation per REQ-HOME-HP03:

- Left: `radial-gradient(circle at 99% 100%, rgba(153,73,255,0.10) 0%, rgba(14,181,209,0) 100%)` plus a subtle `rgba(153,73,255,0.04)` overlay.
- Right: two radial-gradients at `99% 100%` using `rgba(245,237,255,0.20)` and `rgba(249,253,254,0.20)`.

`background-size` ~423×200, `background-position: bottom right`, `pointer-events:none`, `z-index:0`.

## File Changes

| File | Action | Description |
|---|---|---|
| `wp-content/themes/vehica-child/assets/css/home-hero.css` | Modify | Rewrite accordion, texture, collapsed-label, and car-position sections; keep mobile media query. |
| `wp-content/themes/vehica-child/assets/js/home-animations.js` | Modify | Add step-1/2/3 toggle logic and reset on second click. |

## Interfaces / Contracts

No new API or types. Contract is CSS class-based:

- Input: user click on `#wecar-hero .elementor-column`.
- Output: column classes `--active` / `--collapsed` reflect the requested step; all transitions are CSS-driven.

## Testing Strategy

| Layer | What | Approach |
|---|---|---|
| Static | CSS/JS loaded on test | `curl -I` the two asset URLs after deploy; expect 200. |
| Visual | Figma fidelity | Browser inspection: panel widths 598/976/220, label centered, car bottom-right, textures visible. |
| Behavioral | Cross-fade timing | Screen recording or DevTools: title opacity hits 0 before width ends; badge/CTA slide-up completes ~0.3s. |
| Responsive | Mobile <768px | Confirm cards stack and accordion JS is disabled/no-op. |

## Migration / Rollout

No migration. Deploy to `test.wecar.com.ar` only. After validation, flush WP cache and Elementor cache; no production push for this change.

## Assumptions

- **Typography and colors from spec REQ-HOME-HP04** (Syne Bold 700 46/44/38/16, Exo 2 22/30, palette `#111111`, `#464646`, `#FFFFFF`, `#F9F9F9`) are already applied by Elementor from the prior `home-figma-exact-2026-07` change. This polish animates `opacity` and `transform` only; static positioning/typography remains unchanged.
- **Content positions** (title at `x:40, y:120`, subtitle at `x:40, y:248`, badge reposition from `y:-64` to `y:40` on expand) match Figma via the existing Elementor layout and are not modified by this change.
- **`hero-car.png`** exists in `assets/images/` from the prior change (D-2 single asset).

## Risks

| Risk | Likelihood | Mitigation |
|---|---|---|
| Desfasaje entre toggle de clases JS y `transition-delay` del cross-fade | Low | Cambio de clases es instantáneo en JS; delays coordinados en CSS y validados con DevTools/screen recording. |
| Label colapsado solapado con badge/CTA/image residual | Low | Forzar `opacity:0` + `visibility:hidden` en badge, CTA e imagen del panel colapsado **antes** del fade-in del label. |
| Crecimiento del diff > 400 líneas | Medium | Limitar rewrite a secciones afectadas (accordion, texture, label, car pos). No renombrar clases existentes. Si excede, descartar alias opcional `--expanded`. |
| Auto con `right:-10%` se desborda si falta `overflow:hidden` | Low | Mantener `overflow:hidden !important` en `.wecar-hero__card` y columna. |
| Despliegue accidental en producción `wecar.com.ar` | Low | Deploy exclusivo a `test.wecar.com.ar`; validación de URL en script de deploy. |
| Compatibilidad `cubic-bezier(0.4,0,0.2,1)` en browsers viejos | Low | Fallback a `ease` si automáticamente no soportado; no prioritario para el target audience. |
| Click rápido entre estados genera flicker visual en label colapsado | Low | Aceptable; CSS transitions reinician gracefully. |

## Open Questions

- None.

## Forecast Lines Changed

Estimated diff: ~280–340 lines (≈220 CSS + ≈60–80 JS). Fits within the 400-line single-PR budget.
