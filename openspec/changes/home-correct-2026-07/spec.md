# Home Correction 2026-07 — Delta Spec

> **Date**: 2026-07-01
> **Change**: `home-correct-2026-07`
> **Base spec**: `openspec/changes/home-redesign/spec.md` (17 REQs + 8 NFRs, archived)
> **Status**: Ready for `sdd-design`
> **Source of truth**: `openspec/changes/home-correct-2026-07/exploration.md` + `proposal.md`

---

## How to read this spec

This is a **delta spec**, not a re-spec. The base spec is at `openspec/changes/home-redesign/spec.md` and remains the source for the 17 original REQs. This file documents only the changes:

- **MODIFIED REQs** (delta on top of the original): REQ-HOME-002, REQ-HOME-003, REQ-HOME-004, REQ-HOME-007, REQ-HOME-008, REQ-HOME-009.
- **ADDED REQs** (new requirements for this change): REQ-HOME-018 through REQ-HOME-026 (9 new REQs).
- **ADDED NFRs**: NFR-HOME-010 (scroll-bound animation performance budget).

For each MODIFIED REQ: the original 1–2 sentence definition is quoted, then the full new definition and scenarios follow.
For each ADDED REQ: full Given/When/Then scenarios are provided.
For the ADDED NFR: full performance criteria are specified.

---

## MODIFIED Requirements

### REQ-HOME-002: Hero Dual Cards (MODIFIED)

> **Original** (from home-redesign/spec.md): "The home page MUST display a dual-card hero section with two equal-width cards: 'Comprar' (buy) and 'Vender' (sell), each with an icon, title, description, and CTA link."

The home page MUST display a dual-card hero section with two equal-width cards. The cards are teaser blocks, NOT navigation entrypoints. Each card contains a title and body text only — NO icons, NO CTA buttons.

- **Left card**: Title "Encontrá tu próximo auto". Body: "La oferta mas grande de vehículos de Villa María y Villa Nueva". Background: white → light-lavender vertical gradient.
- **Right card**: Title "Vendé tu auto sin dejar de manejarlo". Body: "Simplificamos tu venta particular, enviamos los datos, lo cotizamos, publicamos y vendemos por vos." Background: light-blue → medium-blue vertical gradient.
- Both cards have a wavy/squiggle SVG pattern as low-opacity decoration.
- Border radius ~24–32px; vertical padding ~64–80px; gap between cards ~24–32px.

(Previously: cards had icons, CTA buttons, and different copy — "Comprar tu próximo auto" / "Vendé tu auto".)

#### Scenario: Desktop Hero Render

- GIVEN a visitor on the home page at desktop viewport (> 1024px)
- WHEN the hero section renders
- THEN two equal-width cards appear side by side with the specified gradient backgrounds, titles, and body text
- AND NO icons or buttons are present in either card

#### Scenario: Mobile Hero Stack

- GIVEN a visitor on a viewport narrower than 768px
- WHEN the home page loads
- THEN the hero cards stack vertically and the gradient/text remains legible

#### Scenario: No Navigation Elements

- GIVEN the user inspects the hero cards
- WHEN they look for navigation elements (links, buttons)
- THEN no links or buttons are present in the cards

---

### REQ-HOME-003: 3-Step Process Section (MODIFIED)

> **Original** (from home-redesign/spec.md): "The home page MUST display a '3 pasos' section with a title, subtitle, and 3 numbered steps: 'Cotizás online', 'Peritamos en sucursal', 'Lo publicamo s'."

The home page MUST display a 3-step process section with updated copy matching the approved mockups:

- **Section title**: "Vendé tu auto al mejor precio. Usalo hasta el último día"
- **Step 1**: "Cotizás online" — "Completás un formulario. En la brevedad un asesor se pondrá en contacto con vos para enviarte una estimación en base al mercado actual"
- **Step 2**: "Peritamos en sucursal" — "Nuestros técnicos verifican tu vehículo y comprueban que este apto para la venta al público para dar garantía al comprador"
- **Step 3**: "Lo publicamos. Vos lo seguís manejando." — "Tu auto aparece en nuestro marketplace verificado. Seguís usándolo con normalidad hasta que encontremos al comprador ideal."
- **Final CTA**: "Vendé tu usado sin vueltas" — pill button, lavender background, white text, centered. Links to `/vende-tu-auto/` (fallback `/cotiza/` if page does not exist).

(Previously: section title was "Vendé tu auto en 3 pasos"; step descriptions were shorter; no final CTA button.)

#### Scenario: 3-Step Copy Renders

- GIVEN a visitor on the home page
- WHEN the 3-step section renders
- THEN the section title reads "Vendé tu auto al mejor precio. Usalo hasta el último día"
- AND step 1 title is "Cotizás online" with the full description text
- AND step 2 title is "Peritamos en sucursal" with the full description text
- AND step 3 title is "Lo publicamos. Vos lo seguís manejando." with the full description text

#### Scenario: Final CTA Link Target

- GIVEN the user has scrolled to the end of the 3-step section
- WHEN the "Vendé tu usado sin vueltas" CTA button is visible
- THEN clicking the button navigates to `/vende-tu-auto/`
- AND if `/vende-tu-auto/` does not exist, the button links to `/cotiza/` as fallback

---

### REQ-HOME-004: Scroll Animations (REPLACED)

> **Original** (from home-redesign/spec.md): "The 3-step process cards MUST animate in sequentially when scrolled into view using Intersection Observer."

The 3-step process animation is REPLACED. The new behavior is progressive scroll-driven disclosure (4 frames) — see REQ-HOME-018 for the full scroll behavior specification and NFR-HOME-010 for the performance budget.

(Previously: staggered fade-in on viewport entry. Replaced with state-based scroll-bound animation using IntersectionObserver thresholds at 25/50/75/100% of section visibility.)

---

### REQ-HOME-007: Elegí Wecar Section (MODIFIED)

> **Original** (from home-redesign/spec.md): "The home page MUST display an 'Elegí Wecar' section with 3 feature cards highlighting trust reasons: 'Confianza', 'Transparencia', 'Facilidad'."

The home page MUST display an "Elegí Wecar" section with 3 concrete-benefit cards on a white→light-lavender gradient background:

- **Card 1** (purple `#5E3BE0` solid bg, white text): "Nuestro equipo de expertos te asesora" with a people/team icon.
- **Card 2** (blue `#2563EB` solid bg, white text): "Peritajes profesionales para asegurar su calidad" with a magnifier icon.
- **Card 3** (teal/cyan `#06B6D4` solid bg, white text): "Múltiples posibilidades de financiación" with a handshake icon.
- Section background: white → light-lavender `#F5F3FF` vertical gradient.

(Previously: cards used abstract value titles "Confianza", "Transparencia", "Facilidad" on a white background with icon-on-white style.)

#### Scenario: Elegí Wecar Section Renders

- GIVEN a visitor on the home page
- WHEN the "Elegí Wecar" section renders
- THEN the section title reads "Elegí Wecar"
- AND 3 cards appear with solid-color backgrounds (purple, blue, teal) and white text
- AND each card has an icon and a full-sentence title

#### Scenario: Section Gradient Background

- GIVEN the user reaches the "Elegí Wecar" section
- WHEN the section background renders
- THEN it shows a white-to-light-lavender (`#F5F3FF`) vertical gradient

---

### REQ-HOME-008: Partner Logos Section (MODIFIED)

> **Original** (from home-redesign/spec.md): "The home page MUST display a 'Marcas asociadas' section with a horizontal strip of partner brand logos (Multicars, Le Parc Peugeot, Le Parc Citroën)."

The home page MUST display a "Respaldado por grandes marcas" section with a lavender gradient background and 3 partner logos centered horizontally:

- Section title: "Respaldado por grandes marcas" (renamed from "Marcas Asociadas").
- Background: lavender gradient (e.g., `#C7B8FF` to `#A78BFA`) with wavy pattern decoration.
- 3 logos centered: Multicars, Le Parc Peugeot, Le Parc Citroën.
- **v1 ships with placeholder logos**; real PNG/SVG files are a v2 follow-up (REQ-HOME-026).

(Previously: title was "Marcas Asociadas"; background was plain white; placeholders had dashed borders.)

#### Scenario: Partners Section Renders

- GIVEN a visitor on the home page
- WHEN the partners section renders
- THEN the title reads "Respaldado por grandes marcas"
- AND 3 partner logos are displayed centered horizontally
- AND the section has a lavender gradient background with wavy pattern overlay

#### Scenario: Placeholder Logos (v1)

- GIVEN the real partner logo files have not yet been provided
- WHEN the section renders
- THEN placeholder logos with brand names are visible (not dashed-border empty boxes)

---

### REQ-HOME-009: Footer (MODIFIED)

> **Original** (from home-redesign/spec.md): "The home page MUST display a footer with Custer company info, phone number, and '2026 Custer. All rights reserved.' copyright."

The home page MUST display a footer with updated phone number and wavy pattern decoration:

- Phone number: `+54 9 3534 41-3243` (replaces placeholder `+54 9 11 1234-5678`).
- "2026 Custer. All rights reserved." retained (intentional brand attribution — Decision #1).
- Wavy/squiggle SVG pattern added as low-opacity background decoration.
- Footer background: white.

(Previously: phone was `+54 9 11 1234-5678` placeholder; no wavy pattern.)

#### Scenario: Phone Number Updated

- GIVEN a visitor on the home page
- WHEN the footer renders
- THEN the phone number displays `+54 9 3534 41-3243`
- AND the phone number is a clickable `tel:` link

#### Scenario: Copyright Retained

- GIVEN the footer renders
- WHEN the user reads the copyright line
- THEN it reads "2026 Custer. All rights reserved."

---

## ADDED Requirements

### REQ-HOME-018: Scroll-Bound 3-Step Disclosure (4 Frames)

The 3-step section MUST implement progressive scroll-driven disclosure with 4 distinct states. The animation strategy is state-based (IntersectionObserver thresholds), NOT pinned scroll.

The system MUST use IntersectionObserver with thresholds at 25%, 50%, 75%, and 100% of the section being visible. CSS transitions handle the visual fill-in of the gradient connecting line and card visibility.

- **State 1 (0% visible)**: Only the section title is shown.
- **State 2 (25% visible)**: The "1" circle becomes solid and the first step card ("Cotizás online") appears.
- **State 3 (50% visible)**: The "2" circle becomes solid, the second step card ("Peritamos en sucursal") appears, and the connecting line fills from circle 1 to circle 2.
- **State 4 (75%–100% visible)**: The "3" circle becomes solid, the third step card appears, the line fills from 2 to 3, and the "Vendé tu usado sin vueltas" CTA button becomes fully visible and clickable.

#### Scenario: Progressive Disclosure on Scroll Down

- GIVEN the user is at the top of the 3-step section
- WHEN the section is 0% visible
- THEN only the section title is shown

#### Scenario: Step 1 Appears at 25%

- GIVEN the user scrolls down into the 3-step section
- WHEN the section is 25% visible
- THEN the "1" circle becomes solid and the first step card ("Cotizás online") appears with a CSS transition

#### Scenario: Step 2 Appears at 50%

- GIVEN the user continues scrolling
- WHEN the section is 50% visible
- THEN the "2" circle becomes solid, the second step card appears, and the connecting line fills from circle 1 to circle 2

#### Scenario: All Steps and CTA at 75%–100%

- GIVEN the user continues scrolling
- WHEN the section is 75% to 100% visible
- THEN the "3" circle becomes solid, the third step card appears, the line fills from 2 to 3, and the CTA button is fully visible

#### Scenario: Reverse Scroll

- GIVEN the user scrolls back up past the thresholds in reverse
- WHEN they pass the thresholds backwards
- THEN the steps disappear in reverse order without jumping

#### Scenario: Graceful Degradation

- GIVEN JavaScript is disabled or IntersectionObserver is not supported
- WHEN the 3-step section renders
- THEN all 4 frames (title + 3 steps + CTA) are visible at once without animation

---

### REQ-HOME-019: Carousel Section Gradient Background

The "Encontrá tu próximo auto" carousel section MUST have a purple→blue diagonal gradient background with a low-opacity wavy/squiggle SVG pattern overlay.

- Gradient: purple `#5E3BE0` (top-left) to blue `#2563EB` or `#3B82F6` (bottom-right).
- Wavy pattern: same SVG asset as REQ-HOME-024, rendered at low opacity.

#### Scenario: Carousel Gradient Renders

- GIVEN a visitor on the home page
- WHEN they reach the "Encontrá tu próximo auto" carousel section
- THEN the section background is a purple→blue diagonal gradient
- AND a low-opacity wavy/squiggle SVG pattern is visible as decoration

---

### REQ-HOME-020: Carousel "Ver todos" Link

The carousel section MUST display a "Ver todos →" link in the top-right area, linking to `/autos/`.

#### Scenario: Ver Todos Link Visible

- GIVEN the carousel section renders
- WHEN the user looks at the top-right of the section
- THEN a "Ver todos →" link is visible in white text
- AND clicking the link navigates to `/autos/`

---

### REQ-HOME-021: Carousel Bottom CTA

The carousel section MUST display a "Contactar con un asesor" pill button centered below the carousel, linking to `/contactanos/`.

- Style: pill-shaped, white/lavender background, purple text.
- Link target: `/contactanos/` (Decision #2).

#### Scenario: Contact CTA Visible

- GIVEN the user has scrolled to the bottom of the carousel section
- WHEN the carousel finishes rendering
- THEN a "Contactar con un asesor" pill button is visible and centered
- AND clicking the button navigates to `/contactanos/`

---

### REQ-HOME-022: Elegí Wecar Section Gradient Background

The "Elegí Wecar" section MUST have a white-to-light-lavender (`#F5F3FF`) vertical gradient background.

#### Scenario: Elegí Gradient Renders

- GIVEN the user reaches the "Elegí Wecar" section
- WHEN the section background renders
- THEN it shows a white-to-light-lavender vertical gradient

---

### REQ-HOME-023: Partners Section Gradient Background

The "Respaldado por grandes marcas" section MUST have a lavender gradient background (e.g., `#C7B8FF` to `#A78BFA`) with a wavy pattern overlay.

#### Scenario: Partners Gradient Renders

- GIVEN the user reaches the "Respaldado por grandes marcas" section
- WHEN the section background renders
- THEN it shows a lavender gradient with a wavy pattern overlay

---

### REQ-HOME-024: Wavy/Squiggle SVG Pattern Asset

A new file `wp-content/themes/vehica-child/assets/images/wavy-pattern.svg` MUST exist. The pattern is reusable across multiple sections.

- **Sections using the pattern**: hero left card (low opacity), hero right card (low opacity white), carousel section, partners section, footer.
- The pattern MUST be implemented as a tiling SVG (not a base64 data URI in CSS) for cacheability and small payload.
- The SVG is generated during sdd-apply based on the visual reference in `new-design/home.png`.

#### Scenario: SVG File Exists

- GIVEN the child theme assets are deployed
- WHEN the file system is checked
- THEN `wp-content/themes/vehica-child/assets/images/wavy-pattern.svg` exists and is a valid SVG file

#### Scenario: Pattern Renders Across Sections

- GIVEN the home page loads
- WHEN the hero, carousel, partners, or footer sections render
- THEN the wavy pattern is visible as a low-opacity background decoration in each section

---

### REQ-HOME-025: Vehicle Card Tag Order

Each vehicle card in the carousel MUST show exactly 3 chips in this order: km, year, transmission. The fuel chip from the previous design is removed.

#### Scenario: 3 Chips in Correct Order

- GIVEN a vehicle card in the carousel
- WHEN the card renders
- THEN exactly 3 chips appear in the order: km → year → transmission
- AND no fuel chip is present

#### Scenario: Chip Content

- GIVEN a vehicle with 35,500 km, year 2021, automatic transmission
- WHEN the card renders
- THEN the chips display "35.500 km", "2021", "Automática" in that order

---

### REQ-HOME-026: Real Partner Logo Files (v2 Follow-up)

- **v1**: This change ships with placeholder logos in the partners section (current state from home-redesign).
- **v2 follow-up**: Replace placeholders with real PNG/SVG logo files for Multicars, Le Parc Peugeot, and Le Parc Citroën when the user provides them.
- Logo files SHOULD be placed in `wp-content/themes/vehica-child/assets/images/partners/`.

#### Scenario: v1 Ships with Placeholders

- GIVEN the user has not yet provided real logo files
- WHEN the partners section renders on v1
- THEN placeholder logos with brand names are visible

#### Scenario: v2 Replaces with Real Logos

- GIVEN the user provides real PNG/SVG logo files
- WHEN sdd-apply drops them into `assets/images/partners/` and updates the Elementor data
- THEN the partners section displays the real logo files

---

## Added NFRs

### NFR-HOME-010: Scroll-Bound Animation Performance Budget

The state-based scroll animation (REQ-HOME-018) MUST NOT cause jank on mobile or desktop.

**Performance criteria**:
- INP (Interaction to Next Paint) < 100ms per existing NFR-HOME-001 baseline.
- The animation MUST use CSS `transform` and `opacity` (not layout-affecting properties like `width`, `height`, `top`, `left`).
- The IntersectionObserver MUST use `requestAnimationFrame` for DOM updates.
- The script MUST NOT run on browsers without IntersectionObserver support (graceful degradation: show all frames at once — see REQ-HOME-018 scenario).

#### Scenario: Mobile 60fps

- GIVEN a user on a mobile device (e.g., iPhone 12, mid-range Android)
- WHEN they scroll through the 3-step section at normal speed
- THEN the animation runs at 60fps and no frame drops are visible (verified via Chrome DevTools performance recording)

#### Scenario: Desktop Performance

- GIVEN a user on a desktop browser
- WHEN they scroll through the 3-step section
- THEN INP remains < 100ms and the animation is smooth

---

## Traceability

| REQ | Section affected | Mockup reference | Decision # |
|-----|------------------|------------------|-----------|
| REQ-HOME-002 (M) | Hero | home.png rows 95–410 | — |
| REQ-HOME-003 (M) | 3-step copy | section-1(1).png | — |
| REQ-HOME-004 (M→R) | 3-step animation | section-1(1).png frames 1–4 | #6 |
| REQ-HOME-007 (M) | Elegí Wecar | home.png rows 960–1080 | — |
| REQ-HOME-008 (M) | Partners | home.png rows 1130–1240 | #8 |
| REQ-HOME-009 (M) | Footer | home.png rows 1280–1370 | phone # |
| REQ-HOME-018 (A) | 3-step scroll | section-1(1).png | #6 |
| REQ-HOME-019 (A) | Carousel bg | home.png rows 480–870 | — |
| REQ-HOME-020 (A) | Carousel link | home.png | — |
| REQ-HOME-021 (A) | Carousel CTA | home.png | #2 |
| REQ-HOME-022 (A) | Elegí bg | home.png | — |
| REQ-HOME-023 (A) | Partners bg | home.png | — |
| REQ-HOME-024 (A) | Wavy pattern | home.png (all sections) | #4 |
| REQ-HOME-025 (A) | Card tags | home.png | #7 |
| REQ-HOME-026 (A) | Real logos (v2) | home.png | #5 |
| NFR-HOME-010 (A) | Animation perf | section-1(1).png | #6 |
