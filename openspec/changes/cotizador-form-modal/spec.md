# Spec: Cotizador Form Modal

> Full spec (NEW capabilities). Two domains: `cotizador-modal`, `cotizador-form-data`.

## Domain: cotizador-modal

### REQ-COTMOD-001 — Modal Trigger

The system SHALL open the cotizador modal when the user activates the "Comprimir"/"Comenzar" button on the Cotizador page. The button MUST be enabled (no longer inert).

- **GIVEN** the Cotizador page is loaded
- **WHEN** the user clicks the "Comenzar" button
- **THEN** the modal overlay appears with step 1 visible
- **AND** focus moves into the modal (first focusable field)

### REQ-COTMOD-002 — Modal Overlay Anatomy

The modal overlay MUST render a fixed full-screen backdrop (`rgba(0,0,0,0.5)`) above page content, centered on a 660px max-width card. The card SHALL use white background, 24px border-radius, 30px padding, and 60px gap between sections.

- **GIVEN** the modal is open
- **WHEN** the user views the overlay
- **THEN** a darkened backdrop covers the viewport
- **AND** the 660px card is centered with the specified styling
- **AND** page scroll is locked while open

### REQ-COTMOD-003 — Close Behaviors

The modal MUST be closable via (a) ESC key, (b) click on backdrop, (c) an explicit close control. On close, focus MUST return to the "Comenzar" trigger button. Closing MUST NOT clear already-saved localStorage data.

- **GIVEN** the modal is open
- **WHEN** the user presses ESC OR clicks the backdrop OR clicks close
- **THEN** the modal is removed from the DOM and scroll is restored
- **AND** focus returns to the "Comenzar" button

### REQ-COTMOD-004 — Progress Indicator

The system SHALL render 3 progress bars (6px height, 4px radius, 8px gap) above the form. Bar states: active = `linear-gradient(90deg,#9949FF,#0E6FD1)`, completed = `#9949FF`, inactive = `#F5EDFF`. The bar matching the current step is active; bars before it are completed; bars after are inactive.

- **GIVEN** the user is on step N (1-3)
- **WHEN** the progress indicator renders
- **THEN** bars 1..N-1 are purple (#9949FF), bar N is gradient, bars N+1..3 are inactive (#F5EDFF)

### REQ-COTMOD-005 — Step Navigation Forward

The system SHALL advance one step when the current step passes validation and the user clicks the step action button. Buttons MUST be full-width, 44px height, gradient 144deg #9949FF→#0E6FD1, 16px radius, Syne Bold 16px/20px white. Disabled state MUST apply opacity 0.3.

- **GIVEN** the user is on step N with invalid required fields
- **WHEN** the user attempts to advance
- **THEN** the button stays disabled (opacity 0.3) and no navigation occurs
- **GIVEN** all required fields on step N are valid
- **WHEN** the user clicks the action button
- **THEN** step N+1 is shown and the progress bar updates

### REQ-COTMOD-006 — Step Navigation Back

The system SHALL allow backward navigation without losing entered data. The system SHALL NOT re-validate on going back.

- **GIVEN** the user is on step 2 or 3
- **WHEN** the user activates the back control
- **THEN** the previous step renders with all previously entered values intact

### REQ-COTMOD-007 — Step 1 Layout ("Contanos sobre vos")

Step 1 SHALL render title "Contanos sobre vos" (Syne Bold 38px/44px, #111111) and a 2×2 grid (20px gap) of text inputs: Nombre, Mail, Teléfono, Localidad. Inputs: white bg, 1px #E7E7E7 border, 14px radius, 9px 14px padding. Labels: Exo 2 SemiBold 12px/18px #464646, 4px above. Placeholders: Exo 2 Regular 14px/22px #767676. Action button label: "Continuar".

- **GIVEN** step 1 is visible
- **WHEN** the user inspects the form
- **THEN** the 2×2 grid of 4 inputs renders with the specified styling and labels

### REQ-COTMOD-008 — Step 2 Layout ("Datos del auto")

Step 2 SHALL render title "Datos del auto" and a 2×2 grid of text inputs: Año, Marca, Modelo, Kilómetros (same input style as step 1). It SHALL include a radio group "¿Tiene o tuvo GNC?" with 3 options in a row (space-between): "Sí, tiene instalado GNC", "No, pero tuvo anteriormente", "No, nunca tuvo GNC". Radio control: 20×20 SVG. Option text: Exo 2 Regular 14px/22px #111111. Group label: Exo 2 SemiBold 12px/18px #464646. Action button label: "Continuar".

- **GIVEN** step 2 is visible
- **WHEN** the user views step 2
- **THEN** 4 text inputs and a 3-option GNC radio group render with the specified styling

### REQ-COTMOD-009 — Step 3 Layout ("Peritaje y sucursal")

Step 3 SHALL render title "Peritaje y sucursal" and subtitle "Recordá que la inspección es **gratuita y sin compromiso de venta**" (Exo 2 SemiBold 18px/26px #464646, the bolded phrase in #9949FF). Gap title→fields: 32px. It SHALL include: day pills labelled "Día" (Lun, Mar, Mie, Jue, Vie — 36px height, white bg, 1px #9949FF border, 14px radius, text #9949FF), time pills labelled "Horario" (Mañana, Tarde), a single hardcoded location ("RN9 Km 554, X5900 Villa María, Córdoba"), a 240px×14px-radius static map with pin + tooltip, and a notice block (#E7F1FA bg, 14px radius, ic_info + text #0E6FD1 12px/18px). Action button label: "Enviar".

- **GIVEN** step 3 is visible
- **WHEN** the user views step 3
- **THEN** day pills, time pills, location, static map, and notice render with the specified styling

### REQ-COTMOD-010 — Day/Time Pill Selection

Exactly one day and one time MUST be selectable. Selected pills SHALL indicate active state (filled #9949FF background, white text). Pills are mutually exclusive within their group.

- **GIVEN** step 3 is visible
- **WHEN** the user selects a day pill and a time pill
- **THEN** only one day and one time pill is highlighted in each group

### REQ-COTMOD-011 — Responsive Behavior

Below 660px viewport width, the card MUST shrink to fill the viewport width (with safe horizontal padding) and remain vertically scrollable. The 2×2 input grid MAY collapse to a single column on narrow viewports.

- **GIVEN** the viewport is < 660px wide
- **WHEN** the modal is open
- **THEN** the card adapts to viewport width without horizontal overflow
- **AND** content scrolls vertically

### REQ-COTMOD-012 — Accessibility

The modal MUST: use `role="dialog"`, `aria-modal="true"`, and `aria-labelledby` pointing to the step title; trap focus within the modal while open; restore focus to the trigger on close; expose step changes to assistive tech via `aria-live` on the title region; label every input with a `<label>` (or `aria-label`); render the GNC radio as a `role="radiogroup"` with `role="radio"` items having `aria-checked`.

- **GIVEN** a screen reader user opens the modal
- **WHEN** they tab through the open modal
- **THEN** focus cycles only within modal controls and never reaches the page behind
- **AND** each input is announced with its label

## Domain: cotizador-form-data

### REQ-COTMOD-013 — Form State Shape

The system SHALL maintain a single form state object covering: nombre, mail, telefono, localidad, año, marca, modelo, kilometros, gnc, dia, horario. Step affiliation: step 1 = {nombre, mail, telefono, localidad}; step 2 = {año, marca, modelo, kilometros, gnc}; step 3 = {dia, horario}. The state MUST persist in localStorage under a stable key (e.g. `wecar_cotizador_draft`) and reload on modal reopen within the same browser.

- **GIVEN** the user enters values and closes without submitting
- **WHEN** they reopen the modal
- **THEN** previously entered values are repopulated from localStorage

### REQ-COTMOD-014 — Required-Field Validation

Step 1 required fields SHALL be: nombre, mail, telefono, localidad. Step 2 required: año, marca, modelo, kilometros, gnc. Step 3 required: dia, horario. The "Continuar"/"Enviar" button MUST remain disabled until all required fields for the current step have non-empty, valid values. Empty or invalid fields MUST surface a field-level error message and visual error state.

- **GIVEN** the user leaves a required field empty on step N
- **WHEN** the user blurs the field
- **THEN** the field shows an error state and message
- **AND** the step action button remains disabled

### REQ-COTMOD-015 — Field Format Validation

- mail MUST match a basic email pattern (`^[^\s@]+@[^\s@]+\.[^\s@]+$`)
- telefono MUST contain only digits, spaces, `+`, `-`, `(`, `)` and be 6-20 characters
- año MUST be a 4-digit year between 1900 and current year + 1
- kilometros MUST be a positive integer ≤ 9,999,999
- gnc MUST be one of the 3 defined option values

- **GIVEN** the user enters "abc" in mail
- **WHEN** the field is validated
- **THEN** a format error is shown and the step cannot advance

### REQ-COTMOD-016 — Persistence on Submit

On final step submit ("Enviar"), the system SHALL write the complete form state plus a timestamp and a generated quote id to localStorage under a separate key (e.g. `wecar_cotizador_submissions`) as an array entry, then clear the draft key. After submit, the modal MUST show a confirmation (success message) before closing.

- **GIVEN** all step 3 required fields are valid
- **WHEN** the user clicks "Enviar"
- **THEN** the submission is appended to the submissions array in localStorage
- **AND** the draft is cleared and a success confirmation is shown

### REQ-COTMOD-017 — localStorage Resilience

The system MUST guard all localStorage access in try/catch. If localStorage is unavailable (private mode, quota exceeded), the system MUST still allow the form to function in-memory and MUST surface a non-blocking warning. Submissions MUST NOT be silently lost — if write fails, surface an error to the user.

- **GIVEN** localStorage write throws QuotaExceededError
- **WHEN** the user attempts to submit
- **THEN** an error message is shown and the modal stays open
- **AND** the in-memory state is preserved