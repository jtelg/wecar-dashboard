<div class="wrap wecar-dashboard">
    <div class="wecar-header">
        <div class="wecar-header-left">
            <h1>WeCar — Partners</h1>
            <p class="wecar-subtitle">Rendimiento por concesionaria asociada</p>
        </div>
    </div>

    <div class="wecar-section">
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
                    <?php foreach ($partners as $name => $data): ?>
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
    </div>
</div>
