# Home Correction 2026-07 — Exploration

> **Date**: 2026-07-01
> **Change**: `home-correct-2026-07`
> **Source of truth**: `new-design/home.png` + `new-design/section-1(1).png`
> **Status**: READ-ONLY exploration complete. Ready for `sdd-propose`.

---

## Source of Truth (Design Mockups)

- `new-design/home.png` (1440×~2860) — full home mockup, top to bottom: header, hero dual cards, "Vendé tu auto al mejor precio" section title, "Encontrá tu próximo auto" carousel section (purple→blue gradient), "Elegí Wecar" 3 cards (white→lavender gradient), "Respaldado por grandes marcas" partner logos (lavender→purple gradient), footer (white).
- `new-design/section-1(1).png` (4 stacked frames) — progressive scroll disclosure of the 3-step section. Frame 1: title only. Frame 2: step 1 card appears. Frame 3: steps 1+2 with connecting gradient line. Frame 4: all 3 steps visible + "Vendé tu usado sin vueltas" CTA.

> Note: the dashed black border around each frame in `section-1(1).png` is a **designer annotation** showing the section bounding box, NOT a real CSS rule. The actual design has no border.

---

## Desired Design — Section by Section

### 1. Header / Navigation
- **Logo**: small WeCar logo (car+cloud/wave icon) on the left, ~40–48px height, links to `/`.
- **Menu items** (centered or right-aligned, horizontal): `Inicio`, `Comprar`, `Vender`, `Nosotros`, `Blog`.
- **CTA button** (right): "Contactanos" — pill-shaped, lavender background (`--wecar-purple-light` ≈ `#7B5CE8` or a lighter shade), purple text, rounded full radius.
- **Behavior**: sticky on scroll (already implemented in `home-header.css`).
- **Reference**: `new-design/home.png` top strip.

### 2. Hero — Two Big Cards (no buttons, no icons)

A 2-column row of equal-width cards with a subtle wavy/squiggly background pattern, generous padding, and **NO inner CTA button** (intentional — the cards are teaser blocks, not navigation entrypoints).

- **Left card** ("Encontrá tu próximo auto"):
  - Background: white → very light lavender vertical gradient.
  - Title: **"Encontrá tu próximo auto"** (dark text, ~36–44px, bold, display font).
  - Body: "La oferta mas grande de vehículos de Villa María y Villa Nueva" (gray, ~16–18px).
  - Wavy/squiggle SVG pattern as background decoration (low opacity, top-right area).
- **Right card** ("Vendé tu auto sin dejar de manejarlo"):
  - Background: light blue → medium blue vertical gradient.
  - Title: **"Vendé tu auto sin dejar de manejarlo"** (white, ~36–44px, bold).
  - Body: "Simplificamos tu venta particular, enviamos los datos, lo cotizamos, publicamos y vendemos por vos." (white, ~16–18px, ~3 lines).
  - Same wavy pattern decoration (white at low opacity).
- **Border radius**: large, ~24–32px.
- **Spacing**: vertical padding ~64–80px inside each card, ~24–32px gap between them.
- **Reference**: `new-design/home.png` rows ~95–410.

### 3. "Vendé tu auto en 3 pasos" — Scroll-Driven Progressive Disclosure

A vertically stacked section with one big section title, three step cards that **reveal progressively as the user scrolls** through the section (NOT all-at-once on entry), and a final CTA that appears at the end.

- **Section title** (always visible, top of section): "Vendé tu auto al mejor precio. Usalo hasta el último día" — large, centered, dark text, ~40–48px, display font.
- **Numbered circles with gradient connecting line** (above the cards):
  - Three circles: `1` (lavender `#7B5CE8`), `2` (blue `#2563EB` or `#36BFFA`), `3` (cyan/teal `#06B6D4`).
  - Horizontal connecting line with a purple → blue → cyan gradient. As the user scrolls, the line "fills in" from left to right and the circles become solid/visible in sequence.
- **Three step cards** (reveal progressively):
  - **Step 1** (lavender accent): "Cotizás online" — "Completás un formulario. En la brevedad un asesor se pondrá en contacto con vos para enviarte una estimación en base al mercado actual".
  - **Step 2** (blue accent): "Peritamos en sucursal" — "Nuestros técnicos verifican tu vehículo y comprueban que este apto para la venta al público para dar garantía al comprador".
  - **Step 3** (cyan/teal accent): "Lo publicamos. Vos lo seguís manejando." — "Tu auto aparece en nuestro marketplace verificado. Seguís usándolo con normalidad hasta que encontremos al comprador ideal."
  - Card style: white card with a soft shadow, accent-colored title, dark body text.
- **Final CTA button** (appears only at the end, frame 4): **"Vendé tu usado sin vueltas"** — pill-shaped, lavender background, white text, centered below the cards.
- **Animation behavior**: 4 distinct states (matches the 4 frames in `section-1(1).png`):
  1. Title only.
  2. Step 1 card visible.
  3. Steps 1+2 visible, connecting line 1→2 filled.
  4. All 3 steps visible, full line filled, CTA button appears.
- **Reference**: `new-design/section-1(1).png` (frames 1–4).

### 4. "Encontrá tu próximo auto" — Vehicle Carousel

A full-width section with a **purple → blue diagonal gradient** background, wavy pattern decoration, title + "Ver todos →" link, horizontal scrolling carousel of vehicle cards, and a CTA button at the bottom.

- **Background**: diagonal gradient from purple `#5E3BE0` (top-left) to blue `#2563EB` or `#3B82F6` (bottom-right), with the same wavy/squiggle pattern at low opacity.
- **Title** (top-left): "Encontrá tu próximo auto" (white, ~32–40px, display bold).
- **"Ver todos →" link** (top-right): white text with arrow, links to `/autos/`.
- **Carousel** (horizontal scroll, ~3.5 cards visible on desktop, 1 on mobile):
  - Each card: white background, rounded ~16px, image on top (~60% of card height), padding 16px, then:
    - **Title** (18px bold, dark): "Volkswagen Golf" (make + model from post title).
    - **Variant** (14px regular, gray): "VII 1.4 TSI Comfortline DSG" (from taxonomy `vehica_19226`).
    - **Price** (18px bold, lavender `#7B5CE8`): "ARS 32.760.000".
    - **Tags row** (12px, gray, 3 chips with light blue background): km, year, transmission (e.g., "35.500 km", "2021", "Automática").
  - Currently shown in mockup: 4 visible cards + 1 partial (5 total). All "Volkswagen Golf" (this is mockup data — production will show mixed vehicles).
- **CTA button** (centered below carousel): **"Contactar con un asesor"** — pill-shaped, white/lavender background, purple text. Links to `/contactanos/` (assumed — needs confirmation).
- **Reference**: `new-design/home.png` rows ~480–870.

### 5. "Elegí Wecar" — 3 Concrete-Benefit Cards

A section with white-to-light-lavender vertical gradient background and 3 cards each with an icon, title, and one-line description.

- **Background**: white at top fading to very light lavender `#F5F3FF` or similar at bottom.
- **Title** (centered): "Elegí Wecar" (dark, ~32–40px, bold).
- **Three cards** (equal width, row):
  - **Card 1 — Purple** (`#5E3BE0` solid background, white text):
    - Icon: people/team icon (line icon, white).
    - Title: **"Nuestro equipo de expertos te asesora"**.
  - **Card 2 — Blue** (`#2563EB` solid background, white text):
    - Icon: search/magnifier icon (line icon, white).
    - Title: **"Peritajes profesionales para asegurar su calidad"**.
  - **Card 3 — Teal/cyan** (`#06B6D4` solid background, white text):
    - Icon: handshake icon (line icon, white).
    - Title: **"Múltiples posibilidades de financiación"**.
- **Card style**: rounded ~16px, padding ~32px, solid color background, ~60–80px min height, white text aligned left.
- **Reference**: `new-design/home.png` rows ~960–1080.

### 6. "Respaldado por grandes marcas" — Partner Logos

A section with a lavender gradient background and 3–4 partner logos centered horizontally.

- **Background**: lavender gradient (e.g., `#C7B8FF` to `#A78BFA` or similar) with wavy pattern decoration.
- **Title** (centered): **"Respaldado por grandes marcas"** (dark, ~28–36px, bold).
- **Logos** (centered row, even spacing, ~150–200px each):
  1. **Multicars** (with "GRUPO LE PARC" subtitle, in white).
  2. **Le Parc Peugeot** (with Peugeot lion shield logo).
  3. **Le Parc Citroën** (with Citroën chevron logo).
  - 3 logos in the mockup. The brief mentioned "+1" but only 3 are visible — likely a brief inconsistency. **OPEN QUESTION** (see below).
- **Reference**: `new-design/home.png` rows ~1130–1240.

### 7. Footer

A 2-column footer on a white background with the WeCar logo, description, and contact info.

- **Left column**:
  - WeCar logo (medium, ~40–48px height).
  - "2026 Custer. All rights reserved." (small, gray, ~12–14px).
- **Right column** (or center, with text-align right):
  - Description: "Una plataforma moderna y segura para la compra y venta de vehículos nuevos y usados. Formamos parte de Grupo Le Parc."
  - Phone: **+54 9 3534 41-3243** (lavender color, clickable tel: link).
- **Wavy/squiggle background pattern** (low opacity, all sections).
- **Reference**: `new-design/home.png` rows ~1280–1370.

---

## Current State — Test (test.wecar.com.ar)

- **Last known state** (from `home-redesign/verify-report.md` and `apply-progress.md`, dated 2026-06-30):
  - The shipped `home-redesign` design IS currently live on test (7 sections: header, hero dual with icons+CTAs, 3 pasos with fade-in, carousel, "Elegí WeCar" with abstract values, "Marcas Asociadas" with placeholder dashed borders, footer with `+54 9 11 1234-5678` placeholder phone).
  - On 2026-07-01, a recovery was performed on PRODUCTION after a partial restore. Production is now back to the legacy 14-section home with `+54 9 3534 41-3243` as the real phone number. Test is still on the `home-redesign` shipped design.
  - CSS file size on production: 115,910 bytes (verified 2026-07-01 in `environments-and-recovery/verify-report.md`).
- **Files that control the home (test)**:
  - **DB**: `wp_postmeta.meta_value` for `_elementor_data` of post 35463 (in test DB `dbijhrsz46exbp`).
  - **Elementor JSON source**: `openspec/changes/home-redesign/elementor/home-35463-new.json` (23,905 bytes, 7 sections, ~33 widgets).
  - **Child theme assets** (already deployed to test):
    - `assets/css/tokens.css`
    - `assets/css/home-header.css`, `home-hero.css`, `home-steps.css`, `home-carousel.css`, `home-features.css`, `home-partners.css`, `home-footer.css`
    - `assets/js/home-animations.js`
    - `assets/images/logo-wecar.png`, `vehicle-placeholder.svg`
    - `includes/shortcodes/wecar-vehicle-carousel.php`
- **Gap vs desired** (what the new design needs that the shipped design does NOT have):

  | Section | Shipped (current) | Desired (new design) | Diff |
  |---------|--------------------|----------------------|------|
  | Hero cards | Has icon + title + desc + **CTA button** | Title + desc only, **no icon, no button** | Remove icons + CTAs; rewrite copy |
  | Hero copy | "Comprar tu próximo auto" / "Vendé tu auto" | "Encontrá tu próximo auto" / "Vendé tu auto sin dejar de manejarlo" | Rewrite both card copies |
  | 3-pasos animation | Staggered fade-in on first viewport entry | Progressive scroll-driven disclosure (4 frames, line fills, CTA at end) | Replace animation, add scroll-bound line + final CTA |
  | 3-pasos copy | "Cotizás online" / "Peritamos en sucursal" / "Lo publicamos" | Same titles but with full descriptions matching the mockup | Update card body text |
  | Carousel | Plain section background, no CTA | Purple→blue gradient background, "Contactar con un asesor" CTA below | Add gradient + CTA + "Ver todos" link |
  | Elegí Wecar | Abstract values: "Confianza" / "Transparencia" / "Facilidad", white bg | Concrete benefits with full sentence titles, gradient bg | Rewrite titles, add gradient bg, change card colors (purple/blue/teal solid) |
  | Partners | "Marcas Asociadas", plain white bg, dashed-border placeholders | "Respaldado por grandes marcas", lavender gradient bg, real-looking logos | Rename, add gradient bg, replace placeholders |
  | Partners logos | Empty image URLs, CSS dashed placeholders | Actual visible logos (Multicars, Le Parc Peugeot, Le Parc Citroën) | Provide real PNG/SVG logo files |
  | Footer phone | `+54 9 11 1234-5678` (placeholder) | `+54 9 3534 41-3243` (real number) | Update phone number in JSON |
  | Background pattern | Not present | Wavy/squiggle SVG pattern in hero + carousel + partners + footer | Add new pattern asset (SVG) |

---

## Reuse from `home-redesign` (archived)

### REQs still valid (no modification needed)

- **REQ-HOME-001** (Header Navigation) — header structure matches, copy/links unchanged in new design. ✅ Reusable.
- **REQ-HOME-005** (Vehicle Carousel — populates from `vehica_car` posts) — same shortcode, same query. ✅ Reusable.
- **REQ-HOME-006** (Vehicle Card — image, title, desc, price, tags) — card structure matches the mockup. ✅ Reusable. Minor: new design shows km/year/transmission in that order — current shortcode renders year/KM-placeholder/fuel/transmission. Order may need a small tweak.
- **REQ-HOME-010** (Logo Extraction) — `assets/images/logo-wecar.png` exists, used in header + footer. ✅ Reusable.
- **REQ-HOME-011/012/013/017** (Backup, Rollback, Test-First, CSS Validation) — all still apply and are now codified in `openspec/specs/elementor-data-restoration.md` and `openspec/specs/elementor-css-validation.md`. ✅ Reusable.
- **REQ-HOME-014/015/016** (Responsive, Design System, No Regression) — design tokens in `tokens.css` are still valid. The new design uses the same purple/blue/cyan palette. ✅ Reusable.

### REQs that need modification

- **REQ-HOME-002** (Hero Dual Cards) — copy needs to change; CTA buttons need to be removed; icons need to be removed. **Modified**.
- **REQ-HOME-003** (3-Step Process) — section title and step body copy needs to change. **Modified**.
- **REQ-HOME-004** (Scroll Animations) — animation needs to be replaced: from "staggered fade-in on viewport entry" to "progressive scroll-driven disclosure within section" (4 frames). **Replaced**.
- **REQ-HOME-007** (Elegí Wecar) — section title and 3 card titles need to change. Card colors stay the same (purple/blue/cyan → now solid backgrounds with white text instead of icon-on-white). **Modified**.
- **REQ-HOME-008** (Marcas Asociadas) — section title needs to change to "Respaldado por grandes marcas". Background needs a gradient. **Modified**.
- **REQ-HOME-009** (Footer) — phone number needs to change from `+54 9 11 1234-5678` to `+54 9 3534 41-3243`. **Modified**.

### REQs missing for the new design

- **REQ-HOME-NEW-A: Carousel bottom CTA** — new "Contactar con un asesor" button below the carousel (links to `/contactanos/`, TBD). **NEW**.
- **REQ-HOME-NEW-B: Carousel section gradient background** — purple→blue diagonal gradient with wavy pattern. **NEW**.
- **REQ-HOME-NEW-C: Elegí Wecar section gradient background** — white→lavender vertical gradient. **NEW**.
- **REQ-HOME-NEW-D: Partners section gradient background** — lavender gradient with wavy pattern. **NEW**.
- **REQ-HOME-NEW-E: Wavy/squiggle pattern asset** — reusable decorative SVG pattern used in hero, carousel, partners, footer. **NEW**.
- **REQ-HOME-NEW-F: 3-step scroll-bound connecting line** — horizontal gradient line that fills in as the user scrolls; numbered circles light up in sequence. **NEW**.
- **REQ-HOME-NEW-G: 3-step final CTA** — "Vendé tu usado sin vueltas" button appearing only at the end of the scroll sequence. **NEW**.
- **REQ-HOME-NEW-H: Hero cards without buttons/icons** — explicit non-goal: hero cards are teaser blocks, not navigation entrypoints. **NEW**.
- **REQ-HOME-NEW-I: Real partner logo files** — actual Multicars, Le Parc Peugeot, Le Parc Citroën logo files (not placeholders). **NEW**.

### Design assets (CSS, JS, images) that can be reused as-is

- `wp-content/themes/vehica-child/assets/css/tokens.css` — color/typography/spacing tokens. **Reusable as-is** (palette matches the new design).
- `wp-content/themes/vehica-child/assets/images/logo-wecar.png` — same logo appears in new header and footer. **Reusable as-is**.
- `wp-content/themes/vehica-child/assets/images/vehicle-placeholder.svg` — fallback for vehicles without featured image. **Reusable as-is**.

### Design assets that need to be MODIFIED (not replaced)

- `assets/css/home-header.css` — minor tweaks only (CTA button color may need adjustment to match the lavender pill in the mockup).
- `assets/css/home-hero.css` — **major rewrite**: remove icon styling, remove button styling, switch to vertical gradient backgrounds, add wavy pattern overlay, change card text color rules.
- `assets/css/home-steps.css` — **major rewrite**: replace "01/02/03" big number with small circle indicators, add gradient line state classes (`wecar-step-line--1`, `--2`, `--3` for progressive fill), add styles for the new step descriptions, add final CTA button styling.
- `assets/css/home-carousel.css` — **major rewrite**: add section gradient background, add wavy pattern overlay, add card tag-chip layout (km/year/transmission in chip pills), add bottom CTA button.
- `assets/css/home-features.css` — **major rewrite**: switch card style from "icon + title + desc on white" to "solid color background with icon + title in white", add section gradient background.
- `assets/css/home-partners.css` — **major rewrite**: remove dashed-border placeholder styling, add section gradient background, update title to "Respaldado por grandes marcas".
- `assets/css/home-footer.css` — minor tweaks: update phone number, possibly update background if a pattern is added.
- `assets/js/home-animations.js` — **major rewrite**: replace IntersectionObserver-based fade-in with a scroll-bound animation that updates the line fill state based on scroll position within the section (4 distinct states).
- `includes/shortcodes/wecar-vehicle-carousel.php` — minor: reorder tag chips to match new design (km first, then year, then transmission — currently shows year/KM-placeholder/fuel/transmission).

### Shortcodes / components reusable

- `[wecar_vehicle_carousel count="12"]` shortcode — fully reusable. Only the wrapping section needs to change (background gradient + bottom CTA).

### Elementor JSON structure reusable

- The 7-section structure (header / hero / 3-pasos / carousel / features / partners / footer) is the same. Section 4 (carousel) is structurally identical and only needs the bottom CTA + section background to be added.

---

## Open Questions (to be resolved in `sdd-propose`)

1. **"2026 Custer" in the footer** — the mockup shows "2026 Custer. All rights reserved." The previous design also used "Custer" (the dev shop that originally built the site). The user is correcting the design, not the brand attribution — so "Custer" is likely **intentional and should stay**. Confirm with the user.
2. **The brief mentions 4 partner logos ("+1") but the mockup shows 3** — Multicars, Le Parc Peugeot, Le Parc Citroën. Should we add a 4th (e.g., Wecar's own logo) or stick to the 3 visible in the mockup? **Recommend**: stick to 3 (follow the visual).
3. **"Contactar con un asesor" CTA link target** — likely `/contactanos/` (matches the header CTA) but needs confirmation. Could also be a modal, tel: link, or WhatsApp link.
4. **"Vendé tu usado sin vueltas" CTA link target** — needs confirmation. Likely `/cotiza/` (the existing cotizador page) or `/vende-tu-auto/`.
5. **Wavy pattern asset** — the mockup shows a specific squiggle/wave pattern. Is there an SVG file the user can provide, or should we create one? **Recommend**: ask for a vector asset, or generate a similar SVG if none is available.
6. **Partner logo files** — the previous design had placeholders. Does the user have the real Multicars / Le Parc Peugeot / Le Parc Citroën logos? If not, do we keep placeholders or extract from somewhere?
7. **Scroll-bound animation complexity** — the 4-frame progressive disclosure in `section-1(1).png` could be implemented as:
   - **(a) Pinned scroll** — section is pinned and animates over multiple scroll wheel rotations (scroll-jacking, complex, can be janky on mobile).
   - **(b) State-based on scroll position** — IntersectionObserver thresholds (0%, 33%, 66%, 100% of section visible) update the DOM to reveal frames 1–4. Simpler, smoother, mobile-friendly.
   - **Recommend**: option (b). Confirm with the user.
8. **3-step copy final wording** — the mockup text is clear but should be confirmed verbatim. Step 3 title is on TWO lines: "Lo publicamos." / "Vos lo seguís manejando." — keep that line break or merge?
9. **Vehicle card tag order** — mockup shows `km` first, then `year`, then `transmission`. Current shortcode shows `year / KM-placeholder / fuel / transmission`. Confirm: drop the `fuel` chip, or keep it as a 4th chip?
10. **Production phone in carousel section** — is `+54 9 3534 41-3243` the only real number, or are there regional numbers? The mockup only shows one.

---

## Risks

1. **DO NOT TOUCH PRODUCTION (wecar.com.ar).** The 2026-07-01 incident happened because a meta update was run against `~/www/wecar.com.ar/` instead of `~/www/test.wecar.com.ar/`. Every SSH command in `sdd-apply` and beyond MUST use `~/www/test.wecar.com.ar/public_html/`. This is a hard rule.
2. **Branch naming trap.** Current branch is `feat/redesign-prod` — misleadingly named. The work branch should be renamed to `home-correct-2026-07` (the **tracker** branch) in `sdd-apply` BEFORE any commits. PR-1 and PR-2 will live on their own child branches (`-pr1` and `-pr2`) targeting the tracker. Do not push to production or merge to a branch that touches production.
3. **Elementor data 27-key rule.** Any change to `_elementor_data` for post 35463 must be followed by the CSS file size check (must be > 50 KB). A file < 1 KB means the page is broken and the runbook in `openspec/specs/elementor-data-restoration.md` MUST be followed.
4. **Local JSON backup is insufficient.** The `_elementor_data-35463.json` in `openspec/changes/home-redesign/backups/` is a single-key snapshot. To recover a broken state, use the SQL backup on production at `~/wecar-db-backup-YYYYMMDD.sql`.
5. **Scroll-bound animation can be janky on mobile Safari** — if option (a) is chosen (pinned scroll), test on real iOS device. If option (b) is chosen (state-based on scroll), the risk is low.
6. **Elementor JSON compatibility with Elementor 4.1.4** — the previous change had to fix compatibility issues (icon format, nav-menu widget, title_size). Same risk applies here. Test on test first.
7. **Cache strategy** — after every apply, `wp cache flush` AND clear Elementor CSS cache. If the carousel section gets a new gradient background, the CSS file may grow but should still regenerate cleanly.
8. **Scope creep.** The user asked to "correct" the home to the new design. Do not refactor the child theme, do not touch the dashboard, do not change the listing or single-car pages. Stay surgical.
9. **Review budget.** Spec says 400 changed lines before escalation. The new design has substantial CSS rewrites (hero, steps, carousel, features, partners) + new JS (scroll-bound animation) + new Elementor JSON. Likely 600–800 lines of changes — **may need to be split into chained PRs**.

---

## Recommended Next Phase

**`sdd-propose`** with the following open questions resolved FIRST (in the proposal, as decisions):

1. "Custer" copyright — confirm intentional.
2. "Contactar con un asesor" link target.
3. "Vendé tu usado sin vueltas" link target.
4. Wavy pattern asset — do we have it, or generate?
5. Partner logo files — do we have them?
6. 3-step scroll animation strategy — option (a) pinned vs option (b) state-based.
7. Vehicle card tag order — km/year/transmission or year/km/transmission/fuel?
8. Number of partner logos — 3 (mockup) or 4 (brief)?

After these are answered, `sdd-propose` can produce a clean proposal, and `sdd-spec` can author the deltas. The spec will likely need to:

- Modify REQ-HOME-002, REQ-HOME-003, REQ-HOME-004, REQ-HOME-007, REQ-HOME-008, REQ-HOME-009.
- Add ~9 new REQs (REQ-HOME-NEW-A through REQ-HOME-NEW-I from the list above).
- Reuse most of the NFRs (performance, accessibility, SEO, cache, browser support, responsive, CSS specificity, JS independence, CSS validation).
- Add 1 new NFR: **NFR-HOME-NEW: scroll-bound animation performance budget** — must not cause jank on mobile (target INP < 100ms per existing NFR-HOME-001).

Expected scope: 600–800 lines of changes (CSS rewrites + new JS + new Elementor JSON). Will likely need chained PRs (the budget is 400 lines per PR).
