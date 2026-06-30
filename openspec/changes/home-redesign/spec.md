# Home Redesign — Spec

## Overview

This spec covers the complete redesign of WeCar's front page (ID 35463): 7 sections replacing the legacy Elementor 14-section layout, with responsive design, scroll-triggered animations, real vehicle data in carousel, and WeCar brand identity (purple/cyan/blue palette).

## Functional Requirements

### REQ-HOME-001: Header Navigation

**Description**: The home page MUST display a fixed header with the WeCar logo on the left, a 5-item navigation (Inicio, Comprar, Vender, Nosotros, Blog), and a CTA button "Contactanos" on the right.

**Priority**: P0

**Scenario**:
- GIVEN a visitor on the home page
- WHEN the header section renders
- THEN the WeCar logo appears at the left, nav items are centered, and CTA button is at the right
- AND the header remains fixed on scroll (sticky behavior)

#### Scenario: Logo Source

- GIVEN the WeCar logo has been extracted from the current site
- WHEN the header renders on mobile (< 768px)
- THEN the logo scales proportionally and nav collapses to a hamburger menu

### REQ-HOME-002: Hero Dual Cards

**Description**: The home page MUST display a dual-card hero section with two equal-width cards: "Comprar" (buy) and "Vender" (sell), each with an icon, title, description, and CTA link.

**Priority**: P0

**Scenario**:
- GIVEN a visitor on the home page
- WHEN the hero section renders
- THEN two cards appear side by side: left card "Comprar tu próximo auto" and right card "Vendé tu auto"
- AND each card has an icon, title, short description, and a CTA link
- AND cards use the purple/cyan/blue palette from the design system

#### Scenario: Mobile Layout

- GIVEN the viewport is < 768px
- WHEN the hero dual section renders
- THEN cards stack vertically (full width) with consistent spacing

### REQ-HOME-003: 3-Step Process Section

**Description**: The home page MUST display a "3 pasos" section with a title, subtitle, and 3 numbered steps: "Cotizás online", "Peritamos en sucursal", "Lo publicamos".

**Priority**: P0

**Scenario**:
- GIVEN a visitor on the home page
- WHEN the 3-step section renders
- THEN a section title and subtitle appear above 3 step cards
- AND each step has a number (01, 02, 03), title, and description
- AND step 1 = "Cotizás online", step 2 = "Peritamos en sucursal", step 3 = "Lo publicamos"

### REQ-HOME-004: Scroll-Triggered Step Animations

**Description**: The 3-step process cards MUST animate in sequentially when scrolled into view using Intersection Observer.

**Priority**: P1

**Scenario**:
- GIVEN the visitor scrolls down to the 3-step section
- WHEN each step card enters the viewport
- THEN step 1 animates first, step 2 after a delay, step 3 after another delay (staggered entrance)
- AND the animation uses CSS transitions (fade + slide-up)

#### Scenario: Reduced Motion

- GIVEN the user has `prefers-reduced-motion: reduce` enabled
- WHEN the 3-step section scrolls into view
- THEN all 3 steps appear immediately without animation

### REQ-HOME-005: Vehicle Carousel

**Description**: The home page MUST display a vehicle carousel populated with real vehicles from the `vehica_car` post type (active, published) queried via a PHP shortcode.

**Priority**: P0

**Scenario**:
- GIVEN the carousel shortcode queries `vehica_car` posts with status `publish`
- WHEN the page renders
- THEN at least 3 vehicle cards are displayed in a horizontal scrollable carousel
- AND vehicles show real data (title, price, image) from the database

#### Scenario: Empty State

- GIVEN no `vehica_car` posts are active/published
- WHEN the carousel section renders
- THEN a placeholder message appears: "Próximamente verás los vehículos disponibles" with a CTA to the listing page

### REQ-HOME-006: Vehicle Card

**Description**: Each vehicle card in the carousel MUST display: vehicle image, title (make + model), short description, price, and tags (year, km, fuel type).

**Priority**: P0

**Scenario**:
- GIVEN a vehicle card in the carousel
- WHEN rendered
- THEN it shows the vehicle image at top, title below, description, price in bold, and tags as pills/badges
- AND tags display year, mileage (km), and fuel type

### REQ-HOME-007: Elegí Wecar Section

**Description**: The home page MUST display an "Elegí Wecar" section with 3 feature cards highlighting trust reasons: "Confianza", "Transparencia", "Facilidad".

**Priority**: P1

**Scenario**:
- GIVEN a visitor on the home page
- WHEN the "Elegí Wecar" section renders
- THEN a title "Elegí Wecar" appears above 3 feature cards
- AND each card has an icon, title, and short description
- AND the 3 features are: Confianza, Transparencia, Facilidad

### REQ-HOME-008: Marcas Asociadas

**Description**: The home page MUST display a "Marcas asociadas" section with a horizontal strip of partner brand logos (Multicars, Le Parc Peugeot, Le Parc Citroën).

**Priority**: P2

**Scenario**:
- GIVEN a visitor on the home page
- WHEN the Marcas asociadas section renders
- THEN a title "Marcas asociadas" appears above a horizontal logo strip
- AND partner logos are evenly spaced and centered

#### Scenario: Placeholder Logos

- GIVEN final partner logos are not yet provided
- WHEN the section renders
- THEN placeholder logos with brand names are used

### REQ-HOME-009: Footer

**Description**: The home page MUST display a footer with Custer company info, phone number, and "2026 Custer. All rights reserved." copyright.

**Priority**: P0

**Scenario**:
- GIVEN a visitor on the home page
- WHEN the footer renders
- THEN it displays Custer company information, a phone number, and copyright text
- AND the copyright reads "© 2026 Custer. All rights reserved."

### REQ-HOME-010: Logo Extraction

**Description**: The WeCar logo MUST be extracted from the current site (production) and included in the child theme assets at sufficient resolution for mobile @2x displays.

**Priority**: P0

**Scenario**:
- GIVEN the WeCar logo is extracted from wecar.com.ar
- WHEN saved to the child theme assets directory
- THEN the file is a PNG/SVG at ≥ 2x resolution for mobile
- AND it is referenced by the header component

### REQ-HOME-011: Backup of Home 35463

**Description**: A complete backup of the current `_elementor_data` for page 35463 MUST be created before any changes are applied.

**Priority**: P0

**Scenario**:
- GIVEN the backup phase is executed
- WHEN `wp post meta get 35463 _elementor_data` is run
- THEN the full JSON is saved to `openspec/changes/home-redesign/backups/_elementor_data-35463.json`
- AND the DB dump includes the original meta value

### REQ-HOME-012: Rollback Procedure

**Description**: A functional rollback MUST be documented and tested: restoring the original `_elementor_data` JSON to page 35463 via WP-CLI.

**Priority**: P0

**Scenario**:
- GIVEN the new home design has been applied to page 35463
- WHEN the rollback procedure is executed
- THEN the original `_elementor_data` JSON is restored from backup
- AND `wp cache flush` is run to clear WP Rocket cache
- AND the home page reverts to the pre-redesign state

### REQ-HOME-013: Test-First Workflow

**Description**: All home redesign changes MUST be validated on test.wecar.com.ar before being applied to production.

**Priority**: P0

**Scenario**:
- GIVEN changes are ready for testing
- WHEN applied to the test environment
- THEN visual validation is performed (desktop + mobile screenshots)
- AND all 7 sections render correctly
- AND carousel pulls real vehicle data
- AND animations work in Chrome, Firefox, Safari

### REQ-HOME-014: Responsive Design

**Description**: The home page MUST be fully responsive across mobile (< 768px), tablet (768–1024px), and desktop (> 1024px) breakpoints.

**Priority**: P0

**Scenario**:
- GIVEN a visitor on mobile (< 768px)
- WHEN the home page renders
- THEN all sections stack vertically, hero cards stack, carousel is swipeable, header has hamburger menu

#### Scenario: Tablet

- GIVEN a visitor on tablet (768–1024px)
- WHEN the home page renders
- THEN layout adapts to medium width, hero cards may be side-by-side or stacked, carousel shows 2 cards

#### Scenario: Desktop

- GIVEN a visitor on desktop (> 1024px)
- WHEN the home page renders
- THEN full layout with side-by-side cards, carousel shows 3+ cards, header shows full nav

### REQ-HOME-015: Design System

**Description**: CSS custom properties MUST define the WeCar design system in the child theme `style.css`: palette (#5E3BE0, #36BFFA, #06B6D4, #2563EB), typography scale, spacing scale.

**Priority**: P0

**Scenario**:
- GIVEN the child theme style.css is loaded
- WHEN any component renders
- THEN CSS variables like `--wecar-purple`, `--wecar-cyan`, `--wecar-blue` are available
- AND consistent spacing and typography are applied across all 7 sections

### REQ-HOME-016: No Elementor Global Style Regression

**Description**: The redesign MUST NOT break Elementor global styles or affect other pages (listing, single car, blog, etc.).

**Priority**: P0

**Scenario**:
- GIVEN the home redesign CSS is loaded
- WHEN navigating to any other page (listing, single car, cotiza, blog)
- THEN the page renders with its original Elementor styles intact
- AND no visual regressions are observed

## Non-Functional Requirements

### NFR-HOME-001: Performance

**Description**: The home page MUST achieve Core Web Vitals targets:
- LCP (Largest Contentful Paint) < 2.5 seconds
- INP (Interaction to Next Paint) < 100ms
- CLS (Cumulative Layout Shift) < 0.1

**Priority**: P0

### NFR-HOME-002: Accessibility

**Description**: The home page MUST comply with WCAG 2.1 Level AA:
- Minimum contrast ratio 4.5:1 for text, 3:1 for large text
- All images have meaningful `alt` text
- All interactive elements have visible focus indicators
- `prefers-reduced-motion` respected (animations disabled)
- Semantic HTML (headings hierarchy, landmarks, ARIA labels where needed)

**Priority**: P0

### NFR-HOME-003: SEO

**Description**: The home page MUST include:
- `<title>` tag with WeCar branding
- `<meta name="description">` with relevant content
- Open Graph tags (og:title, og:description, og:image)
- Structured data (JSON-LD Organization schema) — MAY be added later

**Priority**: P1

### NFR-HOME-004: Cache Strategy

**Description**: After deploying changes:
- Run `wp cache flush` to clear WP Rocket server cache
- Purge CDN cache if applicable
- Verify cache is cleared on test.wecar.com.ar before production deploy

**Priority**: P0

### NFR-HOME-005: Browser Support

**Description**: The home page MUST render correctly on the latest 2 versions of:
- Google Chrome
- Mozilla Firefox
- Apple Safari (desktop + mobile)
- Microsoft Edge

**Priority**: P1

### NFR-HOME-006: Responsive Breakpoints

**Description**: The design system MUST define and use these breakpoints:
- Mobile: < 768px
- Tablet: 768px – 1024px
- Desktop: > 1024px

**Priority**: P0

### NFR-HOME-007: CSS Specificity

**Description**: All custom CSS in `vehica-child/style.css` MUST load after Elementor's frontend CSS to ensure proper override precedence without using `!important` except where unavoidable (Vue.js scoped components).

**Priority**: P1

### NFR-HOME-008: JavaScript Independence

**Description**: The scroll-triggered animations MUST gracefully degrade: if JavaScript is disabled or fails to load, all content remains visible and accessible without animation.

**Priority**: P1

## Migration & Rollback

### Backup Procedure

1. Export `_elementor_data` meta for page 35463:
   ```bash
   wp post meta get 35463 _elementor_data --format=json > openspec/changes/home-redesign/backups/_elementor_data-35463.json
   ```
2. Save a DB snapshot including all page meta for 35463.
3. Store both artifacts in the repo under `openspec/changes/home-redesign/backups/`.

### Rollback Procedure

1. Restore the original `_elementor_data`:
   ```bash
   wp post meta update 35463 _elementor_data < openspec/changes/home-redesign/backups/_elementor_data-35463.json
   ```
2. Flush all caches:
   ```bash
   wp cache flush
   ```
3. Verify the home page reverts to the pre-redesign state by visiting the URL and checking the 14 original sections are restored.

### Test-First Workflow

1. Apply changes to test.wecar.com.ar (not production).
2. Validate all 7 sections visually (desktop + mobile).
3. Verify carousel pulls real vehicle data.
4. Test animations in Chrome, Firefox, Safari.
5. Check responsive behavior at all 3 breakpoints.
6. Run accessibility audit (Lighthouse ≥ 90).
7. Only after all checks pass, apply to production.

### Acceptance Criteria

- [ ] Home 35463 shows all 7 sections correctly on test.wecar.com.ar
- [ ] Header displays WeCar logo + 5 nav items + CTA button
- [ ] Hero dual has "Comprar" and "Vender" cards
- [ ] 3-step process shows "Cotizás online", "Peritamos en sucursal", "Lo publicamos"
- [ ] Step animations trigger on scroll with staggered entrance
- [ ] Vehicle carousel loads real data from `vehica_car` posts
- [ ] Vehicle cards show image, title, description, price, and tags
- [ ] "Elegí Wecar" section shows 3 feature cards
- [ ] Marcas asociadas shows partner logo strip
- [ ] Footer shows Custer info, phone, and "© 2026 Custer. All rights reserved."
- [ ] LCP < 2.5s, INP < 100ms, CLS < 0.1
- [ ] WCAG 2.1 AA compliance (contrast, alt text, focus, reduced-motion)
- [ ] Responsive across mobile, tablet, desktop
- [ ] No visual regressions on other pages
- [ ] Backup exists and rollback is functional
- [ ] Cache flushed post-deploy
