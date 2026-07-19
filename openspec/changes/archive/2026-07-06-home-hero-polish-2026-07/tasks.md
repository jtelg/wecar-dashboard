# Tasks: Home Hero Polish 2026-07

## Review Workload Forecast

| Field | Value |
|-------|-------|
| Estimated changed lines | ~280–340 |
| 400-line budget risk | Low |
| Chained PRs recommended | No |
| Suggested split | Single PR |
| Delivery strategy | single-pr |

Decision needed before apply: No
Chained PRs recommended: No
Chain strategy: size-exception
400-line budget risk: Low

## Phase 1: Accordion State Logic (JS)

**File**: `wp-content/themes/vehica-child/assets/js/home-animations.js`

- [x] **T-HP-01** (~10 lines) — Add mobile guard: `if (window.innerWidth < 768) return;` at click handler top. No-op below 768px preserves stacked layout. Deps: none. AC: REQ-HOME-002 mobile scenario.

## Phase 2: Core CSS Implementation

**File**: `wp-content/themes/vehica-child/assets/css/home-hero.css`

- [x] **T-HP-02** (~15 lines) — Collapsed label `::after`: adjust `content` color (`#111` left, `#FFF` right), Syne Bold 700 38/44, `transition: opacity 0.3s ease 0.25s` (delayed until text fades). Deps: T-HP-03. AC: REQ-HOME-002 collapsed label centering + REQ-HOME-HP01 delayed fade-in.
- [x] **T-HP-03** (~20 lines) — Panel widths from flex-ratio to exact px: `.elementor-column` flex `0 0 598px` initial. `--active` → `flex: 0 0 976px`. `--collapsed` → `flex: 0 0 220px`. `transition: flex 0.5s cubic-bezier(0.4,0,0.2,1)`. Deps: none. AC: REQ-HOME-002 step-1/2/3 width states.
- [x] **T-HP-04** (~10 lines) — Cross-fade text: `.elementor-icon-box-title`, `.elementor-icon-box-description` add `opacity 0.2s ease-out`. On `--collapsed` opacity stays 0. On `--active` restoration instant. Deps: T-HP-03. AC: REQ-HOME-HP01 title fades out before width completes.
- [x] **T-HP-05** (~15 lines) — Badge/CTA slide-up: default `opacity:0; transform: translateY(10px)`. `--active` → `opacity:1; transform: translateY(0)` with `0.3s ease-out 0.1s`. Deps: T-HP-03. AC: REQ-HOME-HP01 badge/CTA stagger animation.
- [x] **T-HP-06** (~20 lines) — Car positioning: `.wecar-hero__card__image` absolute `bottom:0; right:0`. Left panel `right:-10%` override. Size transitions per step: 530×270 default, 490×250 left-active. Container `overflow:hidden` enforced. Deps: T-HP-03. AC: REQ-HOME-HP02 bottom-right anchoring + resize.
- [x] **T-HP-07** (~20 lines) — Radial-gradient textures: replace `::before` SVG with per-panel gradients per REQ-HOME-HP03. Left: `rgba(153,73,255,0.1)` → transparent + `rgba(153,73,255,0.04)` overlay. Right: two gradients `rgba(245,237,255,0.2)` and `rgba(249,253,254,0.2)`. `background-size 423×200`, `background-position: bottom right`. Deps: none. AC: REQ-HOME-HP03 per-panel textures + no SVG.
- [x] **T-HP-08** (~10 lines) — Residual cleanup: force `opacity:0 !important; visibility:hidden !important; transition: none !important;` on badge/CTA/image of `--collapsed` to guarantee zero overlap with collapsed label fade-in. Deps: T-HP-03, T-HP-05, T-HP-06. AC: design risk mitigation — no visible badge/CTA behind label.

## Phase 3: Mobile & Deploy

- [x] **T-HP-09** (~10 lines, CSS) — Mobile <768px: verify `::after display:none`, columns `flex: 1 1 100%`, JS no-op guard active. Deps: T-HP-02, T-HP-03. AC: REQ-HOME-002 stacked mobile layout.
- [x] **T-HP-10** (~0 code) — Deploy: SCP both files → `test.wecar.com.ar`. `wp cache flush`, Elementor `Plugin::instance()->files_manager->clear_cache()`, curl verify 200 on CSS/JS URLs, DevTools visual check for widths 598/976/220, label centering, textures, car position. Deps: T-HP-01 through T-HP-09. AC: all requirements met in test environment.

---

### Implementation Order

T-HP-03 (panel widths) is the CSS foundation — all other CSS tasks depend on it. T-HP-01 (JS) is independent. T-HP-02/04/05/06/07/08 are parallel after T-HP-03. T-HP-09 (mobile) verifies all CSS works at breakpoint. T-HP-10 (deploy) is last. All tasks deliver as a single PR (single-pr strategy, ~280–340 lines estimated, well within 400-line budget).

| Phase | Tasks | Focus |
|-------|-------|-------|
| Phase 1 | 1 | JS — mobile guard |
| Phase 2 | 7 | CSS — widths, labels, cross-fade, badge/CTA, car, textures, cleanup |
| Phase 3 | 2 | Mobile verify + deploy |
| Total | 10 | |
