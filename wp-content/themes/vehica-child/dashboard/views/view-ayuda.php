<div class="wrap wecar-dashboard">
    <div class="wecar-header">
        <div class="wecar-header-left">
            <h1>WeCar — Ayuda</h1>
            <p class="wecar-subtitle">Guía rápida para el equipo sobre campos y dashboard NSM</p>
        </div>
    </div>

    <div class="wecar-section">
        <h2>🎯 ¿Qué cambió?</h2>
        <p>Agregamos campos nuevos a los anuncios que permiten medir el <strong>NSM (North Star Metric)</strong>:
        el porcentaje de stock de terceros (partners + particulares) sobre el total de activos.</p>
        <p>Todas las entidades — <strong>Partners, Particulares y Propios</strong> — se administran desde <strong>WeCar NSM → Administrar Datos</strong>.</p>
        <p>El campo <strong>"Partner" ahora se llama "Propietario"</strong> e incluye un buscador con todas las entidades según el Origen seleccionado.</p>
    </div>

    <div class="wecar-section">
        <h2>🏢 Partners — Cómo administrarlos</h2>
        <p><strong>Primero:</strong> crear los partners en el listado.</p>
        <ol style="margin:10px 0 10px 24px;">
            <li>Andá a <strong>WeCar NSM → Administrar Datos</strong></li>
            <li>Hacé click en <strong>"Agregar nuevo"</strong></li>
            <li>Escribí el nombre de la concesionaria → <strong>Publicar</strong></li>
        </ol>
        <p style="color:#2e7d32;font-weight:600;">✅ Hacé esto UNA SOLA VEZ por cada concesionaria. Después aparecen en el editor de anuncios como dropdown.</p>
    </div>

    <div class="wecar-section">
        <h2>✏️ Los campos — Cómo usarlos</h2>
        <p>Al editar un anuncio en <strong>Anuncios</strong>, vas a ver estos campos:</p>

        <table class="wecar-table" style="margin-bottom:16px;">
            <thead>
                <tr>
                    <th>Campo</th>
                    <th>Opciones</th>
                    <th>Cuándo usarlo</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><strong>Origen del vehículo</strong></td>
                    <td>PROPIO / PARTNER / PARTICULAR</td>
                    <td>Todos los anuncios actuales están como <strong>PROPIO</strong>. Si es de concesionaria → <strong>PARTNER</strong>. Si es de particular → <strong>PARTICULAR</strong>.</td>
                </tr>
                <tr>
                    <td><strong>Propietario</strong></td>
                    <td>Dropdown con buscador en sidebar</td>
                    <td>Seleccionar la entidad según el Origen elegido. <strong>Primero crearla en Administrar Datos.</strong> Si seleccionás sin Origen, se auto-completa al guardar.</td>
                </tr>
                <tr>
                    <td><strong>Estado del vehículo</strong></td>
                    <td>ACTIVO / VENDIDO / RETIRADO</td>
                    <td>ACTIVO = a la venta, VENDIDO = se vendió, RETIRADO = ya no está disponible</td>
                </tr>
            </tbody>
        </table>

        <div style="background:#f0f7ff;border-left:4px solid #0E6FD1;padding:12px 16px;border-radius:6px;font-size:13px;margin-bottom:10px;">
            🤖 <strong>Automático:</strong> La <strong>fecha de publicación</strong> se setea al crear el anuncio.
            La <strong>fecha de baja</strong> se setea al pasar a VENDIDO o RETIRADO.
            Si volvés a ACTIVO, la fecha de baja se borra sola.
        </div>
    </div>

    <div class="wecar-section">
        <h2>📊 El Dashboard — WeCar NSM</h2>
        <p>Lo encuentran en el menú lateral → <strong>WeCar NSM</strong></p>

        <table class="wecar-table" style="margin-bottom:16px;">
            <thead>
                <tr>
                    <th>Solapa</th>
                    <th>Qué muestra</th>
                </tr>
            </thead>
            <tbody>
                <tr><td><strong>WeCar NSM</strong></td><td>Panel principal: NSM, mix de stock (propios/partners/particulares), resumen de partners</td></tr>
                <tr><td><strong>Partners</strong></td><td>Detalle de cada concesionaria: activos, vendidos, retirados, días promedio</td></tr>
                <tr><td><strong>Particulares</strong></td><td>Métricas de vehículos de particulares</td></tr>
                <tr><td><strong>Histórica</strong></td><td>Evolución de métricas día a día (últimos 90 días)</td></tr>
                <tr><td><strong>Administrar Datos</strong></td><td>Listado unificado para agregar/editar/borrar partners, particulares y concesionarias propias</td></tr>
                <tr><td><strong>Ayuda</strong></td><td>Esta guía</td></tr>
            </tbody>
        </table>
    </div>

    <div class="wecar-section">
        <h2>📖 Columnas del Dashboard — Qué significa cada una</h2>

        <h3 style="margin:16px 0 8px;">Panel Principal (WeCar NSM)</h3>
        <table class="wecar-table" style="margin-bottom:16px;">
            <thead>
                <tr><th>Columna / Indicador</th><th>Qué significa</th></tr>
            </thead>
            <tbody>
                <tr><td><strong>NSM</strong></td><td>North Star Metric: porcentaje del stock que es de terceros (Partners + Particulares) sobre el total de activos. Target: 75%.</td></tr>
                <tr><td><strong>Stock Propio</strong></td><td>Cantidad de vehículos activos marcados como Origen = PROPIO, con su porcentaje sobre el total.</td></tr>
                <tr><td><strong>Stock Partners</strong></td><td>Cantidad de vehículos activos marcados como Origen = PARTNER, con su porcentaje sobre el total.</td></tr>
                <tr><td><strong>Stock Particulares</strong></td><td>Cantidad de vehículos activos marcados como Origen = PARTICULAR, con su porcentaje sobre el total.</td></tr>
                <tr><td><strong>Total Activos</strong></td><td>Total de vehículos en estado ACTIVO. Debajo muestra altas y bajas del <strong>mes actual</strong>.</td></tr>
            </tbody>
        </table>

        <h3 style="margin:16px 0 8px;">Partners (idem en resumen del panel principal)</h3>
        <table class="wecar-table" style="margin-bottom:16px;">
            <thead>
                <tr><th>Columna</th><th>Qué significa</th></tr>
            </thead>
            <tbody>
                <tr><td><strong>Partner</strong></td><td>Nombre de la concesionaria asociada.</td></tr>
                <tr><td><strong>Autos Activos</strong></td><td>Cantidad de vehículos de ese partner que están actualmente a la venta (Estado = ACTIVO).</td></tr>
                <tr><td><strong>Vendidos</strong></td><td>Cantidad de vehículos de ese partner que se vendieron (Estado = VENDIDO).</td></tr>
                <tr><td><strong>Retirados</strong></td><td>Cantidad de vehículos de ese partner que se retiraron sin vender (Estado = RETIRADO).</td></tr>
                <tr><td><strong>Días Prom.</strong></td><td>Promedio de días que los vehículos de ese partner estuvieron publicados <strong>hasta que se vendieron</strong>. Se calcula como: fecha de baja − fecha de publicación, promediado entre todos los vehículos vendidos de ese partner. Si un partner no tiene ventas, muestra 0.</td></tr>
                <tr><td><strong>Estado</strong></td><td><span class="wecar-badge wecar-badge-active">Activo</span> = los vehículos vendidos se vendieron en menos de 60 días promedio. <span class="wecar-badge wecar-badge-warning">Baja rotación</span> = los vehículos vendidos tardaron más de 60 días promedio en venderse. Si un partner solo tiene autos activos (cero ventas), muestra Activo con Días Prom. en 0.</td></tr>
            </tbody>
        </table>

        <h3 style="margin:16px 0 8px;">Particulares</h3>
        <table class="wecar-table" style="margin-bottom:16px;">
            <thead>
                <tr><th>Columna / Indicador</th><th>Qué significa</th></tr>
            </thead>
            <tbody>
                <tr><td><strong>Activos</strong></td><td>Vehículos de particulares actualmente a la venta.</td></tr>
                <tr><td><strong>Vendidos</strong></td><td>Vehículos de particulares que se vendieron.</td></tr>
                <tr><td><strong>Retirados</strong></td><td>Vehículos de particulares que se retiraron sin vender.</td></tr>
                <tr><td><strong>Tasa de Conversión</strong></td><td>Porcentaje de vehículos de particulares que se vendieron sobre el total de operaciones cerradas: <code>Vendidos / (Vendidos + Retirados) × 100</code>. No incluye los activos porque todavía están en juego. Mide qué tan efectivo es el canal. Ejemplo: 5 vendidos + 2 retirados = 5/7 = 71.4%.</td></tr>
            </tbody>
        </table>

        <h3 style="margin:16px 0 8px;">Histórica</h3>
        <table class="wecar-table" style="margin-bottom:16px;">
            <thead>
                <tr><th>Columna</th><th>Qué significa</th></tr>
            </thead>
            <tbody>
                <tr><td><strong>Fecha</strong></td><td>Día en que se tomó la foto de métricas (1:33 AM, vía WP-Cron).</td></tr>
                <tr><td><strong>NSM</strong></td><td>North Star Metric de ese día.</td></tr>
                <tr><td><strong>Total</strong></td><td>Total de vehículos activos ese día.</td></tr>
                <tr><td><strong>Propios</strong></td><td>Cantidad de vehículos PROPIO activos ese día.</td></tr>
                <tr><td><strong>Partners</strong></td><td>Cantidad de vehículos PARTNER activos ese día.</td></tr>
                <tr><td><strong>Part.</strong></td><td>Cantidad de vehículos PARTICULAR activos ese día.</td></tr>
                <tr><td><strong>Vend.</strong></td><td>Cantidad de vehículos vendidos hasta ese día.</td></tr>
                <tr><td><strong>Ret.</strong></td><td>Cantidad de vehículos retirados hasta ese día.</td></tr>
                <tr><td><strong>Conv.</strong></td><td>Tasa de conversión general de ese día.</td></tr>
            </tbody>
        </table>
    </div>

    <div class="wecar-section">
        <h2>📐 Fórmula del NSM</h2>
        <div style="background:#1a1a1a;color:white;padding:20px;border-radius:12px;">
            <h3 style="color:#0EB5D1;margin:0 0 10px;">Fórmula del NSM</h3>
            <code style="font-size:16px;background:rgba(255,255,255,0.1);padding:12px;display:block;border-radius:6px;">
            NSM = (Partners activos + Particulares activos) / Total activos × 100
            </code>
            <p style="margin-top:12px;font-size:14px;color:#ccc;">
                🎯 <strong>Target: 75%</strong> — el 75% del stock debería ser de terceros.<br>
                Hoy está en <strong>0%</strong> porque todos los anuncios están como PROPIO.
                A medida que carguen partners, el número sube solo.
            </p>
        </div>
    </div>

    <div class="wecar-section">
        <h2>✅ Checklist diario</h2>
        <ol style="margin:0 0 0 24px;line-height:2;">
            <li><strong>Dar de alta un partner/particular/propio (hacerlo UNA SOLA VEZ):</strong><br>
                WeCar NSM → Administrar Datos → elegir sección → Agregar nuevo → nombre → Publicar</li>
            <li><strong>Nuevos vehículos de concesionarias:</strong><br>
                Origen → PARTNER &nbsp;|&nbsp; Propietario → seleccionar &nbsp;|&nbsp; Estado → ACTIVO</li>
            <li><strong>Vehículos vendidos:</strong><br>
                Estado → VENDIDO (la fecha de baja se setea sola)</li>
            <li><strong>Vehículos retirados:</strong><br>
                Estado → RETIRADO (la fecha de baja se setea sola)</li>
            <li><strong>Migrar vehículos existentes a PARTNER:</strong><br>
                Editar vehículo → Origen → PARTNER → Partner → seleccionar</li>
        </ol>
    </div>

    <div class="wecar-section">
        <h2>❓ Preguntas frecuentes</h2>
        <div style="margin-bottom:12px;">
            <p><strong>¿Qué significa "Días Prom." en la tabla de partners?</strong></p>
            <p style="color:#666;">Es el promedio de días que los vehículos de ese partner tardaron en venderse. Se calcula como: <code>(fecha de baja − fecha de publicación)</code> promediado entre todos los vehículos vendidos. Si da más de 60 días, el partner aparece como <strong>Baja rotación</strong>.</p>
        </div>
        <div style="margin-bottom:12px;">
            <p><strong>¿Puedo cambiar un vehículo de PARTNER a PROPIO después?</strong></p>
            <p style="color:#666;">Sí, el dashboard se actualiza automáticamente.</p>
        </div>
        <div style="margin-bottom:12px;">
            <p><strong>¿Los cambios se reflejan al toque?</strong></p>
            <p style="color:#666;">Sí, apenas guardan el anuncio.</p>
        </div>
        <div style="margin-bottom:12px;">
            <p><strong>¿Los particulares se administran igual que los partners?</strong></p>
            <p style="color:#666;">Sí. Se crean desde Administrar Datos y aparecen en el dropdown de Propietario cuando el Origen es PARTICULAR.</p>
        </div>
        <div style="margin-bottom:12px;">
            <p><strong>¿Qué pasa si borro un partner del listado?</strong></p>
            <p style="color:#666;">Los vehículos que tenían ese partner quedan sin asignar. En el dashboard aparecen como "Sin asignar".</p>
        </div>
        <div style="margin-bottom:12px;">
            <p><strong>¿Cada cuánto se actualiza la vista Histórica?</strong></p>
            <p style="color:#666;">El sistema toma una foto de las métricas todos los días a la 1:33 AM.</p>
        </div>
        <div style="margin-bottom:0;">
            <p><strong>¿Se pueden editar los partners después?</strong></p>
            <p style="color:#666;">Sí, desde WeCar NSM → Administrar Datos pueden editar nombre o eliminar.</p>
        </div>
    </div>
</div>
