# Tasks: Cotizador Form Modal

## Review Workload Forecast

| Field | Value |
|-------|-------|
| Estimated changed lines | 780–830 |
| 800-line budget risk | Medium |
| Chained PRs recommended | Yes |
| Suggested split | PR 1: Foundation + Modal Shell → PR 2: Steps + Validation → PR 3: Persistence + Polish + Deploy |
| Delivery strategy | auto-forecast |
| Chain strategy | feature-branch-chain |

Decision needed before apply: No
Chained PRs recommended: Yes
Chain strategy: feature-branch-chain
400-line budget risk: High

### Suggested Work Units

| Unit | Goal | Likely PR | Focused test command | Runtime harness | Rollback boundary |
|------|------|-----------|----------------------|-----------------|-------------------|
| 1 | Modal shell opens/closes with empty steps | PR 1 → `feat/cotizador-modal` | Click "Comenzar" → modal opens; ESC closes; focus returns | Browser: navigate to /cotizador, click button | Remove modal HTML + enqueue; button re-disabled |
| 2 | All 3 steps render, validate, navigate | PR 2 → PR 1 branch | Fill step 1 → advance → step 2 → advance → step 3; invalid blocks | Browser: walk 3-step flow, inspect disabled buttons | Revert step HTML/CSS/JS; modal shell still works |
| 3 | Persistence, responsive, a11y, deploy | PR 3 → PR 2 branch | Close/reopen preserves draft; submit saves; 360px responsive | DevTools: inspect localStorage, responsive panel | Revert persistence/responsive CSS; steps still functional |

## Phase 1: Foundation

- [x] 1.1 Download Figma assets: `map-villa-maria.png`, `icon-pin.svg`, `icon-info.svg`, `icon-radio.svg` → `assets/images/cotizador/` (pre-existing; `icon-radio-checked.svg` deferred to PR 2)
- [x] 1.2 Enable "Comenzar" button in `page-cotizador.php`: removed `disabled` + `aria-disabled`, added `data-wecar-cotizador-open`
- [x] 1.3 Add modal container HTML in `page-cotizador.php` before `get_footer()`: overlay `div[role=dialog]`, card, close button, 3 progress bars, 3 step panels (hidden), success screen
- [x] 1.4 Enqueue `cotizador-modal.css` (deps: `wecar-cotizador`, `wecar-tokens`) and `cotizador-modal.js` (defer, footer) inside `$is_quote_page` block in `functions.php`

## Phase 2: Modal Shell

- [x] 2.1 CSS backdrop (`rgba(0,0,0,0.5)`, fixed fullscreen), card (660px max, 24px radius, 30px padding, white), close control, scroll lock (`body.wecar-cotizador-modal-is-open { overflow: hidden }`)
- [x] 2.2 CSS progress bars (3 × 6px height, 4px radius, 8px gap; active gradient, completed purple, inactive light)
- [x] 2.3 JS `openModal()`: show overlay, read draft from localStorage → state, render step 1, focus first field, lock scroll
- [x] 2.4 JS `closeModal()`: ESC keydown, backdrop click, close button → hide overlay, restore scroll, return focus to "Comenzar"
- [x] 2.5 JS focus trap: collect tabbable elements per step, intercept Tab/Shift+Tab at boundaries
- [x] 2.6 JS step state machine: `showStep(n)` swaps `.wecar-cotizador-modal__step--active`, updates progress bar classes, CSS translateX + opacity 200ms transition

## Phase 3: Form Steps

- [x] 3.1 Step 1 HTML in `page-cotizador.php`: title "Contanos sobre vos" (Syne 38/44), 2×2 grid (20px gap) with Nombre/Mail/Teléfono/Localidad inputs + labels; "Continuar" button
- [x] 3.2 Step 2 HTML: title "Datos del auto", 2×2 grid Año/Marca/Modelo/Kilómetros, GNC radio group (3 options with SVG radios, `role=radiogroup`); "Continuar" button
- [x] 3.3 Step 3 HTML: title "Peritaje y sucursal", subtitle with bold purple phrase, day pills (Lun–Vie), time pills (Mañana/Tarde), hardcoded location text, 240px static map + pin overlay, info notice block; "Enviar" button
- [x] 3.4 CSS shared input style (white bg, 1px #E7E7E7 border, 14px radius, 9px 14px padding), labels (Exo 2 SemiBold 12/18), placeholders (#767676), action buttons (gradient, 44px, 16px radius, disabled opacity 0.3)
- [x] 3.5 CSS GNC radio (20×20 SVG swap), day/time pills (36px, white/purple toggle, 14px radius), map container (240px, 14px radius, pin absolute), notice block (#E7F1FA bg, 14px radius)
- [x] 3.6 JS input binding: all text inputs → `state.fields` on input event; GNC radio click → `state.fields.gnc`; pill click → `state.fields.dia`/`horario` (mutually exclusive)

## Phase 4: Validation & Persistence

- [x] 4.1 JS validation engine: `VALIDATORS` map (mail regex, telefono regex, año 4-digit 1900–current+1, km positive int ≤9999999, gnc enum); `validateField(name)` returns error string or null
- [x] 4.2 JS blur → `validateField()` → show field error state + message; input → clear error; `validateStep(n)` checks all required fields, returns boolean
- [x] 4.3 JS button gate: "Continuar"/"Enviar" disabled (`opacity 0.3`, `pointer-events: none`) until `validateStep()` passes; re-check on every input/change
- [x] 4.4 JS localStorage draft: `persistDraft()` writes `state.fields` to `wecar_cotizador_draft` on every change; `loadDraft()` populates state on open; try/catch wrapper
- [x] 4.5 JS submit: build submission object (fields + timestamp + UUID quote id), push to `wecar_cotizador_submissions` array, clear draft key, show success screen, auto-close after timeout
- [x] 4.6 JS resilience: all localStorage access in try/catch; on QuotaExceededError → show error message, keep modal open, preserve in-memory state; on unavailable → in-memory only + console warning

## Phase 5: Polish & Accessibility

- [x] 5.1 CSS responsive: `@media (max-width: 660px)` — card fills viewport width (16px horizontal padding), 2×2 grid collapses to 1 column, content scrollable, map height 180px, title 30px/36px
- [x] 5.2 ARIA: `role=dialog`, `aria-modal=true`, `aria-labelledby` → step title id, `aria-live=polite` on title region, all inputs have `<label for>` + `aria-describedby` error, GNC radios `aria-checked`, pills `aria-pressed`, success screen `aria-live=assertive`
- [x] 5.3 CSS `prefers-reduced-motion: reduce` — translateX disabled, opacity fade retained, button gradient hover transition disabled

## Phase 6: QA & Deploy

- [x] 6.1 Manual QA: open/close (button, ESC, backdrop), focus trap cycle, focus restoration, step forward/back data preservation
- [x] 6.2 Manual QA: validation (empty fields, bad email, bad year, bad km), button enable/disable, localStorage draft survive close/reopen, submit + clear draft
- [x] 6.3 Manual QA: responsive (360px, 660px, 1100px), cross-browser (Chrome, Firefox, Safari desktop; iOS Safari, Chrome Android)
- [ ] 6.4 Deploy to TEST via SCP → validate → `wp cache flush` → cross-browser QA → deploy to production → cache flush → smoke test
