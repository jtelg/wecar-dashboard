# Home Redesign — Tasks

## Overview

Replace the current 14-section Autokan-branded Elementor home (post ID 35463) with a 7-section modern WeCar design featuring a dual-card hero, animated 3-step process, real vehicle carousel, trust features, partner logos, and responsive footer. Work follows a **test-first workflow**: all changes are validated on `test.wecar.com.ar` before production migration. Implementation is organized in 23 tasks across 5 phases, delivered via 4 chained PRs.

## Review Workload Forecast

- **Total estimated changed lines**: ~2,877 LOC total | ~1,350 LOC reviewable (excluding auto-generated backup data: ~1,525 LOC)
- **400-line budget risk**: **High** — reviewable code alone exceeds budget by 3x
- **Chained PRs recommended**: **Yes** — 4 PRs via feature-branch-chain
- **Decision needed before apply**: **Yes** — see Open Questions below (especially carousel approach and header implementation)

### Line budget breakdown

| Category | Estimated LOC | Reviewable? |
|---|---|---|
| Backup artifacts (auto-generated JSON/SQL) | ~1,525 | No — auto-generated data dumps |
| Design system CSS + animations JS | ~210 | Yes |
| Section-specific CSS (7 files) | ~390 | Yes |
| Vehicle carousel shortcode (PHP) | ~120 | Yes |
| Elementor JSON (hand-authored, 7 sections) | ~560 | Yes — structured data, predictable schema |
| functions.php updates | ~30 | Yes |
| Logo SVG | ~10 | Yes |
| Apply/migration commands + doc | ~32 | Yes |

## Chained PR Strategy

**Strategy**: `feature-branch-chain` — each child PR targets the previous PR's branch. A draft/no-merge tracker PR (`feat/redesign`) is created first.

```
feat/redesign (tracker, draft, no-merge)
├── feat/redesign-base      ← PR #1 — Backup + DS Foundation
├── feat/redesign-sections  ← PR #2 — Section CSS + Shortcode
├── feat/redesign-apply-test← PR #3 — Elementor JSON + Apply + Validate
└── feat/redesign-prod      ← PR #4 — Production Migration + Cleanup
```

### PR #1 (Base)
- **Branch**: `feat/redesign-base` → targets `feat/redesign` (tracker)
- **Title**: `feat(home): backup home 35463 and establish design system foundation`
- **Files**:
  - `openspec/changes/home-redesign/backups/_elementor_data-35463.json` (new, auto-generated)
  - `openspec/changes/home-redesign/backups/home-35463-page-template.txt` (new, auto-generated)
  - `openspec/changes/home-redesign/backups/home-35463-pre-redesign-2026-06-30.sql` (new, auto-generated)
  - `wp-content/themes/vehica-child/assets/css/wecar-design-system.css` (new)
  - `wp-content/themes/vehica-child/assets/js/home-animations.js` (new)
  - `wp-content/themes/vehica-child/assets/images/logo-wecar.svg` (new)
  - `wp-content/themes/vehica-child/functions.php` (modified — enqueue new assets)
  - `openspec/changes/home-redesign/tasks.md` (this file — created during planning)
- **Estimated LOC**: ~250 reviewable + ~1,525 auto-generated backup data
- **Depends on**: nothing (first PR after tracker)
- **Description**: Phase 1 (Backup T001–T004) + Phase 2 design system foundation (T005, T006, T007, T009)

### PR #2
- **Branch**: `feat/redesign-sections` → targets `feat/redesign-base`
- **Title**: `feat(home): add section-specific CSS and vehicle carousel shortcode`
- **Files**:
  - `wp-content/themes/vehica-child/assets/css/home-header.css` (new)
  - `wp-content/themes/vehica-child/assets/css/home-hero.css` (new)
  - `wp-content/themes/vehica-child/assets/css/home-steps.css` (new)
  - `wp-content/themes/vehica-child/assets/css/home-carousel.css` (new)
  - `wp-content/themes/vehica-child/assets/css/home-features.css` (new)
  - `wp-content/themes/vehica-child/assets/css/home-partners.css` (new)
  - `wp-content/themes/vehica-child/assets/css/home-footer.css` (new)
  - `wp-content/themes/vehica-child/includes/shortcodes/wecar-vehicle-carousel.php` (new)
  - `wp-content/themes/vehica-child/functions.php` (modified — register shortcode)
- **Estimated LOC**: ~510 (CSS is declarative/low-complexity, shortcode is ~120 LOC PHP)
- **Depends on**: PR #1 (design system CSS variables must exist)
- **Note**: ~510 LOC exceeds the 400-line budget, but ~390 of that is CSS (declarative, formulaic, low cognitive load). Acceptable without `size:exception` given CSS nature.

### PR #3
- **Branch**: `feat/redesign-apply-test` → targets `feat/redesign-sections`
- **Title**: `feat(home): apply new Elementor JSON to test environment and validate`
- **Files**:
  - `openspec/changes/home-redesign/elementor/home-35463-redesign.json` (new, ~560 LOC, 7 sections)
  - Screenshot artifacts (test validation)
  - Backups from iterated rounds (if any)
- **Commands executed** (not files):
  - `wp post meta update 35463 _elementor_data < elementor/home-35463-redesign.json`
  - `wp cache flush`
  - Visual validation on desktop + mobile
- **Estimated LOC**: ~560 (Elementor JSON is structured data following a known schema — request `size:exception` if reviewer prefers strict limit)
- **Depends on**: PR #2 (CSS must be in place so visual validation is accurate)

### PR #4
- **Branch**: `feat/redesign-prod` → targets `feat/redesign-apply-test`
- **Title**: `feat(home): migrate redesign to production`
- **Files**:
  - `openspec/changes/home-redesign/backups/prod/_elementor_data-35463.json` (new, auto-generated)
  - `openspec/changes/home-redesign/backups/prod/home-35463-pre-redesign-2026-06-30.sql` (new, auto-generated)
- **Commands executed** (not files):
  - SCP child theme files to production
  - `wp post meta update 35463 _elementor_data < elementor/home-35463-redesign.json`
  - `wp cache flush`
  - Visual validation on production
  - Cleanup of local backup files (if no longer needed)
- **Estimated LOC**: ~40 reviewable + ~1,525 auto-generated backup data
- **Depends on**: PR #3 (validated on test)

---

## Tasks

### Phase 1: Backup & Safety Net (test.wecar.com.ar)

- [ ] **TASK-001**: Backup `_elementor_data` of home 35463 in test
  - **Description**: Run `wp post meta get 35463 _elementor_data --format=json` on test.wecar.com.ar and save output to `openspec/changes/home-redesign/backups/_elementor_data-35463.json`. Store the backup JSON in the repo.
  - **Files**: `openspec/changes/home-redesign/backups/_elementor_data-35463.json`
  - **Commands**:
    ```bash
    ssh wecar "cd ~/www/wecar.com.ar/public_html && wp post meta get 35463 _elementor_data --format=json" > openspec/changes/home-redesign/backups/_elementor_data-35463.json
    ```
  - **Verification**: File exists, is valid JSON, and a sample field (e.g. `elementor_widget_id` or `_elementor_version`) matches expected content.
  - **PR**: Part of PR #1
  - **Estimated LOC**: ~1,500 (auto-generated JSON dump, ~111 KB)
  - **Work unit guard**: Data artifact, not reviewable code — does not count against review budget.

- [ ] **TASK-002**: Backup `_wp_page_template` of home 35463 in test
  - **Description**: Save the current page template meta value to a text file.
  - **Files**: `openspec/changes/home-redesign/backups/home-35463-page-template.txt`
  - **Commands**:
    ```bash
    ssh wecar "cd ~/www/wecar.com.ar/public_html && wp post meta get 35463 _wp_page_template" > openspec/changes/home-redesign/backups/home-35463-page-template.txt
    ```
  - **Verification**: File contains expected template value (e.g., `elementor_header_footer` or `default`).
  - **PR**: Part of PR #1
  - **Estimated LOC**: ~5

- [ ] **TASK-003**: Backup DB snapshot in test
  - **Description**: Run `wp db export` to create a full SQL snapshot of the test database before changes. This provides a last-resort rollback mechanism.
  - **Files**: `openspec/changes/home-redesign/backups/home-35463-pre-redesign-2026-06-30.sql`
  - **Commands**:
    ```bash
    ssh wecar "cd ~/www.wecar.com.ar/public_html && wp db export openspec/changes/home-redesign/backups/home-35463-pre-redesign-2026-06-30.sql"
    ```
  - **Verification**: SQL file exists, is non-empty, and contains `wp_postmeta` INSERT statements for post_id 35463.
  - **PR**: Part of PR #1
  - **Estimated LOC**: ~5 (command), SQL file is auto-generated

- [ ] **TASK-004**: Verify backup integrity
  - **Description**: Run a checksum on the backup JSON and SQL files. Optionally do a dry-run restore test by parsing the JSON and confirming Elementor structure is intact.
  - **Files** (none new — verification only)
  - **Commands**:
    ```bash
    # SHA-256 checksum for all backup artifacts
    Get-FileHash -Path "openspec/changes/home-redesign/backups/_elementor_data-35463.json"
    Get-FileHash -Path "openspec/changes/home-redesign/backups/home-35463-pre-redesign-2026-06-30.sql"
    
    # Quick JSON validity check
    Get-Content "openspec/changes/home-redesign/backups/_elementor_data-35463.json" | ConvertFrom-Json | Out-Null
    ```
  - **Verification**: Checksums recorded in a simple manifest or commit message. JSON parses without error. SQL has no syntax corruption.
  - **PR**: Part of PR #1
  - **Estimated LOC**: ~10 (script/documentation)

### Phase 2: Design System Foundation (test.wecar.com.ar)

- [ ] **TASK-005**: Create wecar-design-system.css with design tokens
  - **Description**: Create `assets/css/wecar-design-system.css` with `:root` CSS custom properties for the full WeCar design system:
    - Colors: `--wecar-purple` (#5E3BE0), `--wecar-cyan` (#36BFFA), `--wecar-blue` (#2563EB) with dark/light variants
    - Typography: font stacks (Montserrat display, Open Sans body), fluid type scale (xs to 2xl), line heights, font weights
    - Spacing: consistent scale (0.25rem to 5rem), section padding, container max-width/padding
    - Radius & shadows: sm to xl, shadow md/lg/hover
    - Breakpoints: mobile (≤767px), tablet (768–1024px), desktop (≥1025px)
  - **Files**: `wp-content/themes/vehica-child/assets/css/wecar-design-system.css`
  - **Verification**: Open browser DevTools on any page, inspect `:root` and confirm all CSS variables are available. No console errors.
  - **PR**: Part of PR #1
  - **Estimated LOC**: ~100

- [ ] **TASK-006**: Create home-animations.js (Intersection Observer)
  - **Description**: Create `assets/js/home-animations.js` with:
    - Scope to `body.home` only (early return if not home page)
    - Intersection Observer with `threshold: 0.2` watching `.wecar-step` elements
    - Staggered entrance: add `.wecar-step--visible` class with delay based on `data-step-index` (0ms, 150ms, 300ms)
    - `prefers-reduced-motion: reduce` support: immediately show visible class, no transitions
    - Passive event listeners where supported
    - Script loaded in footer via `wp_enqueue_script` with `true` for `$in_footer`
  - **Files**: `wp-content/themes/vehica-child/assets/js/home-animations.js`
  - **Verification**: Load home page in Chrome/Firefox/Safari, scroll to 3-step section, verify cards animate in staggered sequence. Enable `prefers-reduced-motion: reduce` in DevTools and verify no animation.
  - **PR**: Part of PR #1
  - **Estimated LOC**: ~80

- [ ] **TASK-007**: Update functions.php to enqueue new CSS/JS and register shortcode hook
  - **Description**: Add to `functions.php`:
    1. Enqueue `wecar-design-system.css` with dependency on `elementor-frontend`
    2. Enqueue all 7 section CSS files (`home-header.css`, `home-hero.css`, `home-steps.css`, `home-carousel.css`, `home-features.css`, `home-partners.css`, `home-footer.css`) scoped to condition `is_page(35463)` or `is_front_page()`
    3. Enqueue `home-animations.js` scoped to home page, in footer, with dependency on jQuery (if needed)
    4. Add `require_once` for the shortcode file (path to be created in TASK-008)
  - **Files**: `wp-content/themes/vehica-child/functions.php`
  - **Verification**: Open home page, verify all CSS files load in `<head>` (check Network tab), verify JS file loads before `</body>`. Check other pages — no extra CSS/JS loaded outside home.
  - **PR**: Part of PR #1 (enqueues) and PR #2 (shortcode registration)
  - **Estimated LOC**: ~30 added to existing functions.php

- [ ] **TASK-008**: Create wecar-vehicle-carousel.php shortcode
  - **Description**: Create `includes/shortcodes/wecar-vehicle-carousel.php` registering `[wecar_vehicles]` shortcode that:
    - Queries `vehica_car` posts with `post_status = publish`, meta query `vehica_41301 = activo`, `posts_per_page = 12`, ordered by date DESC
    - For each vehicle, renders a card with: featured image (fallback placeholder if none), title (make + model), short description/excerpt, price from `vehica_41400` meta, tags (year from `vehica_41300`, km, fuel type) as pills/badges
    - Empty state: "Próximamente verás los vehículos disponibles" with a CTA to `/autos/`
    - Output is wrapped in a `div.wecar-carousel` with each card as `div.wecar-vehicle-card`
    - Graceful degradation: if no vehicles found or query fails, show empty state
  - **Note**: Meta keys `vehica_41400` (price), `vehica_41300` (year), and mileage/fuel keys must be verified in Open Questions before finalizing.
  - **Files**: `wp-content/themes/vehica-child/includes/shortcodes/wecar-vehicle-carousel.php`
  - **Verification**: Insert `[wecar_vehicles]` on any test page or use the Elementor Shortcode widget. Confirm ≥1 vehicle card renders with real data, or empty state shows if no vehicles. Check Network tab for no 500 errors.
  - **PR**: Part of PR #2
  - **Estimated LOC**: ~120

- [ ] **TASK-009**: Extract WeCar logo from current site
  - **Description**: Download the WeCar logo from production site (`wecar.com.ar`) or test. Inspect the current header to find the logo URL, download it at ≥ 2x mobile resolution. SVG is preferred; if only PNG exists, extract at ≥ 240px width. Save to child theme assets.
  - **Files**: `wp-content/themes/vehica-child/assets/images/logo-wecar.svg`
  - **Verification**: SVG file renders correctly in browser, scales proportionally, works on mobile @2x displays. Compare against current site logo for fidelity.
  - **PR**: Part of PR #1
  - **Estimated LOC**: ~10 (SVG markup)

- [ ] **TASK-010**: Create 7 section-specific CSS files
  - **Description**: Create one CSS file per home section, each scoped under `body.home` or a section-level class (e.g. `.wecar-home-header`):
    1. **home-header.css**: Sticky header, 3-column layout (logo/nav/CTA), mobile hamburger, backdrop blur on sticky, responsive logo sizing
    2. **home-hero.css**: Dual-card layout (50/50 desktop, stack mobile), purple/cyan card backgrounds, hover lift transition, icon circle styling, CTA button styling
    3. **home-steps.css**: 3-column step cards, connector lines via `::before`/`::after` (desktop only), `.wecar-step--hidden`/`.wecar-step--visible` animation classes, `prefers-reduced-motion` override
    4. **home-carousel.css**: Vehicle card grid, card layout (image top, title, price, tags), responsive columns (1 mobile, 2 tablet, 3+ desktop), empty state styling
    5. **home-features.css**: "Elegí Wecar" 3-card layout, centered text, icon sizes, consistent vertical rhythm
    6. **home-partners.css**: Horizontal logo strip, grayscale default, color on hover, even spacing
    7. **home-footer.css**: Dark background (`--wecar-footer-bg`), 2-column layout, light text, copyright styling, responsive stacking
  - **Files**:
    - `wp-content/themes/vehica-child/assets/css/home-header.css`
    - `wp-content/themes/vehica-child/assets/css/home-hero.css`
    - `wp-content/themes/vehica-child/assets/css/home-steps.css`
    - `wp-content/themes/vehica-child/assets/css/home-carousel.css`
    - `wp-content/themes/vehica-child/assets/css/home-features.css`
    - `wp-content/themes/vehica-child/assets/css/home-partners.css`
    - `wp-content/themes/vehica-child/assets/css/home-footer.css`
  - **Verification**: Each CSS file loads in the browser (Network tab). Inspect each section and verify styles are applied. Test mobile/tablet/desktop breakpoints. Verify no style leakage on other pages (listing, single car, blog).
  - **PR**: Part of PR #2
  - **Estimated LOC**: ~390 total (~50–100 per file)

### Phase 3: Build New Home (test.wecar.com.ar)

- [ ] **TASK-011**: Construct new `_elementor_data` JSON for home 35463
  - **Description**: Hand-author the Elementor JSON for 7 sections. Each section follows Elementor's data structure (`elType`, `widgetType`, `settings`, `elements`). Structure:
    1. **Header section**: full-width, sticky enabled, 3-column inner section: Image widget (logo), Nav Menu widget (WP menu), Button widget (CTA)
    2. **Hero Dual section**: full-width, 2-column inner section (50/50), each with an Icon Box widget ("Comprar" / "Vender")
    3. **3 Pasos section**: heading + subtitle + 3-column inner section, each step with Heading + Text Editor widgets, custom CSS classes for animation hooks
    4. **Vehicle Carousel section**: heading + Shortcode widget with `[wecar_vehicles]`
    5. **Elegí Wecar section**: heading + 3-column inner section, each with Icon Box widget (Confianza/Transparencia/Facilidad)
    6. **Marcas Asociadas section**: heading + 3-column inner section, each with Image widget (placeholder logos)
    7. **Footer section**: full-width, dark background, 2-column layout: logo + company info + phone (left), copyright (right)
  - **File**: `openspec/changes/home-redesign/elementor/home-35463-redesign.json`
  - **Verification**: JSON is valid (parses with `ConvertFrom-Json`). Each section has distinct `id` fields. No duplicate IDs. The structure matches Elementor's expected schema (nested `elements` array, `settings` objects).
  - **PR**: Part of PR #3
  - **Estimated LOC**: ~560

- [ ] **TASK-012**: Apply new `_elementor_data` to test home
  - **Description**: Upload the new Elementor JSON to test server and apply it via WP-CLI. Then flush cache and verify.
  - **Files** (none — command execution)
  - **Commands**:
    ```bash
    # SCP the JSON to test server
    scp openspec/changes/home-redesign/elementor/home-35463-redesign.json wecar:~/www/wecar.com.ar/public_html/openspec/changes/home-redesign/elementor/
    
    # SSH and apply
    ssh wecar "cd ~/www/wecar.com.ar/public_html && wp post meta update 35463 _elementor_data --format=json < openspec/changes/home-redesign/elementor/home-35463-redesign.json"
    ```
  - **Verification**: Visit `test.wecar.com.ar` — the 7 new sections render. Inspect Elementor edit mode to confirm JSON was accepted. If Elementor regenerates IDs, diff the re-exported JSON against the source and adjust.
  - **PR**: Part of PR #3
  - **Estimated LOC**: ~3 (commands)

- [ ] **TASK-013**: Cache flush in test
  - **Description**: Run WP-CLI cache flush on test environment to ensure WP Rocket + SiteGround Optimizer serve fresh CSS/JS/HTML.
  - **Files** (none — command execution)
  - **Commands**:
    ```bash
    ssh wecar "cd ~/www/wecar.com.ar/public_html && wp cache flush"
    
    # Verify cache is cleared
    curl -I https://test.wecar.com.ar/ | findstr "x-proxy-cache"
    ```
  - **Verification**: `curl -I` returns `x-proxy-cache: MISS` on first request, `HIT` on second (dynamic content cached after first serve). CSS/JS files include the updated version query string.
  - **PR**: Part of PR #3
  - **Estimated LOC**: ~3 (commands)

### Phase 4: Visual Validation (test.wecar.com.ar)

- [ ] **TASK-014**: Screenshot home in test (desktop 1440px)
  - **Description**: Capture a full-page screenshot of `test.wecar.com.ar` at 1440×900 viewport. Save as reference artifact.
  - **Files**: (screenshot stored as artifact, not committed to repo unless explicitly decided)
  - **Verification**: All 7 sections visible, layout matches design spec (header, hero, 3-steps, carousel, elegí wecar, marcas, footer).
  - **PR**: Part of PR #3
  - **Estimated LOC**: ~5

- [ ] **TASK-015**: Screenshot home in test (mobile 375px)
  - **Description**: Capture a full-page screenshot at 375×812 viewport (mobile). Verify responsive behavior: header hamburger, stacked hero cards, single-column steps, single carousel card, stacked footer.
  - **Files**: (screenshot artifact)
  - **Verification**: All sections stack vertically, no horizontal scroll, text is readable, CTA buttons are tappable.
  - **PR**: Part of PR #3
  - **Estimated LOC**: ~5

- [ ] **TASK-016**: Manual QA with user
  - **Description**: Walk through the home page checking all REQ-HOME scenarios from the spec:
    - Header sticky + logo + nav + CTA (REQ-HOME-001)
    - Hero dual cards with correct links (REQ-HOME-002)
    - 3-step process with animation (REQ-HOME-003, REQ-HOME-004)
    - Vehicle carousel with real data or empty state (REQ-HOME-005, REQ-HOME-006)
    - Elegí Wecar 3 features (REQ-HOME-007)
    - Marcas asociadas logo strip (REQ-HOME-008)
    - Footer with copyright "2026 Custer. All rights reserved." (REQ-HOME-009)
    - Responsive at 3 breakpoints (REQ-HOME-014)
    - No regressions on other pages (REQ-HOME-016)
    - Accessibility: contrast, alt text, focus indicators, reduced-motion (NFR-HOME-002)
  - **Files**: (none — manual testing)
  - **Verification**: All acceptance criteria from spec.md are met.
  - **PR**: Part of PR #3
  - **Estimated LOC**: ~10 (checklist)

- [ ] **TASK-017**: Iterate on issues found
  - **Description**: Fix any issues discovered during QA (TASK-016). Re-run backup before each iteration. Common issues:
    - Elementor ID regeneration: re-export and diff, adjust source JSON
    - CSS specificity conflicts: scope rules under `body.home`
    - Carousel query returning wrong meta keys: verify and adjust
    - Animation timing: tune delay values
  - **Files**: Depends on the issue
  - **Verification**: Each fix is verified by re-running the relevant scenario. After all fixes, re-capture screenshots (TASK-014/015).
  - **PR**: Part of PR #3
  - **Estimated LOC**: Variable, typically ~10–30 per iteration

### Phase 5: Production Migration

- [ ] **TASK-018**: Backup `_elementor_data` of home 35463 in PRODUCTION
  - **Description**: Same as TASK-001 but on production (`wecar.com.ar`, not test). Save backup to a `backups/prod/` subdirectory.
  - **Files**: `openspec/changes/home-redesign/backups/prod/_elementor_data-35463.json`
  - **Commands**:
    ```bash
    ssh wecar "cd ~/www.wecar.com.ar/public_html && wp post meta get 35463 _elementor_data --format=json" > openspec/changes/home-redesign/backups/prod/_elementor_data-35463.json
    ```
  - **Verification**: File exists, is valid JSON, checksum recorded.
  - **PR**: Part of PR #4
  - **Estimated LOC**: ~5

- [ ] **TASK-019**: Deploy child theme files to production
  - **Description**: SCP all new child theme files (CSS, JS, shortcode, logo) from the local repo to `~/www/wecar.com.ar/public_html/wp-content/themes/vehica-child/` on the production server. Do NOT overwrite existing `functions.php` — use the modified version from the repo.
  - **Files**: Copy of all files from PR #1 and PR #2 to production server
  - **Commands**:
    ```bash
    # Sync assets directory
    scp -r wp-content/themes/vehica-child/assets/css wecar:~/www/wecar.com.ar/public_html/wp-content/themes/vehica-child/assets/
    scp -r wp-content/themes/vehica-child/assets/js wecar:~/www/wecar.com.ar/public_html/wp-content/themes/vehica-child/assets/
    scp -r wp-content/themes/vehica-child/assets/images wecar:~/www/wecar.com.ar/public_html/wp-content/themes/vehica-child/assets/
    scp wp-content/themes/vehica-child/includes/shortcodes/wecar-vehicle-carousel.php wecar:~/www/wecar.com.ar/public_html/wp-content/themes/vehica-child/includes/shortcodes/
    scp wp-content/themes/vehica-child/functions.php wecar:~/www/wecar.com.ar/public_html/wp-content/themes/vehica-child/functions.php
    ```
  - **Verification**: SSH to production, list files, confirm all new files exist with correct content.
  - **PR**: Part of PR #4
  - **Estimated LOC**: ~10 (command documentation)

- [ ] **TASK-020**: Apply new `_elementor_data` to production home
  - **Description**: Same as TASK-012 but on production. Upload the validated Elementor JSON and apply via WP-CLI.
  - **Commands**:
    ```bash
    scp openspec/changes/home-redesign/elementor/home-35463-redesign.json wecar:~/www/wecar.com.ar/public_html/openspec/changes/home-redesign/elementor/
    ssh wecar "cd ~/www/wecar.com.ar/public_html && wp post meta update 35463 _elementor_data --format=json < openspec/changes/home-redesign/elementor/home-35463-redesign.json"
    ```
  - **Verification**: Visit `wecar.com.ar` — the 7 sections render correctly.
  - **PR**: Part of PR #4
  - **Estimated LOC**: ~3

- [ ] **TASK-021**: Cache flush in production
  - **Description**: Same as TASK-013 but on production. Flush WP Rocket + SiteGround Optimizer caches. Verify with `curl -I`.
  - **Commands**:
    ```bash
    ssh wecar "cd ~/www/wecar.com.ar/public_html && wp cache flush"
    ```
  - **Verification**: `curl -I https://wecar.com.ar/` returns `x-proxy-cache: MISS` on first request. Page renders new design.
  - **PR**: Part of PR #4
  - **Estimated LOC**: ~3

- [ ] **TASK-022**: Visual validation in production
  - **Description**: Repeat TASK-014/015/016 checklist on production. Capture screenshots. Ensure production matches test. Test rollback procedure mentally (backup exists, command known).
  - **Files**: (screenshot artifacts)
  - **Verification**: Production home matches test home visually. All sections render. Carousel works. Animations work. No regressions on other pages.
  - **PR**: Part of PR #4
  - **Estimated LOC**: ~10

- [ ] **TASK-023**: Cleanup
  - **Description**: Remove local backup files from the repo if they are no longer needed (only the production backup `.gitignore`-d or removed). Alternatively, keep them as historical record — decide with the team. Close the tracker PR. Clean up feature branches after all PRs are merged.
  - **Files**: Potential removal of `openspec/changes/home-redesign/backups/` files (if not needed)
  - **Verification**: No stale backup artifacts in working tree. Feature branches deleted locally and on remote.
  - **PR**: Part of PR #4
  - **Estimated LOC**: ~5

---

## Open Questions to Resolve Before Apply

1. **META-KEY-001**: Verify exact meta key for vehicle year — Resolve with:
   ```sql
   SELECT meta_key FROM wp_postmeta WHERE post_id IN (SELECT ID FROM wp_posts WHERE post_type='vehica_car' LIMIT 1) AND meta_key LIKE 'vehica_41%'
   ```
2. **META-KEY-002**: Verify exact meta key for vehicle price (`vehica_41400` assumed — confirm on production DB)
3. **META-KEY-003**: Verify exact meta key for vehicle mileage (km) — Resolve by querying meta keys for a sample vehicle
4. **META-KEY-004**: Verify exact meta key for vehicle fuel type — may be a taxonomy term rather than meta
5. **CAROUSEL-001**: Confirm if Elementor Loop Grid widget is available in current Elementor version (v4.0.5 stated in design). If yes, use Option A (Loop Grid wrapper around shortcode output). If not, use Option B (custom CSS carousel with horizontal scroll + snap points).
6. **HEADER-001**: Confirm header implementation approach — is it an Elementor Theme Builder template (global across all pages, requires Elementor Pro), or a section inside page 35463 (page-specific, simpler)? Check by inspecting current site's header source.
7. **TEST-DATA-001**: Verify whether `test.wecar.com.ar` already has a clone of production data, or if a DB restore from backup is needed first. Run `wp post count vehica_car` on test to check.
8. **NAV-MENU-001**: Confirm if a WordPress menu named "Main Nav" (or similar) already exists with the 5 items (Inicio, Comprar, Vender, Nosotros, Blog), or if a new menu must be created and assigned.
9. **LOGO-001**: Confirm the extracted logo has sufficient resolution for mobile @2x displays (≥ 240px width). SVG is ideal.

## Test-First Workflow

```
Phase 1 (Backup in test)
  T001 → T002 → T003 → T004
     ↓ (backups verified)
Phase 2 (Design system in test)
  T005 → T006 → T007 → T008 → T009 → T010
     ↓ (CSS/JS/shortcode enqueued)
Phase 3 (Build in test)
  T011 → T012 → T013
     ↓ (JSON applied, cache flushed)
Phase 4 (Validate in test)
  T014 → T015 → T016 → T017
     ↓ (all scenarios pass)
Phase 5 (Migrate to production)
  T018 → T019 → T020 → T021 → T022 → T023
```

Each task must pass verification before the next task begins. If any task fails, fix it and re-run from the last clean state. Rollback (restore backup JSON + cache flush) is always available after T001–T004.

## Risk Mitigations Per Phase

| Phase | Risk | Mitigation |
|---|---|---|
| **Phase 1: Backup** | Data loss during JSON manipulation | Three independent backups (JSON, template, SQL). Checksum verification. |
| **Phase 2: Foundation** | CSS leaks to other pages | All rules scoped under `body.home` or `.wecar-home-*`. Verify non-home pages after each CSS addition. |
| **Phase 3: Build** | Elementor regenerates IDs on JSON import | Apply in test first. Re-export and diff against source. Adjust source IDs before production. |
| **Phase 3: Build** | Shortcode query fails due to wrong meta keys | Resolve META-KEY questions before finalizing shortcode. Add defensive fallbacks (empty state). |
| **Phase 4: Validate** | Test environment has no active `vehica_car` vehicles | Shortcode includes empty state with CTA. Verify this scenario explicitly. |
| **Phase 4: Validate** | Animations broken on Safari mobile | Test on real iOS device or BrowserStack. Use `IntersectionObserver` + CSS only (no JS-driven animation frames). |
| **Phase 5: Migration** | Production goes down | Backups taken first. Rollback procedure documented and tested. Roll-forward for minor issues. Immediate rollback for critical failures. |
| **Phase 5: Migration** | Stale cache serves old CSS/JS | Bump version query strings in `wp_enqueue_style/script`. Run `wp cache flush`. Verify with `curl -I`. |

## Deliverable Checklist

| Phase | Tasks | PR | Status |
|---|---|---|---|
| Phase 1: Backup & Safety Net | T001–T004 | PR #1 | 🔲 |
| Phase 2: Design System Foundation | T005–T010 | PR #1 (T005, T006, T007 partial, T009) + PR #2 (T007 partial, T008, T010) | 🔲 |
| Phase 3: Build New Home | T011–T013 | PR #3 | 🔲 |
| Phase 4: Visual Validation | T014–T017 | PR #3 | 🔲 |
| Phase 5: Production Migration | T018–T023 | PR #4 | 🔲 |
| Branch cleanup (local + remote) | — | — | 🔲 |
| Update AGENTS.md or README if needed | — | — | 🔲 |
