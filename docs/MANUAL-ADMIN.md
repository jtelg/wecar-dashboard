# Manual del Administrador — WeCar

**Versión:** 1.0  
**Fecha:** Julio 2026  
**Sitio:** https://wecar.com.ar

---

## Índice

1. [Introducción](#1-introducción)
2. [El Sitio Web](#2-el-sitio-web)
3. [El Cotizador (Vendé tu Auto)](#3-el-cotizador-vendé-tu-auto)
4. [Flujo Completo de Datos](#4-flujo-completo-de-datos)
5. [Google Sheets — Dónde se guardan los datos](#5-google-sheets--dónde-se-guardan-los-datos)
6. [Dashboard NSM — Panel de Control](#6-dashboard-nsm--panel-de-control)
7. [Preguntas Frecuentes](#7-preguntas-frecuentes)
8. [Contacto Técnico](#8-contacto-técnico)

---

## 1. Introducción

**WeCar** es una plataforma de compra y venta de autos con presencia en Villa María, Córdoba. Este manual explica cómo funciona el sitio web y el sistema de cotizaciones (el formulario "Vendé tu auto").

El sitio está desarrollado sobre **WordPress** con el plugin **Elementor** para el diseño visual, y conexiones automáticas con **n8n** (un sistema de automatización), **Google Sheets** y **correo electrónico**.

No necesitás saber programación para entender este manual ni para operar el sitio día a día.

---

## 2. El Sitio Web

### 2.1 Páginas principales

| Página | URL | Descripción |
|--------|-----|-------------|
| Home | `wecar.com.ar` | Página principal con el buscador de autos, características de WeCar, marcas asociadas |
| Vendé tu auto | `wecar.com.ar/vende-tu-auto` | Página informativa + acceso al cotizador |
| Cotizador | `wecar.com.ar/cotizador` | Página dedicada al formulario de peritaje |
| Anuncios | `wecar.com.ar/anuncios/` | Listado de vehículos en venta |

### 2.2 Estructura visual

El sitio usa la identidad de marca WeCar:

- **Colores principales:** Violeta (#9949FF) · Azul (#0E6FD1) · Celeste (#0EB5D1)
- **Tipografía:** Syne (títulos) · Exo 2 (textos) · Inter (footer)
- **Logo:** Logo WeCar en el encabezado y footer

El diseño es responsive: funciona correctamente en celulares, tablets y computadoras.

### 2.3 ¿Cómo se administran los anuncios de autos?

Los vehículos se administran desde el menú **Anuncios** en el panel de WordPress.

Cada anuncio tiene campos clave:

- **Origen del vehículo:** PROPIO (autos de WeCar) / PARTNER (concesionarias) / PARTICULAR (vendedores particulares)
- **Propietario:** La concesionaria o persona dueña del auto
- **Estado:** ACTIVO (a la venta) / VENDIDO / RETIRADO
- **Fecha de publicación:** Se setea automáticamente al crear el anuncio
- **Fecha de baja:** Se setea automáticamente al pasar a VENDIDO o RETIRADO

---

## 3. El Cotizador (Vendé tu Auto)

### 3.1 ¿Para qué sirve?

El cotizador es un formulario en línea donde una persona interesada en **vender su auto** puede:
1. Contar sus datos de contacto
2. Describir su vehículo
3. Pedir un turno para peritaje (inspección gratuita)

### 3.2 ¿Dónde está?

Hay dos formas de acceder:

- Desde el menú del sitio → **Vendé tu auto**
- Directo en: `wecar.com.ar/cotizador`

### 3.3 ¿Cómo funciona el formulario?

Es un formulario en **3 pasos** con diseño moderno:

**Paso 1 — Datos personales:**
- Nombre
- Email
- Teléfono
- Localidad

**Paso 2 — Datos del vehículo:**
- Año
- Marca
- Modelo
- Kilómetros
- GNC (¿tiene/tuvo/nunca?)

**Paso 3 — Peritaje:**
- Día preferido (lunes a viernes)
- Horario (mañana o tarde)

➡️ Al enviar, el sistema **guarda los datos automáticamente** y envía notificaciones.

### 3.4 Criterios de calificación

Antes del formulario, la página muestra una sección interactiva con los **criterios de calificación**:

| Criterio | Detalle |
|----------|---------|
| 🕐 Año del vehículo | 2010 en adelante |
| 🚗 Autos y SUVs | Hasta 120.000 km |
| 🛻 Pick-Ups | Hasta 150.000 km |

Si el auto no cumple, se ofrece contacto por WhatsApp como alternativa.

---

## 4. Flujo Completo de Datos

Cuando un usuario completa y envía el formulario, esto es lo que pasa **automáticamente**:

```
Usuario completa el formulario
        │
        ▼
    WordPress recibe los datos
        │
        ▼
    Envía los datos a n8n (sistema de automatización)
        │
        ├──→ Google Sheets — Guarda la cotización en una planilla
        │
        ├──→ Email al ADMIN — jtelgarecz@gmail.com
        │     Asunto: "Nuevo cotizador - {Nombre del cliente}"
        │
        └──→ Email al USUARIO — al mail que puso en el formulario
              Asunto: "Recibimos tu cotización - WeCar"
```

### 4.1 ¿Qué datos se guardan?

Todos estos datos quedan registrados:

| Campo | Dónde se guarda |
|-------|----------------|
| Nombre | Google Sheets + Email admin |
| Email | Google Sheets + Email admin (y se usa para responderle) |
| Teléfono | Google Sheets + Email admin |
| Localidad | Google Sheets + Email admin |
| Marca, Modelo, Año | Google Sheets + Email admin |
| Kilómetros | Google Sheets + Email admin |
| GNC | Google Sheets + Email admin |
| Día y horario de peritaje | Google Sheets + Email admin |
| ID único | Google Sheets |
| Fecha y hora del envío | Google Sheets |

### 4.2 ¿Cada cuánto se procesa?

**En tiempo real.** Apenas el usuario completa el formulario, los datos llegan a Google Sheets y los emails se disparan automáticamente. No hay demoras ni procesos manuales.

---

## 5. Google Sheets — Dónde se guardan los datos

### 5.1 Ubicación de la planilla

Todas las cotizaciones se almacenan en una planilla de Google Sheets:

- **Nombre:** COTIZACIONES (wecar.com.ar)
- **Cuenta:** jtelgarecz@custer.com.ar
- **Enlace:** https://docs.google.com/spreadsheets/d/1uEb4N94f6lKcFAf-qZ5qJk6KcNQS5CwrK4hDYZ_ZQSI/edit

### 5.2 Columnas de la planilla

| Columna | Contenido |
|---------|-----------|
| ID | Identificador único (timestamp) |
| Fecha | Fecha del envío |
| Email | Correo del usuario |
| Nombre | Nombre completo |
| Telefono | Número de contacto |
| Localidad | Ciudad/localidad |
| Año | Año del vehículo |
| Marca | Marca del auto |
| Modelo | Modelo específico |
| KM | Kilómetros recorridos |
| GNC | Instalado / Tuvo / Nunca |
| Día | Día de peritaje seleccionado |
| Horario | Mañana / Tarde |

### 5.3 ¿Cómo se agregan los datos?

**Automáticamente.** No necesitás hacer nada. Cada vez que alguien completa el formulario en la web, una nueva fila se agrega sola a esta planilla.

### 5.4 ¿Puedo editar la planilla?

Sí, podés agregar columnas extra para **notas internas**, estados de seguimiento, etc. El sistema solo escribe en las columnas existentes y no modifica ni borra nada que agregues manualmente.

---

## 6. Dashboard NSM — Panel de Control

### 6.1 ¿Qué es el NSM?

El **NSM (North Star Metric)** es una métrica clave que mide el porcentaje de stock de terceros (concesionarias asociadas + vendedores particulares) sobre el total de vehículos activos.

```
NSM = (autos de Partners + Particulares) / Total de activos × 100
```

**Target:** 75% → que el 75% del stock sea de terceros.

### 6.2 ¿Dónde está el dashboard?

En el panel de administración de WordPress → menú **WeCar NSM**.

### 6.3 Solapas del dashboard

| Solapa | ¿Qué muestra? |
|--------|--------------|
| **WeCar NSM** | Panel principal con NSM, mix de stock, resumen de partners y particulares |
| **Partners** | Detalle de cada concesionaria: autos activos, vendidos, retirados, días promedio |
| **Particulares** | Métricas de vendedores particulares con tasa de conversión |
| **Histórica** | Evolución día a día de las métricas |
| **Administrar Datos** | Listado para agregar/editar partners, particulares y propios |
| **Ayuda** | Guía de referencia (la misma info que acá) |

### 6.4 ¿Qué significan las columnas?

#### Panel Principal (WeCar NSM)

| Indicador | Significado |
|-----------|-------------|
| NSM | Porcentaje del stock que es de terceros (Partners + Particulares) |
| Stock Propio | Autos de WeCar (concesionaria propia) |
| Stock Partners | Autos de concesionarias asociadas |
| Stock Particulares | Autos de vendedores particulares |
| Total Activos | Total de autos a la venta |
| Altas / Bajas | Autos publicados / vendidos o retirados en el mes |

#### Partners

| Columna | Significado |
|---------|-------------|
| Autos Activos | Autos de ese partner actualmente a la venta |
| Vendidos | Autos que se vendieron |
| Retirados | Autos que se retiraron sin vender |
| Días Prom. | Promedio de días que tardan en venderse (calculado sobre autos VENDIDOS) |
| Estado | **Activo** (venden en menos de 60 días) o **Baja rotación** (más de 60 días) |

#### Particulares

| Indicador | Significado |
|-----------|-------------|
| Tasa de Conversión | `Vendidos / (Vendidos + Retirados) × 100` — mide qué tan efectivo es el canal |

### 6.5 ¿Cómo administrar partners, particulares y propios?

Desde **WeCar NSM → Administrar Datos** podés:

1. **Agregar:** Hacé click en "Agregar nuevo", escribí el nombre y Publicá
2. **Editar:** Hacé click en "Editar" al lado del nombre
3. **Ver vehículos asignados:** El listado muestra cuántos autos tiene cada entidad

> ⚠️ **Importante:** Cada entidad se crea UNA SOLA VEZ. Después aparece como opción en el editor de anuncios.

### 6.6 Filtro por fecha

En el panel principal y en las solapas de Partners, Particulares e Histórica podés filtrar por rango de fechas usando los presets de 7, 30 o 90 días, o seleccionando fechas personalizadas.

---

## 7. Preguntas Frecuentes

### ¿Qué hago cuando alguien completa el cotizador?

Te va a llegar un email automático a **jtelgarecz@gmail.com** con todos los datos. También podés consultar la planilla de Google Sheets. Lo que sigue es contacto comercial: llamar al cliente, coordinar el peritaje, etc.

### ¿El usuario recibe alguna confirmación?

Sí. Apenas envía el formulario, recibe un email automático a la dirección que indicó confirmando que su cotización fue recibida y que un asesor se va a contactar.

### ¿Puedo agregar más personas para que reciban los emails?

Sí, contactando al equipo técnico. Se puede configurar para que llegue a más direcciones.

### ¿Dónde están los datos guardados?

En Google Sheets (cuenta de jtelgarecz@custer.com.ar) y en los emails que se envían automáticamente.

### ¿Cada cuánto se actualiza el dashboard?

Los números del panel principal se actualizan al instante cuando cambiás un anuncio. La vista **Histórica** se actualiza una vez por día automáticamente (durante la madrugada).

### ¿Puedo borrar una cotización de la planilla?

Sí, podés borrar filas manualmente en Google Sheets. No afecta al sistema ni a los emails ya enviados.

### ¿Qué pasa si el sitio deja de funcionar?

El cotizador depende del sitio web funcionando. Si el sitio está caído, el formulario no se puede enviar. En ese caso:
1. Contactá al equipo técnico para restaurar el sitio
2. Una vez restaurado, todos los datos que se perdieron durante la caída no se recuperan automáticamente

### ¿Cómo agrego una nueva concesionaria (partner)?

1. Andá a **WeCar NSM → Administrar Datos** en WordPress
2. En la sección "Partners", hacé click en **Agregar nuevo**
3. Escribí el nombre de la concesionaria y Publicá
4. Después, al editar un anuncio, ese partner aparece en el dropdown "Propietario"

### ¿Qué significa "Baja rotación" en el dashboard?

Significa que los autos de ese partner tardan más de 60 días en venderse en promedio. Podría ser una señal para revisar precios o condiciones.

---

## 8. Contacto Técnico

Si encontrás un problema o necesitás cambios en el sistema, contactá al equipo de desarrollo:

| Canal | Detalle |
|-------|---------|
| **Soporte técnico** | jtelgarecz@custer.com.ar |
| **Hosting/Servidor** | SiteGround — ssh.wecar.com.ar |

### Información técnica (para referencia interna)

| Ítem | Detalle |
|------|---------|
| CMS | WordPress + Elementor |
| Hosting | SiteGround |
| Automatización | n8n (bot.custer.com.ar) |
| Base de datos | Google Sheets |
| Correo saliente | SMTP no-reply@multicars.ar |
| Repositorio | GitHub (desarrollo) |

---

© 2026 WeCar — Todos los derechos reservados.
