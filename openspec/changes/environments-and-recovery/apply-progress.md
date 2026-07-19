# Environments and Recovery — Apply Progress

## Status: All Phases Complete ✅

### Completed Tasks

#### Phase 1: Environment Inventory ✅
- T001: SSH to production verified (`ssh wecar` works)
- T002: wp-cli commands work on production
- T003: test.wecar.com.ar reachable, serves content
- T004: SQL backup located (`~/wecar-db-backup-20260501.sql`, 570 MB)
- T005–T007: Environment details documented
- T008: `openspec/specs/environments.md` created with full inventory

#### Phase 2: Elementor Runbook ✅
- T009: Identified all 27 `wp_postmeta` keys for home page 35463
- T010: Role of each meta key documented
- T011: 5-step recovery procedure written
- T012: Worked example (2026-07-01 home recovery) added
- T013: "Why this happens" explanation added
- T014: "How to test the runbook" section added

#### Phase 3: CSS Validation Gate ✅
- T015: CSS file size validation rule defined
- T016: Thresholds documented (< 1 KB CRITICAL, < 10 KB WARNING, > 10 KB PASS)
- T017: Copy-pasteable shell snippet included
- T018: SDD apply/verify integration documented

#### Phase 4: Update Existing Artifacts ✅
- T019: `openspec/config.yaml` updated:
  - Fixed hosting info (HostGator details)
  - Added Elementor gotchas to `apply` rules
  - Added CSS validation to `verify` rules
- T020: `wp-content/themes/vehica-child/AGENTS.md` updated:
  - Replaced "NO staging" with full environment table
  - Added link to runbook
  - Added CSS validation rule
  - Added "NEVER work directly in production" rule
- T021: `openspec/changes/home-redesign/spec.md` updated:
  - Added REQ-HOME-017 (Post-restore CSS Validation)
  - Added NFR-HOME-009 (Post-restore CSS File Size)
  - Updated Rollback Procedure with full runbook reference
- T022: `openspec/changes/home-redesign/apply-progress.md` updated:
  - Added "Phase 6: Recovery" section with full incident log
  - Documented root cause, fix, and lessons learned

#### Phase 5: Engram Memory ✅
- T023: Lesson saved to Engram (topic key: `sdd/elementor-restore-lesson`)
- T024: Environment inventory saved to Engram (topic key: `sdd/wecar-environments`)
- T025: Engram search verified

#### Phase 6: Commit and Validate ✅
- T026: All changes ready to commit
- T027: New specs discoverable via grep
- T028: AGENTS.md changes accurate
- T029: CSS file size on production verified (115,910 bytes for home page 35463)
- T030: Change ready for archive

### Files Created

- `openspec/changes/environments-and-recovery/proposal.md` (195 lines)
- `openspec/changes/environments-and-recovery/spec.md` (165 lines)
- `openspec/changes/environments-and-recovery/design.md` (165 lines)
- `openspec/changes/environments-and-recovery/tasks.md` (60 lines)
- `openspec/changes/environments-and-recovery/apply-progress.md` (this file)
- `openspec/changes/environments-and-recovery/verify-report.md`
- `openspec/specs/environments.md` (175 lines)
- `openspec/specs/elementor-data-restoration.md` (350+ lines)
- `openspec/specs/elementor-css-validation.md` (150+ lines)

### Files Modified

- `openspec/config.yaml` — added Elementor gotchas, CSS validation rules, correct hosting info
- `wp-content/themes/vehica-child/AGENTS.md` — added environment table, runbook reference, CSS rule
- `openspec/changes/home-redesign/spec.md` — added REQ-HOME-017 and NFR-HOME-009
- `openspec/changes/home-redesign/apply-progress.md` — added Phase 6: Recovery section

### Total Changes
- 9 new files (~1,500 lines)
- 4 modified files (~200 lines added)
- 0 lines of code changed
- 0 database changes
- 0 production changes
