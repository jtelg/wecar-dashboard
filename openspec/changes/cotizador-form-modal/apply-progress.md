# Apply Progress: Cotizador Form Modal

## Work Unit 1 — Foundation + Modal Shell (PR 1 of feature-branch-chain)

**Status:** completed  
**Branch target:** `feat/cotizador-modal`  
**Applied:** 2026-07-21

### Completed Tasks

#### Phase 1: Foundation
- [x] 1.1 Figma assets present in `assets/images/cotizador/` (`map-villa-maria.png`, `icon-pin.svg`, `icon-info.svg`, `icon-radio.svg`). `icon-radio-checked.svg` deferred to PR 2.
- [x] 1.2 Enabled "Comenzar" button in `templates/page-cotizador.php`: removed `disabled` and `aria-disabled`, added `data-wecar-cotizador-open`, kept `type="button"`.
- [x] 1.3 Added modal container HTML before `get_footer()`: `role="dialog"`, `aria-modal="true"`, `aria-labelledby` pointing to active step title, 660px card, close control, 3 progress bars, 3 step panels, success screen.
- [x] 1.4 Enqueued `cotizador-modal.css` (deps `wecar-cotizador`, `wecar-tokens`) and `cotizador-modal.js` (defer, footer) inside the existing `$is_quote_page` block in `functions.php`.

#### Phase 2: Modal Shell
- [x] 2.1 CSS: fixed fullscreen backdrop `rgba(0,0,0,0.5)`, centered 660px max card (white, 24px radius, 30px padding, 60px gap), absolute close control, body scroll lock via `.wecar-cotizador-modal-is-open`.
- [x] 2.2 CSS: 3 progress bars (6px height, 4px radius, 8px gap) with active gradient (`#9949FF` → `#0E6FD1`), completed `#9949FF`, inactive `#F5EDFF`.
- [x] 2.3 JS `openModal()`: reveals overlay, reads draft step from `localStorage` (try/catch), renders stored or step 1, focuses first field, locks scroll.
- [x] 2.4 JS `closeModal()`: triggered by ESC, backdrop click, close button; hides overlay, restores scroll, returns focus to trigger button.
- [x] 2.5 JS focus trap: hand-rolled, collects tabbable elements inside active step/success panel, wraps Tab/Shift+Tab at boundaries.
- [x] 2.6 JS step state machine: `showStep(n)` swaps `.wecar-cotizador-modal__step--active`, updates progress-bar classes, CSS `translateX` + opacity 200ms ease-out transition; `aria-labelledby` follows active step.

## Work Unit 2 — Form Steps + Validation (PR 2 of feature-branch-chain)

**Status:** completed  
**Branch target:** `feat/cotizador-modal` (PR 2 branches from PR 1)  
**Applied:** 2026-07-21

### Completed Tasks

#### Phase 3: Form Steps
- [x] 3.1 Step 1 HTML in `templates/page-cotizador.php`: title "Contanos sobre vos", 2×2 grid with Nombre/Mail/Teléfono/Localidad inputs + labels + error spans; "Continuar" button.
- [x] 3.2 Step 2 HTML: title "Datos del auto", 2×2 grid Año/Marca/Modelo/Kilómetros, GNC radio group with 3 options (instalado/tuvo/nunca) using `icon-radio.svg` / `icon-radio-checked.svg`; "Continuar" button.
- [x] 3.3 Step 3 HTML: title "Peritaje y sucursal", subtitle with bold purple phrase, day pills Lun–Vie, time pills Mañana/Tarde, hardcoded location, 240px static map + pin overlay, info notice block; "Enviar" button + inline error container.
- [x] 3.4 CSS shared input style (white bg, 1px #E7E7E7 border, 14px radius, 9px 14px padding), labels (Exo 2 SemiBold 12/18), placeholders (#767676), action buttons (gradient 144deg, 44px, 16px radius, disabled opacity 0.3 / pointer-events none), error state (#FF3B30 border + message).
- [x] 3.5 CSS specific elements: GNC radio 20×20 SVG swap (unchecked purple stroke, checked purple fill), day/time pills (36px, 1px #9949FF border, 14px radius, selected filled #9949FF / white text), map container (240px, 14px radius, centered pin), notice block (#E7F1FA, 14px radius, 6px 16px padding).
- [x] 3.6 JS input binding: text inputs update `state.fields` on `input`; GNC radio click updates `state.fields.gnc` mutually exclusive; day/time pill click toggles `state.fields.dia` / `state.fields.horario` single-select.

#### Phase 4: Validation & Persistence
- [x] 4.1 JS validation engine: `VALIDATORS` via `validateField(name)` — mail regex, teléfono regex, año 4-digit 1900–current+1, kilómetros positive int ≤ 9,999,999, gnc enum, required checks per step.
- [x] 4.2 JS validation UI: `blur` runs `validateField()` and shows field-level error (red border + message); `input` clears the error for that field.
- [x] 4.3 JS button gate: "Continuar"/"Enviar" disabled (opacity 0.3) until `validateStep()` passes; re-checked on every input/change event.
- [x] 4.4 JS localStorage draft: `persistDraft()` writes `{step, fields}` to `wecar_cotizador_draft` on every change; `loadDraft()` populates state on modal open; `restoreFieldsToDOM()` repopulates text inputs, radio selection, and pill selection.
- [x] 4.5 JS submit: on step 3 "Enviar", builds submission object with fields + timestamp + generated quote id, pushes to `wecar_cotizador_submissions` array, clears `wecar_cotizador_draft`, resets in-memory state, shows success screen, auto-closes modal after 3 seconds.
- [x] 4.6 JS resilience: all localStorage access wrapped in try/catch; QuotaExceededError surfaces inline error and keeps modal open with state preserved; unavailable localStorage falls back to in-memory + console warning.

### Files Changed

| File | Action | Notes |
|------|--------|-------|
| `wp-content/themes/vehica-child/templates/page-cotizador.php` | Modify | Replaced step placeholders with full step 1/2/3 form markup, labels, buttons, map, notice. |
| `wp-content/themes/vehica-child/assets/css/cotizador-modal.css` | Modify | Added field grid, inputs, labels, action buttons, radio/pill/map/notice/error styles, responsive placeholder. |
| `wp-content/themes/vehica-child/assets/js/cotizador-modal.js` | Modify | Added `state.fields`, validation engine, field binding, button gates, localStorage draft/submissions, submit/success flow. |
| `wp-content/themes/vehica-child/assets/images/cotizador/icon-radio-checked.svg` | New | Checked GNC radio icon (purple fill + white dot). |
| `wp-content/themes/vehica-child/functions.php` | No change | PR 1 enqueue remains sufficient. |

### Known Limitations / PR 3 Scope

- No explicit back-control between steps (forward-only via "Continuar"; closing and reopening preserves draft).
- Responsive breakpoints are minimal placeholders only; full mobile QA is PR 3.
- `prefers-reduced-motion` for step transitions exists, but no further motion polish.
- ARIA on custom radio/pill groups covers `role`, `aria-checked`, and label association; additional screen-reader announcements not yet audited.
- Cross-browser/device QA deferred to PR 3.

### Verification Notes

- Static HTML/CSS/JS syntax is valid; JS passes `node --check`.
- `aria-labelledby` is updated by `showStep()` to reference the visible step title ID.
- `localStorage` access is wrapped in try/catch with console warnings and inline error fallback for submit failures.
- Focus trap restricts Tab cycling to the currently visible step or success panel.
- Button disabled state is dynamically bound to `validateStep(currentStep)`.

### Next Recommended

1. Merge PR 2 into `feat/cotizador-modal`.
2. Begin PR 3 (Persistence + Polish + Deploy): responsive breakpoints, back navigation, final ARIA audit, cross-browser QA, and TEST deploy.

### Risks

- **Low:** GNC radio checked state relies on `icon-radio-checked.svg` being deployed alongside the other assets; verify asset path on TEST.
- **Low:** inline numeric inputs use `inputmode="numeric"` but accept any text; validators block non-numeric values.
- **Medium:** draft persistence restores the last active step; if a user left on step 3 they will land on step 3 without an obvious way to review earlier steps until PR 3 adds back navigation.
- **Medium:** CSS class `.wecar-cotizador-modal-is-open` on body assumes no other component toggles body overflow; verify alongside existing page scroll behaviors during QA.

### Skill Resolution

`sdd-apply` completed Work Unit 2 without delegation. Chained-PR strategy remains in effect; PR 3 should branch from `feat/cotizador-modal` after PR 2 is merged.

## Work Unit 3 — Polish + Accessibility + Responsive (PR 3 of feature-branch-chain)

**Status:** completed  
**Branch target:** `feat/cotizador-modal` (PR 3 branches from PR 2)  
**Applied:** 2026-07-21

### Completed Tasks

#### Phase 5: Polish & Accessibility
- [x] 5.1 CSS responsive breakpoints in `cotizador-modal.css`: `@media (max-width: 660px)` card fills viewport (16px horizontal padding), max-width 100%, 2×2 grid collapses to 1 column, card padding 20px, modal vertical padding 10px, card max-height `calc(100vh - 32px)`, GNC radios stack vertically, pills wrap, map height 180px, title 30px/36px; `@media (max-width: 380px)` tighter padding and smaller pill text.
- [x] 5.2 ARIA audit: `role="dialog"` + `aria-modal="true"` on modal root; `aria-labelledby` updated to current step title by `showStep()`; `aria-live="polite"` on step titles; all text inputs have `<label for="...">` and `aria-describedby` pointing to error span; GNC group `role="radiogroup"` with `aria-describedby`; each radio button gets `aria-checked` updated by `updateRadioUI()`; day/time pills get `aria-pressed="true"/"false"` via `updatePillUI()`; success screen uses `aria-live="assertive"` + `aria-atomic="true"`.
- [x] 5.3 CSS `prefers-reduced-motion: reduce` updated to disable `translateX` step transitions while keeping opacity fade; button gradient hover transition already disabled in existing reduced-motion block.
- [x] Back navigation: added `data-wecar-action="back"` "Volver" text buttons on steps 2 and 3; `handleActionClick()` calls `showStep(currentStep - 1)` without validation; styled as transparent purple text button (Exo 2 SemiBold 14px, #9949FF).
- [x] Success screen polish: added inline SVG checkmark icon, centered layout, "¡Cotización enviada!" title (Syne Bold 38px), "Te contactaremos a la brevedad" subtitle (Exo 2 18px, #464646); 3-second auto-close retained from PR 2.

#### Phase 6: QA Preparation
- [x] 6.1 Added QA checkpoint comments in `cotizador-modal.js` referencing open/close, focus trap, validation, persistence, and responsive/a11y review locations.
- [x] 6.2 Added QA checkpoint comments in `cotizador-modal.css` responsive block for 360/660/1100px viewport verification.
- [x] 6.3 Added QA checkpoint comment in `page-cotizador.php` success screen for `aria-live="assertive"` and auto-close verification.
- [ ] 6.4 Deploy to TEST via SCP → validate → `wp cache flush` → cross-browser QA → deploy to production → cache flush → smoke test (deferred to release phase).

### Files Changed

| File | Action | Notes |
|------|--------|-------|
| `wp-content/themes/vehica-child/templates/page-cotizador.php` | Modify | Added back buttons on steps 2/3; added error span `id`s and `aria-describedby` on inputs/custom groups; added `aria-pressed` on pills; added `aria-live="assertive"` + checkmark icon to success screen. |
| `wp-content/themes/vehica-child/assets/css/cotizador-modal.css` | Modify | Added back button styles, success screen styles, expanded responsive breakpoints (660px/380px), updated `prefers-reduced-motion` to keep opacity fade. |
| `wp-content/themes/vehica-child/assets/js/cotizador-modal.js` | Modify | Added back navigation handler, pill `aria-pressed` updates, QA checkpoint comments. |

### Known Limitations / Deploy Scope

- Responsive breakpoints are implemented per spec; real-device QA on TEST is required before production.
- `prefers-reduced-motion` keeps the opacity fade; some users may still perceive this as motion—verify during a11y QA.
- Back navigation preserves in-memory state and draft; no state reset on step change.
- Success screen auto-closes after 3 seconds; no explicit close control on success panel—consider adding if usability testing flags it.

### Verification Notes

- `node --check` passes on `cotizador-modal.js`.
- Back navigation is wired to `data-wecar-action="back"` and skips validation.
- Step titles, pills, and radio controls maintain correct ARIA attributes after DOM restoration.
- CSS responsive breakpoints target 660px and 380px; card uses `max-width: 100%` and scrollable `max-height` on narrow viewports.
- Success screen uses `aria-live="assertive"` and `aria-atomic="true"` for screen-reader announcement.

### Next Recommended

1. Merge PR 3 into `feat/cotizador-modal`.
2. Deploy `feat/cotizador-modal` to TEST (SCP child-theme assets only; do not touch Elementor CSS/metadata).
3. Run manual QA checklist from Phase 6 at 360px, 660px, and 1100px; verify localStorage draft/submit flow, focus trap, back navigation, and success screen.
4. Flush WP cache on TEST, then promote to production following the `AGENTS.md` workflow rule.

### Risks

- **Low:** Back button and submit button share the same flex column; ensure touch targets do not overlap on 360px devices.
- **Low:** `aria-describedby` on inputs references initially empty error spans; some screen readers may announce an empty descriptor—acceptable pattern, but verify with NVDA/VoiceOver.
- **Medium:** CSS `body.wecar-cotizador-modal-is-open` still locks page scroll; confirm no other component (e.g., mobile menu) toggles body overflow on the Cotizador page.
- **Medium:** Deploy to production must follow the child-theme-only invariant; never copy or regenerate Elementor CSS as part of this change.

### Skill Resolution

`sdd-apply` completed Work Unit 3 without delegation. The `cotizador-form-modal` feature is now code-complete and ready for TEST deploy QA.
