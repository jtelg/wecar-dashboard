<div class="wrap wecar-dashboard">
    <div class="wecar-header">
        <div class="wecar-header-left">
            <h1>WeCar — Administrar Datos</h1>
            <p class="wecar-subtitle">Gestión de partners, particulares y concesionarias propias</p>
        </div>
    </div>

    <!-- Sección: Partners -->
    <div class="wecar-section">
        <h2>🏢 Partners (Concesionarias)</h2>
        <p>Administrar las concesionarias asociadas.</p>
        <div style="display:flex;gap:12px;flex-wrap:wrap;margin:12px 0;">
            <a href="edit.php?post_type=wecar_partner" class="button button-primary">
                + Gestionar Partners
            </a>
            <a href="post-new.php?post_type=wecar_partner" class="button">
                + Agregar nuevo
            </a>
        </div>

        <?php
        $partner_posts = WeCar_Partner::get_all();
        if (!empty($partner_posts)):
        ?>
        <div class="wecar-table-wrapper">
            <table class="wecar-table">
                <thead>
                    <tr>
                        <th>Nombre</th>
                        <th>Vehículos asignados</th>
                        <th>Estado</th>
                        <th style="width:120px;">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($partner_posts as $pp):
                        $count = 0;
                        $car_query = new WP_Query([
                            'post_type'      => 'vehica_car',
                            'posts_per_page' => -1,
                            'fields'         => 'ids',
                            'meta_query'     => [[
                                'key'   => WeCar_Partner::META_KEY,
                                'value' => $pp->ID,
                            ]],
                        ]);
                        $count = $car_query->post_count;
                    ?>
                        <tr>
                            <td><strong><?php echo esc_html($pp->post_title); ?></strong></td>
                            <td><?php echo $count; ?> vehículos</td>
                            <td>
                                <?php if ($pp->post_status === 'publish'): ?>
                                    <span class="wecar-badge wecar-badge-active">Publicado</span>
                                <?php else: ?>
                                    <span class="wecar-badge wecar-badge-warning">Borrador</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <a href="post.php?post=<?php echo $pp->ID; ?>&action=edit" class="wecar-link">Editar</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php else: ?>
            <p style="color:#888;">No hay partners cargados todavía.</p>
        <?php endif; ?>
    </div>

    <!-- Sección: Particulares -->
    <div class="wecar-section">
        <h2>👤 Particulares</h2>
        <p>Administrar vendedores particulares.</p>
        <div style="display:flex;gap:12px;flex-wrap:wrap;margin:12px 0;">
            <a href="edit.php?post_type=wecar_particular" class="button button-primary">
                + Gestionar Particulares
            </a>
            <a href="post-new.php?post_type=wecar_particular" class="button">
                + Agregar nuevo
            </a>
        </div>

        <?php
        $particular_posts = WeCar_Particular::get_all();
        if (!empty($particular_posts)):
        ?>
        <div class="wecar-table-wrapper">
            <table class="wecar-table">
                <thead>
                    <tr>
                        <th>Nombre</th>
                        <th>Vehículos asignados</th>
                        <th>Estado</th>
                        <th style="width:120px;">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($particular_posts as $pp):
                        $count = 0;
                        $car_query = new WP_Query([
                            'post_type'      => 'vehica_car',
                            'posts_per_page' => -1,
                            'fields'         => 'ids',
                            'meta_query'     => [[
                                'key'   => WeCar_Particular::META_KEY,
                                'value' => $pp->ID,
                            ]],
                        ]);
                        $count = $car_query->post_count;
                    ?>
                        <tr>
                            <td><strong><?php echo esc_html($pp->post_title); ?></strong></td>
                            <td><?php echo $count; ?> vehículos</td>
                            <td>
                                <?php if ($pp->post_status === 'publish'): ?>
                                    <span class="wecar-badge wecar-badge-active">Publicado</span>
                                <?php else: ?>
                                    <span class="wecar-badge wecar-badge-warning">Borrador</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <a href="post.php?post=<?php echo $pp->ID; ?>&action=edit" class="wecar-link">Editar</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php else: ?>
            <p style="color:#888;">No hay particulares cargados todavía.</p>
        <?php endif; ?>
    </div>

    <!-- Sección: Propios -->
    <div class="wecar-section">
        <h2>🏠 Propios</h2>
        <p>Administrar concesionarias propias de WeCar.</p>
        <div style="display:flex;gap:12px;flex-wrap:wrap;margin:12px 0;">
            <a href="edit.php?post_type=wecar_propio" class="button button-primary">
                + Gestionar Propios
            </a>
            <a href="post-new.php?post_type=wecar_propio" class="button">
                + Agregar nuevo
            </a>
        </div>

        <?php
        $propio_posts = WeCar_Propio::get_all();
        if (!empty($propio_posts)):
        ?>
        <div class="wecar-table-wrapper">
            <table class="wecar-table">
                <thead>
                    <tr>
                        <th>Nombre</th>
                        <th>Vehículos asignados</th>
                        <th>Estado</th>
                        <th style="width:120px;">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($propio_posts as $pp):
                        $count = 0;
                        $car_query = new WP_Query([
                            'post_type'      => 'vehica_car',
                            'posts_per_page' => -1,
                            'fields'         => 'ids',
                            'meta_query'     => [[
                                'key'   => WeCar_Propio::META_KEY,
                                'value' => $pp->ID,
                            ]],
                        ]);
                        $count = $car_query->post_count;
                    ?>
                        <tr>
                            <td><strong><?php echo esc_html($pp->post_title); ?></strong></td>
                            <td><?php echo $count; ?> vehículos</td>
                            <td>
                                <?php if ($pp->post_status === 'publish'): ?>
                                    <span class="wecar-badge wecar-badge-active">Publicado</span>
                                <?php else: ?>
                                    <span class="wecar-badge wecar-badge-warning">Borrador</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <a href="post.php?post=<?php echo $pp->ID; ?>&action=edit" class="wecar-link">Editar</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php else: ?>
            <p style="color:#888;">No hay concesionarias propias cargadas todavía.</p>
        <?php endif; ?>
    </div>
</div>
