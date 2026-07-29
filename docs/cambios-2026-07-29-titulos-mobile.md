# Cambios 2026-07-29 — Títulos hero mobile (cotizador y vende-tu-auto)

Deployado y verificado **solo en TEST** (`test.wecar.com.ar`). Desktop intacto.

## 1. Cotizador — hero (`templates/page-cotizador.php`, `assets/css/cotizador.css`)

- **Problema:** el h1 "Estas muy cerca / de vender tu auto" ocupaba 4 líneas en mobile (~368px).
- **Fix:** nuevo `<br class="wecar-cotizador__br--mobile">` después de "de vender" (oculto con `display: none` fuera de ≤767px, `display: inline` en mobile) → el título queda en 3 líneas: "Estas muy cerca" / "de vender" / "tu auto".
- **Font-size mobile:** `clamp(34px, 10vw, 44px)` → `clamp(24px, 8.5vw, 34px)`, line-height 1.14 → 1.18.
- **display:flex del h1 eliminado en mobile:** con flex, los segmentos separados por `<br>` se convierten en flex items anónimos y el salto de línea no se comporta como en flujo normal. Ahora el h1 es block con `align-self: center` (el centrado vertical lo da el padre `__hero-inner`, que ya es flex).
- Wrap verificado midiendo avances del font Syne 700 (opentype.js): "Estas muy cerca" entra en 1 línea en todos los viewports 320–767px con el nuevo clamp.

## 2. Vendé tu auto — hero (`assets/css/vende-tu-auto.css`)

- **Problema:** el h1 "COTIZÁ GRATIS Y LO VENDEMOS MIENTRAS LO SEGUÍS USANDO" ocupaba 6 líneas en mobile.
- **Fix:** font-size mobile `clamp(34px, 10vw, 42px)` → `clamp(26px, 8vw, 32px)` → 4 líneas balanceadas ("COTIZÁ GRATIS Y" / "LO VENDEMOS" / "MIENTRAS LO" / "SEGUÍS USANDO") en 320–430px.

## 3. Home — header del carrusel (`assets/css/home-carousel.css`)

- **Problema:** "Ver todos →" caía a una fila propia debajo del título en mobile (Elementor apila sus columnas al 100% de ancho), quedando flotando en el hueco violeta.
- **Primera iteración (descartada por el usuario):** forzar `flex-wrap: nowrap` y poner el link a la derecha centrado contra el título. Se revirtió.
- **Versión final (≤768px):** se mantiene el link en su propia fila como el diseño original, pero:
  - Título a 26px/34 (antes 28/36) con columna al 100% + `max-width: 340px` → corta "Encontrá tu próximo / auto" (a 26px el primer segmento mide ~293px; a 28px medía 316px y no entraba ni a ancho completo en 360px de viewport).
  - `margin-bottom` de la sección header 32px → 16px → "Ver todos" más cerca de las cards.
  - Padding interno de las columnas del header a 0 (recupera los 20px que comía el gap default de Elementor).
  - `padding-top` de la sección `#wecar-carousel` 80px → 40px en mobile → el título más cerca del top.

## 4. Drift local↔TEST corregido

- TEST tenía `padding-top: calc(64px + env(safe-area-inset-top, 0px))` en los bloques mobile de ambas páginas (cambio no volcado al repo local). Se incorporó al local antes de deployar para no revertirlo. Ambos archivos locales ahora son idénticos a TEST salvo los cambios de este documento.

## Deploy (mismo procedimiento que 2026-07-27)

1. Backup remoto en `/home/u2131-yaziskitlmmv/wecar-deploy-backups/mobile-titles-<timestamp>/` (ruta exacta en `tmp/remote-check/last-backup.txt`).
2. `scp` de los 3 archivos + `chmod 644`.
3. SHA-256 local = remoto = servido por HTTPS (verificado en los 2 CSS; el template se verificó buscando la clase nueva en el HTML servido).
4. Purge de Dynamic Cache por socket (`msg:OK`).
