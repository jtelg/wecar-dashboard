# WeCar Environments — Permanent Spec

> **Status**: Active. Maintained by the WeCar team.
> **Source of truth**: This file. Update here first, then update AGENTS.md.
> **Last verified**: 2026-07-01

This spec documents every WordPress environment used by the WeCar project. Any new environment MUST be added here. Any access detail change (SSH key, DB password, etc.) MUST trigger a PR that updates this file.

## Summary

| Env | URL | Purpose | Last Synced |
|-----|-----|---------|-------------|
| Production | https://wecar.com.ar | Live traffic, SEO-critical | 2026-07-01 |
| Test | https://test.wecar.com.ar | Staging, design validation | 2026-06-19 |
| SQL Backup | (file on production) | Recovery source | 2026-05-01 |

---

## 1. Production (wecar.com.ar)

### URLs
- Public: `https://wecar.com.ar/`
- Admin: `https://wecar.com.ar/wp-admin/`

### SSH Access
- Host: `ssh.wecar.com.ar`
- Port: `18765` (NOT 22)
- User: `u2131-yaziskitlmmv`
- Key: `~/.ssh/wecar-siteground-fixed` (ED25519, no passphrase)
- SSH alias: `ssh wecar` (configured in `~/.ssh/config`)

```ssh-config
Host wecar
  HostName ssh.wecar.com.ar
  Port 18765
  User u2131-yaziskitlmmv
  IdentityFile ~/.ssh/wecar-siteground-fixed
  IdentitiesOnly yes
```

### Paths
- Webroot: `~/www/wecar.com.ar/public_html/`
- Child theme: `~/www/wecar.com.ar/public_html/wp-content/themes/vehica-child/`
- WP-CLI: `/usr/local/bin/wp`
- PHP: `/usr/local/php82/bin/php-cli` (PHP 8.2.31)

### Database
- Host: `localhost`
- Name: `dbbzno7a6rmoym`
- User: `u8rahn18belgx`
- Password: stored in `wp-config.php` (NOT in repo)
- Table prefix: `wp_`
- Size: ~627 MB

### Software Versions
- WordPress: 6.x (check via `wp core version`)
- Elementor: 4.1.4
- Elementor Pro: 3.35.1
- Vehica theme: 1.0.87
- PHP: 8.2.31

### Elementor CSS Files
- Path: `wp-content/uploads/elementor/css/`
- Pattern: `post-{POST_ID}.css`, `custom-frontend.min.css`, `custom-{widget}.min.css`
- Home page: `post-35463.css` (~115 KB)

### Caching
- WP Rocket: inactive
- SiteGround Optimizer: active
- Dynamic cache: enabled (SiteGround nginx proxy)
- `wp cache flush` works; WP Rocket CLI commands not registered

### Important Gotchas
- Elementor auto-updates between minor versions — verify with `wp plugin get elementor` before any data work
- The home page (post 35463) has 27 `wp_postmeta` rows; restoring only `_elementor_data` is INSUFFICIENT
- Production is SEO-critical; never take it down for more than a few minutes

---

## 2. Test (test.wecar.com.ar)

### URLs
- Public: `https://test.wecar.com.ar/`
- Admin: `https://test.wecar.com.ar/wp-admin/`

### SSH Access
- Same SSH endpoint as production (HostGator shared)
- Same user: `u2131-yaziskitlmmv`
- Same key: `~/.ssh/wecar-siteground-fixed`

### Paths
- Webroot: `~/www/test.wecar.com.ar/public_html/`

### Database
- Host: `localhost`
- Name: `dbijhrsz46exbp`
- User: same as production (`u8rahn18belgx`)
- Table prefix: `wp_`

### Software Versions
- Same as production (cloned from production on 2026-05-01, updated together)

### Important Gotchas
- The test DB was cloned from production BEFORE the home-redesign change. It currently has the NEW home design (post 35463 with new `_elementor_data`).
- Test vehicle data is a subset of production (random sample, ~50 vehicles vs ~100+ in production).
- The test URL may serve stale cache after changes. Always `wp cache flush` after modifications.

---

## 3. SQL Backup (on production server)

### Location
- Path: `~/wecar-db-backup-YYYYMMDD.sql` (one file per backup date)
- Most recent: `~/wecar-db-backup-20260501.sql` (570 MB)
- Format: mysqldump-compatible SQL file

### Source
- Created by HostGator's automated backup system.
- Rotated weekly by HostGator; the most recent file is usually available.

### What's In It
- Full database dump: schema + data.
- All tables: `wp_posts`, `wp_postmeta`, `wp_options`, `wp_users`, etc.
- Includes all Elementor meta keys for every page (this is what makes it useful for recovery).

### What's NOT In It
- Filesystem changes (theme files, plugins, uploads).
- Configuration outside the DB (e.g., `wp-config.php` is part of the dump via `wp_options` autoload, but server-level config is not).

### How to Use It
See `openspec/specs/elementor-data-restoration.md` for the runbook.

---

## Access Verification

Before making changes, verify access:

```bash
# SSH to production
ssh wecar "echo 'Production OK:'; wp --path=~/www/wecar.com.ar/public_html --allow-root core version"

# SSH to test (same host, different path)
ssh wecar "echo 'Test OK:'; wp --path=~/www/test.wecar.com.ar/public_html --allow-root core version"

# Confirm SQL backup exists
ssh wecar "ls -la ~/wecar-db-backup-*.sql"
```

---

## Updating This Spec

When something changes:

1. Update this file (`openspec/specs/environments.md`).
2. Update `wp-content/themes/vehica-child/AGENTS.md` (keep in sync).
3. If Elementor version changes, also update `openspec/specs/elementor-data-restoration.md` (it pins to 4.1.4).
4. Commit in a docs-only PR with title like `docs(env): update SSH key rotation 2026-XX-XX`.
5. If the change is sensitive (e.g., DB password), coordinate out-of-band before the PR.

---

## Why Two Environments (Not Three)

We considered adding a third environment (e.g., a "dev" on localhost) but decided against it because:

- SiteGround shared hosting doesn't support Docker or local development environments.
- A dev would need to replicate the entire WP + Elementor + Vehica stack, which is fragile.
- Two environments (prod + test) is enough to validate changes before production.
- Local development can be done via SSH + `wp-cli` against the test environment.

If we ever migrate to a VPS (e.g., DigitalOcean, Linode), a third "dev" environment can be added.
