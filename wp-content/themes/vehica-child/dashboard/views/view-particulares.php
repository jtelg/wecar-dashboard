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
</div>
