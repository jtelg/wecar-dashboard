# Environments and Recovery — Proposal

## Intent

Document the exact differences between WeCar's three environments (production, test, full DB backup), codify the lessons learned from the 2026-07-01 home-recovery incident, and prevent recurrence of the Elementor CSS file generation bug that broke the home page for ~6 hours.

## Business Problem

On 2026-07-01, the production home (wecar.com.ar) rendered without CSS after a partial restoration. Root cause: when restoring a page from the redesign's local Elementor JSON backup (which only contains `_elementor_data`), Elementor regenerated the per-page CSS file with only 639 bytes (custom CSS) instead of the expected 115 KB (full widget CSS). The missing piece was the other 26 `wp_postmeta` rows for that post — particularly `_elementor_page_assets`, `_elementor_controls_usage`, and `_elementor_element_cache` — which Elementor uses to decide what to put in the CSS file.

The fix required extracting 27 meta rows from a 570 MB SQL backup file (`wecar-db-backup-20260501.sql`) using a Python parser, importing them via `wp db import`, clearing Elementor cache, and triggering a page load to regenerate CSS. Total time: ~2 hours of investigation.

This change ensures:
1. The environments (prod / test / backup) are clearly documented with their access details, DBs, and what each is for.
2. The restoration procedure is documented as a reusable runbook (not improvised every time).
3. Any future SDD change that touches Elementor pages MUST verify CSS file size as a quality gate.
4. The home-redesign change is retroactively corrected (specs and apply-progress updated to reflect the recovery).

## Target Users

- Devs working on WeCar: need to know which environment to touch and how to recover.
- Future AI agents: need the runbook in OpenSpec so the bug doesn't repeat.
- SiteGround admin: needs the environment inventory if migrating or restoring.

## Product Outcome

A permanent, version-controlled runbook for:
- Environment access (SSH, DB credentials, paths).
- Elementor page restoration (the exact 5-step procedure that works).
- CSS validation gate (post-deploy check that catches the bug before users see it).
- Updated home-redesign artifacts with the recovery documented.

## Current State Gap

- `openspec/config.yaml` says `no staging environment` — wrong, test.wecar.com.ar exists and was used.
- `wp-content/themes/vehica-child/AGENTS.md` says `**NO staging** — work directly in production` — wrong and dangerous.
- The local backup file `_elementor_data-35463.json` is incomplete (only the JSON value of `_elementor_data`); restoring from it alone is not enough.
- No runbook exists for restoring an Elementor page to a known-good state.
- No automated check for CSS file size after Elementor data changes.

## Scope

### In Scope

- New OpenSpec change `environments-and-recovery` with proposal, spec, design, tasks.
- Permanent spec `openspec/specs/environments.md` (environment inventory).
- Permanent spec `openspec/specs/elementor-data-restoration.md` (the runbook).
- Permanent spec `openspec/specs/elementor-css-validation.md` (the quality gate).
- Update `openspec/config.yaml` to correct hosting info, add staging, add Elementor gotchas.
- Update `wp-content/themes/vehica-child/AGENTS.md` with:
  - Correct environment inventory.
  - The recovery runbook summary.
  - The CSS validation rule.
- Update `openspec/changes/home-redesign/spec.md` with REQ-HOME-017 (CSS validation).
- Update `openspec/changes/home-redesign/apply-progress.md` with the recovery phase.
- Save the lesson to Engram for cross-session memory.

### Out of Scope

- Migrating from HostGator to a different host.
- Setting up CI/CD or automated backups beyond what exists.
- Upgrading Elementor to a different major version.
- Reverting the redesign — both environments now run the new design (test) and the original design (prod, restored).

## Approach

**Five-step plan:**

1. **Document environments**: Create `openspec/specs/environments.md` with the three environments (production, test, SQL backup) and their access details.

2. **Document the runbook**: Create `openspec/specs/elementor-data-restoration.md` with the exact 5-step procedure that fixed the bug. This becomes the canonical recovery process.

3. **Document the quality gate**: Create `openspec/specs/elementor-css-validation.md` with a CSS file size sanity check that any future SDD apply/verify phase must run.

4. **Update config + AGENTS.md**: Fix the false "no staging" claim, add the runbook summary and CSS rule to AGENTS.md, add the gotchas to config.yaml.

5. **Update home-redesign artifacts**: Add REQ-HOME-017 to the spec and a recovery section to apply-progress. This makes the home-redesign change self-contained and reproducible.

## Affected Files

### New files

- `openspec/changes/environments-and-recovery/proposal.md` (this file)
- `openspec/changes/environments-and-recovery/spec.md`
- `openspec/changes/environments-and-recovery/design.md`
- `openspec/changes/environments-and-recovery/tasks.md`
- `openspec/changes/environments-and-recovery/apply-progress.md`
- `openspec/changes/environments-and-recovery/verify-report.md`
- `openspec/specs/environments.md`
- `openspec/specs/elementor-data-restoration.md`
- `openspec/specs/elementor-css-validation.md`

### Modified files

- `openspec/config.yaml` — fix hosting info, add Elementor gotchas
- `wp-content/themes/vehica-child/AGENTS.md` — add environment inventory, runbook summary, CSS rule
- `openspec/changes/home-redesign/spec.md` — add REQ-HOME-017
- `openspec/changes/home-redesign/apply-progress.md` — add recovery phase

## Risks

| Risk | Likelihood | Mitigation |
|------|------------|------------|
| Specs are too generic and don't cover future Elementor versions | Medium | Pin to Elementor 4.1.4 (current); add "test on a draft page" step |
| Dev forgets to run CSS validation after applying Elementor data | Medium | Make it an explicit REQ and a tasks.md checkbox, not prose |
| Recovery runbook references specific line numbers or hardcoded paths | Low | Use shell commands with placeholders; reference the SQL backup by relative path |

## Success Criteria

- [ ] `openspec/specs/environments.md` lists all three environments with working access commands (verified during this change).
- [ ] `openspec/specs/elementor-data-restoration.md` has a copy-pasteable 5-step procedure that, if followed on a fresh environment, restores an Elementor page correctly (CSS file > 50 KB).
- [ ] `openspec/specs/elementor-css-validation.md` defines a check that, if the page CSS file is < 10 KB, the change is CRITICAL and must be remediated.
- [ ] `AGENTS.md` no longer says "NO staging" — it has a full environment table.
- [ ] The home-redesign change retroactively documents the recovery in apply-progress.md and adds REQ-HOME-017 in spec.md.
- [ ] Engram has a memory entry for the lesson so future sessions can recall it.

## Open Questions

- Should the CSS validation be a hard fail (block deployment) or a warning (deploy but flag)?
  - Decision: hard fail for changes that modify `_elementor_data`; warning otherwise.
- Should the runbook script be in Bash or PHP?
  - Decision: PHP — runs in WP context, has access to `wp_load.php`, more reliable than WP-CLI for meta operations.
- Should we add a `wp cli` command to WeCar for "restore page from backup"?
  - Out of scope for this change; can be a future improvement.
