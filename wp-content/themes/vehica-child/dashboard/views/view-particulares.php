<div class="wrap wecar-dashboard">
    <div class="wecar-header">
        <div class="wecar-header-left">
            <h1>WeCar — Particulares</h1>
            <p class="wecar-subtitle">Vehículos de vendedores particulares</p>
        </div>
    </div>

    <div class="wecar-stats-grid">
        <div class="wecar-stat-card" style="border-color: #0EB5D1;">
            <div class="wecar-stat-number" style="color: #0EB5D1;"><?php echo esc_html($data['activos']); ?></div>
            <div class="wecar-stat-label">Activos</div>
        </div>
        <div class="wecar-stat-card" style="border-color: #2e7d32;">
            <div class="wecar-stat-number" style="color: #2e7d32;"><?php echo esc_html($data['vendidos']); ?></div>
            <div class="wecar-stat-label">Vendidos</div>
        </div>
        <div class="wecar-stat-card" style="border-color: #c62828;">
            <div class="wecar-stat-number" style="color: #c62828;"><?php echo esc_html($data['retirados']); ?></div>
            <div class="wecar-stat-label">Retirados</div>
        </div>
        <div class="wecar-stat-card" style="border-color: #9949FF;">
            <div class="wecar-stat-number" style="color: #9949FF;"><?php echo esc_html($data['conversion']); ?>%</div>
            <div class="wecar-stat-label">Tasa de Conversión</div>
        </div>
    </div>

    <div class="wecar-filter-bar">
        <span class="wecar-filter-label">Filtrar por fecha</span>
        <form class="wf-filter-form" style="display:contents;">
            <input type="date" class="wecar-filter-input wf-desde" placeholder="Desde">
            <span class="wecar-filter-sep">→</span>
            <input type="date" class="wecar-filter-input wf-hasta" placeholder="Hasta">
            <button type="submit" class="wecar-filter-btn wecar-filter-btn-primary wf-submit">Filtrar</button>
        </form>
        <button class="wecar-filter-btn wf-preset" data-days="7">7 días</button>
        <button class="wecar-filter-btn wf-preset" data-days="30">30 días</button>
        <button class="wecar-filter-btn wf-preset" data-days="90">90 días</button>
        <?php if (!empty($filtro)): ?>
            <span class="wecar-filter-active"><?php echo esc_html($filtro['desde']); ?> → <?php echo esc_html($filtro['hasta']); ?></span>
            <button class="wecar-filter-btn wecar-filter-btn-danger wf-clear">Limpiar</button>
        <?php endif; ?>
    </div>

    <div class="wecar-section">
        <h2>Detalle del Funnel</h2>
        <div class="wecar-funnel">
            <div class="wecar-funnel-step">
                <span class="wecar-funnel-label">Total publicados</span>
                <span class="wecar-funnel-value"><?php echo esc_html($data['total']); ?></span>
            </div>
            <div class="wecar-funnel-arrow">↓</div>
            <div class="wecar-funnel-step">
                <span class="wecar-funnel-label">Activos disponibles</span>
                <span class="wecar-funnel-value"><?php echo esc_html($data['activos']); ?></span>
            </div>
            <div class="wecar-funnel-arrow">↓</div>
            <div class="wecar-funnel-step">
                <span class="wecar-funnel-label">Vendidos con éxito</span>
                <span class="wecar-funnel-value"><?php echo esc_html($data['vendidos']); ?></span>
            </div>
            <div class="wecar-funnel-arrow">↓</div>
            <div class="wecar-funnel-step">
                <span class="wecar-funnel-label">Tasa de conversión</span>
                <span class="wecar-funnel-value"><?php echo esc_html($data['conversion']); ?>%</span>
            </div>
        </div>
    </div>

    <div class="wecar-section">
        <div class="wecar-table-wrapper"><table class="wecar-table">
            <thead>
                <tr>
                    <th>Particular</th>
                    <th>Autos Activos</th>
                    <th>Vendidos</th>
                    <th>Retirados</th>
                    <th>Días Prom.</th>
                    <th>Estado</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($particulares)): ?>
                    <tr><td colspan="6">No hay particulares cargados todavía.</td></tr>
                <?php else: ?>
                    <?php foreach ($particulares as $name => $pdata): ?>
                        <tr>
                            <td><strong><?php echo esc_html($name); ?></strong></td>
                            <td><?php echo esc_html($pdata['activos']); ?></td>
                            <td><?php echo esc_html($pdata['vendidos']); ?></td>
                            <td><?php echo esc_html($pdata['retirados']); ?></td>
                            <td><?php echo esc_html($pdata['dias_promedio']); ?></td>
                            <td>
                                <?php if ($pdata['status'] === 'activo'): ?>
                                    <span class="wecar-badge wecar-badge-active">Activo</span>
                                <?php else: ?>
                                    <span class="wecar-badge wecar-badge-warning">Baja rotación</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table></div>
    </div>
</div>
