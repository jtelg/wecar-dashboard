# Home Figma Exact 2026-07 — Delta Spec

> **Date**: 2026-07-02
> **Change**: `home-figma-exact-2026-07`
> **Base spec**: `openspec/changes/home-correct-2026-07/spec.md` (MODIFIED 8, ADDED 9+1 NFR, REMOVED 0)
> **Source of truth**: Figma file `Lvc9M2CbiGm1WekRsiNqEj` (node 2:153 "home")
> **Status**: Ready for `sdd-design`

---

## How to read this spec

This is a **delta spec** on top of `home-correct-2026-07/spec.md`. It documents only changes:

- **MODIFIED REQs** (8): REQ-HOME-001, 002, 003, 005, 006, 007, 008, 009
- **ADDED REQs** (14): REQ-HOME-F01 through REQ-HOME-F14
- **REMOVED REQs** (2): REQ-HOME-004 (scroll animation), REQ-HOME-018 (scroll-bound disclosure)
- **Unchanged REQs**: REQ-HOME-010 through REQ-HOME-017, NFR-HOME-001 through NFR-HOME-009 (carry forward from base)

---

## MODIFIED Requirements

### REQ-HOME-001: Header Navigation (MODIFIED)

> **Original** (from home-redesign/spec.md): "The home page MUST display a fixed header with the WeCar logo on the left, a 5-item navigation (Inicio, Comprar, Vender, Nosotros, Blog), and a CTA button 'Contactanos' on the right."

The home page MUST display a sticky header with padding `20px 148px`, white background, and three horizontal sections: logo left, nav center, CTA right.

- **Logo**: `wecar-isotype` SVG, ~170×30px, left-aligned.
- **Nav**: 5 text links — "Inicio", "Comprar", "Vender", "Nosotros", "Blog" — rendered as a flex row with `gap: 40px`, font Syne Bold 14/18, fill `#111111`.
- **CTA pill**: "Contactanos" button, background `linear-gradient(146deg, #F5EDFF 0%, #FFFFFF 93%)`, borderRadius `12px`, text fill `#9949FF`, font Syne Bold 14/18, height `32px`, padding `11px 14px`. NOT a solid purple button.
- **Sticky**: `position: sticky; top: 0` on desktop and tablet.

(Previously: padding was 10/20, nav was a single text-editor with `<a>` tags, CTA was solid purple `#7B5CE8` with white text and 50px radius.)

#### Scenario: Header Layout and Padding

- GIVEN a visitor on the home page at desktop viewport (> 1024px)
- WHEN the header renders
- THEN the horizontal padding is `20px 148px`
- AND the logo, nav, and CTA are aligned in a single horizontal row

#### Scenario: Lavender Pill CTA

- GIVEN the header renders
- WHEN the user inspects the "Contactanos" button
- THEN the button background is a lavender-to-white gradient (`#F5EDFF → #FFFFFF`)
- AND the text color is purple (`#9949FF`)
- AND the border-radius is `12px`

#### Scenario: Nav Links Spacing

- GIVEN the header renders
- WHEN the 5 nav links are visible
- THEN each link is separated by `40px` gap
- AND all links use Syne Bold 14/18 font

---

### REQ-HOME-002: Hero Dual Cards (MODIFIED)

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

### REQ-HOME-003: 3-Step Process Section (MODIFIED)

> **Original** (from home-correct-2026-07/spec.md): "The home page MUST display a 3-step process section with updated copy matching the approved mockups: section title, 3 steps, final CTA."

The home page MUST display a static 3-step process section with left-aligned title, rounded-square numbered badges, tinted cards, and a gradient CTA. No scroll-bound animation.

- **Section title**: "Vendé tu auto al mejor precio. Usalo hasta el último día" — Syne Bold 38/44, fill `#111111`, **left-aligned**.
- **Numbered badges**: 46×46px rounded squares (borderRadius `16px`), FILLED. Step 1: `#9949FF` (purple), Step 2: `#0E6FD1` (blue), Step 3: `#0EB5D1` (cyan). Number text: Exo 2 Regular 28/38, white.
- **Connecting line**: 792×2px rectangle, gradient `linear-gradient(90deg, #C598FF 0%, #CAE0F5 50%, #CAEFF5 100%)`, STATIC (always fully visible, no progressive fill).
- **Card 1** ("Cotizás online"): bg `#FCFAFF`, stroke `#F5EDFF`, borderRadius `24px`, padding `28px`.
- **Card 2** ("Peritamos en sucursal"): bg `#F9FBFE`, stroke `#E7F1FA`, borderRadius `24px`, padding `28px`.
- **Card 3** ("Lo publicamos. Vos lo seguís manejando."): bg `#F9FDFE`, stroke `#E7F8FA`, borderRadius `24px`, padding `28px`.
- **Final CTA**: "Vendé tu usado sin vueltas" — gradient `#9949FF → #0E6FD1`, borderRadius `16px`, height 44px. Links to `/vende-tu-auto/` (fallback `/cotiza/`).

(Previously: badges were circles (borderRadius 50%) with 3px border, not filled; title was centered; line had progressive fill animation; CTA was solid purple pill.)

#### Scenario: Left-Aligned Section Title

- GIVEN a visitor on the home page
- WHEN the 3-step section renders
- THEN the section title is left-aligned (not centered)

#### Scenario: Rounded-Square Numbered Badges

- GIVEN the 3-step section renders
- WHEN the numbered badges are visible
- THEN each badge is a 46×46px rounded square (borderRadius 16px, not a circle)
- AND badge 1 is filled purple, badge 2 is filled blue, badge 3 is filled cyan
- AND the number text is white Exo 2 Regular 28/38

#### Scenario: Static Connecting Line

- GIVEN the 3-step section renders
- WHEN the connecting line is visible
- THEN the line is fully visible from the start (no progressive fill animation)
- AND the line uses a horizontal gradient from purple through blue to cyan

#### Scenario: Card Tints

- GIVEN the 3-step section renders
- WHEN each card is visible
- THEN card 1 has a lavender tint (`#FCFAFF`), card 2 has a blue tint (`#F9FBFE`), card 3 has a cyan tint (`#F9FDFE`)

#### Scenario: Final CTA Gradient

- GIVEN the user scrolls to the end of the 3-step section
- WHEN the CTA button is visible
- THEN the button uses a purple→blue gradient (not solid purple)
- AND clicking it navigates to `/vende-tu-auto/`

---

### REQ-HOME-005: Vehicle Carousel (MODIFIED)

> **Original** (from home-redesign/spec.md): "The home page MUST display a vehicle carousel populated with real vehicles from the `vehica_car` post type."

The carousel section MUST have a vertical purple→blue gradient background (`linear-gradient(180deg, #9949FF 0%, #0E6FD1 100%)`) with two corner radial textures. The section header shows "Encontrá tu próximo auto" (Syne Bold 38/44, white, left-aligned) with a "Ver todos →" link button to `/autos/`. A "Contactar con un asesor" CTA pill (gradient `#F5EDFF → #FFFFFF`, purple text, borderRadius `16px`) is centered below the carousel, linking to `/contactanos/`.

(Previously: gradient was diagonal `135deg` with colors `#5E3BE0 → #2563EB`; no corner textures; CTA was solid purple.)

#### Scenario: Vertical Gradient Background

- GIVEN a visitor reaches the carousel section
- WHEN the section background renders
- THEN it shows a vertical gradient from purple (`#9949FF`) at top to blue (`#0E6FD1`) at bottom (180deg)

#### Scenario: Corner Radial Textures

- GIVEN the carousel section renders
- WHEN the user inspects the section background
- THEN two radial-gradient textures are visible as corner decorations
- AND the textures are low-opacity white-to-transparent

#### Scenario: Bottom CTA Gradient

- GIVEN the carousel section renders
- WHEN the "Contactar con un asesor" button is visible
- THEN the button uses a lavender-to-white gradient (not solid purple)
- AND clicking it navigates to `/contactanos/`

---

### REQ-HOME-006: Vehicle Card (MODIFIED)

> **Original** (from home-redesign/spec.md): "Each vehicle card in the carousel MUST display: vehicle image, title (make + model), short description, price, and tags (year, km, fuel type)."

Each vehicle card in the carousel MUST display 3 chips with SVG icons and colored backgrounds in this order: km → year → transmission. The card uses a gradient border and borderRadius `24px`.

- **Chip 1 (km)**: fill `#E7F1FA` (light blue), icon `ic_gauge` 12×12, text dark blue `#0F4C89`, borderRadius `10px`.
- **Chip 2 (year)**: fill `#E7F8FA` (light cyan), icon `ic_calendar` 12×12, text dark cyan `#0F7789`, borderRadius `10px`.
- **Chip 3 (transmission)**: fill `#F5EDFF` (light purple), icon `ic_transmission` 12×12, text dark purple `#7B3DCB`, borderRadius `10px`.
- **Card border**: `linear-gradient(180deg, #F9F9F9 0%, #CBCBCB 100%)` 1px, borderRadius `24px`.
- **KM data**: shows real km value when available; falls back to "Consultar km" when no km meta field exists.

(Previously: chips were text-only with no icons, no colored backgrounds; card border was solid `--wecar-border`; chip order was year/KM/transmission.)

#### Scenario: Chip Icons and Colors

- GIVEN a vehicle card in the carousel
- WHEN the card renders
- THEN 3 chips appear in order: km → year → transmission
- AND each chip has an SVG icon (gauge, calendar, transmission) and a distinct colored background

#### Scenario: Gradient Card Border

- GIVEN a vehicle card renders
- WHEN the card border is visible
- THEN the border uses a vertical gradient from light gray to medium gray (not solid)

#### Scenario: KM Fallback

- GIVEN a vehicle has no km meta field
- WHEN the card renders
- THEN the km chip displays "Consultar km" as placeholder text

---

### REQ-HOME-007: Elegí Wecar Section (MODIFIED)

> **Original** (from home-correct-2026-07/spec.md): "The home page MUST display an 'Elegí Wecar' section with 3 concrete-benefit cards on a white→light-lavender gradient background."

The home page MUST display an "Elegí Wecar" section with 3 sentence-card components on a radial purple background (`radial-gradient(circle at 50% 101%, rgba(153,73,255,1) 0%, rgba(255,255,255,0) 100%)`). Section title is left-aligned.

- **Card 1** (purple gradient `#9949FF → #6634A5`): icon `ic_users-round` (42×42, white) + "Nuestro equipo de expertos te asesora" (Syne Bold 18/22, white). Stroke white 2px, borderRadius `16px`, padding `28px`.
- **Card 2** (blue gradient `#0E6FD1 → #0F5AA7`): icon `ic_search` (42×42, white) + "Peritajes profesionales para asegurar su calidad" (Syne Bold 18/22, white).
- **Card 3** (cyan gradient `#0EB5D1 → #0F91A7`): icon `ic_handshake` (42×42, white) + "Múltiples posibilidades de financiación" (Syne Bold 18/22, white).

(Previously: cards used abstract titles "Confianza"/"Transparencia"/"Facilidad" on white backgrounds with colored icons; title was centered; section bg was a simple white→lavender gradient.)

#### Scenario: Solid Gradient Cards

- GIVEN a visitor on the home page
- WHEN the "Elegí Wecar" section renders
- THEN 3 cards appear with solid gradient backgrounds (purple, blue, cyan)
- AND each card has a white SVG icon and white sentence title

#### Scenario: Left-Aligned Section Title

- GIVEN the "Elegí Wecar" section renders
- WHEN the section title is visible
- THEN the title is left-aligned (not centered)

#### Scenario: Radial Purple Background

- GIVEN the "Elegí Wecar" section renders
- WHEN the section background is visible
- THEN it shows a radial purple gradient fading to transparent

---

### REQ-HOME-008: Partner Logos Section (MODIFIED)

> **Original** (from home-correct-2026-07/spec.md): "The home page MUST display a 'Respaldado por grandes marcas' section with a lavender gradient background and 3 partner logos centered horizontally."

The home page MUST display a "Respaldado por grandes marcas" section with 3 real SVG logos centered horizontally, no captions, and left-aligned title. Gap between logos: `60px`.

- **Logos**: `lg-multicars.svg` (244×60), `lg-leparc-peugeot.svg` (268×60), `lg-leparc-citroen.svg` (220×60).
- **Layout**: flex row, `justifyContent: center`, gap `60px`.
- **Title**: left-aligned.

(Previously: logos were placeholders with dashed borders and captions; title was centered; gap was 3rem.)

#### Scenario: Real SVG Logos

- GIVEN the partners section renders
- WHEN the logo images load
- THEN 3 real SVG logos are displayed (Multicars, Le Parc Peugeot, Le Parc Citroën)
- AND no captions are shown below the logos

#### Scenario: Left-Aligned Title

- GIVEN the partners section renders
- WHEN the section title is visible
- THEN the title "Respaldado por grandes marcas" is left-aligned

#### Scenario: Logo Spacing

- GIVEN the partners section renders
- WHEN the 3 logos are visible
- THEN the gap between each logo is `60px`

---

### REQ-HOME-009: Footer (MODIFIED)

> **Original** (from home-correct-2026-07/spec.md): "The home page MUST display a footer with updated phone number and wavy pattern decoration."

The home page MUST display a footer with WHITE background, 4-quadrant layout, and corner radial textures. Layout:

- **Top-left**: Logo `wecar-isotype` SVG (316×56px).
- **Top-right**: Description text "Una plataforma moderna y segura para la compra y venta de vehículos nuevos y usados.\nFormamos parte de Grupo Le Parc." — Exo 2 Regular 14/22, fill `#464646`, right-aligned.
- **Bottom-left**: Copyright "2026 Custer. All rights reserved." — Inter 14/18, fill `#464646`, left-aligned.
- **Bottom-right**: Phone "+54 9 3534 41-3243" — Exo 2 Bold 18/22, fill `#9949FF`, right-aligned, clickable `tel:` link.
- **Corner textures**: two radial-gradient decorations (top-left and top-right corners).
- **Background**: `#FFFFFF` (white, NOT dark navy).

(Previously: background was dark navy `#0F172A`; layout was 2-column 50/50; text was white/light; phone was purple-on-dark.)

#### Scenario: White Background

- GIVEN a visitor on the home page
- WHEN the footer renders
- THEN the background is white (`#FFFFFF`)
- AND all text is dark (`#111111` or `#464646`)

#### Scenario: 4-Quadrant Layout

- GIVEN the footer renders
- WHEN the layout is visible
- THEN the logo is in the top-left, description in the top-right, copyright in the bottom-left, and phone in the bottom-right

#### Scenario: Corner Radial Textures

- GIVEN the footer renders
- WHEN the user inspects the background
- THEN two radial-gradient textures are visible in the top-left and top-right corners

#### Scenario: Phone Clickable

- GIVEN the footer renders
- WHEN the user clicks the phone number
- THEN it triggers a `tel:` call to `+54 9 3534 41-3243`

---

## ADDED Requirements

### REQ-HOME-F01: Hero Card Badge

Each hero card MUST display a positioned badge above the card. The badge indicates the target audience.

- Left card badge: "Para compradores", fill `#0E6FD1` (blue), borderRadius `20px`, padding `8px 18px`, Syne Bold 16/20, white text, positioned above the card top edge.
- Right card badge: "Para vendedores", fill `#9949FF` (purple), same styling.

#### Scenario: Badge Positioning

- GIVEN the hero section renders
- WHEN the badges are visible
- THEN the "Para compradores" badge is positioned above the left card
- AND the "Para vendedores" badge is positioned above the right card

#### Scenario: Badge Styling

- GIVEN a hero badge renders
- WHEN the user inspects it
- THEN it has a solid color fill, rounded corners (20px), and white text in Syne Bold 16/20

---

### REQ-HOME-F02: Hero CTA Buttons

Each hero card MUST display a CTA button at the bottom of the card content area.

- Left card: primary gradient CTA (`linear-gradient(144deg, #9949FF 0%, #0E6FD1 100%)`), borderRadius `16px`, height `44px`, white text, "Quiero ver los modelos disponibles" → `/autos/`.
- Right card: secondary gradient CTA (`linear-gradient(146deg, #F5EDFF 0%, #FFFFFF 93%)`), borderRadius `16px`, height `44px`, purple text `#9949FF`, "Quiero vender mi auto" → `/vende-tu-auto/`.

#### Scenario: Primary CTA

- GIVEN the left hero card renders
- WHEN the CTA button is visible
- THEN clicking it navigates to `/autos/`

#### Scenario: Secondary CTA

- GIVEN the right hero card renders
- WHEN the CTA button is visible
- THEN clicking it navigates to `/vende-tu-auto/`

---

### REQ-HOME-F03: Hero Car Image

Each hero card MUST display a car image positioned on the right side of the card.

- Left card: 490×250px.
- Right card: 530×270px.
- Image asset: downloaded from Figma (`b15fe58e` imageRef).

#### Scenario: Car Images Visible

- GIVEN the hero section renders
- WHEN the cards display their content
- THEN each card has a car image on the right side
- AND the left card image is 490×250px
- AND the right card image is 530×270px

---

### REQ-HOME-F04: Numbered Badge Style

The 3-step section numbered badges MUST use rounded squares (not circles).

- Size: 46×46px.
- Border radius: `16px` (NOT 50%).
- Fill: solid color per step (purple, blue, cyan).
- Text: Exo 2 Regular 28/38, white.

#### Scenario: Rounded Square Shape

- GIVEN the 3-step section renders
- WHEN the numbered badges are visible
- THEN each badge has border-radius 16px (rounded square, not circle)
- AND the badges are filled with solid colors (not bordered)

---

### REQ-HOME-F05: Section Title Left-Alignment

ALL section titles in the home page MUST be left-aligned (not centered).

Affected sections: 3-step process, carousel header, Elegí Wecar, Respaldado por grandes marcas.

#### Scenario: Titles Are Left-Aligned

- GIVEN a visitor on the home page
- WHEN any section title renders
- THEN the title text is left-aligned within its container

---

### REQ-HOME-F06: Footer Corner Radial Textures

The footer MUST display two radial-gradient corner decorations.

- Top-left: `radial-gradient(circle at 99% 100%, rgba(153,73,255,0.04) 0%, rgba(14,181,209,0) 100%)`, 466×220px.
- Top-right: `radial-gradient(circle at 99% 100%, rgba(153,73,255,0.1) 0%, rgba(14,181,209,0) 100%)`, 466×220px.

#### Scenario: Corner Textures Visible

- GIVEN the footer renders
- WHEN the user inspects the background
- THEN two radial-gradient textures are visible in the top-left and top-right corners
- AND the textures are subtle (low opacity)

---

### REQ-HOME-F07: Footer 4-Quadrant Layout

The footer MUST use a 4-quadrant layout (not 2-column).

- Top-left quadrant: logo.
- Top-right quadrant: description text (right-aligned).
- Bottom-left quadrant: copyright (left-aligned).
- Bottom-right quadrant: phone number (right-aligned).

#### Scenario: Quadrant Positions

- GIVEN the footer renders
- WHEN the layout is inspected
- THEN the logo occupies the top-left, description the top-right, copyright the bottom-left, and phone the bottom-right

---

### REQ-HOME-F08: New Asset — Hero Car Image

A car image asset MUST exist at `wp-content/themes/vehica-child/assets/images/hero-car.png` (or `.webp`). Downloaded from Figma node `b15fe58e`.

#### Scenario: Asset File Exists

- GIVEN the child theme assets are deployed
- WHEN the file system is checked
- THEN `wp-content/themes/vehica-child/assets/images/hero-car.png` exists

#### Scenario: No 404 on Asset URL

- GIVEN the home page loads
- WHEN the hero card images are requested
- THEN the HTTP status for `hero-car.png` is 200

---

### REQ-HOME-F09: New Assets — Carousel Chip Icons

Three SVG icon files MUST exist for the vehicle carousel chips:

- `wp-content/themes/vehica-child/assets/images/ic-gauge.svg` (12×12, from Figma node `17:615`).
- `wp-content/themes/vehica-child/assets/images/ic-calendar.svg` (12×12, from Figma node `17:271`).
- `wp-content/themes/vehica-child/assets/images/ic-transmission.svg` (12×12, from Figma node `17:769`).

#### Scenario: Chip Icon Files Exist

- GIVEN the child theme assets are deployed
- WHEN the file system is checked
- THEN all 3 chip icon SVGs exist in `assets/images/`

#### Scenario: Icons Render in Chips

- GIVEN a vehicle card renders
- WHEN the chips are visible
- THEN each chip has its corresponding SVG icon (gauge for km, calendar for year, transmission for transmission)

---

### REQ-HOME-F10: New Assets — Partner SVG Logos

Three real partner logo SVG files MUST exist:

- `wp-content/themes/vehica-child/assets/images/partners/lg-multicars.svg` (244×60).
- `wp-content/themes/vehica-child/assets/images/partners/lg-leparc-peugeot.svg` (268×60).
- `wp-content/themes/vehica-child/assets/images/partners/lg-leparc-citroen.svg` (220×60).

Downloaded from Figma. These are v1 files; user may replace with brand-approved versions later.

#### Scenario: Logo Files Exist

- GIVEN the child theme assets are deployed
- WHEN the file system is checked
- THEN all 3 partner logo SVGs exist in `assets/images/partners/`

#### Scenario: Logos Render Without Captions

- GIVEN the partners section renders
- WHEN the logos are visible
- THEN the 3 logos display as images without any text captions

---

### REQ-HOME-F11: New Assets — Feature Section Icons

Three SVG icon files MUST exist for the Elegí Wecar cards:

- `wp-content/themes/vehica-child/assets/images/ic-users-round.svg` (42×42, from Figma node `17:263`).
- `wp-content/themes/vehica-child/assets/images/ic-search.svg` (42×42, from Figma node `17:335`).
- `wp-content/themes/vehica-child/assets/images/ic-handshake.svg` (42×42, from Figma node `17:623`).

#### Scenario: Feature Icon Files Exist

- GIVEN the child theme assets are deployed
- WHEN the file system is checked
- THEN all 3 feature icon SVGs exist in `assets/images/`

---

### REQ-HOME-F12: Font Loading — Syne + Exo 2

The child theme MUST load Syne (display font) and Exo 2 (body font) from Google Fonts via `wp_enqueue_style` in `functions.php`. These fonts apply to the home page only.

- `--wecar-font-display: 'Syne', sans-serif` — used for headings, nav, badges, CTAs.
- `--wecar-font-body: 'Exo 2', sans-serif` — used for body text, descriptions, chip text.

(Previously: display font was Montserrat, body font was Open Sans.)

#### Scenario: Fonts Load on Home Page

- GIVEN a visitor loads the home page
- WHEN the page renders
- THEN Syne and Exo 2 font files are requested from Google Fonts
- AND headings use Syne Bold, body text uses Exo 2 Regular

#### Scenario: Fonts Do Not Load on Other Pages

- GIVEN a visitor loads a non-home page (e.g., `/autos/`)
- WHEN the page renders
- THEN Syne and Exo 2 are NOT loaded (home-page-only enqueuing)

---

### REQ-HOME-F13: Brand Color Tokens

The child theme CSS tokens MUST define the Figma brand colors:

- `--wecar-figma-purple: #9949FF`
- `--wecar-figma-blue: #0E6FD1`
- `--wecar-figma-cyan: #0EB5D1`

These tokens are used across hero badges, step badges, carousel gradient, feature cards, and footer phone color.

#### Scenario: Tokens Available

- GIVEN the child theme CSS loads
- WHEN any component references the brand tokens
- THEN `--wecar-figma-purple`, `--wecar-figma-blue`, and `--wecar-figma-cyan` are defined and resolve to their hex values

---

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

## REMOVED Requirements

### REQ-HOME-004: Scroll-Triggered Step Animations (REMOVED)

> **Original** (from home-redesign/spec.md): "The 3-step process cards MUST animate in sequentially when scrolled into view using Intersection Observer."

(Reason: The Figma design shows a static 3-step section with no scroll-bound animation. The user's directive is "Match Figma exacto." The progressive fill animation is removed. A simple viewport-entry fade-in MAY be retained as a progressive enhancement but is NOT required by this spec.)

(Migration: The 4-frame scroll animation CSS and JS in `home-steps.css` and `home-animations.js` are removed. The step section now renders all content statically.)

---

### REQ-HOME-018: Scroll-Bound 3-Step Disclosure (REMOVED)

> **Original** (from home-correct-2026-07/spec.md): "The 3-step section MUST implement progressive scroll-driven disclosure with 4 distinct states."

(Reason: Same as REQ-HOME-004 removal. The Figma design is static. The IntersectionObserver thresholds and CSS frame classes are removed.)

(Migration: The JS in `home-animations.js` that set `wecar-steps--frame-{1,2,3,4}` classes is removed. The CSS rules for those classes in `home-steps.css` are removed.)

---

## Traceability

| REQ | Type | Section | Figma Reference | Decision |
|-----|------|---------|-----------------|----------|
| REQ-HOME-001 (M) | Header | #15:42 | D-6 (left-align implied) |
| REQ-HOME-002 (M) | Hero (accordion) | 137:3003 | D-1, D-2 |
| REQ-HOME-003 (M) | 3-step | #121:7293 | D-4, D-7 |
| REQ-HOME-005 (M) | Carousel | #25:237 | — |
| REQ-HOME-006 (M) | Vehicle card | #25:165 | — |
| REQ-HOME-007 (M) | Elegí Wecar | #34:187 | D-6 |
| REQ-HOME-008 (M) | Partners | #32:810 | D-8 |
| REQ-HOME-009 (M) | Footer | #37:183 | D-5, D-6 |
| REQ-HOME-F01 (A) | Hero badge | #137:3006 | D-2 |
| REQ-HOME-F02 (A) | Hero CTAs | #137:3006 | D-1 |
| REQ-HOME-F03 (A) | Hero car img | #137:3006 | D-3 |
| REQ-HOME-F04 (A) | Badge style | #121:7293 | D-7 |
| REQ-HOME-F05 (A) | Title align | all sections | D-6 |
| REQ-HOME-F06 (A) | Footer textures | #37:183 | — |
| REQ-HOME-F07 (A) | Footer layout | #37:183 | — |
| REQ-HOME-F08 (A) | Car image asset | #137:3006 | D-3 |
| REQ-HOME-F09 (A) | Chip icons asset | #25:165 | — |
| REQ-HOME-F10 (A) | Partner logos asset | #32:810 | D-8 |
| REQ-HOME-F11 (A) | Feature icons asset | #34:187 | — |
| REQ-HOME-F12 (A) | Font loading | global | — |
| REQ-HOME-F13 (A) | Color tokens | global | — |
| REQ-HOME-HP01 (A) | Cross-fade text | 137:3003 | — |
| REQ-HOME-HP02 (A) | Car positioning | 137:3003 | D-2 |
| REQ-HOME-HP03 (A) | Radial textures | 137:3003 | D-3 |
| REQ-HOME-HP04 (A) | Content positions | 137:3003 | — |
| REQ-HOME-004 (R) | Scroll animation | #121:7293 | D-4 |
| REQ-HOME-018 (R) | Scroll disclosure | #121:7293 | D-4 |
