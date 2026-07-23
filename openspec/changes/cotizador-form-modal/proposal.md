# Proposal: Cotizador Form Modal

## Intent

The "Comenzar" button on the Cotizador page is disabled with no form behind it. Users see a 3-step promise but cannot act on it. This implements a multi-step modal form collecting quote data into localStorage, completing the quote flow.

## Scope

### In Scope
- Modal overlay triggered by "Comenzar" button
- 3-step form: personal data, car data, inspection scheduling
- Progress indicator (3 bars: gradient/purple/inactive)
- Step navigation with field validation
- localStorage mock persistence
- Hardcoded location (Villa María, Córdoba) with static map
- Figma-exact styling (660px card, 24px radius, design tokens)

### Out of Scope
- Backend API (localStorage only), multi-branch, real map embed
- Email/notification on submission
- Select dropdowns (all text inputs per product decision)
- Analytics/tracking events

## Capabilities

### New Capabilities
- `cotizador-modal`: Modal overlay, step navigation, progress indicator, form rendering
- `cotizador-form-data`: Form state, validation, localStorage persistence

### Modified Capabilities
- None

## Approach

Vanilla JS modal. New files: `cotizador-modal.css`, `cotizador-modal.js`. Modify `page-cotizador.php` to add modal HTML and enable button. Enqueue via `functions.php`. Reuse `--quote-*` vars and `--wecar-figma-*` tokens.

## Affected Areas

| Area | Impact | Description |
|------|--------|-------------|
| `templates/page-cotizador.php` | Modified | Enable button, add modal container |
| `assets/css/cotizador-modal.css` | New | Modal, form steps, inputs, pills |
| `assets/js/cotizador-modal.js` | New | Modal logic, validation, localStorage |
| `functions.php` | Modified | Enqueue new CSS/JS |
| `assets/images/cotizador/` | New assets | Map, radio SVG, info icon, pin marker |

## Risks

| Risk | Likelihood | Mitigation |
|------|------------|------------|
| First modal pattern — no existing overlay reference | High | Self-contained: focus trap, ESC close, backdrop click, aria |
| Focus management/accessibility | Medium | Focus trap, restore focus on close, screen reader test |
| Responsive on mobile (< 660px) | Medium | Max-width 660px, mobile breakpoint, scrollable content |
| Figma assets may not match pixel-perfect | Low | Export PNG/SVG from Figma, verify visually |

## Rollback Plan

Remove modal HTML, re-disable button, remove CSS/JS enqueue. No data migration needed. Git revert.

## Dependencies

- Figma access for asset extraction (map, radio SVG, info icon, pin marker)
- Existing `tokens.css` and `cotizador.css` loaded on page

## Success Criteria

- [ ] "Comenzar" opens modal; 3 steps with correct progress states
- [ ] Steps 1-2: text inputs + GNC radio; Step 3: day/time pills + location
- [ ] Cannot advance without required fields; data persisted to localStorage on submit
- [ ] ESC/backdrop close modal; focus returns to "Comenzar" button
- [ ] Matches Figma pixel-perfect; works Chrome/Firefox/Safari desktop + mobile
