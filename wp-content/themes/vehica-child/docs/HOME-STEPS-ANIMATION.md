# Home Steps Animation

The Home steps sequence uses one Web Animations API (WAAPI) playback path for both the viewport trigger and the TEST replay control. Production deployment is explicitly out of scope.

## Scope and DOM contract

| Role | Selector |
|---|---|
| Section | `#wecar-steps` |
| Step 1 | `.elementor-column[data-id="h03c002"]` |
| Step 2 | `.elementor-column[data-id="h03c003"]` |
| Step 3 | `.elementor-column[data-id="h03c004"]` |
| CTA | `.wecar-steps__cta` |

The playback order is Step 1, Step 2, Step 3, then CTA. Step 1 and the CTA enter from `translateY(36px)`; Steps 2 and 3 enter from `translateX(-36px)`.

## Figma timing

Source: [Custer - Wecar Copy, component set `121:7265`](https://www.figma.com/design/HqFEXE4yW79gbzg1Y5tfNh/Custer---Wecar--Copy-?node-id=121-7265)

| Setting | Exact value |
|---|---|
| Duration per target | `1250.1229047775269ms` |
| Gap between targets | `1.0000000474974513ms` |
| Interval | `1251.1229048250243513ms` |
| Easing | `cubic-bezier(0, 0, 0.3, 1)` |
| Total sequence | `5003.4916192526ms` |

Keep the duration and gap as the source constants. Derive target offsets from their sum so automatic and replay playback cannot drift.

## Architecture and trigger

- `playStepsAnimation(source)` is the single animation engine.
- Viewport playback calls `playStepsAnimation('viewport')`.
- The TEST replay button calls `playStepsAnimation('replay')`.
- A real `scroll` event schedules the position check through `requestAnimationFrame`.
- Playback starts when the section top is at or above `75%` of the viewport height.
- There is no automatic position check on page load; restored scroll position alone must not start playback.
- `resize` may remeasure only after a real scroll has enabled the trigger.
- Step 1 has a higher stacking level than the decorative connector, keeping the line behind the item.

## Motion accessibility and TEST QA

Outside TEST, `prefers-reduced-motion: reduce` keeps all targets visible and skips motion. On `test.wecar.com.ar`, a QA-only override permits animation testing even when the browser reports reduced motion.

TEST also provides:

- `Replay Steps Animation (TEST)`, which reuses the production playback function.
- Console messages prefixed with `[WeCar Steps]`.
- `window.wecarStepsDebug()`, which reports trigger position, scroll state, classes, and reveal eligibility without forcing playback.

These controls must remain host-gated and must not appear on production.

## Deploy safety

Deploy only the reviewed child-theme CSS and JavaScript to TEST. Do not modify production and do not clear, regenerate, copy, or restore Elementor CSS or metadata.

Before and after a TEST deploy, `wp-content/uploads/elementor/css/post-35463.css` must remain:

- Size: `10,470` bytes
- SHA-256: `76249e1f3bd3d841bea15d560415abf269c068f26aa0021a817acf6a0e6b7462`

Any mismatch stops the deployment and requires rollback of only the scoped theme assets.

## Verification checklist

- [ ] On reload, all four targets are hidden before a real scroll, without a visible flash.
- [ ] A restored scroll position does not start playback by itself.
- [ ] Resize before the first real scroll does not start playback.
- [ ] Scrolling until the section top crosses the `75%` viewport line starts playback once.
- [ ] The order is Step 1, Step 2, Step 3, CTA with the exact Figma timing and easing above.
- [ ] The TEST replay produces the same sequence as the viewport trigger.
- [ ] The connector line stays behind Step 1.
- [ ] Reduced-motion behavior is accessible outside TEST.
- [ ] Browser console has no animation errors.
- [ ] Elementor size and SHA-256 are unchanged.

## Rollback boundary

Rollback is limited to the Home steps changes in `assets/js/home-animations.js` and `assets/css/home-steps.css`, plus this documentation. Do not revert unrelated Home work, Elementor data, or generated Elementor CSS. Production remains out of scope.
