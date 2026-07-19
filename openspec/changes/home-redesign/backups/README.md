# Home 35463 — Backup Artifacts

## Files

| File | Description |
|------|-------------|
| `_elementor_data-35463.json` | Full `_elementor_data` meta for page 35463 (test environment) |
| `home-35463-page-template.txt` | `_wp_page_template` value for page 35463 (test) |
| `prod/_elementor_data-35463.json` | Full `_elementor_data` meta for page 35463 (production) |
| `prod/home-35463-page-template.txt` | `_wp_page_template` value for page 35463 (production) |

## Checksums (SHA-256)

| File | SHA-256 |
|------|---------|
| `_elementor_data-35463.json` | `F7E771FFCD96F72B0DC1634721D72A25EFA9F537DEF688160EF988572B2ED219` |
| `prod/_elementor_data-35463.json` | `F7E771FFCD96F72B0DC1634721D72A25EFA9F537DEF688160EF988572B2ED219` |

## Integrity

- Test and production `_elementor_data` are **identical** (132,566 bytes).
- Both parse as valid JSON.
- Both use template: `default`.

## Rollback

To restore the original home page:

```bash
# Test environment
wp post meta update 35463 _elementor_data --format=json < backups/_elementor_data-35463.json --path=/home/u2131-yaziskitlmmv/www/test.wecar.com.ar/public_html --allow-root

# Production environment
wp post meta update 35463 _elementor_data --format=json < backups/prod/_elementor_data-35463.json --path=/home/u2131-yaziskitlmmv/www/wecar.com.ar/public_html --allow-root

# Flush cache
wp cache flush --path=/home/u2131-yaziskitlmmv/www/test.wecar.com.ar/public_html --allow-root
wp cache flush --path=/home/u2131-yaziskitlmmv/www/wecar.com.ar/public_html --allow-root
```
