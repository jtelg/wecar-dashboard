<div class="wrap wecar-dashboard">
    <div class="wecar-header">
        <div class="wecar-header-left">
            <h1>WeCar — Histórica</h1>
            <p class="wecar-subtitle">Evolución de métricas en el tiempo</p>
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

    <?php if (empty($historico['rows'])): ?>
        <div class="wecar-empty">
            <p>Todavía no hay datos históricos. El WP-Cron de snapshots está corriendo diariamente y registrando métricas.</p>
            <p>La tabla <strong>wp_wecar_snapshots</strong> se llena automáticamente cada día a la 1:33 AM.</p>
        </div>
    <?php else:
        $total_pages = $historico['total_pages'];
        $current_page = $historico['page'];
        $per_page = $historico['per_page'];
        $from = ($current_page - 1) * $per_page + 1;
        $to = min($current_page * $per_page, $historico['total_rows']);
    ?>
        <div class="wecar-section">
            <h2>Evolución de NSM — <?php echo esc_html($historico['total_rows']); ?> registros</h2>

            <div class="wecar-table-wrapper">
                <table class="wecar-table wecar-table-compact">
                    <thead>
                        <tr>
                            <th>Fecha</th>
                            <th>NSM</th>
                            <th>Total</th>
                            <th>Propios</th>
                            <th>Partners</th>
                            <th>Part.</th>
                            <th>Vend.</th>
                            <th>Ret.</th>
                            <th>Conv.</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($historico['rows'] as $row): ?>
                            <tr>
                                <td><?php echo esc_html($row->fecha); ?></td>
                                <td><strong><?php echo esc_html($row->nsm); ?>%</strong></td>
                                <td><?php echo esc_html($row->total); ?></td>
                                <td><?php echo esc_html($row->propios); ?></td>
                                <td><?php echo esc_html($row->partners); ?></td>
                                <td><?php echo esc_html($row->particulares); ?></td>
                                <td><?php echo esc_html($row->vendidos); ?></td>
                                <td><?php echo esc_html($row->retirados); ?></td>
                                <td><?php echo esc_html($row->conversion); ?>%</td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <?php if ($total_pages > 1): ?>
            <div class="wecar-pagination">
                <span class="wecar-pagination-info">
                    Mostrando <?php echo $from; ?>–<?php echo $to; ?> de <?php echo $historico['total_rows']; ?>
                </span>
                <div class="wecar-pagination-links">
                    <?php if ($current_page > 1): ?>
                        <a href="?page=wecar-historica&p=1" class="wecar-page-link" title="Primera">
                            <span class="wecar-page-arrow">«</span>
                        </a>
                        <a href="?page=wecar-historica&p=<?php echo $current_page - 1; ?>" class="wecar-page-link" title="Anterior">
                            <span class="wecar-page-arrow">‹</span>
                        </a>
                    <?php endif; ?>

                    <?php
                    $start_p = max(1, $current_page - 2);
                    $end_p = min($total_pages, $current_page + 2);
                    if ($start_p > 1): ?>
                        <a href="?page=wecar-historica&p=1" class="wecar-page-link">1</a>
                        <?php if ($start_p > 2): ?>
                            <span class="wecar-page-link" style="cursor:default;background:none;border:none;box-shadow:none;color:#aaa;">…</span>
                        <?php endif; ?>
                    <?php endif; ?>

                    <?php for ($i = $start_p; $i <= $end_p; $i++): ?>
                        <?php if ($i === $current_page): ?>
                            <span class="wecar-page-link wecar-page-current"><?php echo $i; ?></span>
                        <?php else: ?>
                            <a href="?page=wecar-historica&p=<?php echo $i; ?>" class="wecar-page-link"><?php echo $i; ?></a>
                        <?php endif; ?>
                    <?php endfor; ?>

                    <?php if ($end_p < $total_pages): ?>
                        <?php if ($end_p < $total_pages - 1): ?>
                            <span class="wecar-page-link" style="cursor:default;background:none;border:none;box-shadow:none;color:#aaa;">…</span>
                        <?php endif; ?>
                        <a href="?page=wecar-historica&p=<?php echo $total_pages; ?>" class="wecar-page-link"><?php echo $total_pages; ?></a>
                    <?php endif; ?>

                    <?php if ($current_page < $total_pages): ?>
                        <a href="?page=wecar-historica&p=<?php echo $current_page + 1; ?>" class="wecar-page-link" title="Siguiente">
                            <span class="wecar-page-arrow">›</span>
                        </a>
                        <a href="?page=wecar-historica&p=<?php echo $total_pages; ?>" class="wecar-page-link" title="Última">
                            <span class="wecar-page-arrow">»</span>
                        </a>
                    <?php endif; ?>
                </div>
            </div>
            <?php endif; ?>
        </div>

        <div class="wecar-section">
            <h2>Progreso hacia el Target</h2>
            <?php
            $ultimo = end($historico['rows']);
            $nsm_actual = $ultimo ? (float)$ultimo->nsm : 0;
            $target = 75;
            $progreso = min(round(($nsm_actual / $target) * 100), 100);
            ?>
            <div class="wecar-target-box">
                <div class="wecar-target-label">
                    <span>NSM Actual: <strong><?php echo esc_html($nsm_actual); ?>%</strong></span>
                    <span>Target: <strong><?php echo esc_html($target); ?>%</strong></span>
                    <span>Progreso: <strong><?php echo esc_html($progreso); ?>%</strong></span>
                </div>
                <div class="wecar-target-bar">
                    <div class="wecar-target-fill" style="width: <?php echo esc_html($progreso); ?>%;"></div>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>
