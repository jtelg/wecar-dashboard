# Cambios 2026-07-27 — Polish mobile (hero, steps, cotizador, footer)

Todos los cambios fueron deployados y verificados **solo en TEST** (`test.wecar.com.ar`).
Desktop se preservó intacto salvo donde se indica (compactación del modal, que aplica a todos los viewports).

## 1. Hero home — mobile (`assets/css/home-hero.css`)

- **Pills colapsadas con identidad de marca:** el estado colapsado ocultaba la tarjeta (que era la que tenía el gradiente) y quedaba sin fondo. Ahora la columna colapsada lleva el mismo gradiente de desktop por card (`#F5EDFF→#CAE0F5` Comprá, `#A4E3EE→#0E6FD1` Vendé), textura radial SVG abajo-derecha, `border-radius: 20px` y sombra sutil.
- **Width cerrado = abierto:** reset de `padding` en `.elementor-widget-wrap` (Elementor `column-gap-default` metía 10px por lado en la card expandida).
- **Margen lateral unificado 20px** en contenedor y elementos internos (badge, CTA, padding de card), igual que las secciones del sitio.
- **Label "Comprá/Vendé":** posición y timing compartidos; en estado activo se oculta con el fade mobile (340ms) + `visibility` diferida. Antes heredaba la duración desktop (1.25s) y la posición centrada → quedaba flotando en el centro de la card expandida.
- **Timings unificados:** título/descripción también usan `--wecar-hero-mobile-duration`.
- **Imagen del auto:** `width: 75%` en mobile, con override de la regla desktop de estado activo (50.2%/54.3% de Figma) que ganaba por especificidad al estar fuera del media query. En la card activa, `translate(-50%, 15%)` para que el auto no tape el texto.
- **Offset superior:** `padding-top: 66px` fijo (header ~50px + 16px de aire) y fondo de sección `#F8FAFC` que se extiende bajo el header transparente (elimina la banda blanca muerta y hace visible el efecto transparente→blanco).

## 2. Steps / section_one (`assets/js/home-animations.js`)

- **Fallback de inactividad 3s:** si no hay scroll por 3 segundos y alguna parte de `#wecar-steps` está visible, la animación arranca sola. El timer se reinicia con cada scroll; si la sección no está visible, no revela (el scroll tardío sigue mostrando la animación).
- **Sin flash visible→oculto:** el init corre al ejecutarse el script (deferred, DOM ya parseado) en vez de esperar `window.load`, así el estado oculto se aplica antes del primer paint.

## 3. Cotizador — modal (`assets/css/cotizador-modal.css`, `assets/js/cotizador-modal.js`, `templates/page-cotizador.php`)

- **Causa raíz del botón Enviar invisible (mobile):** el card es `flex-column` con `max-height`; los hijos `__progress`/`__steps` (con `overflow: hidden`) tenían `flex-shrink: 1` → `steps` se encogía y **clipeaba** el contenido excedente sin scroll. Fix: `flex-shrink: 0` en ambos → el card scrollea de verdad (`overflow-y: auto`).
- **`100dvh`:** shell del modal y `max-height` del card usan dynamic viewport height (fallback `100vh`) — la barra de URL del browser ya no empuja el card fuera de la pantalla con el body bloqueado.
- **Compactación mobile (≤660px):** card padding 16px (12px ≤380px), gap 12px, título 26/32 con margen 10px, subtítulo 15/22, gaps body/grilla 12px, error 14px, notice compacto, acciones gap 8px/márgenes 4px, mapa 120px. El paso 3 entra sin scroll; el scroll queda como fallback funcional.
- **Compactación desktop:** gap del card 60→24px, padding 24px 30px, título margen 16px, gaps 14px, acciones margen 8px, mapa 180px. Desktop también entra sin scroll.
- **Kilómetros:** acepta `.` y `,` como separadores de miles (valida rango 1–9.999.999 sobre dígitos normalizados; el envío se guarda normalizado). `inputmode` numeric→decimal para que el teclado mobile muestre separador.

## 4. Footer (`includes/wecar-global-footer.php`, `assets/css/home-footer.css`)

- **Texto nuevo:** "Una plataforma moderna y segura para la compra y venta de vehículos nuevos y usados. Formamos parte de Grupo Le Parc."
- **Rediseño mobile:** stack centrado (logo 180px, descripción centrada max 340px, teléfono como pill lila, copyright 12px atenuado al final), texturas escaladas a 280px/50% opacidad.
- **Bug grid corregido:** los `grid-column`/`grid-row` de desktop persistían en mobile y el teléfono caía en una columna implícita a la derecha. Reset a `auto` en mobile.

## 5. Páginas internas — offset mobile (`assets/css/tokens.css`)

- `padding-top` del body: 80px desktop / **66px mobile** (header ~50px + 16px de aire). Aplica a todas las páginas excepto home, `/vende-tu-auto`, `/cotizador` y `/blog`.

## Hallazgos registrados (no requieren acción inmediata)

- `#wecar-header` y `#wecar-footer` (secciones Elementor del home) están ocultos por JS (`functions.php:334+`); su CSS asociado (`.scrolled` del header home) es código muerto. Header/footer reales: `#wecar-header-global` / `#wecar-footer-global` (PHP, `wp_body_open`/`wp_footer`).
- El toggle `.scrolled` del header funciona en todos los viewports; el efecto no se veía en mobile porque debajo del header todo era blanco (resuelto con el tinte `#F8FAFC` del hero).

## Procedimiento de deploy utilizado (referencia)

1. Backup remoto en `/home/u2131-yaziskitlmmv/wecar-deploy-backups/<nombre>-<timestamp>/`.
2. `scp` del archivo exacto a TEST + `chmod 644`.
3. Verificación de SHA-256 local = remoto = servido por HTTPS.
4. Purge de Dynamic Cache por socket (payload JSON en `nc -U /chroot/tmp/site-tools.sock`, respuesta `msg:OK`).
5. Nunca producción sin pedido explícito.
