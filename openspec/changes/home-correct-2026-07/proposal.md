# Home Correction 2026-07 — Proposal

> **Date**: 2026-07-01
> **Change**: `home-correct-2026-07`
> **Status**: ✅ User approved recommendations for all 8 decisions (2026-07-01). Proceeding to `sdd-spec`.
> **Source of truth**: `openspec/changes/home-correct-2026-07/exploration.md`
> **Decisions (all approved)**: Custer=keep, /contactanos/ for hero CTA, /vende-tu-auto/ for steps CTA, generate wavy SVG, partner logos=placeholders+v2, state-based animation, km/year/transmission, 3 logos.

---

## Intent

Correct the test home page to match the new mockups (`new-design/home.png` + `new-design/section-1(1).png`). The shipped `home-redesign` (currently on test) has the right 7-section structure but wrong details: hero cards have icons and CTAs that shouldn't be there, the 3-step section fades in all at once instead of progressively, several sections are missing gradient backgrounds, and the footer phone number is a placeholder. This change fixes those details — copy, animation, backgrounds, partner section, phone number — bringing the test home in line with the brand vision shown in the mockups.

## Business Problem

The shipped `home-redesign` design has the right structural skeleton but significant visual and functional deviations from the approved mockups:

- **Hero cards** include CTA buttons and icons that make them feel like navigation entrypoints instead of teaser blocks, cluttering the top of the page.
- **3-step section** animates all three steps on viewport entry (fade-in), not progressively as the user scrolls — losing the storytelling journey designed in the 4-frame mockup.
- **Section backgrounds** (carousel, Elegí Wecar, partners) are plain white instead of the gradient backgrounds with wavy patterns that give the design its brand feel.
- **Footer phone** is a placeholder number (`+54 9 11 1234-5678`) instead of the real number (`+54 9 3534 41-3243`) already on production.
- **Partner section** uses placeholders with dashed borders instead of real-looking branded logos.

This makes the home feel unfinished and inconsistent with the brand identity shown to stakeholders on the mockups. We're shipping a 90% solution — this change closes the gap to 100%.

## Scope

### In Scope
- **Hero dual cards**: remove icons, remove CTA buttons, rewrite copy to match mockup ("Encontrá tu próximo auto" / "Vendé tu auto sin dejar de manejarlo"), switch to vertical gradient backgrounds, add wavy pattern overlay.
- **3-step process**: replace staggered fade-in with progressive scroll-driven disclosure (4 frames), add gradient connecting line with numbered circles that fill in sequence, add final CTA button "Vendé tu usado sin vueltas".
- **Carousel section**: add purple→blue diagonal gradient background, add wavy pattern, add "Ver todos →" link, add "Contactar con un asesor" bottom CTA.
- **Elegí Wecar section**: replace abstract value titles with concrete benefit sentences, switch card style to solid-color backgrounds with white text + icon, add white→lavender gradient background.
- **Partners section**: rename to "Respaldado por grandes marcas", add lavender→purple gradient background, replace dashed-border placeholders with real-looking logo blocks.
- **Footer**: update phone number to `+54 9 3534 41-3243`.
- **Vehicle card tag order**: reorder chips to km/year/transmission (drop fuel chip).
- **New asset**: wavy/squiggle SVG pattern used across multiple sections.
- **Environment**: test.wecar.com.ar ONLY.

### Out of Scope
- Production (wecar.com.ar) — explicit no-deploy zone. Hard rule.
- All other pages: vehicle listing, single car, cotizador, blog, contact, nosotros — leave untouched.
- Child theme architecture, PHP functions, or plugin changes — only the specific files listed in exploration.md.
- `wecar-snapshot-cron` — not affected.
- Backend, DB schema, or admin dashboard.

## Approach

**Elementor data first, then CSS/JS.** The 7-section `_elementor_data` JSON from the shipped `home-redesign` is structurally identical to what we need. We'll produce a new JSON (`home-35463-new.json`) that modifies: hero widgets (remove icons, remove CTAs, update copy), section backgrounds (add gradients), and footer (update phone). The CSS/JS layer handles everything else — animations, gradient lines, wavy patterns, card re-styling. Apply to test only via WP-CLI.

**Asset generation for the wavy pattern.** The mockup shows a distinctive squiggle/wave background decoration in hero, carousel, partners, and footer sections. Since no SVG file is in the repo, we'll generate it as `assets/images/wavy-pattern.svg` during sdd-apply, based on the visual reference in the mockup. Partner logo files (Multicars, Le Parc Peugeot, Le Parc Citroën) are a 🔴 blocker — we need the user to provide real PNG/SVG files or we keep the current placeholders with a v2 flag.

**Scroll animation is state-based (recommended).** The 4-frame progressive disclosure uses IntersectionObserver thresholds (25%, 50%, 75%, 100% of the section visible) to update DOM state classes on the step container. CSS transitions handle the visual fill-in of the gradient connecting line and card visibility. No pinned/scroll-jacking — this is option (b) from exploration, and it keeps the implementation simple, mobile-friendly, and testable.

**Branch and PR strategy.** Current branch name `feat/redesign-prod` is dangerously misleading. First action in sdd-apply: rename to `home-correct-2026-07` (the **tracker** branch). Forecast is ~730 lines of changes (CSS rewrites + new JS + Elementor JSON). Since the review budget is 400 lines, we'll need chained PRs. The chain strategy is `feature-branch-chain` (user approved 2026-07-01): PR-1 branch (`-pr1`) targets the tracker, PR-2 branch (`-pr2`) targets PR-1, only the tracker merges to main. See `tasks.md` for the full task breakdown.

## Decisions Needed (from sdd-explore open questions)

The exploration surfaced 8 open questions. For each, the **recommended decision** is stated below so you can quickly confirm or override.

| # | Question | Recommendation | Status |
|---|----------|----------------|--------|
| 1 | **"2026 Custer" in footer** — intentional brand attribution or correction needed? | Keep as-is. "Custer" is the dev shop that built the site; the mockup shows it. This is a design correction, not a rebrand. | 🟢 recommended |
| 2 | **"Contactar con un asesor" CTA link target** — what URL? | `/contactanos/` — matches the header CTA and the existing contact page. | 🟢 recommended |
| 3 | **"Vendé tu usado sin vueltas" CTA link target** — what URL? | `/vende-tu-auto/` if that page exists; falling back to `/cotiza/` if not yet built. | 🟡 need confirmation |
| 4 | **Wavy pattern asset** — user provides SVG or we generate? | **Generate** an SVG inline during sdd-apply, based on the visual reference in `new-design/home.png`. Saved as `assets/images/wavy-pattern.svg`. | 🟡 need confirmation |
| 5 | **Partner logo files** — user has real Multicars/Peugeot/Citroën logos? | **Request** real PNG/SVG files from user. If not provided, keep current placeholders but flag as v2 gap. | 🔴 blocker for production-ready delivery |
| 6 | **Scroll animation strategy** — pinned scroll (a) or state-based (b)? | **State-based (option b)** — IntersectionObserver thresholds, CSS transitions. Simpler, mobile-friendly, testable. | 🟢 recommended |
| 7 | **Vehicle card tag order** — km/year/transmission (3 chips) or year/km/fuel/transmission (4 chips)? | **km/year/transmission (3 chips, drop fuel)** — matches the mockup. The fuel chip is not shown in the new design. | 🟢 recommended |
| 8 | **Number of partner logos** — 3 (mockup shows Multicars, Peugeot, Citroën) or 4 (brief mentioned "+1")? | **3** — follow the visual mockup. The brief's "+1" is likely stale. | 🟡 need confirmation |

**Phone number note**: `+54 9 11 1234-5678` → `+54 9 3534 41-3243` is non-controversial (it matches production) and will be applied as-is without requiring confirmation.

## Affected Components

| File | Change type |
|------|-------------|
| `wp-content/themes/vehica-child/assets/css/home-hero.css` | Major rewrite — remove icon/button styles, add gradient backgrounds, wavy pattern overlay |
| `wp-content/themes/vehica-child/assets/css/home-steps.css` | Major rewrite — replace big-number styling with circle indicators + gradient line states + new CTA |
| `wp-content/themes/vehica-child/assets/css/home-carousel.css` | Major rewrite — add section gradient background, wavy pattern, bottom CTA, new tag-chip layout |
| `wp-content/themes/vehica-child/assets/css/home-features.css` | Major rewrite — solid color cards, white text + icon, section gradient background |
| `wp-content/themes/vehica-child/assets/css/home-partners.css` | Major rewrite — remove dashed placeholders, add gradient bg, new title |
| `wp-content/themes/vehica-child/assets/css/home-footer.css` | Minor — phone number update |
| `wp-content/themes/vehica-child/assets/css/home-header.css` | Minor — CTA button color adjustment to match lavender pill |
| `wp-content/themes/vehica-child/assets/js/home-animations.js` | Major rewrite — replace staggered fade-in with state-based scroll-bound animation (4 frames) |
| `wp-content/themes/vehica-child/includes/shortcodes/wecar-vehicle-carousel.php` | Minor — reorder tag chips, drop fuel chip |
| `wp-content/themes/vehica-child/assets/images/wavy-pattern.svg` | **NEW** — generated inline SVG |
| `openspec/changes/home-correct-2026-07/elementor/home-35463-new.json` | **NEW** — updated Elementor data export |
| *(not committed: real partner logo files unless user provides them)* | *(pending user input)* |

## Risks (Top 3)

1. **Production safety**: this change must NOT touch production. The 2026-07-01 incident happened because a meta update was run against `~/www/wecar.com.ar/` instead of `~/www/test.wecar.com.ar/`. Every SSH command in sdd-apply MUST use `~/www/test.wecar.com.ar/public_html/`.
2. **Branch trap**: current branch `feat/redesign-prod` is dangerously named. Must be renamed to `home-correct-2026-07` in sdd-apply before any commits — do not push to any branch that maps to production.
3. **Scope**: forecast is 600–800 lines against a 400-line review budget. Chained PRs will be required. sdd-tasks must define the chain strategy (likely 2 PRs: one for Elementor JSON + asset, one for CSS/JS).

## Success Criteria

- test.wecar.com.ar renders the new home matching the 2 mockups (visual diff < 5% deviation in desktop and mobile viewports).
- All 4 frames of the scroll animation in the "3 pasos" section are visible when scrolling slowly through the section — frame 1 (title), frame 2 (step 1), frame 3 (steps 1–2 + line fill), frame 4 (all 3 steps + CTA).
- Post-specific CSS file `post-35463.css` on test is > 50 KB after apply (the 27-key rule from REQ-HOME-017).
- No regressions on header navigation, vehicle carousel data loading, or shortcode functionality — existing features remain intact.
- WeCar team can review and sign off the corrected home for eventual production promotion.

## Next Phase

**`sdd-spec`** — will modify 6 existing REQs (REQ-HOME-002, 003, 004, 007, 008, 009) and add ~9 new REQs (NEW-A through NEW-I plus a new NFR for scroll-bound animation performance). sdd-spec depends on user confirmation of the 8 decisions above — or an explicit "use recommendations" from the user to proceed with all 🟢/🟡 recommendations as defaults.
