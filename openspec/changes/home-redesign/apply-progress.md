# Home Redesign — Apply Progress

## Status: Phase 3 Complete — Ready for Phase 4

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
