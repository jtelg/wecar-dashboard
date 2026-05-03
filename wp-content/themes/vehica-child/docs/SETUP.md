# WeCar NSM — Setup & Development Guide

## Access

### SSH Connection

```bash
ssh -i /path/to/wecar_key3 -p 18765 -o StrictHostKeyChecking=no u2131-yaziskitlmmv@ssh.wecar.com.ar
```

| Parameter | Value |
|-----------|-------|
| Host | `ssh.wecar.com.ar` |
| Port | `18765` |
| User | `u2131-yaziskitlmmv` |
| Key | `/c/Users/Usuario/AppData/Local/Temp/wecar_key3` |
| Webroot | `~/www/wecar.com.ar/public_html/` |

> ⚠️ The SSH key is in a temp directory. If lost, regenerate from HostGator control panel.

### SCP for File Transfer

```bash
scp -i /path/to/wecar_key3 -P 18765 -o StrictHostKeyChecking=no local/file.php \
  u2131-yaziskitlmmv@ssh.wecar.com.ar:~/www/wecar.com.ar/public_html/wp-content/themes/vehica-child/destination/
```

> ⚠️ Always use SCP for PHP files. PowerShell heredoc (`cat > file << 'EOF'`) will interpret `$variables`.

### WP-CLI

Available on the server. Common commands:

```bash
# Flush cache after changes
wp cache flush --path=~/www/wecar.com.ar/public_html

# List vehicles
wp post list --post_type=vehica_car --path=~/www/wecar.com.ar/public_html
```

---

## Development Workflow

1. **Edit locally** (in `C:\Users\Usuario\Desktop\DEV\MULTICARS\wecar-dashboard\`)
2. **SCP to server** (no staging — direct to production)
3. **Flush WP cache** after changes
4. **Hard refresh browser** (Ctrl+Shift+R) to bypass browser cache

---

## Project Files

| File | Purpose |
|------|---------|
| `functions.php` | Auto-load, fonts, class init |
| `style.css` | Theme header (Template: vehica) |
| `includes/class-wecar-fields.php` | Custom field definitions + auto-save hooks |
| `includes/class-wecar-metrics.php` | Metrics query engine |
| `includes/class-wecar-dashboard.php` | Admin menu + 6 views |
| `includes/class-wecar-partner-cpt.php` | Partner CPT + dropdown JS |
| `dashboard/assets/dashboard.css` | Dashboard styles (v5) |
| `dashboard/assets/partner-select.js` | Partner dropdown (MutationObserver) |
| `dashboard/views/view-main.php` | Main dashboard |
| `dashboard/views/view-partners.php` | Partner detail table |
| `dashboard/views/view-particulares.php` | Private seller metrics |
| `dashboard/views/view-historica.php` | Historical evolution |
| `dashboard/views/view-ayuda.php` | Team help guide |

---

## Troubleshooting

### "Dropdown no aparece en el editor"
- Hard refresh (Ctrl+Shift+R)
- Check that the car's Origen is set to PARTNER
- partner-select.js uses MutationObserver with 15s timeout
- Verify the file exists on server

### "Días Prom. muestra valores raros"
- Only VENDIDO cars count toward promedio
- Active cars do NOT influence the average
- Partners with 0 sales show 0 days

### "PHP heredoc falla al subir"
- PowerShell interprets `$variables` in heredoc
- Always use SCP for PHP file transfer
- Or create file locally and SCP up

### "Cambios no se reflejan"
- Flush WP cache: `wp cache flush --path=~/www/wecar.com.ar/public_html`
- Hard refresh browser
- Check file was uploaded to correct path
