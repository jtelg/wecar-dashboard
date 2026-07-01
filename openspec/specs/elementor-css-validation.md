# Elementor CSS Validation — Quality Gate

> **Status**: Active. Use this check in any SDD apply or verify phase.
> **Source of truth**: This file. Update if the threshold or procedure changes.
> **Applies to**: Elementor 4.1.4, WordPress 6.x

## The Rule

After any change that modifies Elementor data (`_elementor_data` for any post), the post-specific CSS file MUST be validated:

| Path | Pattern |
|------|---------|
| `wp-content/uploads/elementor/css/post-{POST_ID}.css` | Always |

| File size | Severity | Action |
|-----------|----------|--------|
| < 1 KB | **CRITICAL** | The page is missing most of its CSS. Stop the deploy, run the runbook in `openspec/specs/elementor-data-restoration.md`. |
| 1–10 KB | **WARNING** | The page may be using minimal widgets. Review manually to confirm it's expected. |
| > 10 KB | **PASS** | Normal. The page has full widget CSS. |

For the home page (post 35463), the expected size is ~115 KB. A 10 KB threshold is a conservative floor; specific pages may have different expected sizes.

---

## Why This Rule Exists

On 2026-07-01, the home page (post 35463) rendered without CSS after a partial restoration. The CSS file was 639 bytes instead of ~115 KB. This rule catches the bug at apply time, not at user-visible time.

The bug happens when:
- A page's `_elementor_data` is restored from a JSON file that only contains that one meta key.
- The other 26 `wp_postmeta` rows are missing.
- Elementor regenerates the CSS file with only the custom CSS (639 bytes) instead of the full widget CSS.

See `openspec/specs/elementor-data-restoration.md` for the root cause analysis and the recovery procedure.

---

## How to Check (Manual)

```bash
# SSH to the environment
ssh wecar

# Check the CSS file size for the affected page
ls -la ~/www/wecar.com.ar/public_html/wp-content/uploads/elementor/css/post-{POST_ID}.css

# Quick size check (in bytes)
wc -c ~/www/wecar.com.ar/public_html/wp-content/uploads/elementor/css/post-{POST_ID}.css

# Quick size check (human-readable)
du -h ~/www/wecar.com.ar/public_html/wp-content/uploads/elementor/css/post-{POST_ID}.css
```

Expected output for the home page (post 35463):

```
-rw-r--r--  1 user user  115910  Jul  1 00:00  post-35463.css
```

If you see something like:

```
-rw-r--r--  1 user user     639  Jul  1 00:00  post-35463.css
```

→ The page is broken. Run the runbook.

---

## How to Check (Copy-Paste Snippet)

```bash
# Check all page CSS files at once
ssh wecar "for f in ~/www/wecar.com.ar/public_html/wp-content/uploads/elementor/css/post-*.css; do
  size=\$(wc -c < \"\$f\")
  pid=\$(basename \"\$f\" | sed 's/post-//;s/.css//')
  if [ \"\$size\" -lt 1024 ]; then
    echo \"CRITICAL post-\$pid.css: \${size} bytes\"
  elif [ \"\$size\" -lt 10240 ]; then
    echo \"WARNING post-\$pid.css: \${size} bytes\"
  else
    echo \"PASS post-\$pid.css: \${size} bytes\"
  fi
done"
```

---

## Where to Use This in the SDD Workflow

### In `sdd-apply`

After any task that modifies `_elementor_data` for a post:

1. Apply the change.
2. Clear Elementor cache: `wp eval 'Elementor\Plugin::instance()->files_manager->clear_cache();'`.
3. Flush WP cache: `wp cache flush`.
4. Trigger a page load: `curl -s -o /dev/null https://site.url/`.
5. Check the CSS file size for the affected post (snippets above).
6. Record the size in `apply-progress.md`.
7. If size < 1 KB, STOP and report the issue to the orchestrator. Do not proceed to verify.

### In `sdd-verify`

After the apply phase completes:

1. Run the "Check all page CSS files" snippet.
2. All CSS files MUST be > 1 KB.
3. If any is < 1 KB, the change is CRITICAL — the page is broken.
4. Document the result in `verify-report.md`.

### In `sdd-archive`

Before closing the change:

1. Run the "Check all page CSS files" snippet one more time.
2. All CSS files MUST be > 1 KB.
3. If any is < 1 KB, the change CANNOT be archived.

---

## Page-Specific Expected Sizes (Reference)

| Page | Post ID | Expected CSS size |
|------|---------|-------------------|
| Home | 35463 | ~115 KB |
| (other pages) | varies | varies |

For pages not in this table, use the 10 KB threshold as a conservative floor.

---

## What This Check Does NOT Catch

- The CSS is wrong (e.g., wrong colors, wrong layout) but the file size is correct.
- The page renders correctly but some widgets are missing.
- JavaScript errors that prevent widget interactivity.
- Slow page loads due to large CSS files.

For these issues, manual visual validation is still required.

---

## Related Specs

- `openspec/specs/elementor-data-restoration.md` — the recovery runbook
- `openspec/specs/environments.md` — the three environments
- `wp-content/themes/vehica-child/AGENTS.md` — quick reference
