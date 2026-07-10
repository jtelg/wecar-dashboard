# Home Correction 2026-07 — Tasks

> **Date**: 2026-07-01
> **Change**: `home-correct-2026-07`
> **Status**: Ready for `sdd-apply` (chain strategy: feature-branch-chain)
> **Forecast**: ~730 reviewable lines across 11 theme files + 1 Elementor JSON (~250 lines, structured data, excluded from review budget). Review budget: 400 lines. → **Chained PRs required.**

---

## Overview

This change corrects the test home page to match the approved mockups (`new-design/home.png` + `new-design/section-1(1).png`). The shipped `home-redesign` has the right 7-section structure but wrong visual details — hero cards have icons and CTAs that shouldn't be there, section backgrounds are plain white instead of gradients, the 3-step section fades in all at once instead of progressively, and the footer phone is a placeholder.

The forecast is ~730 reviewable lines across 12 files, which exceeds the 400-line review budget. We split into **2 chained PRs**:

- **PR-1** (~475 reviewable lines): Visual and structural foundation — all CSS rewrites except steps, new SVG pattern, new CSS tokens, new Elementor JSON. After this PR, the home page looks correct visually (gradients, wavy patterns, correct copy, no icons/buttons in hero, updated footer phone) but the 3-step animation still uses the old fade-in behavior.
- **PR-2** (~255 reviewable lines): Steps animation + shortcode tag reorder — new state-based scroll-bound animation (4-frame progressive disclosure), gradient connecting line, final CTA, updated vehicle card tag order.

All work targets `test.wecar.com.ar` ONLY. The branch starts as `home-correct-2026-07` (the **tracker** branch, renamed from the misleadingly-named `feat/redesign-prod`). PR-1 and PR-2 live on their own child branches and target the tracker. The tracker merges to main only after both PRs are reviewed and integrated. No production deployment — this change ships source code to main; production deployment is a separate future change.

---

## Chain Strategy

> **User approved**: `feature-branch-chain` on 2026-07-01.

The feature branch chain model protects `main` from half-applied changes: instead of merging each PR directly to `main`, child PRs stack on top of each other and all merge into a tracker branch. Only after the full feature is integrated does the tracker merge to `main`.

```
main
  └── home-correct-2026-07 (TRACKER branch, draft/no-merge)
        ├── PR-1: Visual & Structural Foundation (~475 lines)
        │   (PR-1 branch: home-correct-2026-07-pr1 → targets home-correct-2026-07)
        └── PR-2: Animation & Shortcode (~255 lines)
            (PR-2 branch: home-correct-2026-07-pr2 → targets home-correct-2026-07-pr1)

After both PRs merged:
  - home-correct-2026-07 (tracker) contains all changes
  - Final integration: PR from home-correct-2026-07 → main, OR direct merge
```

---

## Task List

### Phase 0: Preflight (single task, no PR)

---

#### TASK-024: Rename branch from `feat/redesign-prod` to `home-correct-2026-07` (tracker)

- **Phase**: preflight
- **Files affected**: none (git only)
- **REQ satisfied**: N/A (operational — safety measure)
- **Lines estimate**: 0
- **Dependencies**: none
- **Why**: The current branch name `feat/redesign-prod` is dangerously misleading. It implies the branch maps to production. Rename before any commits to prevent accidental production pushes (see exploration.md §Risks, item 2). This is the TRACKER branch for the feature branch chain — child PRs target it but never push directly to it.
- **Design reference**: Section 6 (Risk Mitigation) — "Branch trap (feat/redesign-prod)". Chain strategy: feature-branch-chain (approved 2026-07-01).
- **Steps**:
  1. Verify current branch: `git branch --show-current` — must output `feat/redesign-prod`.
  2. Create the tracker branch: `git branch -m feat/redesign-prod home-correct-2026-07`.
  3. If the remote `origin/feat/redesign-prod` exists, update tracking: `git push origin -u home-correct-2026-07 && git push origin --delete feat/redesign-prod`.
  4. Create the tracker PR immediately (draft/no-merge) so the branch is visible on GitHub as a PR target:
     ```powershell
     gh pr create --base main --head home-correct-2026-07 --title "home-correct-2026-07: tracker (do not merge)" --body "## Tracker branch for home-correct-2026-07

     This is a tracker PR for the **feature branch chain**. Do NOT merge.

     All child PRs target this branch. After both child PRs are integrated, this tracker will be merged to main.

     - [ ] PR-1 merged into tracker
     - [ ] PR-2 merged into tracker
     - [ ] Final integration verified on test.wecar.com.ar
     " --draft
     ```
- **Verification**:
  - `git branch --show-current` outputs `home-correct-2026-07`.
  - `git log --oneline -3` shows the same commit history as before (no lost commits).
  - `gh pr list --head home-correct-2026-07 --state open` shows a draft PR.
- **Rollback**: `git branch -m home-correct-2026-07 feat/redesign-prod` (if rename was not pushed). If already pushed, `git push origin -u feat/redesign-prod && git push origin --delete home-correct-2026-07 && gh pr close <tracker-pr-number>`.

---

#### TASK-024b: Create PR-1 branch from tracker

- **Phase**: preflight
- **Files affected**: none (git only)
- **REQ satisfied**: N/A (operational — required by feature-branch-chain model)
- **Lines estimate**: 0
- **Dependencies**: TASK-024 (tracker branch must exist)
- **Why**: In the feature-branch-chain model, child PRs live on their own branches, not directly on the tracker. This branch (`home-correct-2026-07-pr1`) will hold all PR-1 commits and target the tracker branch.
- **Design reference**: `chained-pr` skill — "Feature Branch Chain": child PR #1 targets the tracker branch.
- **Steps**:
  1. Verify we are on the tracker branch:
     ```powershell
     git branch --show-current  # must output home-correct-2026-07
     ```
  2. Create and switch to the PR-1 branch:
     ```powershell
     git checkout -b home-correct-2026-07-pr1
     ```
  3. Push the new branch to remote:
     ```powershell
     git push -u origin home-correct-2026-07-pr1
     ```
  4. Verify the branch exists locally and remotely:
     ```powershell
     git branch --list 'home-correct-2026-07-pr1'
     git branch -r --list 'origin/home-correct-2026-07-pr1'
     ```
- **Verification**:
  - `git branch --show-current` outputs `home-correct-2026-07-pr1`.
  - `git log --oneline -1` shows the same HEAD as the tracker branch (no divergence yet).
  - Remote branch `origin/home-correct-2026-07-pr1` exists.
- **Rollback**: `git checkout home-correct-2026-07 && git branch -D home-correct-2026-07-pr1 && git push origin --delete home-correct-2026-07-pr1`.

---

### PR-1: Visual & Structural Foundation (~475 reviewable lines)

> **Goal**: All static visual changes — CSS rewrites, SVG asset, CSS tokens, Elementor JSON. After this PR is deployed to test, the home page renders with correct gradients, wavy patterns, updated copy, no icons/buttons in hero cards, new section backgrounds, and the real phone number in the footer. The 3-step animation is unchanged (old fade-in) — it will be replaced in PR-2.
>
> **Budget note**: PR-1 is ~475 reviewable lines, which is ~75 lines over the 400-line budget. This is acceptable given that ~300 lines are declarative CSS (gradients, backgrounds, typography — low cognitive load). If the reviewer pushes back, the orchestrator can request a `size:exception` or split into PR-1a/PR-1b at apply time.

---

#### TASK-025: Create `wavy-pattern.svg` asset

- **Phase**: PR-1
- **Files affected**:
  - `wp-content/themes/vehica-child/assets/images/wavy-pattern.svg` (NEW)
- **REQ satisfied**: REQ-HOME-024 (Wavy/Squiggle SVG Pattern Asset)
- **Lines estimate**: ~30
- **Dependencies**: none
- **Design reference**: Section 2.1 — SVG content with 6 horizontal squiggles in a 200×200 viewBox, using `stroke="currentColor"` for color configurability.
- **Steps**:
  1. Create `assets/images/wavy-pattern.svg` with the following content (from design.md §2.1):
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
  2. Ensure the SVG uses `stroke="currentColor"` (not hardcoded hex) so each section can color the pattern via CSS `color` property.
  3. Ensure file size < 5 KB.
- **Verification**:
  - `file assets/images/wavy-pattern.svg` returns "SVG Scalable Vector Graphics" or similar.
  - `Get-ChildItem assets/images/wavy-pattern.svg | Select-Object Length` — Length < 5 KB.
  - Open in browser: pattern renders as 6 horizontal squiggles, no broken paths.
- **Rollback**: `git rm assets/images/wavy-pattern.svg`.

---

#### TASK-026: Add new tokens to `tokens.css`

- **Phase**: PR-1
- **Files affected**:
  - `wp-content/themes/vehica-child/assets/css/tokens.css` (MODIFIED — append new tokens)
- **REQ satisfied**: NFR (design system consistency). Enables REQ-HOME-002, REQ-HOME-003, REQ-HOME-007, REQ-HOME-018.
- **Lines estimate**: ~15
- **Dependencies**: none
- **Design reference**: Section 5 — "New tokens needed" adds `--wecar-lavender-bg`, `--wecar-wavy-opacity`, and step circle colors.
- **Steps**:
  1. Open `tokens.css`.
  2. After the existing `--wecar-shadow-hover` declaration, add before the closing `}`:
     ```css
     /* ── Home Correction 2026-07 ───────────────────────────────── */
     --wecar-lavender-bg: #F5F3FF;
     --wecar-wavy-opacity: 0.1;
     --wecar-step-1: var(--wecar-purple-light);
     --wecar-step-2: var(--wecar-blue);
     --wecar-step-3: var(--wecar-cyan-dark);
     ```
  3. Verify the `:root` block closes correctly (no duplicate closing braces).
- **Verification**:
  - `npx stylelint assets/css/tokens.css` (if linting configured) or manual review — no syntax errors.
  - CSS parses: load any page in browser, inspect `:root` in DevTools, confirm 4 new variables appear.
- **Rollback**: `git checkout main -- assets/css/tokens.css`.

---

#### TASK-027: Rewrite `home-hero.css`

- **Phase**: PR-1
- **Files affected**:
  - `wp-content/themes/vehica-child/assets/css/home-hero.css` (MAJOR REWRITE)
- **REQ satisfied**: REQ-HOME-002 (MODIFIED) — Hero dual cards without icons/CTAs, with gradient backgrounds and wavy pattern.
- **Lines estimate**: ~90
- **Dependencies**: TASK-025 (wavy-pattern.svg referenced via URL)
- **Design reference**: Section 2.2 — full selector-by-selector rewrite guidance.
- **Steps**:
  1. Remove all Icon styling: delete `.wecar-hero-card--comprar .elementor-icon`, `.wecar-hero-card--vender .elementor-icon` rules.
  2. Remove all Button styling: delete `.wecar-hero-card .elementor-button` rules.
  3. Rewrite the left card styling:
     ```css
     .wecar-hero__card {
       position: relative;
       overflow: hidden;
       border-radius: var(--wecar-radius-xl);
       padding: var(--wecar-space-12) var(--wecar-space-8);
     }
     .wecar-hero__card--left {
       background: linear-gradient(180deg, #FFFFFF 0%, #F5F3FF 100%);
       color: var(--wecar-text);
     }
     .wecar-hero__card--right {
       background: linear-gradient(180deg, #36BFFA 0%, #2563EB 100%);
       color: #FFFFFF;
     }
     ```
  4. Add the wavy pattern `::before` pseudo-element for both cards:
     ```css
     .wecar-hero__card::before {
       content: '';
       position: absolute;
       inset: 0;
       background-image: url('../images/wavy-pattern.svg');
       background-repeat: repeat;
       color: inherit;
       opacity: var(--wecar-wavy-opacity);
       pointer-events: none;
       z-index: 0;
     }
     ```
  5. Add z-index stacking to keep content above the pattern:
     ```css
     .wecar-hero__card > * {
       position: relative;
       z-index: 1;
     }
     ```
  6. Update typography selectors from `.wecar-hero-card .elementor-heading-title` to `.wecar-hero__card .elementor-heading-title`, set `font-size: var(--wecar-text-2xl)` and `line-height: var(--wecar-line-tight)`.
  7. Update description selector to `.wecar-hero__card .elementor-widget-text-editor`, set `font-size: var(--wecar-text-lg)`, `opacity: 0.85`, `max-width: 480px`.
  8. Keep the existing responsive breakpoint (stack at ≤768px), reduce padding to `var(--wecar-space-8)` vertical.
- **Verification**:
  - CSS syntax check: `npx stylelint assets/css/home-hero.css` or manual review.
  - No remaining references to old class names `wecar-hero-card`, `wecar-hero-card--comprar`, `wecar-hero-card--vender` in the file (use grep).
- **Rollback**: `git checkout main -- assets/css/home-hero.css`.

---

#### TASK-028: Rewrite `home-carousel.css`

- **Phase**: PR-1
- **Files affected**:
  - `wp-content/themes/vehica-child/assets/css/home-carousel.css` (MAJOR REWRITE)
- **REQ satisfied**: REQ-HOME-019 (Carousel gradient background), REQ-HOME-020 ("Ver todos" link), REQ-HOME-021 (Bottom CTA), REQ-HOME-025 (tag chip styling — order handled by PHP in TASK-038)
- **Lines estimate**: ~130
- **Dependencies**: TASK-025 (wavy-pattern.svg)
- **Design reference**: Section 2.4 — gradient bg, wavy overlay, header layout, link styling, CTA button, tag chip restyling.
- **Steps**:
  1. Add section background on `.wecar-carousel-section`:
     ```css
     .wecar-carousel-section {
       background: linear-gradient(135deg, #5E3BE0 0%, #2563EB 100%);
       position: relative;
       overflow: hidden;
     }
     ```
  2. Add wavy pattern overlay:
     ```css
     .wecar-carousel-section::before {
       content: '';
       position: absolute;
       inset: 0;
       background-image: url('../images/wavy-pattern.svg');
       background-repeat: repeat;
       color: #FFFFFF;
       opacity: 0.06;
       pointer-events: none;
       z-index: 0;
     }
     .wecar-carousel-section > * { position: relative; z-index: 1; }
     ```
  3. Add section header (title + link row):
     ```css
     .wecar-carousel__header {
       display: flex;
       justify-content: space-between;
       align-items: center;
       margin-bottom: var(--wecar-space-8);
     }
     .wecar-carousel__title {
       color: #FFFFFF;
       font-size: var(--wecar-text-xl);
       font-weight: var(--wecar-weight-bold);
     }
     .wecar-carousel__link {
       color: #FFFFFF;
       text-decoration: none;
       font-size: var(--wecar-text-base);
     }
     .wecar-carousel__link::after { content: ' →'; }
     ```
  4. Add bottom CTA button:
     ```css
     .wecar-carousel__cta {
       display: inline-block;
       background: #FFFFFF;
       color: var(--wecar-purple);
       border-radius: var(--wecar-radius-full);
       padding: var(--wecar-space-3) var(--wecar-space-8);
       font-weight: var(--wecar-weight-bold);
       text-decoration: none;
       margin-top: var(--wecar-space-8);
       transition: background 0.2s ease, color 0.2s ease;
     }
     .wecar-carousel__cta:hover {
       background: var(--wecar-purple-light);
       color: #FFFFFF;
     }
     ```
  5. Restyle tag chips (all tags share a consistent look — light blue bg, dark blue text, no border):
     ```css
     .wecar-vehicle-card__tag {
       background: #E0F2FE;
       color: #1E3A8A;
       border: none;
       border-radius: var(--wecar-radius-sm);
       padding: 0.25rem 0.5rem;
     }
     ```
  6. Remove `.wecar-vehicle-card__tag--km` special styling (all tags are uniform now).
- **Verification**:
  - CSS syntax check.
  - No remaining `.wecar-vehicle-card__tag--km` or `.wecar-vehicle-card__tag--fuel` selectors.
- **Rollback**: `git checkout main -- assets/css/home-carousel.css`.

---

#### TASK-029: Rewrite `home-features.css`

- **Phase**: PR-1
- **Files affected**:
  - `wp-content/themes/vehica-child/assets/css/home-features.css` (MAJOR REWRITE)
- **REQ satisfied**: REQ-HOME-007 (MODIFIED) — Elegí Wecar section with concrete benefits, REQ-HOME-022 (white→lavender gradient background)
- **Lines estimate**: ~100
- **Dependencies**: none
- **Design reference**: Section 2.5 — solid-color cards with white text, section gradient background, card-specific colors.
- **Steps**:
  1. Add section gradient background on `.wecar-features-section`:
     ```css
     .wecar-features-section {
       background: linear-gradient(180deg, #FFFFFF 0%, #F5F3FF 100%);
     }
     ```
  2. Replace the card style from "white card + icon on white" to "solid color card + white icon + white text":
     ```css
     .wecar-features__card {
       padding: var(--wecar-space-8);
       border-radius: var(--wecar-radius-lg);
       text-align: left;
       min-height: 220px;
       color: #FFFFFF;
     }
     .wecar-features__card--purple { background: #5E3BE0; }
     .wecar-features__card--blue { background: #2563EB; }
     .wecar-features__card--teal { background: #06B6D4; }
     ```
  3. Update icon styling (white color, larger size):
     ```css
     .wecar-features__card .elementor-icon {
       color: #FFFFFF;
       font-size: 2.5rem;
       margin-bottom: var(--wecar-space-4);
     }
     ```
  4. Update title styling:
     ```css
     .wecar-features__card .elementor-heading-title {
       color: #FFFFFF;
       font-size: var(--wecar-text-lg);
       font-weight: var(--wecar-weight-bold);
       line-height: var(--wecar-line-tight);
     }
     ```
  5. Keep the 3-column row layout (flex/grid) unchanged.
- **Verification**:
  - CSS syntax check.
  - No remaining references to old class names `wecar-feature-card--confianza`, `--transparencia`, `--facilidad`.
  - Old abstract value title references removed.
- **Rollback**: `git checkout main -- assets/css/home-features.css`.

---

#### TASK-030: Rewrite `home-partners.css`

- **Phase**: PR-1
- **Files affected**:
  - `wp-content/themes/vehica-child/assets/css/home-partners.css` (MAJOR REWRITE)
- **REQ satisfied**: REQ-HOME-008 (MODIFIED) — "Respaldado por grandes marcas" section, REQ-HOME-023 (lavender gradient background)
- **Lines estimate**: ~70
- **Dependencies**: TASK-025 (wavy-pattern.svg)
- **Design reference**: Section 2.6 — gradient bg, wavy overlay, title styling, placeholder logo styling.
- **Steps**:
  1. Add section background on `.wecar-partners`:
     ```css
     .wecar-partners {
       background: linear-gradient(180deg, #C7B8FF 0%, #A78BFA 100%);
       position: relative;
       overflow: hidden;
     }
     ```
  2. Add wavy pattern overlay:
     ```css
     .wecar-partners::before {
       content: '';
       position: absolute;
       inset: 0;
       background-image: url('../images/wavy-pattern.svg');
       background-repeat: repeat;
       color: #5E3BE0;
       opacity: 0.06;
       pointer-events: none;
       z-index: 0;
     }
     .wecar-partners > * { position: relative; z-index: 1; }
     ```
  3. Update section title color to dark, size `--wecar-text-xl`:
     ```css
     .wecar-partners .elementor-heading-title {
       color: var(--wecar-text);
       font-size: var(--wecar-text-xl);
     }
     ```
  4. Replace dashed-border placeholder styling with subtle white-background logo blocks:
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
  5. Remove any `border: dashed` rules from `.wecar-partner-placeholder`.
  6. Keep the 3-logo horizontal layout with even spacing.
- **Verification**:
  - CSS syntax check.
  - No remaining dashed-border rules.
- **Rollback**: `git checkout main -- assets/css/home-partners.css`.

---

#### TASK-031: Edit `home-footer.css` (phone number, wavy pattern, white background)

- **Phase**: PR-1
- **Files affected**:
  - `wp-content/themes/vehica-child/assets/css/home-footer.css` (MINOR REWRITE)
- **REQ satisfied**: REQ-HOME-009 (MODIFIED) — updated phone number, wavy pattern decoration, white background
- **Lines estimate**: ~30
- **Dependencies**: TASK-025 (wavy-pattern.svg)
- **Design reference**: Section 2.7 — white background, lavender phone color, wavy pattern, copyright kept.
- **Steps**:
  1. Change footer background from dark (`--wecar-footer-bg`) to white:
     ```css
     .wecar-footer {
       background: #FFFFFF;
       color: var(--wecar-text);
     }
     ```
  2. Add wavy pattern overlay:
     ```css
     .wecar-footer::before {
       content: '';
       position: absolute;
       inset: 0;
       background-image: url('../images/wavy-pattern.svg');
       background-repeat: repeat;
       color: var(--wecar-purple-light);
       opacity: 0.05;
       pointer-events: none;
       z-index: 0;
     }
     .wecar-footer { position: relative; overflow: hidden; }
     .wecar-footer > * { position: relative; z-index: 1; }
     ```
  3. Add `.wecar-footer__phone` with lavender color:
     ```css
     .wecar-footer__phone {
       color: var(--wecar-purple-light);
     }
     ```
  4. Keep copyright text "2026 Custer. All rights reserved." styling (existing rules, color changed if needed for dark-on-light).
  5. Update any `color` rules from white-on-dark to dark-on-light as needed.
- **Verification**:
  - CSS syntax check.
  - No remaining dark-background references (e.g., `--wecar-footer-bg` should not be used as background).
- **Rollback**: `git checkout main -- assets/css/home-footer.css`.

---

#### TASK-032: Edit `home-header.css` (CTA button color alignment)

- **Phase**: PR-1
- **Files affected**:
  - `wp-content/themes/vehica-child/assets/css/home-header.css` (MINOR EDIT)
- **REQ satisfied**: REQ-HOME-001 (visual alignment — CTA button matches lavender pill in mockup)
- **Lines estimate**: ~10
- **Dependencies**: none
- **Design reference**: Section 2.8 — CTA button background to `--wecar-purple-light`, hover to `--wecar-purple`.
- **Steps**:
  1. Modify `.wecar-home-header .elementor-button`:
     ```css
     .wecar-home-header .elementor-button {
       background: var(--wecar-purple-light);
       color: #FFFFFF;
       border-radius: var(--wecar-radius-full);
     }
     .wecar-home-header .elementor-button:hover {
       background: var(--wecar-purple);
     }
     ```
  2. If the existing selector is more specific (e.g., `.elementor-button` on an `a` tag), adjust specificity to match. The key is the lavender pill matches `#7B5CE8`.
- **Verification**:
  - CSS syntax check.
  - After deploy: "Contactanos" button in header renders as lavender pill with white text, hover darkens to `--wecar-purple`.
- **Rollback**: `git checkout main -- assets/css/home-header.css`.

---

#### TASK-033: Prepare Elementor JSON for post 35463 (test only)

- **Phase**: PR-1
- **Files affected**:
  - `openspec/changes/home-correct-2026-07/elementor/home-35463-new.json` (NEW)
  - Test DB: `wp_postmeta` for post 35463 (applied via WP-CLI in TASK-034)
- **REQ satisfied**: REQ-HOME-002 (hero copy + class names), REQ-HOME-003 (3-step copy), REQ-HOME-007 (Elegí Wecar titles + classes), REQ-HOME-008 (partners title), REQ-HOME-009 (footer phone)
- **Lines estimate**: JSON is ~250 lines (structured data, not counted in review budget per design.md §9)
- **Dependencies**: TASK-025 through TASK-032 (CSS class names must be finalized first — the JSON uses `_css_classes` that the CSS targets)
- **Design reference**: Section 2.11 — comprehensive edit instructions per Elementor section. Section 10 — class-name map.
- **Steps**:
  1. Verify the `elementor/` directory exists under the change directory. If not, create it:
     ```powershell
     New-Item -ItemType Directory -Path "openspec/changes/home-correct-2026-07/elementor" -Force
     ```
  2. Copy the source JSON from the previous change:
     ```powershell
     Copy-Item "openspec/changes/home-redesign/elementor/home-35463-new.json" "openspec/changes/home-correct-2026-07/elementor/home-35463-new.json"
     ```
  3. Edit the copied JSON per design.md §2.11. The mandatory edits per section:

     **Hero section (`_element_id`: `wecar-hero`)**:
     - Remove the Icon Box icon and Button widgets from both columns (find their `widgetType: "icon"` / `widgetType: "button"` in the inner section elements array and delete the entire widget object).
     - Update the remaining Icon Box widget settings:
       - Left card: `_css_classes` → `wecar-hero__card wecar-hero__card--left`, `title_text` → `"Encontrá tu próximo auto"`, `description_text` → `"La oferta mas grande de vehículos de Villa María y Villa Nueva"`.
       - Right card: `_css_classes` → `wecar-hero__card wecar-hero__card--right`, `title_text` → `"Vendé tu auto sin dejar de manejarlo"`, `description_text` → `"Simplificamos tu venta particular, enviamos los datos, lo cotizamos, publicamos y vendemos por vos."`.

     **3-step section (`_element_id`: `wecar-steps`)**:
     - Add `"wecar-steps"` to the section's `_css_classes` (append to existing string, space-separated).
     - Update section title to `"Vendé tu auto al mejor precio. Usalo hasta el último día"`.
     - Update step card titles and body text per REQ-HOME-003 (see spec.md lines 68-71 for exact copy).
     - Update `_css_classes` on each step card from `wecar-step wecar-step--N` to `wecar-steps__card wecar-steps__card--N`.
     - Add a new Button widget at the end (after the 3 step cards) with:
       - `text`: `"Vendé tu usado sin vueltas"`
       - `link`: `{ url: "/vende-tu-auto/" }` (fallback: `"/cotiza/"`)
       - `_css_classes`: `wecar-steps__cta`
       - Button style: lave;nder bg, white text, pill shape. Set via Elementor button settings: `button_background_color: "#7B5CE8"`, `button_text_color: "#FFFFFF"`, `border_radius: { unit: "px", top: "50", right: "50", bottom: "50", left: "50" }`.

     **Carousel section (`_element_id`: `wecar-carousel`)**:
     - Add `"wecar-carousel-section"` to the section's `_css_classes`.
     - Change section background to gradient: `background_background: "gradient"`, `background_gradient_first_color: "#5E3BE0"`, `background_gradient_second_color: "#2563EB"`, `background_gradient_angle: 135`.
     - Add a Heading widget in the top-right of the header row with:
       - `title`: `"Ver todos →"`
       - `link`: `{ url: "/autos/" }`
       - `_css_classes`: `wecar-carousel__link`
     - Add a Button widget at the bottom (after the shortcode widget) with:
       - `text`: `"Contactar con un asesor"`
       - `link`: `{ url: "/contactanos/" }`
       - `_css_classes`: `wecar-carousel__cta`

     **Elegí Wecar section (`_element_id`: `wecar-features`)**:
     - Add `"wecar-features-section"` to the section's `_css_classes`.
     - Change section background to gradient: `background_background: "gradient"`, `background_gradient_first_color: "#FFFFFF"`, `background_gradient_second_color: "#F5F3FF"`, `background_gradient_angle: 180`.
     - Update card titles per REQ-HOME-007:
       - Card 1: `"Nuestro equipo de expertos te asesora"` → `_css_classes`: `wecar-features__card wecar-features__card--purple`
       - Card 2: `"Peritajes profesionales para asegurar su calidad"` → `_css_classes`: `wecar-features__card wecar-features__card--blue`
       - Card 3: `"Múltiples posibilidades de financiación"` → `_css_classes`: `wecar-features__card wecar-features__card--teal`

     **Partners section (`_element_id`: `wecar-partners`)**:
     - Add `"wecar-partners"` to the section's `_css_classes`.
     - Change section background to gradient: `background_background: "gradient"`, `background_gradient_first_color: "#C7B8FF"`, `background_gradient_second_color: "#A78BFA"`, `background_gradient_angle: 180`.
     - Change section title text to `"Respaldado por grandes marcas"`.
     - Keep 3 placeholder Image widgets as-is (class stays `wecar-partner-placeholder`).

     **Footer (`_element_id`: `wecar-footer`)**:
     - Add `"wecar-footer"` to the section's `_css_classes`.
     - Change section background to classic white: `background_background: "classic"`, `background_color: "#FFFFFF"`.
     - Find the Text Editor widget containing the phone number. Change text from `+54 9 11 1234-5678` to `+54 9 3534 41-3243` wrapped in `<a href="tel:+5493534413243">+54 9 3534 41-3243</a>`.
     - Keep "2026 Custer. All rights reserved." text unchanged.

  4. Validate JSON syntax:
     ```powershell
     Get-Content "openspec/changes/home-correct-2026-07/elementor/home-35463-new.json" | ConvertFrom-Json | Out-Null
     ```
  5. Verify every `_css_classes` value in the JSON matches the class-name map from design.md §10.
- **Verification**:
  - JSON parses without error (`ConvertFrom-Json` succeeds).
  - Class-name map check: grep the JSON for all `_css_classes` values and verify against design.md §10.
  - No duplicate `id` fields across sections (Elementor requires unique IDs per page).
  - All 7 sections present with the correct `_element_id` values.
- **Rollback**: Delete `elementor/home-35463-new.json`. The previous JSON is still available at `openspec/changes/home-redesign/elementor/home-35463-new.json`.

---

#### TASK-034: Deploy PR-1 to test (SCP + apply JSON + verify)

- **Phase**: PR-1
- **Files affected**: none (operational — SCP to test server + WP-CLI commands)
- **REQ satisfied**: N/A (operational deployment step)
- **Lines estimate**: 0 (commands, not file changes)
- **Dependencies**: TASK-025 through TASK-033 (all PR-1 files must exist locally)
- **Design reference**: Section 7 (Deployment Plan) — SCP commands, WP-CLI apply, cache flush, CSS file size gate.
- **Steps**:
  1. SCP all changed CSS, SVG, and JS files to test:
     ```powershell
     scp wp-content/themes/vehica-child/assets/css/tokens.css wecar:~/www/test.wecar.com.ar/public_html/wp-content/themes/vehica-child/assets/css/
     scp wp-content/themes/vehica-child/assets/css/home-hero.css wecar:~/www/test.wecar.com.ar/public_html/wp-content/themes/vehica-child/assets/css/
     scp wp-content/themes/vehica-child/assets/css/home-carousel.css wecar:~/www/test.wecar.com.ar/public_html/wp-content/themes/vehica-child/assets/css/
     scp wp-content/themes/vehica-child/assets/css/home-features.css wecar:~/www/test.wecar.com.ar/public_html/wp-content/themes/vehica-child/assets/css/
     scp wp-content/themes/vehica-child/assets/css/home-partners.css wecar:~/www/test.wecar.com.ar/public_html/wp-content/themes/vehica-child/assets/css/
     scp wp-content/themes/vehica-child/assets/css/home-footer.css wecar:~/www/test.wecar.com.ar/public_html/wp-content/themes/vehica-child/assets/css/
     scp wp-content/themes/vehica-child/assets/css/home-header.css wecar:~/www/test.wecar.com.ar/public_html/wp-content/themes/vehica-child/assets/css/
     scp wp-content/themes/vehica-child/assets/images/wavy-pattern.svg wecar:~/www/test.wecar.com.ar/public_html/wp-content/themes/vehica-child/assets/images/
     ```
  2. SCP the Elementor JSON to a temp location on the server:
     ```powershell
     scp openspec/changes/home-correct-2026-07/elementor/home-35463-new.json wecar:~/home-35463-new.json
     ```
  3. Apply Elementor JSON to test DB:
     ```powershell
     ssh wecar "wp post meta update 35463 _elementor_data --format=json < ~/home-35463-new.json --path=~/www/test.wecar.com.ar/public_html --allow-root"
     ```
  4. Flush all caches:
     ```powershell
     ssh wecar "wp cache flush --path=~/www/test.wecar.com.ar/public_html --allow-root"
     ```
  5. Verify CSS file size (quality gate — must be > 50 KB):
     ```powershell
     ssh wecar "wc -c ~/www/test.wecar.com.ar/public_html/wp-content/uploads/elementor/css/post-35463.css"
     ```
  6. If CSS file is < 50 KB: STOP. Follow the runbook at `openspec/specs/elementor-data-restoration.md`. Do NOT proceed to PR-1 submission.
  7. Visual check: open `https://test.wecar.com.ar/` in a browser. Verify:
     - Hero cards have gradient backgrounds, no icons, no CTA buttons.
     - Carousel has purple→blue gradient background, "Ver todos →" link, bottom CTA.
     - Elegí Wecar cards are solid color (purple/blue/teal) with white text.
     - Partners section has lavender gradient background, 3 logo placeholders (no dashed borders).
     - Footer phone number is `+54 9 3534 41-3243` (clickable tel link).
     - No console errors, no 404s on assets.
- **Verification**:
  - `wc -c` returns > 50,000 bytes for `post-35463.css`.
  - Visual inspection confirms all 5 sections (hero, carousel, features, partners, footer) match the mockup.
  - Browser DevTools shows no 404 errors, no CSS errors.
- **Rollback**:
  1. Restore Elementor data from the last backup. If no backup was taken, restore from the SQL backup:
     ```powershell
     ssh wecar "wp post meta update 35463 _elementor_data --format=json < openspec/changes/home-redesign/elementor/home-35463-new.json --path=~/www/test.wecar.com.ar/public_html --allow-root"
     ```
  2. Re-SCP the old CSS files:
     ```powershell
     git stash && scp ... (all 7 CSS files + SVG) && git stash pop
     ```
  3. Flush cache:
     ```powershell
     ssh wecar "wp cache flush --path=~/www/test.wecar.com.ar/public_html --allow-root"
     ```
  4. Verify: `wc -c post-35463.css` returns > 50 KB.

---

#### TASK-035: Commit PR-1, push, and open PR

- **Phase**: PR-1
- **Files affected**: none (git + GitHub operations)
- **REQ satisfied**: N/A (operational)
- **Lines estimate**: 0
- **Dependencies**: TASK-034 (deploy must succeed and pass visual verification)
- **Steps**:
  1. Verify we are on the PR-1 branch:
     ```powershell
     git branch --show-current  # must output home-correct-2026-07-pr1
     ```
  2. Verify working tree is clean for PR-1 files:
     ```powershell
     git status
     ```
  3. Stage PR-1 files:
     ```powershell
     git add wp-content/themes/vehica-child/assets/images/wavy-pattern.svg
     git add wp-content/themes/vehica-child/assets/css/tokens.css
     git add wp-content/themes/vehica-child/assets/css/home-hero.css
     git add wp-content/themes/vehica-child/assets/css/home-carousel.css
     git add wp-content/themes/vehica-child/assets/css/home-features.css
     git add wp-content/themes/vehica-child/assets/css/home-partners.css
     git add wp-content/themes/vehica-child/assets/css/home-footer.css
     git add wp-content/themes/vehica-child/assets/css/home-header.css
     git add openspec/changes/home-correct-2026-07/elementor/home-35463-new.json
     ```
     Do NOT stage PR-2 files (home-steps.css, home-animations.js, wecar-vehicle-carousel.php) or this file (tasks.md).
  4. Commit:
     ```powershell
     git commit -m "feat(home-correct-2026-07): PR-1 visual foundation — gradients, wavy pattern, hero fix, updated copy"
     ```
     (Conventional commit, no AI co-author — per AGENTS.md rules.)
  5. Push:
     ```powershell
     git push -u origin home-correct-2026-07-pr1
     ```
  6. Open PR — targets the TRACKER branch (`home-correct-2026-07`), NOT main:
     ```powershell
     gh pr create --base home-correct-2026-07 --head home-correct-2026-07-pr1 --title "home-correct-2026-07: PR-1 visual foundation (~475 lines)" --body "## Chain Context

     | Field | Value |
     |-------|-------|
     | Chain | home-correct-2026-07 |
     | Tracker PR | #NNN (draft, created in TASK-024) |
     | Position | 1 of 2 |
     | Base | \`home-correct-2026-07\` (tracker) |
     | Depends on | None |
     | Follow-up | PR-2: Animation & Shortcode |
     | Review budget | 475 / 400 |
     | Starts at | Tracker branch \`home-correct-2026-07\` |
     | Ends with | Page renders correct gradients, wavy patterns, copy, and footer phone |

     ### Chain Overview

     \`\`\`
     main
       └── home-correct-2026-07 (tracker)
            └── 📍 PR-1: Visual & Structural Foundation (this PR)
                 └── PR-2: Animation & Shortcode (follow-up)
     \`\`\`

     ### Scope

     **Includes**: CSS rewrites (hero, carousel, features, partners, footer, header), SVG asset, CSS tokens, Elementor JSON.
     **Excludes**: 3-step scroll animation, shortcode tag reorder (PR-2).

     ### What this PR ships

     - New wavy-pattern.svg asset (REQ-HOME-024)
     - CSS tokens for lavender bg and step circle colors
     - Rewritten home-hero.css — gradient backgrounds, no icons/buttons
     - Rewritten home-carousel.css — purple→blue gradient, Ver todos link, bottom CTA
     - Rewritten home-features.css — solid color cards, gradient bg
     - Rewritten home-partners.css — lavender gradient, placeholder restyle
     - Edited home-footer.css — white bg, real phone, wavy pattern
     - Edited home-header.css — lavender CTA pill
     - New Elementor JSON for post 35463 (applied to test)

     ### REQs covered
     REQ-HOME-001, 002, 003, 007, 008, 009, 019, 020, 021, 022, 023, 024

     ### Verification
     - Deployed to test.wecar.com.ar, CSS file size > 50 KB
     - Visual check matches mockups for hero, carousel, features, partners, footer
     - No console errors, no 404s

     **NO PRODUCTION DEPLOY.** All changes applied to test.wecar.com.ar only. This is part of a feature branch chain — PR-2 (\`home-correct-2026-07-pr2\`) will stack on this PR and target \`home-correct-2026-07-pr1\`.
     "
     ```
  7. PR body MUST include the production warning (already present above).
- **Verification**:
  - `git branch --show-current` outputs `home-correct-2026-07-pr1`.
  - `gh pr view --web` opens the PR correctly on GitHub.
  - PR title matches the convention.
  - PR body includes the chain context table, dependency diagram, and production warning.
  - PR base is `home-correct-2026-07` (tracker), NOT `main`.
- **Rollback**: Close the PR without merging:
  ```powershell
  gh pr close <PR-number>
  git reset HEAD~1 --hard
  git push origin home-correct-2026-07-pr1 --force-with-lease
  ```
  (Force push is acceptable because this is a feature branch with no collaborators.)

---

### PR-2: Animation & Shortcode (~255 reviewable lines)

> **Goal**: Scroll-bound animation (state-based IntersectionObserver), gradient connecting line with numbered circles, shortcode tag reorder. This PR stacks on top of PR-1 (after PR-1 is merged).
>
> **Note**: This PR is well within the 400-line review budget at ~255 lines.

---

#### TASK-036: Rewrite `home-steps.css`

- **Phase**: PR-2
- **Files affected**:
  - `wp-content/themes/vehica-child/assets/css/home-steps.css` (MAJOR REWRITE)
- **REQ satisfied**: REQ-HOME-003 (MODIFIED) — updated step copy; REQ-HOME-018 (Scroll-bound 4-frame disclosure); NFR-HOME-010 (animation uses only `transform` and `opacity`)
- **Lines estimate**: ~160
- **Dependencies**: TASK-037 (CSS shares state class contract with the JS — must be consistent with frame constants)
- **Design reference**: Section 2.3 — circles, track, line-fill, frame-specific rules, card reveal, CTA button. Section 4.3 — CSS transitions.
- **Steps**:
  1. Replace the old step numbering (big "01/02/03" text) with small circle indicators:
     ```css
     .wecar-steps__track {
       display: flex;
       align-items: center;
       justify-content: center;
       max-width: 720px;
       margin: 0 auto var(--wecar-space-8);
       position: relative;
     }
     .wecar-steps__circle {
       width: 24px;
       height: 24px;
       border-radius: 50%;
       border: 2px solid var(--circle-color);
       background: transparent;
       display: flex;
       align-items: center;
       justify-content: center;
       font-size: var(--wecar-text-xs);
       font-weight: var(--wecar-weight-bold);
       transition: background-color 0.4s ease-out, color 0.4s ease-out;
       z-index: 2;
       position: relative;
     }
     .wecar-steps__circle--1 { --circle-color: var(--wecar-step-1); }
     .wecar-steps__circle--2 { --circle-color: var(--wecar-step-2); }
     .wecar-steps__circle--3 { --circle-color: var(--wecar-step-3); }
     .wecar-steps__circle--active {
       background: var(--circle-color);
       color: #FFFFFF;
     }
     ```
  2. Add the gradient connecting line (full width, then scaled via CSS `transform: scaleX()`):
     ```css
     .wecar-steps__line {
       position: absolute;
       top: 50%;
       left: calc(50% - 160px);
       width: 320px;
       height: 3px;
       background: linear-gradient(90deg, var(--wecar-purple-light) 0%, var(--wecar-blue) 50%, var(--wecar-cyan-dark) 100%);
       z-index: 0;
       transform: translateY(-50%);
     }
     .wecar-steps__line-fill {
       width: 100%;
       height: 100%;
       transform: scaleX(var(--wecar-line-fill-scale, 0));
       transform-origin: left center;
       background: inherit;
       transition: transform 0.6s cubic-bezier(0.4, 0, 0.2, 1);
     }
     ```
  3. Add frame-specific state classes (JS adds these to `.wecar-steps`):
     ```css
     .wecar-steps--frame-1 { --wecar-line-fill-scale: 0; }
     .wecar-steps--frame-2 {
       --wecar-line-fill-scale: 0;
     }
     .wecar-steps--frame-2 .wecar-steps__circle--1 { background: var(--wecar-step-1); color: #fff; }
     .wecar-steps--frame-3 {
       --wecar-line-fill-scale: 0.5;
     }
     .wecar-steps--frame-3 .wecar-steps__circle--1 { background: var(--wecar-step-1); color: #fff; }
     .wecar-steps--frame-3 .wecar-steps__circle--2 { background: var(--wecar-step-2); color: #fff; }
     .wecar-steps--frame-4 {
       --wecar-line-fill-scale: 1;
     }
     .wecar-steps--frame-4 .wecar-steps__circle--1 { background: var(--wecar-step-1); color: #fff; }
     .wecar-steps--frame-4 .wecar-steps__circle--2 { background: var(--wecar-step-2); color: #fff; }
     .wecar-steps--frame-4 .wecar-steps__circle--3 { background: var(--wecar-step-3); color: #fff; }
     ```
  4. Add card reveal animation:
     ```css
     .wecar-steps__card {
       opacity: 0;
       transform: translateY(16px);
       transition: opacity 0.5s ease-out, transform 0.5s ease-out;
       background: #FFFFFF;
       border-radius: var(--wecar-radius-lg);
       box-shadow: var(--wecar-shadow-md);
     }
     .wecar-steps--frame-2 .wecar-steps__card--1,
     .wecar-steps--frame-3 .wecar-steps__card--1,
     .wecar-steps--frame-3 .wecar-steps__card--2,
     .wecar-steps--frame-4 .wecar-steps__card {
       opacity: 1;
       transform: translateY(0);
     }
     ```
  5. Add CTA button reveal (appears only in frame 4):
     ```css
     .wecar-steps__cta {
       opacity: 0;
       transform: translateY(12px);
       transition: opacity 0.4s ease-out, transform 0.4s ease-out;
       display: inline-block;
       background: var(--wecar-purple-light);
       color: #FFFFFF;
       border-radius: var(--wecar-radius-full);
       padding: var(--wecar-space-3) var(--wecar-space-8);
       font-weight: var(--wecar-weight-bold);
       text-decoration: none;
     }
     .wecar-steps--frame-4 .wecar-steps__cta {
       opacity: 1;
       transform: translateY(0);
     }
     ```
  6. Add no-JS fallback (shows all content if JS fails):
     ```css
     .wecar-steps:not([class*="wecar-steps--frame"]) .wecar-steps__card,
     .wecar-steps:not([class*="wecar-steps--frame"]) .wecar-steps__cta {
       opacity: 1;
       transform: none;
     }
     ```
  7. Update section title styles:
     ```css
     .wecar-steps .elementor-heading-title {
       font-size: var(--wecar-text-2xl);
       max-width: 720px;
       margin: 0 auto;
       text-align: center;
     }
     ```
  8. Update card body text styles for the longer descriptions:
     ```css
     .wecar-steps__card .elementor-widget-text-editor {
       font-size: var(--wecar-text-sm);
       line-height: var(--wecar-line-relaxed);
     }
     ```
- **Verification**:
  - CSS syntax check.
  - All animation properties use only `opacity` and `transform` (search for `transition:` — verify no `width`, `height`, `top`, `left` are animated) — satisfies NFR-HOME-010.
  - No-JS fallback works: load page, inspect `.wecar-steps`, verify that without any `wecar-steps--frame-N` class, all cards and CTA are visible.
- **Rollback**: `git checkout main -- assets/css/home-steps.css`.

---

#### TASK-037: Rewrite `home-animations.js`

- **Phase**: PR-2
- **Files affected**:
  - `wp-content/themes/vehica-child/assets/js/home-animations.js` (MAJOR REWRITE)
- **REQ satisfied**: REQ-HOME-018 (4-frame progressive disclosure), NFR-HOME-010 (rAF, transform/opacity only, graceful degradation)
- **Lines estimate**: ~80
- **Dependencies**: TASK-036 (CSS frame class names `wecar-steps--frame-N` must match the JS)
- **Design reference**: Section 4.2 (full JS implementation), Section 4.4 (graceful degradation), Section 4.5 (performance).
- **Steps**:
  1. Replace the entire file content with the state-based IntersectionObserver implementation from design.md §4.2:
     ```javascript
     (function () {
       'use strict';

       // Scope to home page only
       if (!document.body.classList.contains('home')) return;

       const section = document.querySelector('.wecar-steps');
       if (!section) return;

       // Graceful degradation: reduced motion or no IO support
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
               target.className = target.className.replace(/wecar-steps--frame-\d+/g, '') + ' wecar-steps--frame-' + frame;
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
  2. Ensure the IIFE wraps the entire script to avoid global scope pollution.
  3. Remove any old fade-in classes (`wecar-step--hidden` / `wecar-step--visible`) logic.
  4. Keep existing header scroll behavior if present in the file (check: if the old file had header sticky logic, preserve it).
- **Verification**:
  - JS syntax check: `node -c assets/js/home-animations.js`.
  - After deploy (TASK-039): scroll through the 3-step section on test. Verify 4 frames:
    - Frame 1: title only.
    - Frame 2 (25% scroll): circle 1 lights up, step 1 card fades in.
    - Frame 3 (50% scroll): circles 1-2 lit, steps 1-2 visible, line half filled.
    - Frame 4 (75% scroll): all 3 circles lit, line fully filled, CTA appears.
  - `prefers-reduced-motion: reduce` enabled in DevTools → all frames appear at once (frame 4).
  - No JS console errors.
- **Rollback**: `git checkout main -- assets/js/home-animations.js`.

---

#### TASK-038: Update shortcode tag order in `wecar-vehicle-carousel.php`

- **Phase**: PR-2
- **Files affected**:
  - `wp-content/themes/vehica-child/includes/shortcodes/wecar-vehicle-carousel.php` (MINOR EDIT)
- **REQ satisfied**: REQ-HOME-025 (Vehicle card tag order — km/year/transmission, drop fuel)
- **Lines estimate**: ~15
- **Dependencies**: none
- **Design reference**: Section 2.10 — new tag rendering block.
- **Steps**:
  1. Open `wecar-vehicle-carousel.php`.
  2. Find the `<div class="wecar-vehicle-card__tags">` block.
  3. Replace the existing tag rendering with the new order (km, year, transmission — no fuel):
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
  4. Remove any `$fuel` variable references (the `$fuel` variable itself and its output).
  5. Verify the PHP syntax: `php -l includes/shortcodes/wecar-vehicle-carousel.php`.
- **Verification**:
  - `php -l` returns "No syntax errors detected".
  - After deploy (TASK-039): inspect a vehicle card in the carousel — exactly 3 chips: "Consultar KM", year, transmission. No fuel chip.
- **Rollback**: `git checkout main -- includes/shortcodes/wecar-vehicle-carousel.php`.

---

#### TASK-039: Deploy PR-2 to test (SCP + verify animation + tag order)

- **Phase**: PR-2
- **Files affected**: none (operational)
- **REQ satisfied**: N/A (operational)
- **Lines estimate**: 0
- **Dependencies**: TASK-036, TASK-037, TASK-038
- **Design reference**: Section 7 (Deployment Plan) — SCP commands.
- **Steps**:
  1. SCP the 3 changed files to test:
     ```powershell
     scp wp-content/themes/vehica-child/assets/css/home-steps.css wecar:~/www/test.wecar.com.ar/public_html/wp-content/themes/vehica-child/assets/css/
     scp wp-content/themes/vehica-child/assets/js/home-animations.js wecar:~/www/test.wecar.com.ar/public_html/wp-content/themes/vehica-child/assets/js/
     scp wp-content/themes/vehica-child/includes/shortcodes/wecar-vehicle-carousel.php wecar:~/www/test.wecar.com.ar/public_html/wp-content/themes/vehica-child/includes/shortcodes/
     ```
  2. Flush cache:
     ```powershell
     ssh wecar "wp cache flush --path=~/www/test.wecar.com.ar/public_html --allow-root"
     ```
  3. Visual verification of scroll animation:
     - Open `https://test.wecar.com.ar/` in Chrome.
     - Slowly scroll through the 3-step section ("Vendé tu auto al mejor precio...").
     - Verify 4 frames as described in TASK-037 verification.
     - Enable `prefers-reduced-motion: reduce` in DevTools Rendering tab → reload → all 3 steps + CTA visible immediately.
  4. Visual verification of tag order:
     - Inspect any vehicle card in the carousel.
     - Confirm exactly 3 chips: "Consultar KM", year, transmission. No fuel chip.
  5. Check console for JS errors.
- **Verification**:
  - 4-frame scroll animation works correctly in both directions.
  - Tag order is km → year → transmission (3 chips only).
  - No console errors.
  - `prefers-reduced-motion` disables animation gracefully.
- **Rollback**:
  ```powershell
  scp wp-content/themes/vehica-child/assets/css/home-steps.css (original from main) ...
  scp wp-content/themes/vehica-child/assets/js/home-animations.js (original from main) ...
  scp wp-content/themes/vehica-child/includes/shortcodes/wecar-vehicle-carousel.php (original from main) ...
  ssh wecar "wp cache flush --path=~/www/test.wecar.com.ar/public_html --allow-root"
  ```

---

#### TASK-040: Commit PR-2, push, and open PR

- **Phase**: PR-2
- **Files affected**: none (git + GitHub operations)
- **REQ satisfied**: N/A (operational)
- **Lines estimate**: 0
- **Dependencies**: TASK-039 (deploy must succeed)
- **Steps**:
  1. Create the PR-2 branch from PR-1's branch (the immediate parent):
     ```powershell
     git checkout home-correct-2026-07-pr1
     git checkout -b home-correct-2026-07-pr2
     ```
     (If PR-1 was merged into the tracker before PR-2, create from the tracker instead: `git checkout home-correct-2026-07 && git checkout -b home-correct-2026-07-pr2`.)
  2. Verify we are on the PR-2 branch:
     ```powershell
     git branch --show-current  # must output home-correct-2026-07-pr2
     ```
  3. Stage PR-2 files:
     ```powershell
     git add wp-content/themes/vehica-child/assets/css/home-steps.css
     git add wp-content/themes/vehica-child/assets/js/home-animations.js
     git add wp-content/themes/vehica-child/includes/shortcodes/wecar-vehicle-carousel.php
     ```
     Do NOT stage the tasks.md file (it's a planning artifact, not part of the PR).
  4. Commit:
     ```powershell
     git commit -m "feat(home-correct-2026-07): PR-2 scroll animation and shortcode tag order"
     ```
  5. Push:
     ```powershell
     git push -u origin home-correct-2026-07-pr2
     ```
  6. Open PR — targets PR-1's branch (`home-correct-2026-07-pr1`), the IMMEDIATE PARENT in the chain:
     ```powershell
     gh pr create --base home-correct-2026-07-pr1 --head home-correct-2026-07-pr2 --title "home-correct-2026-07: PR-2 scroll animation and shortcode (~255 lines)" --body "## Chain Context

     | Field | Value |
     |-------|-------|
     | Chain | home-correct-2026-07 |
     | Tracker PR | #NNN (draft, created in TASK-024) |
     | Position | 2 of 2 |
     | Base | \`home-correct-2026-07-pr1\` (PR-1 branch) |
     | Depends on | PR-1 (#NNN) |
     | Follow-up | Tracker → main integration (TASK-043) |
     | Review budget | 255 / 400 |
     | Starts at | PR-1 branch \`home-correct-2026-07-pr1\` |
     | Ends with | 3-step scroll animation live, tag order corrected |

     ### Chain Overview

     \`\`\`
     main
       └── home-correct-2026-07 (tracker)
            └── PR-1: Visual & Structural Foundation
                 └── 📍 PR-2: Animation & Shortcode (this PR)
     \`\`\`

     ### Scope

     **Includes**: CSS home-steps rewrite (scroll-bound animation), JS home-animations rewrite (IntersectionObserver), PHP shortcode tag order fix.
     **Excludes**: SVG asset, hero/carousel/features/partners/footer CSS, Elementor JSON (all in PR-1).

     ### What this PR ships

     - Rewritten home-steps.css — 4-frame disclosure animation, gradient connecting line, numbered circles, card reveal, CTA fade-in
     - Rewritten home-animations.js — state-based IntersectionObserver, rAF, graceful degradation
     - Edited wecar-vehicle-carousel.php — tag order: km → year → transmission, no fuel tag

     ### REQs covered
     REQ-HOME-003 (steps copy), REQ-HOME-018 (scroll animation), REQ-HOME-025 (tag order), NFR-HOME-010 (performance)

     ### Verification
     - Deployed to test.wecar.com.ar, 4-frame animation verified
     - prefers-reduced-motion gracefully shows all frames at once
     - Tag chips show km → year → transmission (no fuel)
     - No console errors

     **NO PRODUCTION DEPLOY.** Test on test.wecar.com.ar only. This is part of a feature branch chain — after review and merge into \`home-correct-2026-07-pr1\`, the tracker branch will be merged to main in a final integration step (TASK-043).
     "
     ```
  7. PR body MUST include the production warning (already present above).
- **Verification**:
  - `git branch --show-current` outputs `home-correct-2026-07-pr2`.
  - `gh pr view --web` opens the PR correctly on GitHub.
  - PR title matches the convention.
  - PR body includes the chain context table, dependency diagram with 📍 on PR-2, and production warning.
  - PR base is `home-correct-2026-07-pr1` (PR-1's branch), NOT the tracker and NOT main.
- **Rollback**: Close PR, `git checkout home-correct-2026-07-pr1 && git branch -D home-correct-2026-07-pr2 && git push origin --delete home-correct-2026-07-pr2`.

---

### Phase: Verify (after all PRs merged, before archive)

---

#### TASK-041: Run `sdd-verify`

- **Phase**: verify
- **Files affected**: none (verification only)
- **REQ satisfied**: N/A (quality assurance)
- **Lines estimate**: 0 (reporting only)
- **Dependencies**: TASK-040
- **Steps**:
  1. Delegate `sdd-verify` to audit the implementation against `spec.md`.
  2. The verification should cover:
     - All 6 MODIFIED REQs (002, 003, 004→ replaced by 018, 007, 008, 009) — scenarios pass.
     - All 9 ADDED REQs (018 through 026) — scenarios pass.
     - NFR-HOME-010 — animation uses only `transform` and `opacity`, `requestAnimationFrame` used, graceful degradation works.
     - Visual comparison: page on test matches both mockups with < 5% deviation.
     - CSS file size on test: `post-35463.css` > 50 KB.
     - No regression on other pages (listing, single car, blog, contact).
  3. Report any REQ-NFR failures, warnings, or suggestions.
- **Output**: `verify-report.md` (in change directory) with PASS/WARN/FAIL verdict per REQ.
- **Rollback**: If verification fails, fix the issues and re-run verification. Do not archive until all REQs pass.

---

#### TASK-042: Update `apply-progress.md`

- **Phase**: verify
- **Files affected**:
  - `openspec/changes/home-correct-2026-07/apply-progress.md` (NEW)
- **REQ satisfied**: N/A (tracking)
- **Lines estimate**: ~30
- **Dependencies**: TASK-041
- **Steps**:
  1. Create `apply-progress.md` with:
     - Summary of what was applied: list of REQs covered, line counts per PR.
     - Screenshot references (desktop + mobile, placed as artifacts).
     - Any deferred items: real partner logo files (REQ-HOME-026 — v2 follow-up).
     - Known warnings: "Consultar KM" still shows placeholder (km data not in DB), partner logos are placeholders (v2).
- **Verification**: File exists and documents the final state of the change.
- **Rollback**: Delete the file.

---

#### TASK-043: Merge tracker branch to main (final integration)

- **Phase**: verify
- **Files affected**: none (git + GitHub operations)
- **REQ satisfied**: N/A (operational — final integration step)
- **Lines estimate**: 0
- **Dependencies**: TASK-042 (apply-progress documented), both PRs merged into tracker
- **Design reference**: `chained-pr` skill — Feature Branch Chain: "merge the tracker only after the chain is complete."
- **Purpose**: After both PR-1 and PR-2 have been reviewed and merged into the tracker branch (`home-correct-2026-07`), this task opens the final tracker PR to `main` for source-code integration. Production deployment (SSH to wecar.com.ar) is OUT OF SCOPE.
- **Prerequisites** (verify before starting):
  1. PR-1 is merged into `home-correct-2026-07` (tracker).
  2. PR-2 is merged into `home-correct-2026-07` (tracker).
  3. The tracker branch builds and renders correctly on test.wecar.com.ar.
- **Steps**:
  1. Verify the tracker branch has both PRs' commits:
     ```powershell
     git checkout home-correct-2026-07
     git log --oneline -10
     ```
     Confirm the log includes both PR-1 and PR-2 commit messages.
  2. Verify the tracker branch builds on test.wecar.com.ar (optional — should be live already from the individual deploys).
  3. Open the tracker PR to main:
     ```powershell
     gh pr create --base main --head home-correct-2026-07 --title "home-correct-2026-07: integrate feature branch chain" --body "## Chain Context

     | Field | Value |
     |-------|-------|
     | Chain | home-correct-2026-07 |
     | Tracker PR | #NNN (was draft, now ready) |
     | Position | Final integration |
     | Base | \`main\` |
     | Depends on | PR-1 (#NNN), PR-2 (#NNN) |
     | Follow-up | Production deployment (separate future change) |
     | Review budget | ~730 (across both PRs) |

     ### Chain Overview

     \`\`\`
     📍 main ← home-correct-2026-07 (tracker → this PR)
           └── PR-1: Visual & Structural Foundation
                └── PR-2: Animation & Shortcode
     \`\`\`

     ### Contains

     This tracker branch integrates both child PRs:

     **PR-1: Visual & Structural Foundation** (~475 lines)
     - SVG asset, CSS tokens, 6 CSS rewrites, Elementor JSON
     - REQs: 001, 002, 003, 007, 008, 009, 019, 020, 021, 022, 023, 024

     **PR-2: Animation & Shortcode** (~255 lines)
     - CSS animation, JS IntersectionObserver, PHP tag reorder
     - REQs: 003, 018, 025, NFR-010

     ### Deployment Note

     **SOURCE CODE ONLY.** This PR merges source code to \`main\`. Production deployment (SSH to wecar.com.ar, WP-CLI, \`wp post meta update\`) happens in a **separate future change**. Do NOT run any deploy commands against \`~/www/wecar.com.ar/\`.
     "
     ```
  4. Mark the tracker PR as ready for review (it was draft from TASK-024):
     ```powershell
     gh pr ready <tracker-PR-number>
     ```
     (Or update the existing tracker PR description to reflect it's now ready.)
  5. After review approval, merge the tracker PR (squash or merge commit — follow team convention):
     ```powershell
     gh pr merge <tracker-PR-number> --squash
     ```
  6. Verify main branch has the changes locally:
     ```powershell
     git checkout main
     git pull origin main
     git log --oneline -5
     ```
- **HARD RULE**: Even this final PR to main is for SOURCE CODE only. Production deployment (SSH to wecar.com.ar, SCP, WP-CLI) is OUT OF SCOPE for this change. Do NOT run any `wp post meta update` against `~/www/wecar.com.ar/`. The source code goes to main; production deployment happens in a separate future change.
- **Verification**:
  - `gh pr view <tracker-PR-number>` shows the tracker PR is merged.
  - `git log main --oneline -3` includes the merge commit.
  - No production server was touched (verify by checking SSH history or `who`).
- **Rollback**: If the tracker PR was merged to main but not yet deployed to production: `git revert HEAD` on main, push. If already deployed to production (should not happen — this is out of scope), follow the standard rollback runbook.

---

## Forecast Summary

| Phase | Tasks | Lines estimate (reviewable) |
|-------|-------|-----------------------------|
| Preflight | 1 | 0 |
| PR-1 (visual & structural) | 11 (TASK-025 through TASK-035) | ~475 |
| PR-2 (animation & shortcode) | 5 (TASK-036 through TASK-040) | ~255 |
| Verify | 2 (TASK-041, TASK-042) | ~30 (reporting only) |
| **Total** | **19 tasks** | **~730 + ~250 (JSON, structured data)** |

## Dependencies (topological order)

```
TASK-024 (rename branch)
  ↓
TASK-025 (SVG) ──────────────────────────────────┐
TASK-026 (tokens)                                │
  ↓                                               │
TASK-027 (hero CSS) ← depends on TASK-025         │
TASK-028 (carousel CSS) ← depends on TASK-025     ├── all independent of each other
TASK-029 (features CSS)                           │   but depend on TASK-025
TASK-030 (partners CSS) ← depends on TASK-025     │
TASK-031 (footer CSS) ← depends on TASK-025       │
TASK-032 (header CSS)                            ─┘
  ↓
TASK-033 (Elementor JSON) ← depends on TASK-025..032 (class names must be final)
  ↓
TASK-034 (deploy PR-1) ← depends on TASK-025..033
  ↓
TASK-035 (PR-1 commit + push + open)
  ↓  [PR-1 merged]
TASK-036 (steps CSS) ← depends on TASK-037 for frame contract
TASK-037 (animation JS) ← depends on TASK-036 for class names  ── can be parallelized
TASK-038 (shortcode PHP) ── independent
  ↓
TASK-039 (deploy PR-2) ← depends on TASK-036..038
  ↓
TASK-040 (PR-2 commit + push + chain)
  ↓  [PR-2 merged]
TASK-041 (sdd-verify) ← depends on TASK-040
  ↓
TASK-042 (apply-progress)
```

## REQ-to-Task Traceability Matrix

| REQ | Type | Satisfied by |
|-----|------|-------------|
| REQ-HOME-001 | Unmodified (header nav) | TASK-032 |
| REQ-HOME-002 | MODIFIED | TASK-027 (CSS), TASK-033 (JSON) |
| REQ-HOME-003 | MODIFIED | TASK-033 (JSON copy), TASK-036 (CSS copy) |
| REQ-HOME-004 | REPLACED by REQ-HOME-018 | — |
| REQ-HOME-005 | Unmodified (carousel loop) | (reused as-is) |
| REQ-HOME-006 | Unmodified (card render) | (reused as-is) |
| REQ-HOME-007 | MODIFIED | TASK-029 (CSS), TASK-033 (JSON) |
| REQ-HOME-008 | MODIFIED | TASK-030 (CSS), TASK-033 (JSON) |
| REQ-HOME-009 | MODIFIED | TASK-031 (CSS), TASK-033 (JSON) |
| REQ-HOME-018 | ADDED | TASK-036 (CSS), TASK-037 (JS) |
| REQ-HOME-019 | ADDED | TASK-028 (CSS), TASK-033 (JSON) |
| REQ-HOME-020 | ADDED | TASK-028 (CSS), TASK-033 (JSON) |
| REQ-HOME-021 | ADDED | TASK-028 (CSS), TASK-033 (JSON) |
| REQ-HOME-022 | ADDED | TASK-029 (CSS), TASK-033 (JSON) |
| REQ-HOME-023 | ADDED | TASK-030 (CSS), TASK-033 (JSON) |
| REQ-HOME-024 | ADDED | TASK-025 (SVG) |
| REQ-HOME-025 | ADDED | TASK-038 (PHP) |
| REQ-HOME-026 | ADDED (v2) | (not in scope — future) |
| NFR-HOME-010 | ADDED | TASK-036 (CSS: transform/opacity only), TASK-037 (JS: rAF, graceful degradation) |

## Review Workload Forecast

- **Chained PRs recommended**: Yes (forecast ~730 reviewable lines > budget 400 lines)
- **Decision**: ✅ Resolved 2026-07-01 — user approved `feature-branch-chain`. Tracker branch is `home-correct-2026-07`; PR-1 targets the tracker, PR-2 targets PR-1; only the tracker merges to main. See "Chain Strategy" section above.
- **Risk**: PR-1 is at ~475 lines, ~75 lines over the 400 budget. If the reviewer pushes back, the orchestrator can:
  - Request a `size:exception` (CSS is declarative/low-complexity).
  - Split PR-1 into PR-1a (hero + features + SVG + tokens: ~235 lines) and PR-1b (carousel + partners + footer + header + JSON: ~240 lines).
  - This is an acceptable apply-time decision.
