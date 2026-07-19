# Archive Report: home-hero-polish-2026-07

> **Archived**: 2026-07-06
> **Status**: DELIVERED
> **Verdict**: PASS (verify-report)

---

## Change Summary

| Field | Value |
|-------|-------|
| Change name | `home-hero-polish-2026-07` |
| Proposal date | 2026-07-06 |
| Design date | 2026-07-06 |
| Apply date | 2026-07-06 |
| Verify date | 2026-07-06 |
| Archive date | 2026-07-06 |
| Status | **DELIVERED** |
| Deploy target | `test.wecar.com.ar` |

## Artifacts

### OpenSpec (filesystem)
| Artifact | Path |
|----------|------|
| Proposal | `openspec/changes/archive/2026-07-06-home-hero-polish-2026-07/proposal.md` |
| Spec (delta) | `openspec/changes/archive/2026-07-06-home-hero-polish-2026-07/specs/home/spec.md` |
| Design | `openspec/changes/archive/2026-07-06-home-hero-polish-2026-07/design.md` |
| Tasks | `openspec/changes/archive/2026-07-06-home-hero-polish-2026-07/tasks.md` |
| Verify report | `openspec/changes/archive/2026-07-06-home-hero-polish-2026-07/verify-report.md` |
| Archive report | `openspec/changes/archive/2026-07-06-home-hero-polish-2026-07/archive-report.md` |

### Engram (topic keys)
| Artifact | Topic Key | Observation ID |
|----------|-----------|----------------|
| Proposal | `sdd/home-hero-polish-2026-07/proposal` | — |
| Spec | `sdd/home-hero-polish-2026-07/spec` | — |
| Design | `sdd/home-hero-polish-2026-07/design` | — |
| Tasks | `sdd/home-hero-polish-2026-07/tasks` | — |
| Apply progress | `sdd/home-hero-polish-2026-07/apply-progress` | #1169 |
| Verify report | `sdd/home-hero-polish-2026-07/verify-report` | — |
| Archive report | `sdd/home-hero-polish-2026-07/archive-report` | (this observation) |

## Merged Spec — Baseline Delta Sync

The delta in `specs/home/spec.md` was merged into `openspec/specs/home/spec.md`:

| Operation | REQ | Action |
|-----------|-----|--------|
| **MODIFIED** | REQ-HOME-002 | Replaced static dual-card layout with CSS-driven horizontal accordion (3 states: step-1 50/50, step-2 Comprá expanded, step-3 Vendé expanded). Added collapsed labels, cross-fade timing, overflow hidden. |
| **ADDED** | REQ-HOME-HP01 | Cross-fade text transitions — title/subtitle fade-out on collapse, badge/CTA slide-up on expand, collapsed label delayed fade-in. |
| **ADDED** | REQ-HOME-HP02 | Car image positioning — per-step dimensions (530×270 / 490×250), bottom-right anchor, `right: -10%` override for left panel, overflow hidden. |
| **ADDED** | REQ-HOME-HP03 | Radial-gradient textures — per-panel CSS gradients replacing SVG textures. Purple tones for Comprá, light lavender/white for Vendé. No SVGs. |
| **ADDED** | REQ-HOME-HP04 | Panel content positions — title/subtitle/badge/collapsed label coordinates per Figma, badge repositioning from y:-64 to y:40 on expand. |

## REQs Delivered

| Metric | Count |
|--------|-------|
| REQs evaluated | 5 |
| PASS | 5 |
| FAIL | 0 |
| WARNING | 0 |

## Tasks Completion

| Metric | Value |
|--------|-------|
| Total tasks | 10 |
| Completed | 10/10 |
| Unchecked | 0 |

## Final Diff Statistics

| File | Insertions | Deletions | Lines Changed |
|------|-----------|-----------|---------------|
| `home-hero.css` | 58 | 25 | 83 |
| `home-animations.js` | 8 | 0 | 8 |
| **Total** | **66** | **25** | **91** |

## Deploy Status

| Check | Result |
|-------|--------|
| Target | `test.wecar.com.ar` |
| CSS URL | HTTP 200 (12576 bytes) |
| JS URL | HTTP 200 (1988 bytes) |
| Key selectors verified | 598px, 976px, 220px, radial-gradient, 0.25s delay, collapsed states, mobile guard |
| WP cache flush | Success |
| Elementor cache clear | Success |

## Risks Carried Forward

1. **Browser cache**: `Cache-Control: max-age=31536000` on both CSS/JS files. Users with cached versions may not see changes until hard refresh or cache busting. Recommend adding query param `?v=20260706` to stylesheet/script enqueues.
2. **Diagnostic script**: A manual console script is provided at `/tmp/verify-hero-polish.js` for visual validation. No automated E2E test covers the accordion interaction.

## Follow-ups Proposed

1. **W-2 FontAwesome → SVG migration**: FontAwesome icons remain in hero cards (chevron in CTA). If the design direction is to replace all FA with custom SVGs, this should be a separate SDD change.
2. **Production deploy**: This change is deployed to `test.wecar.com.ar` only. Production deploy should be a separate SDD change with its own verify phase targeting the production URL.
3. **Cache busting**: Add version query param to CSS/JS enqueues to avoid stale cache issues on next deploy.

## Deviations Recorded

| # | Deviation | Rationale | Source |
|---|-----------|-----------|--------|
| 1 | Collapsed label uses `::after` pseudo-element (not separate HTML) | Panel has no dedicated label markup; `::after` avoids DOM changes | apply-progress #1169 |
| 2 | Car uses `width`/`height` instead of `max-width` | Exact Figma pixel control; `object-fit: contain` handles scaling | apply-progress #1169 |
| 3 | Right panel textures combined via comma-separated `background` | Avoids extra pseudo-elements; single `::before` per panel | apply-progress #1169 |
| 4 | Badge starts at `top:40px` (not `y:-64` from Figma coordinate) | Equivalent effect: badge invisible in step-1, visible in expanded state | verify-report |

## Commit

- **Hash**: 98ada2e
- **Branch**: home-test
- **Message**: `feat(hero): polish accordion widths, cross-fade, collapsed labels, textures`

---

*Archive prepared by `sdd-archive` phase. SDD cycle complete for `home-hero-polish-2026-07`.*
