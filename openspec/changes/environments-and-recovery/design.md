# Environments and Recovery — Design

## Architecture Overview

```
┌─────────────────────────────────────────────────────────────────┐
│                    WeCar Environments                            │
├─────────────────────────────────────────────────────────────────┤
│                                                                  │
│  ┌──────────────────┐    ┌──────────────────┐                   │
│  │  test.wecar.com.ar│    │  wecar.com.ar    │                   │
│  │  (test/staging)   │    │  (production)    │                   │
│  │                   │    │                  │                   │
│  │  - sandbox for    │    │  - real traffic  │                   │
│  │    new designs    │    │  - SEO-critical  │                   │
│  │  - isolated DB    │    │  - isolated DB   │                   │
│  │  - same SSH host  │    │  - same SSH host │                   │
│  └────────┬──────────┘    └────────┬─────────┘                   │
│           │                         │                             │
│           │   ┌─────────────────┐   │                             │
│           └──►│  SSH endpoint   │◄──┘                             │
│               │  ssh.wecar.com.ar│                                 │
│               │  :18765          │                                 │
│               │  user: u2131-...│                                 │
│               └────────┬────────┘                                 │
│                        │                                          │
│                        ▼                                          │
│               ┌─────────────────┐                                │
│               │  ~/www/         │                                │
│               │  ├── wecar.com.ar/    (production)               │
│               │  └── test.wecar.com.ar/ (test)                   │
│               └────────┬────────┘                                 │
│                        │                                          │
│                        ▼                                          │
│   ┌────────────────────────────────────────────┐                │
│   │  ~/wecar-db-backup-20260501.sql (570 MB)   │                │
│   │  Full SQL dump from 2026-05-01.             │                │
│   │  Used as recovery source for page-level    │                │
│   │  Elementor restorations.                    │                │
│   └────────────────────────────────────────────┘                │
│                                                                  │
└─────────────────────────────────────────────────────────────────┘
```

## Data Flow — Elementor CSS File Generation

When Elementor serves a page, the following happens:

```
Browser request for wecar.com.ar/
            │
            ▼
WordPress loads post 35463
            │
            ▼
Elementor reads _elementor_data (meta key)
            │
            ▼
Elementor reads _elementor_page_assets (meta key)
            │ ── lists which widget assets are needed
            │    (e.g., widget-heading, widget-icon-list, swiper, etc.)
            │
            ▼
Elementor reads _elementor_controls_usage (meta key)
            │ ── lists which controls are used
            │    (e.g., typography, padding, color, etc.)
            │
            ▼
Elementor reads _elementor_page_settings (meta key)
            │ ── custom CSS, page-level options
            │
            ▼
Elementor reads _elementor_css (meta key)
            │ ── check if 'time' is fresh
            │    if stale, regenerate
            │
            ▼
Elementor reads _elementor_element_cache (meta key)
            │ ── cached rendered HTML (optional, performance)
            │
            ▼
Elementor generates post-{ID}.css
            │ ── combines:
            │    - Global widget CSS (custom-frontend.min.css, ~55 KB)
            │    - Post-specific widget CSS (post-{ID}.css, ~115 KB for home)
            │    - Custom CSS from page settings (~639 bytes)
            │    - Media queries and responsive rules
            │
            ▼
Browser receives HTML + CSS links
            │
            ▼
Page renders with full styling
```

**Critical insight**: The post-specific CSS file (115 KB for the home) is only generated correctly if ALL these meta keys are present. If only `_elementor_data` is restored, Elementor thinks the page is "minimal" and writes only 639 bytes of custom CSS.

## Recovery Procedure — 5 Steps

When a page renders without correct CSS after a partial Elementor data restoration:

```
Step 1: Identify the broken page
        - curl the page, find <link> tags for post-{ID}.css
        - curl the CSS file, check size

Step 2: Locate the SQL backup
        - /home/{user}/wecar-db-backup-YYYYMMDD.sql on production
        - 570 MB, takes ~1 minute to grep

Step 3: Extract wp_postmeta rows for that post
        - Read lines ~1471-1829 of the SQL file (where wp_postmeta is)
        - Parse the INSERT statements to find rows where post_id = TARGET
        - Generate a restore.sql file with DELETE + INSERT

Step 4: Apply to production
        - wp db import /tmp/{POST_ID}-restore.sql
        - Delete the stale post-{ID}.css file
        - Clear Elementor cache: wp eval '...'
        - Trigger page load to regenerate

Step 5: Validate
        - curl the CSS file, verify size > 10 KB
        - curl the page, verify all sections render
        - Record the recovery in apply-progress.md
```

## File Structure

```
openspec/
├── config.yaml                          # UPDATED: real hosting info
├── changes/
│   ├── home-redesign/                   # UPDATED: add REQ-HOME-017
│   │   ├── spec.md
│   │   ├── apply-progress.md
│   │   └── ...
│   └── environments-and-recovery/       # NEW
│       ├── proposal.md
│       ├── spec.md
│       ├── design.md                    # this file
│       ├── tasks.md
│       ├── apply-progress.md
│       └── verify-report.md
└── specs/                               # NEW permanent specs
    ├── environments.md
    ├── elementor-data-restoration.md
    └── elementor-css-validation.md

wp-content/themes/vehica-child/
└── AGENTS.md                            # UPDATED: env table, runbook link, CSS rule
```

## Why This Architecture

- **Two environments (prod + test)**: enough to validate changes before production without over-engineering (no CI/CD, no ephemeral environments).
- **SQL backup on the server**: enables recovery even if GitHub is down or the repo is corrupted. The backup is rotated manually by HostGator.
- **Specs in `openspec/specs/`**: these are permanent, cross-change knowledge. Unlike `changes/{name}/spec.md` (which is per-change), specs here live forever.
- **CSS validation in OpenSpec, not in the skill**: the SDD skills are generic. WordPress/Elementor specifics belong in the project, not the global skill library.

## Key Decisions

1. **Don't auto-add Elementor logic to the global sdd-apply skill**: the skill is used by other WordPress projects that may not use Elementor. The lesson is project-specific.

2. **Permanent specs, not just change specs**: this knowledge must survive beyond any single change. It's the kind of thing a new dev needs on day 1.

3. **Hard fail on CSS size in apply, not just verify**: catching the bug during apply is faster and cheaper than catching it in verify. But verify is the final gate.

4. **Use existing tools (Python parser, not a custom script)**: the recovery already works with `grep` + `python3` + `wp db import`. No need to over-engineer with a custom PHP class.

5. **Update home-redesign retroactively**: the home-redesign change was archived but incomplete. This change fixes that.

## Trade-offs

- **More documentation work upfront vs fewer incidents later**: yes, the docs take time, but the 2-hour outage cost more.
- **Spec duplication (change spec + permanent spec)**: yes, but the change spec is scoped to one change; the permanent spec is the canonical reference. Different audiences.
- **Manual CSS file size check vs automated**: we don't have CI/CD. Manual is fine for now; can be automated later.
