# Design: Cotizador Form Modal

## Technical Approach

Self-contained vanilla-JS modal added to the Cotizador page. Modal HTML is rendered by PHP in `page-cotizador.php` (hidden by default), exposed via `templates/page-cotizador.php` so it pre-renders for SEO/no-JS-failover visibility markup. JS attaches behavior, manages state machine, validation, and localStorage. New dedicated assets `cotizador-modal.css` + `cotizador-modal.js`, enqueued from `functions.php` only on the cotizador page, reusing existing `--quote-*` (page scope) and `--wecar-figma-*` (global tokens) variables. No frameworks; continues the IIFE + strict-mode pattern of `cotizador.js`.

## Architecture Decisions

| Decision | Choice | Alternatives | Rationale |
|---|---|---|---|
| Modal HTML location | Static PHP markup in `page-cotizador.php`, hidden until `.is-open` | JS-generated DOM | SSR markup keeps semantics for crawlers, less JS parsing, easier ARIA wiring; matches existing template style |
| Modal CSS file | New `assets/css/cotizador-modal.css` (depends on `wecar-cotizador` + `wecar-tokens`) | Extend `cotizador.css` | Modal is a distinct concern; existing file already 176 lines; separation aids rollback |
| Modal JS file | New `assets/js/cotizador-modal.js`, separate IIFE | Extend `cotizador.js` | Animation logic and form state are separable concerns; lets animation load without form weight |
| State management | Single plain object in module closure + localStorage mirror | Class-based/store lib | Matches existing IIFE pattern; no dependencies; trivial inspectability |
| Step transition animation | CSS class swap `.wecar-cotizador-modal__step--active` with `transform: translateX` + opacity 200ms ease-out; replaced steps `display:none` after transitionend | WAAPI keyframes | CSS-driven keeps transitions cheap and respects `prefers-reduced-motion` cheaper than orchestrating WAAPI per step |
| Validation timing | Blur → field error; input → clear error; submit click re-validates and blocks; "Continuar"/"Enviar" enabled when step is valid | Live-as-you-type | Specified by REQ-COTMOD-014 (blur surfaces errors) without being annoying while typing |
| Focus trap | Hand-rolled: maintain list of focusable elements per open step; intercept Tab/Shift+Tab at boundaries; restore trigger on close | `:focus-visible`-only | Required by REQ-COTMOD-012; no external dep; matches project constraints |
| Map asset | Static PNG exported from Figma, 240px tall, clip + overlay pin absolutely positioned | Real map / iframe | Spec mandates "static 240px image"; product decision "single hardcoded location" |

## Data Flow

    Comenzar button ──click──→ openModal()
         │                          │
         │                          ├─ read draft from localStorage → state
         │                          └─ render step 1, focus first field
         │
    User input ──blur──→ validateField() ──→ updateState() + persistDraft()
         │                                         │
         └─ next click ──→ validateStep() ──pass──→ showStep(n+1) ─→ update progress bars
         │                                  fail ─→ mark errors, keep button disabled
         │
    Back click ───showStep(n-1)──→ no re-validation, values from state
         │
    submit (step 3) ──→ push to submissions[], clear draft, show success ──→ close on timeout

## File Changes

| File | Action | Description |
|------|--------|-------------|
| `wp-content/themes/vehica-child/templates/page-cotizador.php` | Modify | Enable "Comenzar" button (`disabled` removed, `data-wecar-cotizador-open`); append modal container markup with 3 steps, progress bars, close control |
| `wp-content/themes/vehica-child/assets/css/cotizador-modal.css` | New | Backdrop, card, progress bars, inputs, pills, GNC radios, map/notice, success screen, responsive breakpoints |
| `wp-content/themes/vehica-child/assets/js/cotizador-modal.js` | New | Modal open/close, focus trap, step state machine, validation, localStorage draft + submissions |
| `wp-content/themes/vehica-child/functions.php` | Modify | Enqueue `cotizador-modal.css` (deps `['wecar-cotizador','wecar-tokens']`) and `cotizador-modal.js` (defer footer) inside existing `$is_quote_page` block; preserve SCP deploy invariant |
| `wp-content/themes/vehica-child/assets/images/cotizador/` | Add | `map-villa-maria.png`, `icon-pin.svg`, `icon-info.svg`, `icon-radio.svg`, `icon-radio-checked.svg` |

## Interfaces / Contracts

```js
// cotizador-modal.js (module-private shape)
const state = {
  step: 1, // 1..3
  fields: {
    nombre: '', mail: '', telefono: '', localidad: '',
    anio: '', marca: '', modelo: '', kilometros: '',
    gnc: '', dia: '', horario: '' // gnc enum: 'instalado'|'tuvo'|'nunca'
  }
};
const VALIDATORS = { mail:/^[^\s@]+@[^\s@]+\.[^\s@]+$/, telefono:/^[\d\s+\-()]{6,20}$/, anio:/^(19|20)\d{2}$/ };
const KEY_DRAFT = 'wecar_cotizador_draft';
const KEY_SUBMISSIONS = 'wecar_cotizador_submissions';
```

## Testing Strategy

| Layer | What to Test | Approach |
|-------|-------------|----------|
| Unit (HTML/visual) | Markup renders 3 steps, progress bars, inputs, pills, GNC radios | Manual browser inspection against Figma screenshots |
| Functional | Open/close (button, ESC, backdrop), focus trap cycle, focus restoration | Keyboard walkthrough + DOM audit |
| Validation | All 7 required fields per step, 5 format validators, button enable gates | Manual + devtools localStorage inspection |
| Persistence | Draft survives close/reopen before submit; submissions array grows on submit; draft cleared after submit | Clear localStorage, walk flow, inspect `JSON.parse(localStorage.getItem(...))` |
| Resilience | QuotaExceededError simulated (override localStorage.setItem to throw) → modal stays open, error shown, state preserved | Devtools override + retry |
| Responsive | 360px, 660px, 1100px viewports: card width, input grid (1 col vs 2 col), scroll, pill wrap | Browser responsive panel |
| Cross-browser | Chrome, Firefox, Safari desktop; iOS Safari + Chrome Android | Manual QA on TEST env |

## Threat Matrix

N/A — no routing, shell, subprocess, VCS/PR automation, executable-file classification, or process-integration boundary. Change is frontend-only: HTML markup, CSS, vanilla-JS event handlers, localStorage (sandboxed browser API).

## Migration / Rollout

No DB migration or feature flag. Deploy to TEST first (per AGENTS.md workflow rule), validate visually and via console, then promote to prod. Rollback per proposal: revert modal HTML, re-disable button, remove the two enqueue entries. localStorage keys are namespaced; no cleanup needed.

## Open Questions

- [ ] Should the confirmation success screen auto-close after N seconds or require explicit click? (Spec says "before closing" — confirm timing UX)
- [ ] Should the draft be cleared on full submit failure only, or also be cleared after a manual "Cancelar"? Spec says close MUST NOT clear draft — implies manual cancel keeps draft; confirm
- [ ] Final Figma asset export scale for `map-villa-maria.png` (1x / 2x for retina)?
- [ ] Confirm GNC option internal enum strings used as radio values ("instalado"/"tuvo"/"nunca") — implementation choice, no Figma spec