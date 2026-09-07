<?php if (!function_exists('is_visible')) require_once 'content_helper.php'; if (!is_visible('autoridades')) return; ?>
<!-- ============================================= -->
<!-- NUESTRAS AUTORIDADES                          -->
<!-- ============================================= -->
<section class="autoridades" id="autoridades">
    <div class="autoridades__container">

        <!-- Header -->
        <div class="autoridades__header">
            <div class="autoridades__header-left">
                <span class="autoridades__tag"><?= content_get('autoridades', 'etiqueta_superior', 'Liderazgo Institucional') ?></span>
                <h2 class="autoridades__title"><?= content_get('autoridades', 'titulo', 'Nuestras Autoridades') ?></h2>
                <p class="autoridades__subtitle"><?= content_get('autoridades', 'descripcion', 'Profesionales comprometidos con la excelencia académica, la innovación educativa y la gestión transparente de nuestra comunidad universitaria.') ?></p>
            </div>
            <a href="#" class="btn--outline-directorio" id="btn-directorio">
                <?= content_get('autoridades', 'boton_directorio', 'Ver Directorio') ?>
                <span class="btn__icon-right"><i class="fas fa-arrow-right"></i></span>
            </a>
        </div>

        <!-- Grid de cards -->
        <div class="autoridades__grid">
            <?php
            // Fuente real: la colección "Equipo" del panel (CONTENIDO -> Equipo),
            // tal como indica el aviso "INFORMACIÓN IMPORTANTE" de esa sección.
            $equipo_items = collection_items('equipo');

            // Solo personas publicadas y marcadas para aparecer en esta sección.
            // OJO: field_bool_parse() guarda true/false reales al editar desde el
            // panel, pero los datos de ejemplo usan '1'/'0' como texto. Se aceptan
            // ambos formatos para no perder registros guardados desde el panel.
            $itb_campo_activo = function ($v) {
                return $v === null || $v === true || $v === '1' || $v === 1;
            };
            $lista_autoridades = array_values(array_filter($equipo_items, function ($p) use ($itb_campo_activo) {
                return $itb_campo_activo($p['publicado'] ?? true) && $itb_campo_activo($p['mostrar_en_home'] ?? true);
            }));

            // Respetar el orden definido al arrastrar en el listado de Equipo
            usort($lista_autoridades, function ($a, $b) {
                return (int)($a['orden'] ?? 999) <=> (int)($b['orden'] ?? 999);
            });

            // La colección usa otros nombres de campo (nombre_completo, foto);
            // se traducen aquí a los que ya espera esta tarjeta.
            $lista_autoridades = array_map(function ($p) {
                return [
                    'nombre' => $p['nombre_completo'] ?? ($p['nombre'] ?? ''),
                    'cargo'  => $p['cargo'] ?? '',
                    'imagen' => $p['foto'] ?? '',
                ];
            }, $lista_autoridades);

            // Respaldo solo si no queda nadie publicado (colección vacía),
            // para no dejar la sección en blanco.
            if (empty($lista_autoridades)) {
                $lista_autoridades = [
                    [
                        'nombre' => 'PhD. Roberto Tolozano Benites',
                        'cargo' => 'Canciller',
                        'imagen' => 'img/autoridad_1.png'
                    ],
                    [
                        'nombre' => 'PhD. Elena Tolozano Benites',
                        'cargo' => 'Rectora',
                        'imagen' => 'img/autoridad_2.png'
                    ],
                    [
                        'nombre' => 'PhD. Luis Alzate Peralta',
                        'cargo' => 'Vicerrector Académico<br>y de Investigación',
                        'imagen' => 'img/autoridad_3.png'
                    ],
                    [
                        'nombre' => 'PhD. Michelle Tolozano Lapierre',
                        'cargo' => 'Vicerrectora de Extensión<br>y Gestión Administrativa',
                        'imagen' => 'img/autoridad_4.png'
                    ]
                ];
            }

            foreach ((array)$lista_autoridades as $auth):
                $foto_path = trim($auth['imagen'] ?? '');
                if (!content_image_exists($foto_path)) $foto_path = 'img/placeholder_autoridad.svg';
                $nombre = htmlspecialchars($auth['nombre'] ?? '', ENT_QUOTES, 'UTF-8');
                $cargo = nl2br(htmlspecialchars($auth['cargo'] ?? '', ENT_QUOTES, 'UTF-8'));
            ?>
            <div class="autoridades__card">
                <div class="autoridades__card-img">
                    <img src="<?= htmlspecialchars($foto_path, ENT_QUOTES, 'UTF-8') ?>" alt="<?= $nombre ?>">
                    <div class="autoridades__card-actions">
                        <div class="autoridades__card-socials">
                            <a href="#" aria-label="Correo"><img src="img/correo-electronico.png" alt="Correo"></a>
                            <a href="#" aria-label="Teléfono"><img src="img/telefono-fijo.png" alt="Teléfono"></a>
                        </div>
                        <button class="autoridades__card-plus" aria-label="Ver perfil">
                            <i class="fas fa-plus"></i>
                        </button>
                    </div>
                </div>
                <div class="autoridades__card-body">
                    <h3 class="autoridades__card-name"><?= $nombre ?></h3>
                    <span class="autoridades__card-role"><?= $cargo ?></span>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
