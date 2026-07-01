# Elementor Data Restoration — Runbook

> **Status**: Active. Required reading before any Elementor data work.
> **Source of truth**: This file. Update here, then update home-redesign apply-progress.
> **Tested**: 2026-07-01 (home page 35463 recovery)
> **Applies to**: Elementor 4.1.4, WordPress 6.x, PHP 8.2

## The Rule

**Never restore a single Elementor meta key.** Elementor uses 27 `wp_postmeta` rows to render a page correctly. If you only restore `_elementor_data`, the page will render with broken CSS (Elementor regenerates only 639 bytes of CSS instead of ~115 KB).

## Why This Happens

Elementor's CSS file generation looks at multiple meta keys to decide what styles to produce:

| Meta key | What it controls | If missing... |
|----------|------------------|---------------|
| `_elementor_data` | The page structure (sections, columns, widgets) | Page renders blank or with errors |
| `_elementor_page_assets` | Which widget asset libraries to enqueue (swiper, google-maps, etc.) | Widgets render but with broken JS |
| `_elementor_controls_usage` | Which controls are used (typography, color, spacing) | CSS file is too small (~639 bytes) |
| `_elementor_page_settings` | Page-level custom CSS, layout options | Custom styles missing |
| `_elementor_css` | CSS generation timestamp | Elementor doesn't know when to regenerate |
| `_elementor_element_cache` | Cached rendered HTML (performance) | Slower renders |
| Other 21 keys | Edit mode, version, SEO, view counts, etc. | Various edge cases |

**If you only have `_elementor_data`, Elementor thinks the page is minimal and writes a 639-byte CSS file with only the custom CSS from page settings.**

## The 27 Keys (Full List for Home Page 35463)

```
_elementor_edit_mode         (x3 - duplicates from Elementor 4.x)
_edit_lock
_elementor_template_type
_elementor_version
_wp_page_template
_elementor_page_settings
_elementor_data
_edit_last
_yoast_wpseo_content_score
_yoast_wpseo_estimated-reading-time-minutes
_yoast_wpseo_focuskw
_yoast_wpseo_metadesc
_eael_custom_js
_thumbnail_id
_yoast_wpseo_meta-robots-noindex
_yoast_wpseo_meta-robots-nofollow
_elementor_pro_version
_pys_head_footer
_yoast_wpseo_title
_eael_post_view_count
_yoast_wpseo_linkdex
_elementor_controls_usage   ← CRITICAL for CSS file size
_elementor_page_assets      ← CRITICAL for CSS file size
_elementor_css              ← CRITICAL for CSS file size
_elementor_element_cache    ← CRITICAL for CSS file size
```

The exact 27 keys vary per page, but the structure is the same. The 4 marked CRITICAL are the ones that most affect CSS rendering.

---

## The 5-Step Recovery Procedure

Use this when:
- A page renders with broken CSS after a partial restoration.
- A page's `post-{ID}.css` file is suspiciously small (< 10 KB for a typical page).
- Elementor data was restored from a JSON file that only contained `_elementor_data`.

### Step 1: Identify the broken page

```bash
# SSH to production
ssh wecar

# Check the CSS file size for the affected page
ls -la ~/www/wecar.com.ar/public_html/wp-content/uploads/elementor/css/post-{POST_ID}.css

# Expected: > 10 KB for a typical page, > 50 KB for the home
# Broken: < 2 KB
```

If the file is < 2 KB, proceed to Step 2.

### Step 2: Locate the SQL backup

```bash
# List all SQL backups on the server
ls -la ~/wecar-db-backup-*.sql

# Should show files like:
# -rw-r--r--  570995150  ~/wecar-db-backup-20260501.sql
```

Pick the most recent backup that predates the broken state. The backup file is ~570 MB.

### Step 3: Extract the wp_postmeta rows for the affected post

Copy this Python script to the server and run it. It will:
- Read the SQL backup (in chunks to save memory)
- Find all `wp_postmeta` rows where `post_id = YOUR_POST_ID`
- Generate a restore SQL file

```python
#!/usr/bin/env python3
"""Extract wp_postmeta rows for a specific post from a SQL dump."""
import re
import sys

def find_insert_lines(sql_path, start_marker='CREATE TABLE `wp_postmeta`'):
    """Find line numbers of wp_postmeta INSERT statements."""
    with open(sql_path, 'r', encoding='utf-8', errors='ignore') as f:
        for line_num, line in enumerate(f, 1):
            if start_marker in line:
                yield line_num
                return
    return

def extract_post_rows(sql_path, post_id, output_path, start_line, end_line):
    """Extract all rows for a post_id from the wp_postmeta block."""
    with open(sql_path, 'r', encoding='utf-8', errors='ignore') as f:
        # Seek to start of wp_postmeta block
        for _ in range(start_line - 1):
            f.readline()
        block = f.readline()
        for line_num in range(start_line + 1, end_line + 1):
            line = f.readline()
            block += line
            if line.strip().endswith(';'):
                # End of an INSERT statement
                # Search for the post_id in this block
                rows = []
                pattern = re.compile(r'\((\d+),' + str(post_id) + r",'([^']+)'")
                for match in pattern.finditer(block):
                    meta_key = match.group(2)
                    # Find the row boundaries
                    pos = match.start()
                    # Find the start of the row (look backwards for '(')
                    row_start = block.rfind('(', 0, pos)
                    if row_start == -1:
                        continue
                    # Find the end of the row (matching ')')
                    paren_depth = 0
                    in_quote = False
                    quote_char = None
                    j = row_start
                    while j < len(block):
                        c = block[j]
                        if c in ("'", '"') and (j == 0 or block[j-1] != '\\'):
                            if not in_quote:
                                in_quote = True
                                quote_char = c
                            elif c == quote_char:
                                in_quote = False
                                quote_char = None
                        elif c == '(' and not in_quote:
                            paren_depth += 1
                        elif c == ')' and not in_quote:
                            paren_depth -= 1
                            if paren_depth == 0:
                                row_text = block[row_start:j+1]
                                rows.append(row_text)
                                break
                        j += 1
                # Generate SQL
                if rows:
                    with open(output_path, 'w', encoding='utf-8') as out:
                        out.write(f"-- Restore post {post_id} meta from backup\n")
                        out.write(f"DELETE FROM `wp_postmeta` WHERE `post_id` = {post_id};\n")
                        out.write(f"INSERT INTO `wp_postmeta` VALUES\n")
                        out.write(',\n'.join(rows))
                        out.write(';\n')
                    print(f"Extracted {len(rows)} rows for post {post_id}")
                    print(f"SQL written to {output_path}")
                block = ''

if __name__ == '__main__':
    post_id = sys.argv[1] if len(sys.argv) > 1 else '35463'
    sql_path = sys.argv[2] if len(sys.argv) > 2 else '/home/{USER}/wecar-db-backup-20260501.sql'
    output_path = sys.argv[3] if len(sys.argv) > 3 else f'/tmp/{post_id}-restore.sql'

    # Find the start of wp_postmeta in the SQL file
    # Typical: line 1471 (CREATE TABLE) to ~1829 (last INSERT)
    # Verify these with: grep -n "CREATE TABLE \`wp_postmeta\`" file.sql
    start_line = 1471
    end_line = 1829

    extract_post_rows(sql_path, post_id, output_path, start_line, end_line)
```

Usage:

```bash
# Find the wp_postmeta block in the SQL backup
ssh wecar 'grep -n "CREATE TABLE \`wp_postmeta\`" ~/wecar-db-backup-20260501.sql'
# Output: 1471:CREATE TABLE `wp_postmeta` (

# Note: the line range may differ for your backup. Adjust the script.

# Run the extraction
scp extract-postmeta.py wecar:/tmp/
ssh wecar "python3 /tmp/extract-postmeta.py 35463 ~/wecar-db-backup-20260501.sql /tmp/35463-restore.sql"
# Output:
# Extracted 27 rows for post 35463
# SQL written to /tmp/35463-restore.sql
```

### Step 4: Apply to production

```bash
# Import the restore SQL
ssh wecar "wp db import /tmp/35463-restore.sql --path=/home/u2131-yaziskitlmmv/www/wecar.com.ar/public_html --allow-root"
# Output: Success: Imported from '/tmp/35463-restore.sql'.

# Delete the stale CSS file
ssh wecar "rm /home/u2131-yaziskitlmmv/www/wecar.com.ar/public_html/wp-content/uploads/elementor/css/post-{POST_ID}.css"

# Clear Elementor cache via PHP (wp eval-file)
cat > /tmp/clear-el-cache.php << 'EOF'
<?php
define('ABSPATH', '/home/u2131-yaziskitlmmv/www/wecar.com.ar/public_html/');
require ABSPATH . 'wp-load.php';
\Elementor\Plugin::instance()->files_manager->clear_cache();
wp_cache_flush();
echo "Done\n";
EOF
scp /tmp/clear-el-cache.php wecar:/tmp/
ssh wecar "php /tmp/clear-el-cache.php"
# Output: Done

# Flush WordPress cache
ssh wecar "wp cache flush --path=/home/u2131-yaziskitlmmv/www/wecar.com.ar/public_html --allow-root"
# Output: Success: The cache was flushed.

# Trigger a page load to regenerate CSS
ssh wecar "curl -s -o /dev/null -w '%{http_code}' https://wecar.com.ar/"
# Output: 200
```

### Step 5: Validate

```bash
# Check the CSS file size
ssh wecar "wc -c /home/u2131-yaziskitlmmv/www/wecar.com.ar/public_html/wp-content/uploads/elementor/css/post-{POST_ID}.css"
# Expected: > 100000 (100 KB) for the home page
# If < 10000 (10 KB), something is still wrong — re-run from Step 3

# Check the page HTML
ssh wecar "curl -s https://wecar.com.ar/ | head -100"
# Verify: the page has all sections, the HTML structure is correct

# Check the CSS file content (should NOT be just custom CSS)
ssh wecar "head -30 /home/u2131-yaziskitlmmv/www/wecar.com.ar/public_html/wp-content/uploads/elementor/css/post-{POST_ID}.css"
# Should have: .elementor-{POST_ID} .elementor-element-XXX { ... } rules
# NOT just: /* Start custom CSS */ .financiacion img { width: 110px; }
```

---

## Worked Example: Home Page Recovery (2026-07-01)

**Context**: The home page (post 35463) was restored from a local JSON file that only contained `_elementor_data`. The page rendered with the correct HTML content but no CSS — sections stacked vertically, colors missing, layout broken.

**Diagnosis**:
- The CSS file `post-35463.css` was 639 bytes (just custom CSS).
- The expected size was ~115 KB.
- Root cause: the other 26 `wp_postmeta` rows were missing, so Elementor didn't know which widget assets to enqueue.

**Fix applied** (5 steps above):
1. Identified the broken page (CSS file 639 bytes).
2. Located the SQL backup (`~/wecar-db-backup-20260501.sql`, 570 MB).
3. Extracted 27 `wp_postmeta` rows for post 35463 using the Python script.
4. Imported the SQL, deleted the stale CSS file, cleared Elementor cache.
5. Validated: CSS file regenerated to 115,910 bytes, page renders correctly.

**Time taken**: ~2 hours of investigation + 5 minutes for the fix.

---

## How to Test the Runbook

You MUST test this runbook at least once per quarter to ensure it still works:

1. On the TEST environment, pick a non-critical page (e.g., create a draft page with some content).
2. Delete one of its `wp_postmeta` rows to simulate the bug.
3. Run the 5 steps to restore it.
4. Verify the CSS file is the expected size.
5. Delete the test page.

If the test fails, update this runbook with the fix.

---

## Alternative Recovery Methods

If the SQL backup is not available or too old:

1. **Restore from Elementor revisions**: Elementor keeps page revisions in `wp_posts` with `post_type=revision`. Find the last good revision and restore it via `wp post meta update`.
2. **Recreate the page from scratch**: Use the Elementor editor to rebuild it (last resort, takes hours).
3. **Use a previous Elementor export**: If someone exported the page as a JSON template, you can import it via Elementor > Templates > Import.

The SQL backup method is the most reliable because it restores all 27 meta keys at once.

---

## Why the Local JSON Backup Is Not Enough

The `openspec/changes/{name}/backups/_elementor_data-{POST_ID}.json` file in the repo is the OUTPUT of `wp post meta get 35463 _elementor_data`. It contains only the JSON value of one meta key.

If you restore from this file:
- ✅ The page structure is restored.
- ❌ The other 26 meta keys are still missing.
- ❌ The CSS file is too small.
- ❌ The page renders without styles.

**This is a known limitation of single-key backups.** The full backup MUST come from the SQL dump.

If you need a portable backup, you must dump ALL meta keys for the post:

```bash
wp db query "SELECT * FROM wp_postmeta WHERE post_id = 35463" --path=... --allow-root > 35463-all-meta.txt
```

This is what the runbook does automatically, using the SQL backup as source.

---

## Related Specs

- `openspec/specs/environments.md` — the three environments and their access details
- `openspec/specs/elementor-css-validation.md` — the CSS size check that catches this bug
- `wp-content/themes/vehica-child/AGENTS.md` — quick reference for the runbook
