# Home Redesign — Technical Design

## 1. Architecture Overview

The redesign keeps WeCar's WordPress + Elementor stack intact and replaces only the content layer of the existing front page (post ID `35463`). A child theme provides the new visual layer (design tokens, section styling, scroll-triggered animations), while the page structure is authored as Elementor JSON and applied directly to `_elementor_data` via WP-CLI.

```mermaid
flowchart TB
    subgraph Sources
        A[Current production home 35463<br/>_elementor_data 111 KB / 14 sections]
        B[vehica-child theme<br/>style.css / JS / shortcode]
        C[vehica_car posts<br/>DB meta: price, year, km, fuel, image]
    end

    subgraph Build
        D[openspec/.../elementor/home-35463-redesign.json<br/>7 sections]
        E[home-animations.js<br/>Intersection Observer]
        F[wecar-vehicle-carousel.php<br/>[wecar_vehicles] shortcode]
    end

    subgraph Deploy
        G[WP-CLI: wp post meta update 35463 _elementor_data]
        H[wp cache flush]
    end

    subgraph Output
        I[Rendered home page<br/>7 sections, responsive, animated]
    end

    A --> D
    B --> E
    C --> F
    D --> G
    E --> G
    F --> G
    G --> H
    H --> I
```

### High-level decisions

- **DB-first content migration**: the new home is serialized as Elementor JSON and written to `wp_postmeta.meta_value` for `_elementor_data`. This is the same mechanism Elementor itself uses; no custom PHP template replaces `page.php`.
- **Child theme as design system**: all new CSS variables, section overrides, and JavaScript live in `vehica-child`, leaving parent Vehica and Elementor global styles untouched.
- **Real vehicle data via shortcode**: the carousel is rendered by a PHP shortcode registered in the child theme. It queries `vehica_car` posts that are published and active (`vehica_41301 = activo`), then outputs markup that Elementor wraps inside a section.
- **Cache as a deployment step**: WP Rocket + SiteGround Optimizer require an explicit flush after every meta/CSS/JS change; this is treated as part of the deploy, not an afterthought.

## 2. File Structure

```
wp-content/themes/vehica-child/
├── style.css                              # child theme header + design tokens
├── functions.php                          # enqueue assets, register shortcode
├── assets/
│   ├── css/
│   │   ├── wecar-design-system.css        # tokens only (colors, type, spacing)
│   │   ├── home-header.css                # header sticky + mobile drawer
│   │   ├── home-hero.css                  # dual-card hero
│   │   ├── home-steps.css                 # 3-step process + connectors
│   │   ├── home-carousel.css              # vehicle carousel + card + empty state
│   │   ├── home-features.css              # Elegí Wecar cards
│   │   ├── home-partners.css              # Marcas asociadas
│   │   └── home-footer.css                # footer layout
│   ├── js/
│   │   └── home-animations.js             # Intersection Observer + reduced-motion
│   └── images/
│       └── logo-wecar.svg                 # extracted logo, ≥ 2x mobile resolution
└── includes/
    └── shortcodes/
        └── wecar-vehicle-carousel.php     # [wecar_vehicles] shortcode

openspec/changes/home-redesign/
├── proposal.md
├── spec.md
├── design.md
├── elementor/
│   └── home-35463-redesign.json           # new 7-section Elementor data
└── backups/
    ├── _elementor_data-35463.json         # original meta value
    └── home-35463-pre-redesign-2026-06-30.sql # DB snapshot
```

## 3. Implementation Strategy

Work is grouped by **reviewable work units**. Each unit produces a deployable state and includes its own verification step.

| Work unit | Deliverable | Verification |
|-----------|-------------|--------------|
| **W1 — Backup** | Original `_elementor_data` JSON + DB snapshot saved in repo | `wp post meta get 35463 _elementor_data` matches backup checksum |
| **W2 — Design system** | Child theme CSS variables + utility classes enqueued only on home | Tokens render correctly in browser devtools, no regressions on other pages |
| **W3 — Elementor structure** | New 7-section JSON applied to page 35463 on test | All 7 sections visible in Elementor preview and front end |
| **W4 — Vehicle carousel shortcode** | `[wecar_vehicles]` queries real `vehica_car` posts and renders cards | Carousel shows ≥ 3 active vehicles or the empty-state message |
| **W5 — Scroll animations** | `home-animations.js` animates the 3-step section with reduced-motion fallback | Steps animate in Chrome/Firefox/Safari; disabled with `prefers-reduced-motion: reduce` |
| **W6 — Responsive + cache** | Breakpoints tested, cache flush procedure documented | Lighthouse mobile score ≥ 90, visual diff on 3 breakpoints |
| **W7 — Production migration** | Same steps repeated on `wecar.com.ar` with rollback tested | Production home matches test; rollback restores original state |

### Phase order

1. **Backup**: export `_elementor_data` and create a DB snapshot.
2. **Design system**: add CSS variables and enqueue rules; do not modify Elementor data yet.
3. **Build**: author the 7-section Elementor JSON locally, import to test, and iterate visually.
4. **Validate**: desktop + mobile screenshots, carousel data, animations, accessibility audit.
5. **Migrate**: backup production, apply JSON/CSS/JS, flush cache, verify.

## 4. Data Flow

```
Browser request /
    └─> WordPress resolves front page to post 35463
        └─> page.php delegates to Elementor
            └─> Elementor reads wp_postmeta.meta_key = _elementor_data
                ├─> JSON describes 7 sections
                ├─> Section 4 (carousel) contains a Shortcode widget
                │   └─> [wecar_vehicles] executed by vehica-child
                │       └─> WP_Query on vehica_car (publish + activo)
                │           └─> Loop renders vehicle cards HTML
                └─> Elementor frontend renders markup
                    └─> vehica-child CSS/JS loaded
                        └─> WP Rocket + SG Optimizer cache layer
                            └─> Response to browser
```

### Cache invalidation

- After any `_elementor_data` update: `wp cache flush`.
- After CSS/JS changes: bump version query string in `wp_enqueue_style/script` and flush cache.
- Verify via `curl -I` that WP Rocket cache misses on first request after flush.

## 5. Section-by-Section Design

### 5.1 Header

- **Elementor structure**: Header template via Elementor Theme Builder (or a top Section on the page if Theme Builder is not used). Single row, 3 columns: logo | nav | CTA.
- **Logo**: Image widget pointing to `assets/images/logo-wecar.svg`. Width constrained via CSS custom property `--wecar-header-logo-width`.
- **Nav**: Elementor Nav Menu widget bound to the existing WordPress menu (`Inicio, Comprar, Vender, Nosotros, Blog`). Avoid hardcoding links so content edits remain in WP Admin.
- **CTA**: Button widget with text "Contactanos", linked to `/contactanos/`.
- **Sticky behavior**: Elementor Sticky settings on the section (`Effects Offset: 0`, `Stay in Column: no`). CSS adds a subtle backdrop blur and shadow when `.elementor-sticky--active`.
- **Mobile (< 768px)**: Nav Menu widget collapses to hamburger; drawer slides from the right. CSS adjusts header height and logo size.
- **CSS files**: `home-header.css`.

### 5.2 Hero Dual

- **Elementor structure**: full-width section, 2-column inner section (50/50), each column an Icon Box widget.
- **Left card — Comprar**:
  - Icon: car/speed icon, purple background circle.
  - Title: "Comprar tu próximo auto".
  - Description: one-line value proposition.
  - CTA: "Ver autos" → `/autos/`.
- **Right card — Vender**:
  - Icon: tag/sell icon, cyan background circle.
  - Title: "Vendé tu auto".
  - Description: one-line value proposition.
  - CTA: "Cotizar" → `/cotiza/`.
- **Styling**: cards use CSS variables `--wecar-purple` and `--wecar-cyan`, border radius `--wecar-radius-lg`, soft shadow, hover lift transition.
- **Mobile**: columns stack vertically; cards become full-width with preserved padding.
- **CSS files**: `home-hero.css`.

### 5.3 3 Pasos Animados

- **Elementor structure**: section with heading + subtitle, followed by a 3-column inner section. Each column is an Icon Box widget (or Heading + Text Editor).
- **Steps**:
  1. "01 — Cotizás online"
  2. "02 — Peritamos en sucursal"
  3. "03 — Lo publicamos"
- **Connector lines**: CSS pseudo-elements `::before` / `::after` draw horizontal lines between step numbers on desktop. Hidden on mobile.
- **Animation**:
  - Each step card starts with `.wecar-step--hidden` (opacity 0, translateY 24px).
  - `home-animations.js` uses `IntersectionObserver` to add `.wecar-step--visible` with staggered `transition-delay` (0 ms, 150 ms, 300 ms).
  - CSS transition handles `opacity` and `transform`.
  - `prefers-reduced-motion: reduce` bypasses the hidden state entirely.
- **Graceful degradation**: if JavaScript fails, the hidden class is not applied because the script adds it; base CSS shows content by default.
- **CSS files**: `home-steps.css`.

### 5.4 Vehicle Carousel

- **Elementor structure**: section with heading "Destacados" + Shortcode widget containing `[wecar_vehicles]`.
- **Shortcode behavior**:
  - `WP_Query` args:
    - `post_type = vehica_car`
    - `post_status = publish`
    - `posts_per_page = 12`
    - `meta_query`: `vehica_41301 = activo`
    - `orderby = date`, `order = DESC`
  - For each vehicle:
    - Image: featured image, fallback placeholder.
    - Title: post title (make + model).
    - Description: trimmed excerpt or custom field.
    - Price: `vehica_41400` or equivalent price meta.
    - Tags: year (`vehica_41300`), mileage, fuel type.
  - Empty state: "Próximamente verás los vehículos disponibles" + CTA to listing.
- **Carousel behavior**:
  - **Option A (preferred)**: shortcode outputs a flat grid; Elementor Loop Grid or Image Carousel wrapper provides arrows/dots/pagination. This keeps JS in Elementor.
  - **Option B**: custom JS carousel if Elementor widget cannot style the card. Decision made during build after testing Loop Grid with the card markup.
- **Responsive**: mobile horizontal swipe, tablet 2 cards, desktop 3+ cards.
- **CSS files**: `home-carousel.css`.

### 5.5 Elegí Wecar

- **Elementor structure**: section heading + 3-column inner section. Each column is an Icon Box widget.
- **Cards**:
  1. Confianza — purple icon.
  2. Transparencia — blue icon.
  3. Facilidad — cyan icon.
- **Styling**: centered text, large icons, consistent vertical rhythm. No animations required.
- **CSS files**: `home-features.css`.

### 5.6 Marcas Asociadas

- **Elementor structure**: section heading + 3-column inner section, each column an Image widget.
- **Logos**: placeholder SVGs with text "Logo Multicars", "Logo Le Parc Peugeot", "Logo Le Parc Citroën". Grayscale by default, color on hover.
- **Replaceability**: placeholder files are named `partner-multicars.svg`, `partner-peugeot.svg`, `partner-citroen.svg`. Replacing the SVG in `assets/images/` updates the section without touching Elementor data.
- **CSS files**: `home-partners.css`.

### 5.7 Footer

- **Elementor structure**: full-width section, 2-column layout.
- **Left column**: WeCar logo + short company description + phone number.
- **Right column**: copyright text "© 2026 Custer. All rights reserved." and optional social links.
- **Styling**: dark background (`--wecar-footer-bg`), light text, generous vertical padding.
- **CSS files**: `home-footer.css`.

## 6. Design System (Child Theme CSS Variables)

All tokens live in `wecar-design-system.css` and are loaded globally via `functions.php`.

### Colors

```css
:root {
  --wecar-purple: #5E3BE0;
  --wecar-purple-dark: #4A2DB8;
  --wecar-purple-light: #7B5CE8;

  --wecar-cyan: #36BFFA;
  --wecar-cyan-dark: #06B6D4;
  --wecar-cyan-light: #76D6FF;

  --wecar-blue: #2563EB;
  --wecar-blue-dark: #1D4ED8;

  --wecar-bg: #FFFFFF;
  --wecar-surface: #F8FAFC;
  --wecar-text: #0F172A;
  --wecar-text-muted: #64748B;
  --wecar-border: #E2E8F0;

  --wecar-footer-bg: #0F172A;
  --wecar-footer-text: #F8FAFC;
}
```

### Typography

```css
:root {
  --wecar-font-display: 'Montserrat', sans-serif; /* bold, geometric display */
  --wecar-font-body: 'Open Sans', sans-serif;     /* readable body */

  --wecar-text-xs: clamp(0.75rem, 0.7rem + 0.25vw, 0.875rem);
  --wecar-text-sm: clamp(0.875rem, 0.8rem + 0.35vw, 1rem);
  --wecar-text-base: clamp(1rem, 0.9rem + 0.5vw, 1.125rem);
  --wecar-text-lg: clamp(1.125rem, 1rem + 0.65vw, 1.5rem);
  --wecar-text-xl: clamp(1.5rem, 1.25rem + 1.25vw, 2.25rem);
  --wecar-text-2xl: clamp(2rem, 1.6rem + 2vw, 3.5rem);

  --wecar-line-tight: 1.1;
  --wecar-line-normal: 1.5;
  --wecar-line-relaxed: 1.7;

  --wecar-weight-regular: 400;
  --wecar-weight-semibold: 600;
  --wecar-weight-bold: 700;
}
```

### Spacing & Layout

```css
:root {
  --wecar-space-1: 0.25rem;
  --wecar-space-2: 0.5rem;
  --wecar-space-3: 0.75rem;
  --wecar-space-4: 1rem;
  --wecar-space-6: 1.5rem;
  --wecar-space-8: 2rem;
  --wecar-space-12: 3rem;
  --wecar-space-16: 4rem;
  --wecar-space-20: 5rem;

  --wecar-section-padding-y: clamp(3rem, 5vw, 6rem);
  --wecar-container-max: 1280px;
  --wecar-container-padding: clamp(1rem, 3vw, 2rem);
}
```

### Radius & Shadows

```css
:root {
  --wecar-radius-sm: 0.5rem;
  --wecar-radius-md: 0.75rem;
  --wecar-radius-lg: 1rem;
  --wecar-radius-xl: 1.5rem;
  --wecar-radius-full: 9999px;

  --wecar-shadow-sm: 0 1px 2px rgba(15, 23, 42, 0.06);
  --wecar-shadow-md: 0 4px 12px rgba(15, 23, 42, 0.08);
  --wecar-shadow-lg: 0 12px 32px rgba(15, 23, 42, 0.12);
  --wecar-shadow-hover: 0 20px 40px rgba(94, 59, 224, 0.18);
}
```

### Breakpoints

```css
:root {
  --wecar-bp-mobile: 767px;
  --wecar-bp-tablet: 1024px;
}

/* Usage */
@media (max-width: 767px) { /* mobile */ }
@media (min-width: 768px) and (max-width: 1024px) { /* tablet */ }
@media (min-width: 1025px) { /* desktop */ }
```

## 7. JavaScript / Animation Strategy

### `home-animations.js`

- Scope: run only when `body.home` is present.
- Intersection Observer with `threshold: 0.2` watches `.wecar-step` elements.
- On intersection, add `.wecar-step--visible` with staggered delay derived from `data-step-index`.
- On `prefers-reduced-motion: reduce`, immediately add the visible class without transition.
- Carousel: if Option A is chosen, no custom carousel JS is needed. If Option B is chosen, a lightweight touch/snap carousel script is added with `passive` event listeners.

### Performance

- Animations use only `transform` and `opacity`.
- Event listeners are `passive` where supported.
- Script is deferred (`wp_enqueue_script` with `true` in footer).

### Accessibility

- `prefers-reduced-motion` respected.
- Focus indicators remain visible; no motion hides content.
- If JS fails, content is visible by default.

## 8. Backup & Rollback Strategy

### Backup

Run before any change to test or production:

```bash
# Full DB snapshot
wp db export backups/home-35463-pre-redesign-2026-06-30.sql

# Elementor data only
wp post meta get 35463 _elementor_data --format=json > backups/_elementor_data-35463.json

# Page template meta
wp post meta get 35463 _wp_page_template > backups/home-35463-page-template.txt
```

Store all artifacts in `openspec/changes/home-redesign/backups/` and commit them.

### Rollback

```bash
# Restore original Elementor data
wp post meta update 35463 _elementor_data < backups/_elementor_data-35463.json

# Clear all caches
wp cache flush

# Verify the 14 original sections are restored
```

Rollback must be tested on the test environment before production deploy.

## 9. Migration Plan

### Test environment (`test.wecar.com.ar`, DB `dbijhrsz46exbp`)

1. SSH to `wecar` and switch to test path.
2. Run backup commands and commit artifacts.
3. Upload new child theme files (CSS/JS/shortcode/logo).
4. Apply new `_elementor_data`:
   ```bash
   wp post meta update 35463 _elementor_data < elementor/home-35463-redesign.json
   ```
5. Flush cache:
   ```bash
   wp cache flush
   ```
6. Visual verification:
   - Desktop 1920×1080
   - Tablet 768×1024
   - Mobile 375×812
7. Functional verification:
   - Header sticky + mobile hamburger.
   - Hero cards link correctly.
   - 3-step animation triggers on scroll.
   - Carousel shows real vehicles or empty state.
   - Footer text and phone correct.
8. Iterate; re-run backup before each iteration.

### Production environment (`wecar.com.ar`, DB `dbbzno7a6rmoym`)

1. Backup production DB + `_elementor_data`.
2. Deploy child theme files (same set as test).
3. Apply new `_elementor_data`.
4. Flush cache.
5. Verify same checklist as test.
6. If any critical issue is found, execute rollback immediately and notify stakeholders.

### Roll-forward vs rollback criteria

- Roll forward if visual/functional issues are minor and fixable within the maintenance window.
- Rollback immediately if the home is broken on mobile, carousel is empty due to query error, or global Elementor styles regress on other pages.

## 10. Risks & Mitigations

| Risk | Likelihood | Impact | Mitigation |
|------|------------|--------|------------|
| Elementor regenerates internal IDs on JSON import, breaking widget references | Medium | High | Test import on test first; diff the re-exported JSON against the source; adjust IDs before production. |
| WP Rocket / SiteGround Optimizer serve stale CSS/JS | High | Medium | Bump asset version strings; run `wp cache flush`; purge CDN; verify with `curl -I`. |
| `vehica_car` meta keys differ between test and production | Medium | High | Query the same keys on both DBs before writing the shortcode; add defensive fallbacks. |
| Safari mobile fails to animate steps smoothly | Medium | Medium | Use `IntersectionObserver` + CSS transitions only; test on real iOS device or BrowserStack. |
| Header Nav Menu widget styling conflicts with parent theme | Medium | Medium | Use highly specific child-theme selectors and load CSS after Elementor frontend. |
| Empty carousel on test (no active vehicles) | Low | Low | Empty-state message + CTA already specified; verify shortcode returns graceful HTML. |
| Logo SVG does not scale at @2x | Low | Medium | Extract logo at ≥ 240 px width; use SVG if possible. |
| Changes affect other pages due to overly broad CSS selectors | Medium | High | Scope all rules under `body.home` or `.wecar-home-*` classes; test listing, single car, and blog pages. |
| Elementor 4.1.4 update changes JSON schema | Low | High | Apply redesign on current 4.0.5 first; schedule 4.1.4 update separately with its own test cycle. |

## 11. Open Technical Questions

- [ ] Should the carousel use Elementor's Loop Grid (Option A) or a custom JS carousel (Option B)? Decision depends on whether Loop Grid can style the vehicle card markup produced by the shortcode.
- [ ] Is the header implemented as an Elementor Theme Builder template (global) or as a section inside page 35463? A global template keeps consistency across pages but requires Elementor Pro access.
- [ ] Which exact custom field keys hold price, mileage, and fuel type on production? Verify `vehica_41400` and related meta keys before finalizing the shortcode query.
- [ ] Does `test.wecar.com.ar` already contain a clone of production data, or does it need to be restored from the 14 GB backup before testing begins?
- [ ] Is there an existing WP menu assigned to the 5 nav items, or should a new menu be created and assigned to the Nav Menu widget?
