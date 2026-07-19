#!/bin/bash
# =============================================================================
# backup-home.sh — Backup _elementor_data and _wp_page_template for home 35463
# =============================================================================
# Usage:
#   ./backup-home.sh test          → backup test.wecar.com.ar home 35463
#   ./backup-home.sh prod          → backup wecar.com.ar home 35463
#   ./backup-home.sh test prod     → backup both environments
#
# Requirements:
#   - SSH alias "wecar" configured in ~/.ssh/config
#   - WP-CLI available on the remote server
# =============================================================================

set -euo pipefail

BACKUP_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
TIMESTAMP=$(date +%Y%m%d-%H%M%S)

do_backup() {
    local env="$1"
    local wp_path=""

    if [ "$env" = "test" ]; then
        wp_path="/home/u2131-yaziskitlmmv/www/test.wecar.com.ar/public_html"
        echo "🔹 Backing up test.wecar.com.ar (home 35463) ..."
    elif [ "$env" = "prod" ]; then
        wp_path="/home/u2131-yaziskitlmmv/www/wecar.com.ar/public_html"
        echo "🔹 Backing up wecar.com.ar (home 35463) ..."
    else
        echo "❌ Unknown environment: $env. Use 'test' or 'prod'."
        exit 1
    fi

    local target_dir="${BACKUP_DIR}"
    if [ "$env" = "prod" ]; then
        target_dir="${BACKUP_DIR}/prod"
    fi

    # Export _elementor_data as JSON
    echo "   Exporting _elementor_data ..."
    ssh wecar "HOME=/home/u2131-yaziskitlmmv WP_CLI_CACHE_DIR=/dev/null wp post meta get 35463 _elementor_data --format=json --path=${wp_path} --allow-root" \
        > "${target_dir}/_elementor_data-35463.json" 2>/dev/null

    # Export _wp_page_template
    echo "   Exporting _wp_page_template ..."
    ssh wecar "HOME=/home/u2131-yaziskitlmmv WP_CLI_CACHE_DIR=/dev/null wp post meta get 35463 _wp_page_template --path=${wp_path} --allow-root" \
        > "${target_dir}/home-35463-page-template.txt" 2>/dev/null

    # Verify files
    if [ -s "${target_dir}/_elementor_data-35463.json" ]; then
        local size
        size=$(wc -c < "${target_dir}/_elementor_data-35463.json")
        echo "   ✅ _elementor_data-35463.json saved (${size} bytes)"
    else
        echo "   ❌ _elementor_data-35463.json is empty or missing!"
        exit 2
    fi

    if [ -s "${target_dir}/home-35463-page-template.txt" ]; then
        echo "   ✅ home-35463-page-template.txt saved"
    else
        echo "   ⚠️  _wp_page_template is empty — file saved anyway"
    fi

    echo "   ✅ Backup complete for ${env}"
}

# ── Main ──────────────────────────────────────────────────────────
if [ $# -eq 0 ]; then
    echo "Usage: $0 [test] [prod]"
    echo "Examples:"
    echo "  $0 test         — backup test environment only"
    echo "  $0 test prod    — backup both environments"
    exit 0
fi

for env in "$@"; do
    do_backup "$env"
done

echo ""
echo "✅ All backups completed successfully."
