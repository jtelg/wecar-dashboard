# Environments and Recovery — Tasks

## Phase 1: Environment Inventory

- [ ] T001: Verify SSH access to production server (`ssh wecar`)
- [ ] T002: Verify SSH access works for wp-cli commands
- [ ] T003: Confirm test.wecar.com.ar is reachable and serves content
- [ ] T004: Locate the SQL backup file on production (`~/wecar-db-backup-*.sql`)
- [ ] T005: Document production environment details (URL, SSH, DB, Elementor version)
- [ ] T006: Document test environment details (URL, SSH, DB, Elementor version)
- [ ] T007: Document the SQL backup (path, size, date, source)
- [ ] T008: Create `openspec/specs/environments.md` with the inventory

## Phase 2: Elementor Runbook

- [ ] T009: Identify all 27 `wp_postmeta` keys for an Elementor page (using home 35463 as example)
- [ ] T010: Document the role of each meta key (`_elementor_data`, `_elementor_page_assets`, etc.)
- [ ] T011: Write the 5-step recovery procedure in `openspec/specs/elementor-data-restoration.md`
- [ ] T012: Add the worked example (2026-07-01 home recovery) to the runbook
- [ ] T013: Add a "Why this happens" explanation to the runbook
- [ ] T014: Add a "How to test the runbook" section to the runbook

## Phase 3: CSS Validation Gate

- [ ] T015: Define the CSS file size validation rule in `openspec/specs/elementor-css-validation.md`
- [ ] T016: Document the threshold (< 1 KB = CRITICAL, < 10 KB = WARNING)
- [ ] T017: Add a copy-pasteable shell snippet to check CSS size
- [ ] T018: Document where to add this check in the SDD apply/verify phases

## Phase 4: Update Existing Artifacts

- [ ] T019: Update `openspec/config.yaml`:
  - Fix hosting info (HostGator details, test environment exists)
  - Add Elementor gotchas to `apply` rules
  - Add CSS validation to `verify` rules
- [ ] T020: Update `wp-content/themes/vehica-child/AGENTS.md`:
  - Replace "NO staging" claim with full environment table
  - Add link to runbook
  - Add CSS validation rule
  - Add note that production work requires test first
- [ ] T021: Update `openspec/changes/home-redesign/spec.md`:
  - Add REQ-HOME-017: Post-restore CSS validation
  - Add NFR for CSS validation in apply/verify
- [ ] T022: Update `openspec/changes/home-redesign/apply-progress.md`:
  - Add "Phase 6: Recovery" section
  - Document the 2026-07-01 incident
  - List the exact commands used

## Phase 5: Engram Memory

- [ ] T023: Save lesson to Engram with topic key `sdd/elementor-restore-lesson`
- [ ] T024: Save environment inventory to Engram with topic key `sdd/wecar-environments`
- [ ] T025: Verify Engram memory is searchable by querying for "Elementor restore"

## Phase 6: Commit and Validate

- [ ] T026: Commit all changes to a new branch (`docs/environments-and-recovery`)
- [ ] T027: Verify the new specs are discoverable (search for "Elementor" in `openspec/`)
- [ ] T028: Verify the AGENTS.md change is accurate (compare SSH details against actual)
- [ ] T029: Run the CSS validation on production to confirm the home is at > 100 KB
- [ ] T030: Mark change as ready for archive

## Review Workload Forecast

- Estimated changed lines: ~600 (mostly new markdown files, no code changes)
- 400-line budget risk: Low
- Chained PRs recommended: No (single docs PR is fine)
- Decision needed before apply: No
