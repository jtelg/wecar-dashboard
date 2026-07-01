# Environments and Recovery — Spec

## Requirements

### REQ-ENV-001: Environment Inventory (MUST)

The project MUST maintain a permanent spec `openspec/specs/environments.md` documenting every WordPress environment used by the project, including at minimum:

- URL (public, admin)
- SSH access (host, port, user, key path)
- Webroot path
- Database name, user, and table prefix
- Elementor version installed
- WordPress version installed
- Vehicle theme (vehica) version
- Child theme path
- Last known data sync date
- Purpose (production traffic, staging, backup, etc.)

The spec MUST be kept in sync with reality. Any change to access details (e.g., SSH key rotation, DB password) MUST trigger a PR that updates the spec.

### REQ-ENV-002: Test Environment Exists (MUST)

A test environment MUST exist for the project so no production changes ship without a prior validation pass. Working directly in production is FORBIDDEN except for emergency hotfixes (which MUST be followed by a retro).

### REQ-ENV-003: Production Backup Available (MUST)

A full SQL dump of the production database MUST be available on the production server (not in the repo, not in a remote only) so it can be used as a recovery source if a partial restore goes wrong. The dump MUST be at most 90 days old at any time; older dumps MAY be archived but MUST NOT be the only recovery source.

### REQ-ENV-004: Elementor Data Restoration Runbook (MUST)

A runbook MUST exist at `openspec/specs/elementor-data-restoration.md` describing how to restore an Elementor page to a known-good state. The runbook MUST include:

1. The list of `wp_postmeta` keys that MUST be restored together (not only `_elementor_data`).
2. The exact commands to extract those keys from a SQL backup and import them.
3. The post-restore validation steps (CSS file size, page render check).
4. A worked example using the 2026-07-01 home-recovery incident.

The runbook MUST be tested at least once per quarter by running it on the test environment against a non-production page.

### REQ-ENV-005: CSS File Size Validation (MUST)

After any change that modifies `_elementor_data` for a post, the post-specific CSS file MUST be validated:

- Path pattern: `wp-content/uploads/elementor/css/post-{POST_ID}.css`
- Expected size: > 10 KB for a typical page (the home page is 115 KB).
- If the file is < 1 KB, the change MUST be treated as CRITICAL — the page is likely missing the other meta keys.

The validation MUST be added as a check in the SDD apply and verify phases for any change tagged with `affects: elementor-data`.

### REQ-ENV-006: AGENTS.md Reflects Reality (MUST)

`wp-content/themes/vehica-child/AGENTS.md` MUST contain:

1. A complete environment table (replaces the "NO staging" claim).
2. A link to the runbook in `openspec/specs/elementor-data-restoration.md`.
3. The CSS validation rule from REQ-ENV-005.
4. A note that working directly in production is FORBIDDEN except for emergency hotfixes.

### REQ-ENV-007: SDD Skills Reference Elementor Gotchas (SHOULD)

The SDD skills (`sdd-apply`, `sdd-verify`) SHOULD have a project-level notes file (`openspec/SKILLS-NOTES.md` or similar) that flags WordPress/Elementor-specific gotchas. At minimum, the notes MUST cover:

- Elementor stores data in many meta keys; restoring only `_elementor_data` is insufficient.
- Elementor generates per-post CSS files in `uploads/elementor/css/`; these are not in the repo.
- Elementor cache must be cleared after any data change: `wp eval 'Elementor\Plugin::instance()->files_manager->clear_cache();'`.

### REQ-ENV-008: Home-Redesign Documents Recovery (MUST)

The `openspec/changes/home-redesign/` change MUST document the 2026-07-01 recovery in:

- `apply-progress.md` — a new "Phase 6: Recovery" section with timestamps, root cause, and the exact commands used.
- `spec.md` — a new requirement `REQ-HOME-017: Post-restore CSS validation` that codifies the lesson.

### REQ-ENV-009: Engram Captures the Lesson (MUST)

A memory entry MUST be saved to Engram with topic key `sdd/elementor-restore-lesson` containing:

- The bug description (Elementor regenerates only 639 bytes of CSS when other meta keys are missing).
- The fix (restore all 27 `wp_postmeta` rows, not only `_elementor_data`).
- The date of the incident (2026-07-01).
- The change that documented it (environments-and-recovery).

This ensures future AI sessions can recall the lesson even if the OpenSpec is not loaded.

## Scenarios

### Scenario 1: Dev restores home page from local JSON backup

**Given** the home page (post 35463) has been changed in production and the dev wants to restore it from `openspec/changes/home-redesign/backups/_elementor_data-35463.json`.

**When** the dev runs only:
```bash
wp post meta update 35463 _elementor_data < openspec/changes/home-redesign/backups/_elementor_data-35463.json
wp cache flush
```

**Then** the home page renders with the right HTML content but with broken CSS — only 639 bytes of CSS instead of ~115 KB.

**And** the dev notices the broken CSS within minutes (per REQ-ENV-005) and follows the runbook in `openspec/specs/elementor-data-restoration.md` to restore the other 26 meta keys from the SQL backup.

### Scenario 2: New dev onboards to the project

**Given** a new dev joins the team and needs to understand the environments.

**When** they read `wp-content/themes/vehica-child/AGENTS.md`.

**Then** they see a clear environment table with URL, SSH, DB, and purpose for each — no need to ask the team.

**And** they see the runbook link and the CSS validation rule before they make their first change.

### Scenario 3: Future SDD change modifies Elementor data

**Given** a future change (e.g., `home-section-update`) modifies `_elementor_data` for post 35463 or any other Elementor page.

**When** the `sdd-apply` phase completes.

**Then** the apply progress MUST include a step that:
- Triggers a page load to regenerate CSS.
- Reads the resulting `post-{POST_ID}.css` file size.
- Records the size in `apply-progress.md`.
- If size < 1 KB, marks the change as CRITICAL and blocks archive.

## Non-Functional Requirements

### NFR-ENV-001: Runbook Discoverability (MUST)

The runbook MUST be discoverable in under 30 seconds by:
- A new dev reading AGENTS.md.
- An AI agent loading the project's OpenSpec.
- A grep for "Elementor restore" or "Elementor CSS" in the repo.

### NFR-ENV-002: Specs Stay in Sync (MUST)

When an environment is created, deleted, or its access details change, the relevant spec MUST be updated in the same PR. The CI check (if any) MUST fail if the spec is out of date.

### NFR-ENV-003: No Sensitive Data in Repo (MUST)

The environment spec MUST NOT contain:
- Real passwords (use placeholders like `<DB_PASSWORD>`).
- Real SSH private keys.
- Real API tokens.

DB credentials and keys MAY be stored in a separate, encrypted location (not in the repo).
