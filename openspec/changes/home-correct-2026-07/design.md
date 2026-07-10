# Home Correction 2026-07 — Technical Design

> **Date**: 2026-07-01
> **Change**: `home-correct-2026-07`
> **Status**: Ready for fresh-context review
> **Source of truth**: spec.md + proposal.md + exploration.md

---

## 1. Architecture Overview

The correction stays inside the existing WordPress + Elementor + vehica-child stack. No new plugins, no new page templates, no backend changes. We replace the visual layer of the front page (post 35463) by:

1. Updating the Elementor JSON (`_elementor_data`) on **test only**.
2. Rewriting the section CSS and the scroll-animation JS in `vehica-child`.
3. Adding one new tiling SVG asset.
4. Reordering the vehicle-card tag chips in the existing shortcode.

```
[ Browser ]
   ↓
[ Elementor renders post 35463 using _elementor_data from DB ]
   ↓
[ vehica-child enqueues: tokens.css, home-*.css, home-animations.js ]
   ↓
[ Shortcode [wecar_vehicle_carousel] injects vehicle cards from vehica_car posts ]
```

### High-level decisions

- **Elementor data first, CSS/JS second**: the 7-section JSON from `home-redesign` is structurally reused. Widget copy, CSS classes, and section backgrounds are edited in the JSON; animation and decorative styling live in code.
- **State-based scroll animation**: IntersectionObserver thresholds at 25/50/75/100% drive discrete CSS frame classes. No scroll-jacking, no pinning.
- **Single reusable SVG pattern**: one tiling `wavy-pattern.svg` is referenced by URL in every section that needs it, with color controlled via `currentColor` and opacity controlled per section in CSS.
- **Test-only deploy**: every SSH/WP-CLI path targets `~/www/test.wecar.com.ar/public_html/`. Production promotion is a separate future change.

---

## 2. File-Level Changes

### 2.1 NEW: `assets/images/wavy-pattern.svg`

- **REQ satisfied**: REQ-HOME-024
- **Type**: SVG asset, ~1–3 KB, tiling pattern
- **Design constraints**:
  - Color: configurable via CSS `currentColor` (use `stroke="currentColor"` on the path).
  - Opacity: controlled by parent element's `opacity` (set in CSS at 0.05–0.15).
  - Pattern density: ~5–8 wavy strokes per 200×200 tile.
  - Format: SVG, no raster fallback needed (modern browsers only — consistent with NFR-HOME-014).
- **Usage in CSS**: `background-image: url('../images/wavy-pattern.svg'); background-repeat: repeat;`
- **CSS variable for color**: `--wecar-wavy-color` (defaults to `currentColor`).
- **Pattern shape**: based on the visual reference in `new-design/home.png` — soft horizontal squiggles, ~3–5 px stroke width, organic feel (not geometric).
- **Proposed SVG content** (tiling 200×200 viewBox, 6 squiggles):

```svg
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 200 200" width="200" height="200">
  <g fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round">
    <path d="M10 30 Q 30 10, 50 30 T 90 30 T 130 30 T 170 30 T 210 30" />
    <path d="M-10 60 Q 10 40, 30 60 T 70 60 T 110 60 T 150 60 T 190 60" />
    <path d="M10 90 Q 30 70, 50 90 T 90 90 T 130 90 T 170 90 T 210 90" />
    <path d="M-10 120 Q 10 100, 30 120 T 70 120 T 110 120 T 150 120 T 190 120" />
    <path d="M10 150 Q 30 130, 50 150 T 90 150 T 130 150 T 170 150 T 210 150" />
    <path d="M-10 180 Q 10 160, 30 180 T 70 180 T 110 180 T 150 180 T 190 180" />
  </g>
</svg>
```

#### 2.1.1 Pattern color per section

| Section | `::before` color | Reasoning |
|---------|------------------|-----------|
| Hero left card (`.wecar-hero__card--left::before`) | `var(--wecar-text)` (dark) | Dark pattern on light bg |
| Hero right card (`.wecar-hero__card--right::before`) | `#FFFFFF` | White pattern on blue bg |
| Carousel section (`.wecar-carousel-section::before`) | `#FFFFFF` | White pattern on purple→blue gradient |
| Partners section (`.wecar-partners::before`) | `var(--wecar-purple)` | Purple pattern on lavender bg |
| Footer (`.wecar-footer::before`) | `var(--wecar-purple-light)` | Subtle purple pattern |

### 2.2 MODIFIED: `assets/css/home-hero.css`

- **REQ satisfied**: REQ-HOME-002 (MODIFIED)
- **Change type**: MAJOR REWRITE
- **Elementor JSON dependency**: the two hero Icon Box widgets must have `_css_classes` changed from `wecar-hero-card wecar-hero-card--comprar` / `wecar-hero-card wecar-hero-card--vender` to `wecar-hero__card wecar-hero__card--left` and `wecar-hero__card wecar-hero__card--right`; icon and inner button widgets must be removed from each column.
- **What changes**:
  - REMOVE: `.wecar-hero-card--comprar .elementor-icon`, `.wecar-hero-card--vender .elementor-icon` (no more icons in cards).
  - REMOVE: `.wecar-hero-card .elementor-button` (no more CTA buttons in cards).
  - KEEP: two-column 50/50 layout, but increase internal padding to `var(--wecar-space-12)` vertical and `var(--wecar-space-8)` horizontal.
  - ADD: `.wecar-hero__card` base with `position: relative; overflow: hidden; border-radius: var(--wecar-radius-xl);`.
  - ADD: `.wecar-hero__card--left { background: linear-gradient(180deg, #FFFFFF 0%, #F5F3FF 100%); color: var(--wecar-text); }`.
  - ADD: `.wecar-hero__card--right { background: linear-gradient(180deg, #36BFFA 0%, #2563EB 100%); color: #FFFFFF; }`.
  - ADD: `.wecar-hero__card::before` pseudo-element for the wavy pattern overlay:
    ```css
    .wecar-hero__card::before {
      content: '';
      position: absolute;
      inset: 0;
      background-image: url('../images/wavy-pattern.svg');
      background-repeat: repeat;
      color: inherit;
      opacity: 0.08;
      pointer-events: none;
      z-index: 0;
    }
    ```
  - ADD: `.wecar-hero__card > * { position: relative; z-index: 1; }` to keep text above the pattern.
  - MODIFY: title selector from `.wecar-hero-card .elementor-heading-title` to `.wecar-hero__card .elementor-heading-title`; font-size `var(--wecar-text-2xl)`, line-height `var(--wecar-line-tight)`.
  - MODIFY: description selector to `.wecar-hero__card .elementor-widget-text-editor`; font-size `var(--wecar-text-lg)`, opacity 0.85, max-width 480 px.
- **Color tokens used**: `--wecar-purple-light`, `--wecar-blue-medium`, `--wecar-lavender-bg` equivalent `#F5F3FF`.
- **Responsive**: cards stack vertically below 768 px viewport (existing breakpoint); on mobile each card keeps full gradient and padding reduces to `var(--wecar-space-8)` vertical.
- **Lines changed estimate**: ~90 lines (removals + new gradient rules).

### 2.3 MODIFIED: `assets/css/home-steps.css`

- **REQ satisfied**: REQ-HOME-003, REQ-HOME-018, NFR-HOME-010
- **Change type**: MAJOR REWRITE
- **Elementor JSON dependency**: section `_element_id` stays `wecar-steps`; the section wrapper must gain class `wecar-steps` (in addition to the id) so JS can target it. Each step card's `_css_classes` changes from `wecar-step wecar-step--1/2/3` to `wecar-steps__card wecar-steps__card--1/2/3`. A new Text Editor or Button widget is added as the final CTA with class `wecar-steps__cta`.
- **What changes**:
  - REPLACE: large "01/02/03" numbers with small circle indicators (24 px diameter, solid color per step).
  - REPLACE: `.wecar-step--hidden` / `.wecar-step--visible` fade-in keyframes with state-based classes:
    - `.wecar-steps--frame-1`, `.wecar-steps--frame-2`, `.wecar-steps--frame-3`, `.wecar-steps--frame-4` added by JS based on IntersectionObserver.
  - ADD: `.wecar-steps__track` — the horizontal track containing circles and line; max-width 720 px, centered, position relative.
  - ADD: `.wecar-steps__line` — the full gradient connecting line, height 3 px, background `linear-gradient(90deg, var(--wecar-purple-light) 0%, var(--wecar-blue) 50%, var(--wecar-cyan) 100%)`, positioned absolutely between the first and last circle.
  - ADD: `.wecar-steps__line-fill` — the filled portion, a full-width element scaled via `transform: scaleX(var(--wecar-line-fill-scale, 0))`, background the same gradient, transitioned via CSS `transform 0.6s cubic-bezier(0.4, 0, 0.2, 1)`.
  - ADD: `.wecar-steps__circle` (24 px × 24 px, border-radius 50%, border 2 px solid current step color, color from `--wecar-step-N`) with numbered text centered.
  - ADD: `.wecar-steps__circle--active` (background filled with step color, text white).
  - ADD: frame-specific rules to light circles and fill the line:
    ```css
    .wecar-steps--frame-1 { --wecar-line-fill-scale: 0; }
    .wecar-steps--frame-2 { --wecar-line-fill-scale: 0; .wecar-steps__circle--1 { background: var(--wecar-step-1); color: #fff; } }
    .wecar-steps--frame-3 { --wecar-line-fill-scale: 0.5; .wecar-steps__circle--1, .wecar-steps__circle--2 { background: var(--wecar-step-1); color: #fff; } .wecar-steps__circle--2 { background: var(--wecar-step-2); } }
    .wecar-steps--frame-4 { --wecar-line-fill-scale: 1; .wecar-steps__circle--1 { background: var(--wecar-step-1); } .wecar-steps__circle--2 { background: var(--wecar-step-2); } .wecar-steps__circle--3 { background: var(--wecar-step-3); } }
    ```
  - ADD: `.wecar-steps__card` white card with soft shadow, opacity 0 by default, opacity 1 when its frame is active:
    ```css
    .wecar-steps__card { opacity: 0; transform: translateY(16px); transition: opacity 0.5s ease, transform 0.5s ease; }
    .wecar-steps--frame-2 .wecar-steps__card--1,
    .wecar-steps--frame-3 .wecar-steps__card--1,
    .wecar-steps--frame-3 .wecar-steps__card--2,
    .wecar-steps--frame-4 .wecar-steps__card { opacity: 1; transform: translateY(0); }
    ```
  - ADD: `.wecar-steps__cta` final CTA button, opacity 0 by default, opacity 1 in frame 4.
  - MODIFY: section title font-size `var(--wecar-text-2xl)`, max-width 720 px, centered.
  - MODIFY: card text styles for the new copy (multi-line body text, `line-height: var(--wecar-line-relaxed)`).
- **Lines changed estimate**: ~160 lines.

### 2.4 MODIFIED: `assets/css/home-carousel.css`

- **REQ satisfied**: REQ-HOME-005 (reuse), REQ-HOME-019, REQ-HOME-020, REQ-HOME-021, REQ-HOME-025
- **Change type**: MAJOR REWRITE
- **Elementor JSON dependency**: section `_element_id` `wecar-carousel` must gain class `wecar-carousel-section`. Add a Heading or Text Editor widget with class `wecar-carousel__link` for "Ver todos →" and a Button widget with class `wecar-carousel__cta` for "Contactar con un asesor".
- **What changes**:
  - ADD: section background on `.wecar-carousel-section`:
    ```css
    background: linear-gradient(135deg, #5E3BE0 0%, #2563EB 100%);
    ```
  - ADD: `.wecar-carousel-section::before` wavy pattern overlay, opacity 0.06, color white.
  - ADD: `.wecar-carousel__header` flex row (title left, link right) with `justify-content: space-between; align-items: center;`.
  - ADD: `.wecar-carousel__title` white, `var(--wecar-text-xl)`.
  - ADD: `.wecar-carousel__link` white, no underline, arrow via `::after { content: ' →'; }`.
  - ADD: `.wecar-carousel__cta` bottom CTA button, centered below the carousel:
    ```css
    background: #FFFFFF;
    color: var(--wecar-purple);
    border-radius: var(--wecar-radius-full);
    padding: var(--wecar-space-3) var(--wecar-space-8);
    ```
  - MODIFY: `.wecar-vehicle-card__tags` gap and tag colors. Tags now use light blue/white background:
    ```css
    .wecar-vehicle-card__tag {
      background: #E0F2FE;
      color: #1E3A8A;
      border: none;
    }
    ```
  - REMOVE: `.wecar-vehicle-card__tag--km` special background; all tags share the same chip style.
- **Lines changed estimate**: ~130 lines.

### 2.5 MODIFIED: `assets/css/home-features.css`

- **REQ satisfied**: REQ-HOME-007 (MODIFIED), REQ-HOME-022
- **Change type**: MAJOR REWRITE
- **Elementor JSON dependency**: section `_element_id` `wecar-features` must gain class `wecar-features-section`. Each feature Icon Box `_css_classes` changes from `wecar-feature-card wecar-feature-card--confianza/transparencia/facilidad` to `wecar-features__card wecar-features__card--purple/blue/teal`. Section background is moved from widget-level to section-level gradient.
- **What changes**:
  - ADD: section background on `.wecar-features-section`:
    ```css
    background: linear-gradient(180deg, #FFFFFF 0%, #F5F3FF 100%);
    ```
  - REPLACE: card style from "white card with icon + title + description" to "solid color card with white icon + white text".
  - ADD:
    ```css
    .wecar-features__card--purple { background: #5E3BE0; }
    .wecar-features__card--blue { background: #2563EB; }
    .wecar-features__card--teal { background: #06B6D4; }
    ```
  - ADD: `.wecar-features__card` padding `var(--wecar-space-8)`, border-radius `var(--wecar-radius-lg)`, text-align left, min-height 220 px.
  - MODIFY: `.wecar-features__card .elementor-icon` color white, font-size 2.5 rem, margin-bottom `var(--wecar-space-4)`.
  - MODIFY: `.wecar-features__card .elementor-heading-title` color white, font-size `var(--wecar-text-lg)`, font-weight `var(--wecar-weight-bold)`, line-height `var(--wecar-line-tight)`.
- **Lines changed estimate**: ~100 lines.

### 2.6 MODIFIED: `assets/css/home-partners.css`

- **REQ satisfied**: REQ-HOME-008 (MODIFIED), REQ-HOME-023
- **Change type**: MAJOR REWRITE
- **Elementor JSON dependency**: section `_element_id` `wecar-partners` must gain class `wecar-partners`. Title widget text changes to "Respaldado por grandes marcas". Image widgets keep class `wecar-partner-placeholder` for v1.
- **What changes**:
  - REMOVE: dashed-border placeholder styles from `.wecar-partner-placeholder`.
  - ADD: section background on `.wecar-partners`:
    ```css
    background: linear-gradient(180deg, #C7B8FF 0%, #A78BFA 100%);
    ```
  - ADD: `.wecar-partners::before` wavy pattern overlay, opacity 0.06, color `#5E3BE0`.
  - MODIFY: section title color dark (`var(--wecar-text)`), font-size `var(--wecar-text-xl)`.
  - MODIFY: `.wecar-partner-placeholder` to render as white text on transparent background, font-weight bold, with a subtle white logo-shaped container (no dashed border):
    ```css
    .wecar-partner-placeholder {
      display: flex;
      align-items: center;
      justify-content: center;
      width: 180px;
      height: 70px;
      background: rgba(255, 255, 255, 0.25);
      border-radius: var(--wecar-radius-md);
      color: #FFFFFF;
      font-size: var(--wecar-text-sm);
      font-weight: var(--wecar-weight-bold);
      text-transform: uppercase;
      letter-spacing: 0.03em;
    }
    ```
  - KEEP: 3-logo horizontal layout, even spacing.
- **Lines changed estimate**: ~70 lines.

### 2.7 MODIFIED: `assets/css/home-footer.css`

- **REQ satisfied**: REQ-HOME-009 (MODIFIED)
- **Change type**: MINOR
- **Elementor JSON dependency**: footer text widget that contains the phone number must be updated from `+54 9 11 1234-5678` to `+54 9 3534 41-3243` and wrapped in an `<a href="tel:+5493534413243">`. Section `_element_id` `wecar-footer` must gain class `wecar-footer`.
- **What changes**:
  - MODIFY: footer background from dark (`var(--wecar-footer-bg)`) to white; text colors to dark variants.
  - MODIFY: phone number color to `var(--wecar-purple-light)` (lavender).
  - ADD: subtle wavy pattern overlay via `.wecar-footer::before` at opacity 0.05, color `var(--wecar-purple-light)`.
  - ADD: `.wecar-footer__phone` uses `color: var(--wecar-purple-light);`.
  - KEEP: copyright text "2026 Custer. All rights reserved." aligned right on desktop, centered on mobile.
- **Lines changed estimate**: ~30 lines.

### 2.8 MODIFIED: `assets/css/home-header.css`

- **REQ satisfied**: REQ-HOME-001 (no change to REQ, but visual alignment with new design)
- **Change type**: MINOR
- **Elementor JSON dependency**: the header button widget already has text "Contactanos"; ensure it links to `/contactanos/`.
- **What changes**:
  - MODIFY: `.wecar-home-header .elementor-button` background to `var(--wecar-purple-light)` (#7B5CE8) and text color to `#FFFFFF` to match the lavender pill in the mockup.
  - ADD: hover state `background: var(--wecar-purple)`.
- **Lines changed estimate**: ~10 lines.

### 2.9 MODIFIED: `assets/js/home-animations.js`

- **REQ satisfied**: REQ-HOME-018, NFR-HOME-010
- **Change type**: MAJOR REWRITE
- **What changes**:
  - REPLACE: existing per-step IntersectionObserver (fade-in) with a single observer on `.wecar-steps`.
  - ADD: state-based observer configuration:
    ```javascript
    const observer = new IntersectionObserver(
      (entries) => {
        entries.forEach(entry => {
          const ratio = entry.intersectionRatio;
          const section = entry.target;
          let frame = 1;
          if (ratio >= 0.75) frame = 4;
          else if (ratio >= 0.5) frame = 3;
          else if (ratio >= 0.25) frame = 2;
          requestAnimationFrame(() => {
            let fillScale = 0;
            if (frame >= 4) fillScale = 1;
            else if (frame >= 3) fillScale = 2/3;
            else if (frame >= 2) fillScale = 1/3;
            section.style.setProperty('--wecar-line-fill-scale', fillScale);
            section.className = section.className.replace(/wecar-steps--frame-\d+/g, '') + ` wecar-steps--frame-${frame}`;
          });
        });
      },
      {
        threshold: [0, 0.25, 0.5, 0.75, 1.0],
        rootMargin: '0px',
      }
    );
    observer.observe(document.querySelector('.wecar-steps'));
    ```
  - ADD: feature detection for `IntersectionObserver`; if unsupported, set `wecar-steps--frame-4` immediately (graceful degradation per NFR-HOME-010).
  - ADD: `prefers-reduced-motion: reduce` detection — if true, immediately set `wecar-steps--frame-4` and skip the observer.
  - REMOVE: existing fade-in classes `.wecar-step--hidden` / `.wecar-step--visible` logic.
  - KEEP: header scroll behavior if present; if not present, no changes needed.
- **Lines changed estimate**: ~80 lines.

### 2.10 MODIFIED: `includes/shortcodes/wecar-vehicle-carousel.php`

- **REQ satisfied**: REQ-HOME-025
- **Change type**: MINOR
- **What changes**:
  - MODIFY: tag rendering order from `[year, km-placeholder, fuel, transmission]` to `[km, year, transmission]`.
  - REMOVE: fuel chip rendering (`$fuel` variable and the corresponding `<span>`).
  - KEEP: the "Consultar KM" placeholder because real km data is not yet available in the DB.
- **Implementation detail**: replace the `<div class="wecar-vehicle-card__tags">` block with:
  ```php
  <div class="wecar-vehicle-card__tags">
      <span class="wecar-vehicle-card__tag"><?php echo esc_html__('Consultar KM', 'vehica'); ?></span>
      <?php if ($year) : ?>
          <span class="wecar-vehicle-card__tag"><?php echo esc_html($year); ?></span>
      <?php endif; ?>
      <?php if ($transmission) : ?>
          <span class="wecar-vehicle-card__tag"><?php echo esc_html($transmission); ?></span>
      <?php endif; ?>
  </div>
  ```
- **Lines changed estimate**: ~15 lines.

### 2.11 NEW: `elementor/home-35463-new.json` (in this change dir, not in theme)

- **REQ satisfied**: REQ-HOME-002 (copy), REQ-HOME-003 (copy), REQ-HOME-007 (copy), REQ-HOME-008 (copy), REQ-HOME-009 (phone)
- **Type**: Elementor data export (JSON, ~24 KB)
- **Source**: copied from `openspec/changes/home-redesign/elementor/home-35463-new.json`, then edited locally.
- **Edits required**:
  - Hero section (`_element_id`: `wecar-hero`):
    - Remove icon widgets and button widgets from both columns.
    - Update left card `title_text` to "Encontrá tu próximo auto", `description_text` to "La oferta mas grande de vehículos de Villa María y Villa Nueva".
    - Update right card `title_text` to "Vendé tu auto sin dejar de manejarlo", `description_text` to "Simplificamos tu venta particular, enviamos los datos, lo cotizamos, publicamos y vendemos por vos.".
    - Update `_css_classes` on each Icon Box to `wecar-hero__card wecar-hero__card--left` and `wecar-hero__card wecar-hero__card--right`.
    - Update section/background gradients: left card white→#F5F3FF, right card #36BFFA→#2563EB.
  - 3-step section (`_element_id`: `wecar-steps`):
    - Add class `wecar-steps` to the section settings (`_css_classes`).
    - Update section title to "Vendé tu auto al mejor precio. Usalo hasta el último día".
    - Update step 1 title/body, step 2 title/body, step 3 title/body per REQ-HOME-003.
    - Add the connecting-line markup and circles. Because Elementor JSON cannot easily represent raw markup, inject them via a Text Editor widget with HTML at the top of the section, or add them as pseudo-elements in CSS (recommended: CSS pseudo-elements, no markup change required). If pseudo-elements are insufficient, use an HTML widget with the track markup.
    - Add a final Button widget with text "Vendé tu usado sin vueltas", link `/vende-tu-auto/` (fallback `/cotiza/`), class `wecar-steps__cta`.
  - Carousel section (`_element_id`: `wecar-carousel`):
    - Add class `wecar-carousel-section`.
    - Change section background to gradient #5E3BE0→#2563EB.
    - Add "Ver todos →" link widget (top-right) with class `wecar-carousel__link`.
    - Add bottom CTA button "Contactar con un asesor" linking to `/contactanos/`, class `wecar-carousel__cta`.
  - Elegí Wecar section (`_element_id`: `wecar-features`):
    - Add class `wecar-features-section`.
    - Change section background to gradient #FFFFFF→#F5F3FF.
    - Update card titles per REQ-HOME-007.
    - Update `_css_classes` to `wecar-features__card wecar-features__card--purple/blue/teal`.
  - Partners section (`_element_id`: `wecar-partners`):
    - Add class `wecar-partners`.
    - Change section background to gradient #C7B8FF→#A78BFA.
    - Update section title to "Respaldado por grandes marcas".
    - Keep 3 placeholder image widgets; class stays `wecar-partner-placeholder`.
  - Footer (`_element_id`: `wecar-footer`):
    - Add class `wecar-footer`.
    - Update phone number text to `+54 9 3534 41-3243` and wrap in `tel:+5493534413243` link.
    - Change section background to white.
- **Deployment**: applied to test DB via:
  ```bash
  ssh wecar "wp post meta update 35463 _elementor_data --format=json < elementor/home-35463-new.json --path=~/www/test.wecar.com.ar/public_html --allow-root"
  ```

---

## 3. Data Flow

```
[ User visits test.wecar.com.ar/ ]
  ↓
[ WordPress routes to post 35463 (front page) ]
  ↓
[ Elementor reads _elementor_data from wp_postmeta → renders widgets ]
  ↓
[ Each section's HTML is wrapped in Elementor containers with custom CSS classes ]
  ↓
[ vehica-child enqueues: tokens.css, home-*.css (per-section), home-animations.js ]
  ↓
[ Shortcode [wecar_vehicle_carousel] runs: WP_Query for vehica_car posts ]
  ↓
[ Each vehicle card is rendered with: image, title, variant, price, tags (km, year, transmission) ]
  ↓
[ home-animations.js initializes: header sticky behavior, IntersectionObserver for 3-step section ]
  ↓
[ User scrolls: 3-step section transitions through 4 frames based on scroll position ]
  ↓
[ Cache: SiteGround Optimizer + WP Rocket (flush via WP-CLI after apply) ]
```

### Cache invalidation

- After `_elementor_data` update: `wp cache flush --path=~/www/test.wecar.com.ar/public_html --allow-root`.
- After CSS/JS changes: bump child theme version in `style.css` or use `wp_enqueue_*` version argument; then flush cache.
- Verify Elementor regenerated CSS:
  ```bash
  ssh wecar "wc -c ~/www/test.wecar.com.ar/public_html/wp-content/uploads/elementor/css/post-35463.css"
  ```
  Must be > 50,000 bytes.

---

## 4. Scroll Animation Strategy (detailed)

### 4.1 State machine

```
Frame 1: TITLE_ONLY
  ↓ (section 25% visible)
Frame 2: STEP_1_VISIBLE
  ↓ (section 50% visible)
Frame 3: STEP_1_2_VISIBLE
  ↓ (section 75% visible)
Frame 4: ALL_STEPS_VISIBLE
  ↓ (section exits viewport)
END
```

### 4.2 IntersectionObserver configuration

```javascript
(function () {
  'use strict';

  if (!document.body.classList.contains('home')) return;

  const section = document.querySelector('.wecar-steps');
  if (!section) return;

  const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
  if (prefersReducedMotion || !('IntersectionObserver' in window)) {
    section.classList.add('wecar-steps--frame-4');
    return;
  }

  const observer = new IntersectionObserver(
    (entries) => {
      entries.forEach(entry => {
        const ratio = entry.intersectionRatio;
        const target = entry.target;
        let frame = 1;
        if (ratio >= 0.75) frame = 4;
        else if (ratio >= 0.5) frame = 3;
        else if (ratio >= 0.25) frame = 2;

        requestAnimationFrame(() => {
          let fillScale = 0;
          if (frame >= 4) fillScale = 1;
          else if (frame >= 3) fillScale = 2/3;
          else if (frame >= 2) fillScale = 1/3;
          target.style.setProperty('--wecar-line-fill-scale', fillScale);
          target.className = target.className.replace(/wecar-steps--frame-\d+/g, '') + ` wecar-steps--frame-${frame}`;
        });
      });
    },
    {
      threshold: [0, 0.25, 0.5, 0.75, 1.0],
      rootMargin: '0px',
    }
  );

  observer.observe(section);
})();
```

### 4.3 CSS transitions

- Circle fill-in:
  ```css
  .wecar-steps__circle {
    background: transparent;
    color: var(--circle-color);
    transition: background-color 0.4s ease-out, color 0.4s ease-out;
  }
  .wecar-steps__circle--active {
    background: var(--circle-color);
    color: #fff;
  }
  ```
- Line fill-in:
  ```css
.wecar-steps__line-fill {
  width: 100%;
  transform: scaleX(var(--wecar-line-fill-scale, 0));
  transform-origin: left center;
  transition: transform 0.6s cubic-bezier(0.4, 0, 0.2, 1);
}
  ```
- Card reveal:
  ```css
  .wecar-steps__card {
    opacity: 0;
    transform: translateY(16px);
    transition: opacity 0.5s ease-out, transform 0.5s ease-out;
  }
  .wecar-steps--frame-2 .wecar-steps__card--1,
  .wecar-steps--frame-3 .wecar-steps__card--1,
  .wecar-steps--frame-3 .wecar-steps__card--2,
  .wecar-steps--frame-4 .wecar-steps__card {
    opacity: 1;
    transform: translateY(0);
  }
  ```
- CTA button:
  ```css
  .wecar-steps__cta {
    opacity: 0;
    transform: translateY(12px);
    transition: opacity 0.4s ease-out, transform 0.4s ease-out;
  }
  .wecar-steps--frame-4 .wecar-steps__cta {
    opacity: 1;
    transform: translateY(0);
  }
  ```

### 4.4 Graceful degradation

- If `IntersectionObserver` is not available (~0.1% of users), the script sets `wecar-steps--frame-4` immediately.
- If `prefers-reduced-motion: reduce` is active, the script sets `wecar-steps--frame-4` immediately.
- If JavaScript fails entirely, the base CSS should show all content. Add a no-JS fallback:
  ```css
  .wecar-steps:not(.wecar-steps--frame-1):not(.wecar-steps--frame-2):not(.wecar-steps--frame-3):not(.wecar-steps--frame-4) .wecar-steps__card,
  .wecar-steps:not(.wecar-steps--frame-1):not(.wecar-steps--frame-2):not(.wecar-steps--frame-3):not(.wecar-steps--frame-4) .wecar-steps__cta {
    opacity: 1;
    transform: none;
  }
  ```

### 4.5 Performance considerations (NFR-HOME-010)

- Use `requestAnimationFrame` for DOM class changes (avoid layout thrash).
- Animate only `transform` and `opacity`.
- The line fill uses `transform: scaleX()`, which is GPU-accelerated and does not trigger layout. The fill is a full-width element with `transform-origin: left center`, and JS writes a `--wecar-line-fill-scale` custom property (0, 1/3, 2/3, or 1) based on the current frame. This satisfies NFR-HOME-010 (only `transform` and `opacity` for animations).
- Debounce the observer callback if needed (with thresholds, debouncing is usually unnecessary).
- Test on mobile devices (iPhone 12, mid-range Android).

---

## 5. Color & Typography System

### Existing tokens (from `tokens.css`)

- `--wecar-purple: #5E3BE0` (primary)
- `--wecar-purple-light: #7B5CE8`
- `--wecar-blue: #2563EB`
- `--wecar-blue-medium: #3B82F6`
- `--wecar-cyan: #36BFFA`
- `--wecar-cyan-dark: #06B6D4`
- `--wecar-lavender-bg` is **not present** in `tokens.css`; use literal `#F5F3FF` in CSS and Elementor JSON, or add `--wecar-lavender-bg: #F5F3FF` to `tokens.css`.

### New tokens needed

Add to `assets/css/tokens.css`:

```css
:root {
  --wecar-lavender-bg: #F5F3FF;
  --wecar-wavy-opacity: 0.1;
  /* Step circle colors (per REQ-HOME-003): lavender, blue, cyan-dark */
  --wecar-step-1: var(--wecar-purple-light);
  --wecar-step-2: var(--wecar-blue);
  --wecar-step-3: var(--wecar-cyan-dark);
}
```

### Typography notes

- Hero titles: `var(--wecar-text-2xl)` (clamp 2rem–3.5rem).
- Hero body: `var(--wecar-text-lg)`.
- Section titles: `var(--wecar-text-2xl)`.
- Step titles: `var(--wecar-text-lg)` bold.
- Step body: `var(--wecar-text-sm)` with `line-height: var(--wecar-line-relaxed)`.
- Feature titles: `var(--wecar-text-lg)` bold.

---

## 6. Risk Mitigation

| Risk | Mitigation |
|------|-----------|
| Production accident | Hard rule: every SSH command uses `~/www/test.wecar.com.ar/public_html/`. No production paths. |
| Branch trap (`feat/redesign-prod`) | Renamed to `home-correct-2026-07` (tracker) in sdd-apply before any commits. PR-1 and PR-2 live on child branches. |
| 27-key Elementor data rule | After `wp post meta update`, verify `post-35463.css` > 50 KB on test. If not, follow `openspec/specs/elementor-data-restoration.md`. |
| Scroll animation jank on mobile | State-based (not pinned), rAF for class changes, transform/opacity only; line fill uses `transform: scaleX()` on one full-width element. |
| CSS file regeneration | `wp cache flush` after apply; delete `post-35463.css` and trigger a page load if Elementor does not regenerate. |
| Scope creep | Stay surgical: only the files listed in section 2. No child theme refactor, no dashboard changes. |
| Real partner logo files | v1 ships placeholders. v2 follow-up tracked in REQ-HOME-026. |
| Class-name mismatch between JSON and CSS | Maintain a class-name map in sdd-tasks; verify every designed selector has a matching `_css_classes` value in the JSON before apply. |
| Missing `--wecar-lavender-bg` token | Add the token to `tokens.css` as part of this change. |

---

## 7. Deployment Plan (Test Only)

1. **Branch rename**: `git branch -m feat/redesign-prod home-correct-2026-07` (the tracker branch; PR-1 and PR-2 will live on `-pr1` and `-pr2` child branches)
2. **Local edit**: update CSS, JS, shortcode, generate SVG, add token to `tokens.css`, prepare Elementor JSON.
3. **Local commit**: `git add ... && git commit -m "feat(home-correct-2026-07): ..."` (conventional commits, no AI co-author).
4. **SCP child theme assets** to test:
   ```powershell
   scp wp-content/themes/vehica-child/assets/css/home-*.css wecar:~/www/test.wecar.com.ar/public_html/wp-content/themes/vehica-child/assets/css/
   scp wp-content/themes/vehica-child/assets/js/home-animations.js wecar:~/www/test.wecar.com.ar/public_html/wp-content/themes/vehica-child/assets/js/
   scp wp-content/themes/vehica-child/assets/images/wavy-pattern.svg wecar:~/www/test.wecar.com.ar/public_html/wp-content/themes/vehica-child/assets/images/
   scp wp-content/themes/vehica-child/includes/shortcodes/wecar-vehicle-carousel.php wecar:~/www/test.wecar.com.ar/public_html/wp-content/themes/vehica-child/includes/shortcodes/
   ```
5. **Apply Elementor JSON to test**:
   ```bash
   ssh wecar "wp post meta update 35463 _elementor_data --format=json < elementor/home-35463-new.json --path=~/www/test.wecar.com.ar/public_html --allow-root"
   ```
6. **Flush cache**:
   ```bash
   ssh wecar "wp cache flush --path=~/www/test.wecar.com.ar/public_html --allow-root"
   ```
7. **Verify CSS file size**:
   ```bash
   ssh wecar "wc -c ~/www/test.wecar.com.ar/public_html/wp-content/uploads/elementor/css/post-35463.css"
   ```
   Must be > 50,000 bytes.
8. **Visual verification**: open `test.wecar.com.ar` in browser, screenshot, compare to mockups.
9. **Open PR**: push branch, open PR to main.
10. **NO PRODUCTION DEPLOY.** Production promotion is a separate future change.

---

## 8. Open Implementation Questions for sdd-tasks

- Which exact CSS selector to use for the wavy pattern overlay (z-index, pointer-events) — the design proposes `::before` with `pointer-events: none` and `z-index: 0`.
- How to handle the `requestAnimationFrame` polyfill if needed — none required; modern browsers only.
- Exact breakpoint for hero card stacking — 767 px (existing project convention).
- Whether to use `prefers-reduced-motion` media query to disable the animation for users with motion sensitivity — **RECOMMEND: yes**, implemented in JS by jumping to frame 4 and in CSS by reducing transitions.
- Whether to add `--wecar-lavender-bg` to `tokens.css` or use the literal hex value everywhere — **RECOMMEND: add the token** for consistency.
- How to position the connecting line: absolute positioning relative to the section, with `top` set so the line aligns with the vertical center of the step circles. Implementation detail, not a design decision — the line element is `.wecar-steps__line` and `.wecar-steps__line-fill` (already specified in section 4.3).

---

## 9. Summary of Change Magnitude

| Category | Count | Lines estimate |
|----------|-------|----------------|
| NEW files | 2 (`wavy-pattern.svg`, `elementor/home-35463-new.json`) | ~60 (SVG) + ~24 KB (JSON, not counted in line budget) |
| MAJOR REWRITE CSS | 5 (hero, steps, carousel, features, partners) | ~550 |
| MINOR MODIFY CSS | 3 (header, footer, tokens.css) | ~50 |
| MAJOR REWRITE JS | 1 (animations) | ~80 |
| MINOR MODIFY PHP | 1 (shortcode) | ~15 |
| **Total estimated lines** | **12 files** | **~755** |

This exceeds the 400-line review budget → **chained PRs required** (planned in sdd-tasks).

---

## 10. Class-Name Map (for sdd-apply)

| Element | Old class (home-redesign) | New class (home-correct-2026-07) |
|---------|---------------------------|----------------------------------|
| Hero left card | `wecar-hero-card wecar-hero-card--comprar` | `wecar-hero__card wecar-hero__card--left` |
| Hero right card | `wecar-hero-card wecar-hero-card--vender` | `wecar-hero__card wecar-hero__card--right` |
| 3-step section | `#wecar-steps` | `wecar-steps` (class added) |
| Step 1 card | `wecar-step wecar-step--1` | `wecar-steps__card wecar-steps__card--1` |
| Step 2 card | `wecar-step wecar-step--2` | `wecar-steps__card wecar-steps__card--2` |
| Step 3 card | `wecar-step wecar-step--3` | `wecar-steps__card wecar-steps__card--3` |
| Carousel section | `#wecar-carousel` | `wecar-carousel-section` (class added) |
| Features section | `#wecar-features` | `wecar-features-section` (class added) |
| Feature card 1 | `wecar-feature-card wecar-feature-card--confianza` | `wecar-features__card wecar-features__card--purple` |
| Feature card 2 | `wecar-feature-card wecar-feature-card--transparencia` | `wecar-features__card wecar-features__card--blue` |
| Feature card 3 | `wecar-feature-card wecar-feature-card--facilidad` | `wecar-features__card wecar-features__card--teal` |
| Partners section | `#wecar-partners` | `wecar-partners` (class added) |
| Footer | `#wecar-footer` | `wecar-footer` (class added) |
