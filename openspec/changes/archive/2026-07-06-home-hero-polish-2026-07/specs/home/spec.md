# Delta for Home — Hero Polish 2026-07

> **Date**: 2026-07-06
> **Change**: `home-hero-polish-2026-07`
> **Modifies**: REQ-HOME-002 from `home-figma-exact-2026-07/spec.md`
> **Source of truth**: Figma node `137:3003` (hero component set, 3 steps)

---

## MODIFIED Requirements

### REQ-HOME-002: Hero Dual Cards

(Previously: two static equal-width cards rendered side-by-side with no accordion behavior, no collapsed state, no cross-fade transitions.)

The home page MUST display a dual-card hero section as a CSS-driven horizontal accordion with three states:

- **step-1 (initial)**: Both panels 50/50 — `width: 598px` each, `borderRadius: 40px`, `gap: 20px`. Texts "Comprá"/"Vendé" `opacity: 0`.
- **step-2 (Comprá expanded)**: Left panel `width: 976px, x: 0`. Right panel collapsed `width: 220px, x: 996`. Collapsed label "Vendé" `opacity: 1`.
- **step-3 (Vendé expanded)**: Right panel `width: 976px, x: 240`. Left panel collapsed `width: 220px, x: 0`. Collapsed label "Comprá" `opacity: 1`.

Both panels share `hero-car.png` (D-2). Container `overflow: hidden`.

#### Scenario: Initial State 50/50

- GIVEN a visitor loads the home page
- WHEN the hero section renders
- THEN both panels are visible at equal width (598px each)
- AND collapsed labels "Comprá"/"Vendé" are hidden (`opacity: 0`)

#### Scenario: Comprá Panel Expands on Click

- GIVEN the hero is in step-1 state
- WHEN the user clicks the "Comprá" (left) panel
- THEN the left panel expands to 976px
- AND the right panel collapses to 220px
- AND the collapsed label "Vendé" fades in (`opacity: 1`)

#### Scenario: Vendé Panel Expands on Click

- GIVEN the hero is in step-1 state
- WHEN the user clicks the "Vendé" (right) panel
- THEN the right panel expands to 976px
- AND the left panel collapses to 220px
- AND the collapsed label "Comprá" fades in (`opacity: 1`)

#### Scenario: Collapsed Label Centering

- GIVEN a panel is in collapsed state (220px)
- WHEN the collapsed label renders
- THEN the label is vertically and horizontally centered within the 220px panel
- AND uses Syne Bold 700, 38px/44px

#### Scenario: Mobile Hero Stack

- GIVEN a visitor on viewport < 768px
- WHEN the home page loads
- THEN hero cards stack vertically (accordion disabled)
- AND all content remains legible

---

## ADDED Requirements

### REQ-HOME-HP01: Cross-Fade Text Transitions

The hero MUST animate text content with a cross-fade during panel expand/collapse.

- **On collapse**: title and subtitle fade out (`opacity: 1 → 0`) BEFORE the panel width transition completes.
- **On expand**: badges and CTA slide up (`translateY: 10px → 0`) + fade in (`opacity: 0 → 1`) with `~0.3s ease-out` timing, staggered after the title/subtitle fade in.
- Collapsed label fade-in is delayed until main content has fully faded out.

#### Scenario: Title Fades Out Before Collapse

- GIVEN the hero is in step-1 state
- WHEN the user clicks a panel to expand the other
- THEN the collapsing panel's title and subtitle fade to `opacity: 0`
- AND the width transition starts AFTER or DURING the text fade-out (not after)

#### Scenario: Expand Animates Badge and CTA

- GIVEN a panel is in collapsed state
- WHEN the user clicks to expand it
- THEN the badge and CTA slide up from `translateY: 10px` to `0` and fade from `opacity: 0` to `1`
- AND the animation completes in approximately `0.3s ease-out`

#### Scenario: Collapsed Label Delayed Fade-In

- GIVEN a panel is collapsing
- WHEN the main content (title, subtitle) reaches `opacity: 0`
- THEN the collapsed label fades in with a short delay

---

### REQ-HOME-HP02: Car Image Positioning

Each hero panel MUST position `hero-car.png` anchored to the bottom-right with overflow hidden on the container.

- **step-1 (50/50)**: car at `x: 618, y: 105`, size `530×270`.
- **step-2 (Comprá expanded)**: car at `x: 466, y: 105`, size `490×250`.
- **step-3 (Vendé expanded)**: car at `x: 520, y: 95`, size `530×270`.
- Left panel uses `right: -10%` anchoring. Container has `overflow: hidden`.

#### Scenario: Car Anchored Bottom-Right

- GIVEN a hero panel renders
- WHEN the car image is visible
- THEN it is positioned at the bottom-right of the panel
- AND does not overlap the CTA or body text

#### Scenario: Car Resizes on Expand/Collapse

- GIVEN the hero transitions between steps
- WHEN a panel expands or collapses
- THEN the car image size transitions smoothly to match the Figma dimensions for that step

#### Scenario: Overflow Hidden Prevents Spillover

- GIVEN the car is positioned with negative right offset
- WHEN the panel renders
- THEN the car does not overflow outside the panel bounds (container `overflow: hidden`)

---

### REQ-HOME-HP03: Radial-Gradient Textures

Each hero panel MUST display CSS radial-gradient background textures (replacing any SVG texture). No SVGs.

- **Option-1 ("Comprá")**:
  - Texture 1: `radial-gradient(circle at 99% 100%, rgba(153,73,255,0.1) 0%, rgba(14,181,209,0) 100%)`, 423×200.
  - Texture 2: subtle `rgba(153,73,255,0.04)` overlay.
- **Option-2 ("Vendé")**:
  - Texture 1: `radial-gradient(circle at 99% 100%, rgba(245,237,255,0.2) 0%, rgba(14,181,209,0) 100%)`, 423×200.
  - Texture 2: `radial-gradient(circle at 99% 100%, rgba(249,253,254,0.2) 0%, rgba(14,181,209,0) 100%)`, 423×200.

#### Scenario: Comprá Panel Textures

- GIVEN the "Comprá" panel renders
- WHEN the background is inspected
- THEN a radial-gradient from purple (10% opacity) to transparent is visible at bottom-right
- AND a second subtle purple overlay (4% opacity) is present

#### Scenario: Vendé Panel Textures

- GIVEN the "Vendé" panel renders
- WHEN the background is inspected
- THEN two radial-gradient textures from light lavender/white (20% opacity) to transparent are visible

#### Scenario: No SVG Textures

- GIVEN either hero panel renders
- WHEN the background is inspected
- THEN no SVG-based textures are used (CSS radial-gradient only)

---

### REQ-HOME-HP04: Panel Content Positions

Content within each expanded hero panel MUST follow Figma positioning:

- **Title**: `x: 40, y: 120` (step-1: `y: 92`), `width: 489×108`.
- **Subtitle**: `x: 40, y: 248`, `width: 412×60` (option-1) / `412×90` (option-2).
- **Badge**: `x: 40`, repositioned from `y: -64` (step-1) to `y: 40` (expanded).
- **Collapsed label**: centered in 220px panel, `y: 206`, Syne Bold 700 38/44.

#### Scenario: Expanded Content Alignment

- GIVEN a panel is in expanded state (976px)
- WHEN the content renders
- THEN title starts at `x: 40, y: 120`
- AND subtitle starts at `x: 40, y: 248`

#### Scenario: Badge Repositions on Expand

- GIVEN the hero transitions from step-1 to step-2 or step-3
- WHEN the badge is visible
- THEN it moves from `y: -64` (above card) to `y: 40` (inside expanded panel)

---

## Traceability

| REQ | Type | Figma Node | Proposal Decisions |
|-----|------|-----------|-------------------|
| REQ-HOME-002 (M) | Accordion states | 137:3003 | D-1, D-2 |
| REQ-HOME-HP01 (A) | Cross-fade | 137:3003 | — |
| REQ-HOME-HP02 (A) | Car position | 137:3003 | D-2 |
| REQ-HOME-HP03 (A) | Textures | 137:3003 | D-3 |
| REQ-HOME-HP04 (A) | Content positions | 137:3003 | — |
