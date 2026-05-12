<div class="wrap wecar-dashboard">
    <div class="wecar-header">
        <div class="wecar-header-left">
            <h1>WeCar — Panel de Control Marketplace</h1>
            <p class="wecar-subtitle">Métricas de inventario y North Star Metric</p>
        </div>
        <div class="wecar-nsm-big">
            <span class="wecar-nsm-value"><?php echo esc_html($nsm); ?>%</span>
            <span class="wecar-nsm-label">NSM — Stock de Terceros</span>
            <div class="wecar-nsm-bar">
                <div class="wecar-nsm-fill" style="width: <?php echo min($nsm, 100); ?>%;"></div>
            </div>
            <span class="wecar-nsm-target">Target: 75%</span>
        </div>
    </div>

    <div class="wecar-filter-bar">
        <span class="wecar-filter-label">Filtrar por fecha</span>
        <form class="wf-filter-form" style="display:contents;">
            <input type="date" class="wecar-filter-input wf-desde" name="wf_desde" placeholder="Desde">
            <span class="wecar-filter-sep">→</span>
            <input type="date" class="wecar-filter-input wf-hasta" name="wf_hasta" placeholder="Hasta">
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

    <div class="wecar-stats-grid">
        <div class="wecar-stat-card" style="border-color: #9949FF;">
            <div class="wecar-stat-number" style="color: #9949FF;"><?php echo esc_html($mix['propio']); ?></div>
            <div class="wecar-stat-label">Stock Propio</div>
            <div class="wecar-stat-bar">
                <div class="wecar-stat-bar-fill" style="width: <?php echo esc_html($mix['propio_pct']); ?>%; background: #9949FF;"></div>
            </div>
            <div class="wecar-stat-pct"><?php echo esc_html($mix['propio_pct']); ?>%</div>
        </div>

        <div class="wecar-stat-card" style="border-color: #0E6FD1;">
            <div class="wecar-stat-number" style="color: #0E6FD1;"><?php echo esc_html($mix['partner']); ?></div>
            <div class="wecar-stat-label">Stock Partners</div>
            <div class="wecar-stat-bar">
                <div class="wecar-stat-bar-fill" style="width: <?php echo esc_html($mix['partner_pct']); ?>%; background: #0E6FD1;"></div>
            </div>
            <div class="wecar-stat-pct"><?php echo esc_html($mix['partner_pct']); ?>%</div>
        </div>

        <div class="wecar-stat-card" style="border-color: #0EB5D1;">
            <div class="wecar-stat-number" style="color: #0EB5D1;"><?php echo esc_html($mix['particular']); ?></div>
            <div class="wecar-stat-label">Stock Particulares</div>
            <div class="wecar-stat-bar">
                <div class="wecar-stat-bar-fill" style="width: <?php echo esc_html($mix['particular_pct']); ?>%; background: #0EB5D1;"></div>
            </div>
            <div class="wecar-stat-pct"><?php echo esc_html($mix['particular_pct']); ?>%</div>
        </div>

        <div class="wecar-stat-card" style="border-color: #2e7d32;">
            <div class="wecar-stat-number" style="color: #2e7d32;"><?php echo esc_html($mix['total']); ?></div>
            <div class="wecar-stat-label">Total Activos</div>
            <div class="wecar-stat-meta">
                <span class="wecar-up">↑ <?php echo esc_html($resumen['altas']); ?> altas</span>
                <span class="wecar-down">↓ <?php echo esc_html($resumen['bajas']); ?> bajas</span>
            </div>
            <div class="wecar-stat-pct">este mes</div>
        </div>
    </div>

    <div class="wecar-section">
        <h2>Partners — Resumen</h2>
        <div class="wecar-table-wrapper"><table class="wecar-table">
            <thead>
                <tr>
                    <th>Partner</th>
                    <th>Autos Activos</th>
                    <th>Vendidos</th>
                    <th>Retirados</th>
                    <th>Días Prom.</th>
                    <th>Estado</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($partners)): ?>
                    <tr><td colspan="6">No hay partners cargados todavía.</td></tr>
                <?php else: ?>
                    <?php foreach (array_slice($partners, 0, 10) as $name => $data): ?>
                        <tr>
                            <td><strong><?php echo esc_html($name); ?></strong></td>
                            <td><?php echo esc_html($data['activos']); ?></td>
                            <td><?php echo esc_html($data['vendidos']); ?></td>
                            <td><?php echo esc_html($data['retirados']); ?></td>
                            <td><?php echo esc_html($data['dias_promedio']); ?></td>
                            <td>
                                <?php if ($data['status'] === 'activo'): ?>
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
        <p class="wecar-table-footer">
            <a href="admin.php?page=wecar-partners" class="wecar-link">Ver todos los partners →</a>
        </p>
    </div>

    <div class="wecar-section">
        <h2>Particulares — Resumen</h2>
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
                    <?php foreach (array_slice($particulares, 0, 10) as $name => $data): ?>
                        <tr>
                            <td><strong><?php echo esc_html($name); ?></strong></td>
                            <td><?php echo esc_html($data['activos']); ?></td>
                            <td><?php echo esc_html($data['vendidos']); ?></td>
                            <td><?php echo esc_html($data['retirados']); ?></td>
                            <td><?php echo esc_html($data['dias_promedio']); ?></td>
                            <td>
                                <?php if ($data['status'] === 'activo'): ?>
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
        <p class="wecar-table-footer">
            <a href="admin.php?page=wecar-particulares" class="wecar-link">Ver todos los particulares →</a>
        </p>
    </div>

    <div class="wecar-section">
        <h2>Propios — Resumen</h2>
        <div class="wecar-table-wrapper"><table class="wecar-table">
            <thead>
                <tr>
                    <th>Propio</th>
                    <th>Autos Activos</th>
                    <th>Vendidos</th>
                    <th>Retirados</th>
                    <th>Días Prom.</th>
                    <th>Estado</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($propios)): ?>
                    <tr><td colspan="6">No hay propios cargados todavía.</td></tr>
                <?php else: ?>
                    <?php foreach (array_slice($propios, 0, 10) as $name => $data): ?>
                        <tr>
                            <td><strong><?php echo esc_html($name); ?></strong></td>
                            <td><?php echo esc_html($data['activos']); ?></td>
                            <td><?php echo esc_html($data['vendidos']); ?></td>
                            <td><?php echo esc_html($data['retirados']); ?></td>
                            <td><?php echo esc_html($data['dias_promedio']); ?></td>
                            <td>
                                <?php if ($data['status'] === 'activo'): ?>
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
        <p class="wecar-table-footer">
            <a href="admin.php?page=wecar-admin-datos" class="wecar-link">Ver todos los propios →</a>
        </p>
    </div>
</div>
