# Home Redesign — Apply Progress

## Status: Phase 6 Complete — Change Archived (with recovery documentation)

### Completed Tasks
- **Phase 1** (PR #1: feat/redesign-base): 8 commits ✅
  - T001: Backup script created
  - T002: Home 35463 backed up in test
  - T003: Home 35463 backed up in production
  - T004: Backup integrity verified (SHA-256 match, valid JSON)
  - T005: CSS design tokens (tokens.css)
  - T006: home-animations.js (Intersection Observer)
  - T007: functions.php updated (enqueue CSS/JS)
  - T009: WeCar logo extracted from production

- **Phase 2** (PR #2: feat/redesign-sections): 9 commits ✅
  - T008: Vehicle carousel shortcode ([wecar_vehicle_carousel])
  - T010: 7 section CSS files + Elementor JSON (7 sections, 33 widgets)

- **Phase 3** (PR #3: feat/redesign-apply-test): 3 commits ✅
  - T011: Applied new _elementor_data to test.wecar.com.ar
  - T012: Cache flush in test
  - T013: Validated render — all 7 sections, vehicle carousel, CSS/JS loading

- **Phase 4** (PR #4: feat/redesign-prod): Production migration ✅
  - T018–T023: Production deployment (see archive-report.md for details)

- **Phase 5** (Archive): PASS WITH WARNINGS ✅
  - Verify report: 16/16 REQs met, 8/8 NFRs met, 3 warnings (KM data, partner logos, PNG logo)
  - Archive report: change closed

- **Phase 6** (2026-07-01): Recovery after production rollback ✅
  - See "Phase 6: Recovery" section below for the full incident log

### Issues Found & Fixed
1. **PHP TypeError**: `html_tag` setting caused `strtolower(): Argument #1 must be of type string, array given` — removed `html_tag` from section settings and heading widgets
2. **Icon format**: Elementor 4.x requires `selected_icon` object format (not string `icon` field)
3. **Nav Menu**: `nav-menu` widget fails when referenced menu doesn't exist — replaced with text-editor widget with HTML links
4. **title_size**: Icon-box widget expects HTML tag string (e.g., "h2"), not CSS size object
5. **Missing taxonomy**: Test DB lacks `vehica_41301` — made shortcode status filter optional

### Files Created/Modified
- `openspec/changes/home-redesign/backups/backup-home.sh` (new)
- `openspec/changes/home-redesign/backups/_elementor_data-35463.json` (new)
- `openspec/changes/home-redesign/backups/home-35463-page-template.txt` (new)
- `openspec/changes/home-redesign/backups/prod/_elementor_data-35463.json` (new)
- `openspec/changes/home-redesign/backups/prod/home-35463-page-template.txt` (new)
- `openspec/changes/home-redesign/backups/README.md` (new)
- `wp-content/themes/vehica-child/assets/css/tokens.css` (new)
- `wp-content/themes/vehica-child/assets/js/home-animations.js` (new)
- `wp-content/themes/vehica-child/functions.php` (modified)
- `wp-content/themes/vehica-child/assets/images/logo-wecar.png` (new)
- `wp-content/themes/vehica-child/assets/images/vehicle-placeholder.svg` (new)
- `wp-content/themes/vehica-child/includes/shortcodes/wecar-vehicle-carousel.php` (new)
- `wp-content/themes/vehica-child/assets/css/home-header.css` (new)
- `wp-content/themes/vehica-child/assets/css/home-hero.css` (new)
- `wp-content/themes/vehica-child/assets/css/home-steps.css` (new)
- `wp-content/themes/vehica-child/assets/css/home-carousel.css` (new)
- `wp-content/themes/vehica-child/assets/css/home-features.css` (new)
- `wp-content/themes/vehica-child/assets/css/home-partners.css` (new)
- `wp-content/themes/vehica-child/assets/css/home-footer.css` (new)
- `openspec/changes/home-redesign/elementor/home-35463-new.json` (new)

### Commits Created
**PR #1 (feat/redesign-base):** 8 commits
1. `chore(home-redesign): add backup script for home 35463`
2. `chore(home-redesign): backup home 35463 in test environment`
3. `chore(home-redesign): backup home 35463 in production environment`
4. `chore(home-redesign): verify backup integrity`
5. `feat(home-redesign): add CSS design tokens (colors, typography, spacing)`
6. `feat(home-redesign): add home-animations.js with Intersection Observer`
7. `feat(home-redesign): enqueue new CSS/JS in functions.php`
8. `feat(home-redesign): extract wecar logo from current site`

**PR #2 (feat/redesign-sections):** 9 commits
1. `feat(home-redesign): add wecar vehicle carousel shortcode`
2. `feat(home-redesign): add CSS for header section`
3. `feat(home-redesign): add CSS for hero dual section`
4. `feat(home-redesign): add CSS for 3 pasos section`
5. `feat(home-redesign): add CSS for vehicle carousel section`
6. `feat(home-redesign): add CSS for elegi wecar section`
7. `feat(home-redesign): add CSS for marcas asociadas section`
8. `feat(home-redesign): add CSS for footer section`
9. `feat(home-redesign): build new _elementor_data JSON for home 35463`

**PR #3 (feat/redesign-apply-test):** 3 commits
1. `feat(home-redesign): fix Elementor JSON for compatibility with Elementor 4.x`
2. `fix(home-redesign): make vehicle carousel status filter optional`
3. `feat(home-redesign): apply new home to test.wecar.com.ar`

### Current Branch
`feat/redesign-apply-test` (20 commits ahead of `feat/redesign-sections`)

### Next Steps
- Phase 4 (PR #4: feat/redesign-prod): Apply to production wecar.com.ar

---

## Phase 6: Recovery — 2026-07-01 (Added after incident)

### Incident Summary

After the redesign was applied to production (Phase 4) and the change was archived (Phase 5), the home page on production (wecar.com.ar) needed to be reverted to the original design. The user requested restoration of the original home.

**Initial (incorrect) restoration**:

```bash
# Only restored _elementor_data, not the other 26 meta keys
ssh wecar "wp eval 'update_post_meta(35463, \"_elementor_data\", json_decode(file_get_contents(\"...\")));' --path=... --allow-root"
ssh wecar "wp cache flush --path=... --allow-root"
```

**Result**: The page rendered with the correct HTML content but **no CSS** — sections stacked vertically, colors missing, layout broken.

### Root Cause

Elementor stores page data in **27 different `wp_postmeta` rows**, not just `_elementor_data`. When we restored only one, Elementor's CSS file generation produced only 639 bytes (just the custom CSS from page settings) instead of the expected ~115 KB (full widget CSS).

**The 4 critical meta keys for CSS generation**:

| Meta key | What it does |
|----------|--------------|
| `_elementor_data` | Page structure (sections, columns, widgets) |
| `_elementor_page_assets` | Lists which widget asset libraries to enqueue |
| `_elementor_controls_usage` | Lists which controls are used (controls the CSS output) |
| `_elementor_css` | CSS generation timestamp and state |

Without these, Elementor thinks the page is minimal and writes a 639-byte CSS file.

### Detection

User reported the home page was "sin CSS" (without CSS). Investigation:
- `wc -c post-35463.css` returned **639 bytes** (expected: ~115 KB)
- The CSS file content was only custom CSS, no widget styles
- The HTML structure was correct (proved `_elementor_data` was restored)

### Fix Applied (5 steps)

1. **Located the SQL backup** on the production server:
   ```bash
   ssh wecar "ls -la ~/wecar-db-backup-*.sql"
   # /home/u2131-yaziskitlmmv/wecar-db-backup-20260501.sql (570 MB)
   ```

2. **Found the wp_postmeta block** in the SQL backup:
   ```bash
   ssh wecar 'grep -n "CREATE TABLE \`wp_postmeta\`" ~/wecar-db-backup-20260501.sql'
   # Line 1471: CREATE TABLE
   # Line 1488-1829: INSERT statements
   ```

3. **Extracted the 27 meta rows** for post 35463 using a Python script (see `openspec/specs/elementor-data-restoration.md` for the full script):
   ```python
   # Reads lines 1471-1829 of the SQL file
   # Parses INSERT statements to find rows where post_id=35463
   # Generates a restore.sql file with DELETE + INSERT
   # Result: 27 rows extracted
   ```

4. **Applied to production**:
   ```bash
   ssh wecar "wp db import /tmp/35463-restore.sql --path=/home/u2131-yaziskitlmmv/www/wecar.com.ar/public_html --allow-root"
   # Success: Imported from '/tmp/35463-restore.sql'.
   
   ssh wecar "rm /home/u2131-yaziskitlmmv/www/wecar.com.ar/public_html/wp-content/uploads/elementor/css/post-35463.css"
   # Deleted the stale CSS file
   
   ssh wecar "php /tmp/clear-el-cache.php"
   # Elementor\Plugin::instance()->files_manager->clear_cache()
   # Elementor CSS cache cleared
   
   ssh wecar "wp cache flush --path=/home/u2131-yaziskitlmmv/www/wecar.com.ar/public_html --allow-root"
   # Success: The cache was flushed.
   
   ssh wecar "curl -s -o /dev/null -w '%{http_code}' https://wecar.com.ar/"
   # 200
   ```

5. **Verified**:
   ```bash
   ssh wecar "ls -la /home/u2131-yaziskitlmmv/www/wecar.com.ar/public_html/wp-content/uploads/elementor/css/post-35463.css"
   # -rw-r--r--  1 user user  115910  Jul  1 01:37  post-35463.css
   ```
   The CSS file regenerated to **115,910 bytes** (115 KB) — full widget CSS. ✅

### Lessons Learned

1. **An Elementor page is 27 meta keys, not 1.** Any rollback or restoration MUST restore all of them.
2. **The local JSON backup in the repo (`_elementor_data-35463.json`) is NOT enough for restoration.** It only contains one meta key. Use the SQL backup.
3. **CSS file size is a quality gate.** Always check `wc -c post-{POST_ID}.css` after any Elementor data change. < 1 KB = broken.
4. **The SDD apply phase must catch this bug.** Future Elementor changes MUST validate CSS file size as part of the apply/verify workflow.
5. **This recovery procedure must be documented in a permanent spec, not buried in apply-progress.** Future devs and AI agents need to find it quickly.

### Remediation

This incident triggered a new SDD change: **`environments-and-recovery`** (2026-07-01). It creates:
- `openspec/specs/environments.md` — inventory of the 3 environments
- `openspec/specs/elementor-data-restoration.md` — the full runbook
- `openspec/specs/elementor-css-validation.md` — the CSS file size quality gate
- Updates to `openspec/config.yaml` and `AGENTS.md`
- An Engram memory entry with topic key `sdd/elementor-restore-lesson`

### Time Metrics

- Detection: ~10 minutes (user reported, dev investigated)
- Diagnosis: ~45 minutes (identified the CSS file size mismatch, traced to missing meta keys)
- Fix: ~5 minutes (extracted and applied the 27 meta rows)
- Documentation: ~1 hour (created the runbook and the new change)
- **Total**: ~2 hours of investigation and remediation
