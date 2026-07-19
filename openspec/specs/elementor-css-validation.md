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

For the Home page (post 35463), expected size depends on the page architecture and environment. Redesigned TEST is approximately 10,470 bytes; legacy production is approximately 115,910 bytes. Never compare one design/environment against the other.

---

## Why This Rule Exists

On 2026-07-01, the legacy production Home (post 35463) rendered without CSS after a partial restoration. Its CSS file was 639 bytes instead of its same-design baseline of approximately 115 KB. This rule catches the bug at apply time, not at user-visible time.

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

Example expected output for the legacy production Home (post 35463); do not use this as the redesigned TEST baseline:

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
| Home - redesigned TEST | 35463 | ~10,470 bytes |
| Home - legacy production | 35463 | ~115,910 bytes |
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


---

## TEST Deploy Incident: Generated CSS vs Theme Asset Cache (2026-07-16)

### Evidence and root causes

- The approximately **115,910-byte** `post-35463.css` baseline belongs to the legacy production Home. Redesigned TEST legitimately generates a **10,470-byte** file; the archived Figma task used an approximately 10 KB observation and a greater-than-1-KB integrity gate.
- The two environments contain different Elementor page architectures: TEST `_elementor_data` is approximately 17,400 bytes and uses the redesigned `h01...` IDs, while production legacy data is approximately 111,486 bytes and uses unrelated IDs.
- Generated CSS size is meaningful only against a baseline for the same page, design, and environment. **NEVER compare generated CSS size across different Elementor architectures or use production legacy size as the TEST target.**
- Home custom assets used the static child-theme version (`?ver=1.1`). That URL could remain cached by browsers or a CDN after deployment, so clients could keep executing stale CSS or animation code.

### Two cache operations that MUST stay separate

**Theme asset deployment/cache flush** updates files under `wp-content/themes/vehica-child/assets/`. Home CSS and JS use per-file `filemtime()` URL versions. A normal deploy may copy only reviewed theme files and run the ordinary WordPress object-cache flush required by the environment.

**Elementor generated CSS regeneration** changes files under `wp-content/uploads/elementor/css/` and depends on the page's Elementor metadata. **NEVER run Elementor CSS clearing or regeneration as a generic theme-only deploy step.** Copying theme CSS/JS and running `wp cache flush` alone must not modify generated Elementor CSS.

### Mandatory TEST deployment gates

Before deploying Home theme assets:

1. Record the TEST-specific size and checksum for `wp-content/uploads/elementor/css/post-35463.css`. Compare only with the known-good redesigned TEST baseline, currently 10,470 bytes.
2. Record the exact child-theme asset paths and local checksums. Do not include Elementor metadata or generated CSS in a theme-only deployment.
3. Preserve or identify a scoped backup of the child-theme files being replaced.

After deploying:

1. Confirm remote child-theme checksums match local files and verify their URLs contain the new per-file modification-time version.
2. Re-check the TEST generated CSS checksum. A theme-only deployment must not change `post-35463.css` or trigger Elementor regeneration.
3. Load TEST with a cold browser cache and verify layout, console/network responses, and the complete section-2 animation sequence.
4. If a visual break is reported, first isolate the deployed child-theme assets and compare them with the scoped backup. Do not regenerate Elementor CSS as a first response.

### Safe recovery and rollback

- For stale or faulty Home CSS/JS, roll back only the scoped child-theme files, then verify their `filemtime()` URLs and perform a cold-cache browser check. Do not touch Elementor data or generated CSS.
- **NEVER copy production generated CSS or the full legacy production metadata into redesigned TEST.** They represent different page architectures and would overwrite the redesign rather than repair it.
- If Elementor metadata truly requires restoration, follow `elementor-data-restoration.md` and restore the complete metadata set - not only `_elementor_data` - from a known-good snapshot of the **same design and environment**.
- Preserve the current CSS, metadata evidence, timestamps, sizes, and checksums before recovery. Do not overwrite evidence with repeated regeneration attempts.
