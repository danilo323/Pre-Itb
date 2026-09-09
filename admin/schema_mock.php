<?php
// admin/schema_mock.php
// Generado para V2.0
return [
    'groups' => [
        'globales' => 'GLOBALES',
        'paginas' => 'PÁGINAS',
        'contenido' => 'CONTENIDO',
    ],
    'items' => [
        'ajustes' => [
            'label' => 'Ajustes generales',
            'group' => 'globales',
            'icon' => 'bi bi-gear-fill',
            'type' => 'singleton',
            'fields' => [
                'div_seo' => [
                    'type' => 'divider',
                    'label' => 'SEO e Identidad de Marca',
                ],
                'site_name' => [
                    'type' => 'text',
                    'label' => 'Nombre del sitio',
                    'default' => 'ITB - Instituto Superior Tecnológico Bolivariano de Tecnología',
                ],
                'description' => [
                    'type' => 'textarea',
                    'label' => 'Descripción (Meta description)',
                    'default' => 'Instituto Superior Tecnológico Bolivariano de Tecnología - Educación Superior de Excelencia. Formamos profesionales de alto nivel con educación práctica, tecnología e innovación.',
                    'help' => 'Aparece en buscadores. Máx 160 caracteres.',
                ],
                'keywords' => [
                    'type' => 'text',
                    'label' => 'Palabras clave',
                    'default' => 'educación, instituto, guayaquil, carreras, tecnología, bolivariano, itb',
                    'help' => 'Separadas por comas.',
                ],
                'div_logos' => [
                    'type' => 'divider',
                    'label' => 'Logotipos',
                ],
                'logo_principal' => [
                    'type' => 'image',
                    'label' => 'Logo Principal',
                    'default' => 'img/logo.png',
                ],
            ],
        ],
        'menu' => [
            'label' => 'Menú principal',
            'group' => 'globales',
            'icon' => 'bi bi-list',
            'type' => 'singleton',
            'fields' => [
                'div_main' => [
                    'type' => 'divider',
                    'label' => 'Navegación Principal',
                ],
                'items_menu' => [
                    'type' => 'menu_builder',
                    'label' => 'Items del Menú',
                    'help' => 'Una fila por enlace, y la indentación muestra de quién cuelga cada una. Con el botón + de una fila agregas un sub-item colgado de ella; con las flechas de nivel (<- y ->) la sacas o la cuelgas. Las flechas arriba/abajo mueven la fila. Usa el ícono de lápiz para editar.',
                    'fields' => [
                        'texto' => ['type' => 'text', 'label' => 'Texto del enlace'],
                        'url' => ['type' => 'text', 'label' => 'URL (Ej: /admisiones)'],
                        'nivel' => [
                            'type' => 'select',
                            'label' => 'Nivel',
                            'options' => [
                                'padre' => 'Padre (Principal)',
                                'hijo' => 'Hijo (Submenú)'
                            ],
                            'default' => 'padre'
                        ]
                    ],
                    'default' => [
                        ['texto' => 'Instituto', 'url' => '#', 'nivel' => 'padre'],
                        ['texto' => 'Sobre Nosotros', 'url' => '#', 'nivel' => 'hijo'],
                        ['texto' => 'Himno e Identidad', 'url' => '#', 'nivel' => 'hijo'],
                        ['texto' => 'Transparencia / Leyes', 'url' => '#', 'nivel' => 'hijo'],
                        ['texto' => 'Oferta Académica', 'url' => '#', 'nivel' => 'padre'],
                        ['texto' => 'Vida Estudiantil', 'url' => '#', 'nivel' => 'padre'],
                        ['texto' => 'Admisiones', 'url' => '#', 'nivel' => 'padre'],
                    ],
                ],
                'div_top' => [
                    'type' => 'divider',
                    'label' => 'Barra Superior (Top Bar)',
                ],
                'top_link_1' => [
                    'type' => 'text',
                    'label' => 'Enlace Superior 1',
                    'default' => 'Portal Estudiantil SGA',
                ],
                'top_link_2' => [
                    'type' => 'text',
                    'label' => 'Enlace Superior 2',
                    'default' => 'Educación Continua',
                ],
                'top_link_3' => [
                    'type' => 'text',
                    'label' => 'Enlace Superior 3',
                    'default' => 'Vinculación',
                ],
                'top_link_4' => [
                    'type' => 'text',
                    'label' => 'Enlace Superior 4',
                    'default' => 'Investigación',
                ],
                'top_link_5' => [
                    'type' => 'text',
                    'label' => 'Enlace Superior 5',
                    'default' => 'UNIEBEC',
                ],
                'div_cta' => [
                    'type' => 'divider',
                    'label' => 'Botones CTA',
                ],
                'cta_btn_1' => [
                    'type' => 'text',
                    'label' => 'Botón 1 (Secundario)',
                    'default' => 'Solicitar Información',
                ],
                'cta_btn_2' => [
                    'type' => 'text',
                    'label' => 'Botón 2 (Principal)',
                    'default' => 'Matricúlame',
                ],
            ],
        ],
        'footer' => [
            'label' => 'Pie de Página',
            'group' => 'globales',
            'icon' => 'bi bi-layout-text-window-reverse',
            'type' => 'singleton',
            'fields' => [
                'div_prefooter' => [
                    'type' => 'divider',
                    'label' => 'Banner CTA (arriba del pie de página)',
                ],
                'cta_titulo' => [
                    'type' => 'text',
                    'label' => 'Título del banner',
                    'default' => '¿Aún no decides qué carrera estudiar?',
                ],
                'cta_desc' => [
                    'type' => 'textarea',
                    'label' => 'Descripción del banner',
                    'default' => 'Descubre tu vocación con nuestro test guiado, visita el ITB y conoce de cerca nuestra propuesta académica o recibe asesoría personalizada para elegir el programa ideal para ti.',
                ],
                'cta_btn_1' => [
                    'type' => 'text',
                    'label' => 'Botón 1',
                    'default' => 'Test Vocacional',
                ],
                'cta_btn_2' => [
                    'type' => 'text',
                    'label' => 'Botón 2',
                    'default' => 'Vive la Experiencia ITB',
                ],
                'cta_btn_3' => [
                    'type' => 'text',
                    'label' => 'Botón 3',
                    'default' => 'Habla con un Asesor',
                ],
                'div_logo' => [
                    'type' => 'divider',
                    'label' => 'Logo y Contacto',
                ],
                'logo' => [
                    'type' => 'image',
                    'label' => 'Logo del pie de página (fondo oscuro)',
                    'default' => 'img/logo-itb-white.png',
                    'help' => 'Usa la versión blanca del logo: este bloque tiene fondo azul oscuro.',
                ],
                'contacto_1' => [
                    'type' => 'textarea',
                    'label' => 'Teléfonos (línea 1 y 2)',
                    'default' => "PBX: (04) 500 0175 - 230 7028\n500 2164 - 372 7040",
                ],
                'contacto_2' => [
                    'type' => 'text',
                    'label' => 'Teléfono gratuito',
                    'default' => '1800 ITB-ITB: 482-482',
                ],
                'div_enlaces1' => [
                    'type' => 'divider',
                    'label' => 'Columna: Enlaces 1',
                ],
                'enlaces_columna_1' => [
                    'type' => 'textarea',
                    'label' => 'Enlaces (uno por línea)',
                    'default' => "Admisiones Pregrado\nCarreras y Programas\nCalendario Académico\nTalento Humano\nVinculación",
                ],
                'div_enlaces2' => [
                    'type' => 'divider',
                    'label' => 'Columna: Enlaces 2',
                ],
                'enlaces_columna_2' => [
                    'type' => 'textarea',
                    'label' => 'Enlaces (uno por línea)',
                    'default' => "Noticias y Novedades ITB\nDirectorio General\nASOMI\nCONDUCE ECUADOR\nTrabaja en el ITB",
                ],
                'div_campus' => [
                    'type' => 'divider',
                    'label' => 'Columna: Campus y Mapa',
                ],
                'info_campus' => [
                    'type' => 'alert',
                    'alert_type' => 'warning',
                    'label' => '<strong>EL MAPA YA NO ES UNA IMAGEN.</strong> Ahora es un mapa de verdad: al hacer clic en un campus del pie de página, el mapa se mueve hasta él y debajo aparece su dirección.<br><strong>Todos los campus se pueden pulsar</strong> desde el primer momento. Si uno no tiene dirección escrita, el mapa lo busca por su nombre usando el texto de "Texto de apoyo" de más abajo, así que el pin puede caer aproximado: <u>escribe la dirección para que sea exacto</u>.',
                ],
                'lista_campus' => [
                    'type' => 'repeater',
                    'label' => 'Campus',
                    'item_label' => 'Campus',
                    'help' => 'El primero de la lista es el que se muestra en el mapa al abrir la página.',
                    'default' => [
                        ['nombre' => 'Campus Matriz', 'direccion' => 'Roca #101 y Pedro Carbo esq., Guayaquil, Ecuador', 'mapa_url' => ''],
                        ['nombre' => 'Campus Boyacá', 'direccion' => '', 'mapa_url' => ''],
                        ['nombre' => 'Campus Naval', 'direccion' => '', 'mapa_url' => ''],
                        ['nombre' => 'Campus Teresa Benites', 'direccion' => '', 'mapa_url' => ''],
                        ['nombre' => 'Campus Tomás Martínez', 'direccion' => '', 'mapa_url' => ''],
                    ],
                    'subfields' => [
                        'nombre' => [
                            'type' => 'text',
                            'label' => 'Nombre del campus',
                            'help' => 'Es el texto que se ve en la lista del pie de página.',
                        ],
                        'direccion' => [
                            'type' => 'text',
                            'label' => 'Dirección',
                            'help' => 'Se muestra debajo del mapa y es lo que se busca para ubicar el pin. Escríbela completa (calle, ciudad y país) para que el mapa acierte, ej: "Roca #101 y Pedro Carbo esq., Guayaquil, Ecuador".',
                        ],
                        'mapa_url' => [
                            'type' => 'text',
                            'label' => 'Enlace del mapa (opcional)',
                            'help' => 'Déjalo vacío y el mapa se ubica solo con la dirección de arriba. Rellénalo solo si el pin cae mal: entra en Google Maps, busca el sitio, pulsa Compartir → Insertar un mapa y pega aquí la dirección que aparece dentro de src="...".',
                        ],
                    ],
                ],
                'mapa_contexto' => [
                    'type' => 'text',
                    'label' => 'Texto de apoyo para buscar en el mapa',
                    'default' => 'Instituto Superior Tecnológico Bolivariano, Guayaquil, Ecuador',
                    'help' => 'Solo se usa en los campus que aún no tienen dirección: el mapa busca "nombre del campus" + este texto. Sirve para que la búsqueda no se vaya a otra ciudad o a otro país.',
                ],
                'div_redes' => [
                    'type' => 'divider',
                    'label' => 'Redes Sociales',
                ],
                'social_texto' => [
                    'type' => 'text',
                    'label' => 'Texto junto a los íconos',
                    'default' => '#ITB Instituto Superior Universitario Bolivariano en',
                ],
                'youtube_url' => [
                    'type' => 'text',
                    'label' => 'YouTube URL',
                    'default' => '#',
                ],
                'instagram_url' => [
                    'type' => 'text',
                    'label' => 'Instagram URL',
                    'default' => '#',
                ],
                'facebook_url' => [
                    'type' => 'text',
                    'label' => 'Facebook URL',
                    'default' => '#',
                ],
                'gplus_url' => [
                    'type' => 'text',
                    'label' => 'Google+ URL',
                    'default' => '#',
                ],
                'twitter_url' => [
                    'type' => 'text',
                    'label' => 'Twitter / X URL',
                    'default' => '#',
                ],
                'vimeo_url' => [
                    'type' => 'text',
                    'label' => 'Vimeo URL',
                    'default' => '#',
                ],
                'linkedin_url' => [
                    'type' => 'text',
                    'label' => 'LinkedIn URL',
                    'default' => '#',
                ],
                'div_bottom' => [
                    'type' => 'divider',
                    'label' => 'Barra inferior',
                ],
                'copyright' => [
                    'type' => 'text',
                    'label' => 'Texto de copyright',
                    'default' => '© 2026 TIC - ITB | TODOS LOS DERECHOS RESERVADOS',
                ],
            ],
        ],
        'inicio' => [
            'label' => 'Inicio',
            'group' => 'paginas',
            'icon' => 'bi bi-house-door-fill',
            'type' => 'page',
            'sections' => [
                'hero' => [
                    'label' => 'HERO (PORTADA)',
                    'fields' => [
                        'div_left' => [
                            'type' => 'divider',
                            'label' => 'Lado Izquierdo (Textos y Botón)',
                        ],
                        'subtitulo' => [
                            'type' => 'text',
                            'label' => 'Subtítulo (etiqueta pequeña)',
                            'default' => 'Educación Superior de Excelencia',
                            'help' => 'Texto pequeño naranja sobre el título principal.',
                        ],
                        'titulo' => [
                            'type' => 'textarea',
                            'label' => 'Título principal',
                            'default' => 'Construye tu Futuro, Lidera el Mañana',
                            'help' => 'Título grande de la portada. Máx. 2 líneas.',
                        ],
                        'descripcion' => [
                            'type' => 'textarea',
                            'label' => 'Descripción',
                            'default' => 'Bienvenida al ITB. Formamos profesionales de alto nivel con educación práctica, tecnología e innovación para ayudarte a alcanzar el éxito laboral.',
                            'help' => 'Párrafo de descripción debajo del título.',
                        ],
                        'cta_texto' => [
                            'type' => 'text',
                            'label' => 'Texto del botón',
                            'default' => 'Explorar Programas',
                            'help' => 'Texto del botón principal de la portada.',
                        ],
                        'div_stats' => [
                            'type' => 'divider',
                            'label' => 'Estadísticas (Debajo del botón)',
                        ],
                        'stat_numero' => [
                            'type' => 'text',
                            'label' => 'Texto de estadística (ej: +20K...)',
                            'default' => '+20K Estudiantes Graduados',
                            'help' => 'Dato destacado que aparece debajo del botón.',
                        ],
                        'rating' => [
                            'type' => 'text',
                            'label' => 'Calificación de estrellas',
                            'default' => '4.9',
                            'help' => 'Número de calificación con estrellas. Solo números.',
                        ],
                        'div_right' => [
                            'type' => 'divider',
                            'label' => 'Lado Derecho y Fondo (Multimedia)',
                        ],
                        'circular_text' => [
                            'type' => 'textarea',
                            'label' => 'Texto que gira alrededor del botón',
                            'default' => "EST. 1995\nITB INSTITUTO UNIVERSITARIO",
                            'help' => 'Escribe UNA FRASE POR LÍNEA. Los puntitos separadores (•) se agregan solos, no los escribas. Cuidado: entre todas las frases no pases de unos 45 caracteres o las letras se amontonarán para caber en el círculo.',
                        ],
                        'video_url' => [
                            'type' => 'text',
                            'label' => 'URL del Video (YouTube)',
                            'default' => 'https://youtu.be/eTgzLxWGgS4?si=itTyNJd-Es1E4f-B',
                            'help' => 'Enlace del video institucional que se abre al presionar play.',
                        ],
                        'div_galeria' => [
                            'type' => 'divider',
                            'label' => 'Galería de Imágenes de Fondo',
                        ],
                        'info_portada' => [
                            'type' => 'alert',
                            'alert_type' => 'info',
                            'label' => '<strong>CÓMO SE COMPORTA LA PORTADA:</strong> enciende <em>Animaciones y efectos</em> para que las fotos roten con su acercamiento, o enciende <em>Imagen fija</em> en UNA de las fotos para que la portada se quede quieta mostrando solo esa. Es una cosa o la otra: al encender un interruptor, los demás se apagan solos.',
                        ],
                        // Interruptor maestro. Junto con el 'estatica' de cada foto
                        // forma el grupo exclusivo 'hero_modo': siempre hay
                        // exactamente uno encendido. Este es el que se enciende
                        // solo cuando el usuario apaga todos los demás
                        // ('exclusive_default'), que es justo la regla pedida:
                        // si ninguna foto está marcada como fija, hay animación.
                        'animaciones' => [
                            'type' => 'bool',
                            'label' => 'Animaciones y efectos',
                            'default' => true,
                            'exclusive_group' => 'hero_modo',
                            'exclusive_default' => true,
                            'help' => 'Encendido: las fotos rotan con deslizamiento y acercamiento. Apagado: la portada se queda fija en la foto que marques abajo. Con una sola foto cargada no hay nada que rotar, así que se ve fija de todas formas.',
                        ],
                        'imagenes_fondo' => [
                            'type' => 'repeater',
                            'label' => '',
                            'item_label' => 'Imagen',
                            'help' => 'Agrega tantas fotos de fondo como desees. Con las animaciones encendidas el carrusel las rota automáticamente.',
                            'default' => [
                                ['archivo' => 'img/hero_1.jpeg', 'estatica' => false],
                                ['archivo' => 'img/hero_2.jpg',  'estatica' => false],
                                ['archivo' => 'img/hero_3.jpg',  'estatica' => false],
                            ],
                            'subfields' => [
                                'archivo' => [
                                    'type' => 'image',
                                    'label' => 'Foto de Fondo',
                                    'help' => 'Tamaño recomendado: 1920x1080px',
                                ],
                                'estatica' => [
                                    'type' => 'bool',
                                    'label' => 'Imagen fija',
                                    'default' => false,
                                    'exclusive_group' => 'hero_modo',
                                    'help' => 'Enciéndelo para que la portada se quede quieta en esta foto. Solo una foto puede estar marcada.',
                                ],
                            ]
                        ],

                    ],
                ],
                'trayectoria' => [
                    'label' => 'TRAYECTORIA',
                    'fields' => [
                        'div_global' => [
                            'type' => 'divider',
                            'label' => 'Textos Generales',
                        ],
                        'etiqueta_superior' => [
                            'type' => 'text',
                            'label' => 'Etiqueta superior',
                            'default' => 'NUESTRA TRAYECTORIA',
                        ],
                        'titulo' => [
                            'type' => 'textarea',
                            'label' => 'Título de la sección',
                            'default' => 'Trayectoria y Compromiso con la Educación',
                            'required' => true
                        ],
                        'descripcion' => [
                            'type' => 'textarea',
                            'label' => 'Descripción',
                            'default' => 'Desde 1995, el Instituto Superior Tecnológico Bolivariano de Tecnología ha formado profesionales con una educación integral basada en valores, innovación y excelencia académica. Nuestro compromiso es transformar vidas a través del conocimiento.',
                        ],
                        'div_perfil' => [
                            'type' => 'divider',
                            'label' => 'Perfil del Canciller',
                        ],
                        'canciller_foto' => [
                            'type' => 'image',
                            'label' => 'Foto',
                            'default' => 'img/icon_trayectoria.jpg',
                        ],
                        'canciller_nombre' => [
                            'type' => 'text',
                            'label' => 'Nombre',
                            'default' => 'PhD. Roberto Tolozano Benites',
                            'required' => true
                        ],
                        'canciller_cargo' => [
                            'type' => 'text',
                            'label' => 'Cargo',
                            'default' => 'Canciller del ITB',
                        ],
                        'div_btn' => [
                            'type' => 'divider',
                            'label' => 'Botón',
                        ],
                        'btn_historia' => [
                            'type' => 'text',
                            'label' => 'Texto del botón',
                            'default' => 'Nuestra Historia',
                        ],
                        'div_imagen' => [
                            'type' => 'divider',
                            'label' => 'Imagen Central',
                        ],
                        'imagen_central' => [
                            'type' => 'image',
                            'label' => 'Foto central de la sección',
                            'default' => 'img/trayectoria.png',
                            'help' => 'Foto grande del centro de la sección. Tamaño recomendado: 600x700px.',
                        ],
                        'div_stats' => [
                            'type' => 'divider',
                            'label' => 'Estadísticas (Lado Derecho)',
                        ],
                        'estadisticas' => [
                            'type' => 'repeater',
                            'label' => '',
                            'item_label' => 'Estadística',
                            'help' => 'Agrega o edita los bloques de números del lado derecho. Máximo recomendado: 3.',
                            'default' => [
                                ['numero' => '29+', 'texto' => 'Años de Experiencia'],
                                ['numero' => '+17,000', 'texto' => 'Estudiantes Graduados'],
                                ['numero' => '+35', 'texto' => 'Carreras Disponibles'],
                            ],
                            'subfields' => [
                                'numero' => [
                                    'type' => 'text',
                                    'label' => 'Número',
                                    'help' => 'Ej: 29+ o +17,000',
                                ],
                                'texto' => [
                                    'type' => 'text',
                                    'label' => 'Descripción',
                                    'help' => 'Ej: Años de Experiencia',
                                ],
                            ],
                        ],
                    ],
                ],
                'areas' => [
                    'label' => 'ÁREAS DE FORMACIÓN',
                    'fields' => [
                        'div_global' => [
                            'type' => 'divider',
                            'label' => 'Textos Generales',
                        ],
                        'etiqueta_superior' => [
                            'type' => 'text',
                            'label' => 'Etiqueta superior',
                            'default' => 'Áreas de Conocimiento',
                        ],
                        'titulo' => [
                            'type' => 'text',
                            'label' => 'Título de la sección',
                            'default' => 'Nuestras Áreas de Formación',
                            'required' => true
                        ],
                        'descripcion' => [
                            'type' => 'textarea',
                            'label' => 'Subtítulo',
                            'default' => 'Descubre las áreas de estudio que ofrecemos para tu desarrollo profesional',
                        ],
                        'lista_areas' => [
                            'type' => 'repeater',
                            'label' => 'Facultades / Áreas',
                            'item_label' => 'Área',
                            'subfields' => [
                                'titulo' => ['type' => 'text', 'label' => 'Título', 'default' => 'Nueva Área', 'required' => true],
                                'descripcion' => ['type' => 'textarea', 'label' => 'Descripción', 'required' => true],
                                'programas' => ['type' => 'textarea', 'label' => 'Programas (uno por línea)', 'required' => true],
                                'icono' => ['type' => 'image', 'label' => 'Ícono (PNG/SVG)'],
                                'imagen_fondo' => ['type' => 'image', 'label' => 'Imagen de Fondo (Opcional)', 'help' => 'Si agregas una imagen aquí, la tarjeta tomará el estilo oscuro con fondo.'],
                                'btn_texto' => ['type' => 'text', 'label' => 'Texto del botón', 'default' => 'Explorar programas', 'required' => true]
                            ],
                            'default' => [
                                [
                                    'titulo' => 'Salud',
                                    'descripcion' => 'Formación integral en ciencias de la salud con laboratorios especializados y prácticas clínicas reales.',
                                    'programas' => "Enfermería\nFisioterapia\nLaboratorio Clínico",
                                    'icono' => 'img/doctor.png',
                                    'imagen_fondo' => '',
                                    'btn_texto' => 'Explorar programas'
                                ],
                                [
                                    'titulo' => 'Ciencias Empresariales',
                                    'descripcion' => 'Desarrolla habilidades de liderazgo, gestión y emprendimiento con enfoque práctico y global.',
                                    'programas' => "Administración de Empresas\nContabilidad\nMarketing Digital",
                                    'icono' => 'img/laptop.png',
                                    'imagen_fondo' => 'img/Areas_formacion_1.png',
                                    'btn_texto' => 'Explorar programas'
                                ],
                                [
                                    'titulo' => 'Transporte',
                                    'descripcion' => 'Especialízate en logística y transporte marítimo, terrestre y multimodal con certificaciones internacionales.',
                                    'programas' => "Logística y Transporte\nComercio Exterior\nOperaciones Portuarias",
                                    'icono' => 'img/coche.png',
                                    'imagen_fondo' => '',
                                    'btn_texto' => 'Explorar programas'
                                ]
                            ]
                        ]
                    ]
                ],
                'programas' => [
                    'label' => 'PROGRAMAS DESTACADOS',
                    'fields' => [
                        'div_global' => [
                            'type' => 'divider',
                            'label' => 'Textos Generales',
                        ],
                        'etiqueta_superior' => [
                            'type' => 'text',
                            'label' => 'Etiqueta superior',
                            'default' => 'Oferta Académica',
                        ],
                        'titulo' => [
                            'type' => 'text',
                            'label' => 'Título de la sección',
                            'default' => 'Programas Destacados',
                        ],
                        'descripcion' => [
                            'type' => 'textarea',
                            'label' => 'Descripción',
                            'default' => 'Descubre nuestros programas tecnológicos de mayor demanda laboral, diseñados para insertarte rápidamente en el mercado de trabajo.',
                        ],
                        'btn_ver_todos' => [
                            'type' => 'text',
                            'label' => 'Botón "Ver todos"',
                            'default' => 'Ver todos los programas',
                        ],
                        'info_programas' => [
                            'type' => 'alert',
                            'alert_type' => 'info',
                            'label' => '<strong>CÓMO SE MUESTRAN:</strong> la sección enseña siempre <strong>cuatro tarjetas</strong>. Los tres primeros programas ocupan una tarjeta fija cada uno; el <strong>cuarto y todos los que agregues después comparten la última tarjeta</strong>, que los va pasando sola de arriba hacia abajo. Agrega los que quieras con "Añadir Programa": la rejilla no crece, crece la cuarta tarjeta por dentro.',
                        ],
                        'lista_programas' => [
                            'type' => 'repeater',
                            'label' => 'Lista de Programas',
                            'item_label' => 'Programa',
                            // Antes venía con 'fixed_items' => true, que esconde los
                            // botones de añadir, eliminar y reordenar: la lista era
                            // intocable. Se quita para poder agregar programas, que
                            // es justo lo que alimenta el carrusel de la 4a tarjeta.
                            'help' => 'El orden manda: los tres primeros van en tarjetas fijas y del cuarto en adelante rotan juntos en la última.',
                            'subfields' => [
                                'imagen' => ['type' => 'image', 'label' => 'Imagen'],
                                'modalidad' => ['type' => 'text', 'label' => 'Modalidad', 'help' => 'Ej: Presencial, Híbrido, En línea'],
                                'area' => ['type' => 'text', 'label' => 'Área'],
                                'titulo' => ['type' => 'text', 'label' => 'Título del programa', 'required' => true],
                                'duracion' => ['type' => 'text', 'label' => 'Duración'],
                                'sede' => ['type' => 'text', 'label' => 'Sede']
                            ],
                            'default' => [
                                [
                                    'imagen' => 'img/enfermeria.jpg',
                                    'modalidad' => 'Presencial',
                                    'area' => 'Salud',
                                    'titulo' => 'Tecnología Superior en Enfermería',
                                    'duracion' => '5 Semestres',
                                    'sede' => 'Guayaquil'
                                ],
                                [
                                    'imagen' => 'img/Mecanica.jpg',
                                    'modalidad' => 'Presencial',
                                    'area' => 'Ciencias Empresariales',
                                    'titulo' => 'Tecnología Superior en Marketing Digital',
                                    'duracion' => '5 Semestres',
                                    'sede' => 'Guayaquil'
                                ],
                                [
                                    'imagen' => 'img/desarrollo_software.jpg',
                                    'modalidad' => 'Presencial',
                                    'area' => 'Transporte',
                                    'titulo' => 'Tecnología Superior en Logística y Transporte',
                                    'duracion' => '5 Semestres',
                                    'sede' => 'Guayaquil'
                                ],
                                [
                                    'imagen' => 'img/administracion.jpg',
                                    'modalidad' => 'Híbrido',
                                    'area' => 'Tecnología',
                                    'titulo' => 'Tecnología Superior en Desarrollo de Software',
                                    'duracion' => '5 Semestres',
                                    'sede' => 'Guayaquil'
                                ]
                            ]
                        ]
                    ],
                ],
                'experiencia' => [
                    'label' => 'EXPERIENCIA ITB',
                    'fields' => [
                        'div_header' => [
                            'type' => 'divider',
                            'label' => 'Cabecera (Textos Principales)',
                        ],
                        'etiqueta_superior' => [
                            'type' => 'text',
                            'label' => 'Etiqueta superior (badge)',
                            'default' => 'Vida Estudiantil',
                        ],
                        'titulo' => [
                            'type' => 'text',
                            'label' => 'Título principal',
                            'default' => 'Tu Experiencia ITB',
                            'required' => true
                        ],
                        'descripcion' => [
                            'type' => 'textarea',
                            'label' => 'Descripción',
                            'default' => 'Más allá de lo académico, el ITB te ofrece una experiencia universitaria completa con servicios y beneficios diseñados para tu bienestar.',
                            'help' => 'Párrafo bajo el título principal.',
                            'required' => true
                        ],
                        'lista_caracteristicas' => [
                            'type' => 'repeater',
                            'label' => 'Lista de Beneficios',
                            'item_label' => 'Beneficio',
                            'subfields' => [
                                'titulo' => ['type' => 'text', 'label' => 'Título (Obligatorio)', 'required' => true],
                                'descripcion' => ['type' => 'textarea', 'label' => 'Descripción (Obligatorio)', 'required' => true]
                            ],
                            'default' => [
                                [
                                    'titulo' => 'Servicios Médicos',
                                    'descripcion' => 'Atención médica y odontológica gratuita para estudiantes.'
                                ],
                                [
                                    'titulo' => 'Becas y Financiamiento',
                                    'descripcion' => 'Programas de becas por excelencia académica y apoyo financiero.'
                                ],
                                [
                                    'titulo' => 'Laboratorios Modernos',
                                    'descripcion' => 'Tecnología de punta en todos nuestros laboratorios especializados.'
                                ],
                                [
                                    'titulo' => 'Bolsa de Empleo',
                                    'descripcion' => 'Conexión directa con empresas aliadas para tus prácticas y primer empleo.'
                                ]
                            ]
                        ],
                        'div_btn' => [
                            'type' => 'divider',
                            'label' => 'Botón',
                        ],
                        'btn_texto' => [
                            'type' => 'text',
                            'label' => 'Texto del botón',
                            'default' => 'Más beneficios',
                        ],
                        'div_img' => [
                            'type' => 'divider',
                            'label' => 'Imagen (Derecha)',
                        ],
                        'imagen' => [
                            'type' => 'image',
                            'label' => 'Imagen principal',
                            'default' => 'img/experiencia.png',
                        ],
                    ],
                ],
                'testimonios' => [
                    'label' => 'TESTIMONIOS',
                    'fields' => [
                        'div_global' => [
                            'type' => 'divider',
                            'label' => 'Textos Generales',
                        ],
                        'etiqueta_superior' => [
                            'type' => 'text',
                            'label' => 'Etiqueta superior (badge)',
                            'default' => 'HISTORIAS DE ÉXITO',
                        ],
                        'titulo' => [
                            'type' => 'text',
                            'label' => 'Título de la sección',
                            'default' => 'Lo que dicen nuestros Graduados',
                        ],
                        'div_testimonio' => [
                            'type' => 'divider',
                            'label' => 'Historias de Éxito',
                        ],
                        'info_testimonios' => [
                            'type' => 'alert',
                            'alert_type' => 'info',
                            'label' => '<strong>SE MUESTRA UNO A LA VEZ</strong> y van pasando solos de izquierda a derecha; los puntos de abajo permiten saltar a uno concreto. Agrega los que quieras con "Añadir Historia". Si solo dejas uno, la sección se queda quieta.',
                        ],
                        'lista_testimonios' => [
                            'type' => 'repeater',
                            'label' => '',
                            'item_label' => 'Historia',
                            'help' => 'El primero de la lista es el que se ve al abrir la página.',
                            'default' => [
                                [
                                    'imagen'  => 'img/MariaFernanda.png',
                                    'cita'    => '"El ITB me brindó las herramientas y el conocimiento necesario para destacarme en el campo laboral. Los docentes y el enfoque práctico marcaron la diferencia en mi formación profesional. Hoy lidero un equipo de trabajo gracias a la preparación que recibí."',
                                    'rol'     => 'Graduada',
                                    'nombre'  => 'María Fernanda López',
                                    'carrera' => 'Graduada en Enfermería - Promoción 2022',
                                ],
                            ],
                            'subfields' => [
                                'imagen' => [
                                    'type' => 'image',
                                    'label' => 'Foto del Graduado',
                                    'help' => 'Aparece a la izquierda. Vertical, mínimo 420x520px.',
                                ],
                                'cita' => [
                                    'type' => 'textarea',
                                    'label' => 'Testimonio',
                                    'help' => 'Texto del testimonio. Máx. 3-4 oraciones.',
                                ],
                                'rol' => [
                                    'type' => 'text',
                                    'label' => 'Rol o Título',
                                    'default' => 'Graduado',
                                ],
                                'nombre' => [
                                    'type' => 'text',
                                    'label' => 'Nombre del Graduado',
                                    'help' => 'Nombre que aparece bajo el testimonio.',
                                ],
                                'carrera' => [
                                    'type' => 'text',
                                    'label' => 'Carrera y Promoción',
                                    'help' => 'Aparece al final, bajo el nombre.',
                                ],
                            ],
                        ],
                    ],
                ],
                'autoridades' => [
                    'label' => 'AUTORIDADES',
                    'fields' => [
                        'info_gestion' => [
                            'type' => 'alert',
                            'alert_type' => 'info',
                            'label' => '<strong>INFORMACIÓN IMPORTANTE:</strong> Para agregar, editar o eliminar las personas que aparecen aquí, dirígete al menú principal en la sección <strong>CONTENIDO -> Equipo</strong> y marca la casilla <strong>Mostrar en Home</strong> en cada perfil.',
                        ],
                        'div_global' => [
                            'type' => 'divider',
                            'label' => 'Textos Generales',
                        ],
                        'etiqueta_superior' => [
                            'type' => 'text',
                            'label' => 'Etiqueta superior',
                            'default' => 'Nuestro Equipo',
                        ],
                        'titulo' => [
                            'type' => 'text',
                            'label' => 'Título de la sección',
                            'default' => 'Nuestras Autoridades',
                        ],
                        'descripcion' => [
                            'type' => 'textarea',
                            'label' => 'Descripción',
                            'default' => 'Profesionales comprometidos con la excelencia académica, la innovación educativa y la gestión transparente de nuestra comunidad universitaria.',
                        ],
                        'boton_directorio' => [
                            'type' => 'text',
                            'label' => 'Botón "Ver Directorio"',
                            'default' => 'Ver Directorio',
                        ],
                    ],
                ],
                'servicios' => [
                    'label' => 'SERVICIOS INSTITUCIONALES',
                    'fields' => [
                        'div_serv1' => [
                            'type' => 'divider',
                            'label' => 'Bloque Principal (Izquierda)',
                        ],
                        'serv1_titulo' => [
                            'type' => 'text',
                            'label' => 'Título Principal',
                            'default' => 'Bienestar Estudiantil',
                        ],
                        'serv1_desc' => [
                            'type' => 'textarea',
                            'label' => 'Descripción Principal',
                            'default' => 'Servicios médicos, psicológicos y odontológicos gratuitos',
                        ],
                        'serv1_btn' => [
                            'type' => 'text',
                            'label' => 'Texto del Botón',
                            'default' => 'Más servicios',
                        ],
                        'div_servicios_lista' => [
                            'type' => 'divider',
                            'label' => 'Tarjetas de Servicios (Derecha)',
                        ],
                        'lista_servicios' => [
                            'type' => 'repeater',
                            'label' => 'Lista de Servicios',
                            'item_label' => 'Servicio',
                            'subfields' => [
                                'titulo' => ['type' => 'text', 'label' => 'Título', 'required' => true],
                                'desc' => ['type' => 'textarea', 'label' => 'Descripción', 'required' => true],
                                'btn_texto' => ['type' => 'text', 'label' => 'Texto del Botón (ej: Ver Tour, Acceder)'],
                                'imagen' => ['type' => 'image', 'label' => 'Imagen de Fondo (Opcional)'],
                                'imagen_fija' => [
                                    'type' => 'bool',
                                    'label' => 'Mostrar la imagen siempre',
                                    'help' => 'Apagado: la tarjeta se ve blanca y la foto solo aparece al pasar el mouse. Encendido: la foto se ve siempre, sin efecto.'
                                ]
                            ],
                            'default' => [
                                [
                                    'titulo' => "Campus Virtual",
                                    'desc' => "Plataforma educativa 24/7",
                                    'btn_texto' => "Ver Tour",
                                    'imagen' => ""
                                ],
                                [
                                    'titulo' => "Horarios",
                                    'desc' => "Consulta tus horarios de clase",
                                    'btn_texto' => "Ver Horarios",
                                    'imagen' => ""
                                ],
                                [
                                    'titulo' => "Servicios Digitales",
                                    'desc' => "Trámites en línea y gestión académica",
                                    'btn_texto' => "Acceder",
                                    'imagen' => ""
                                ],
                                [
                                    'titulo' => "Podcast ITB",
                                    'desc' => "Escucha nuestro contenido educativo",
                                    'btn_texto' => "Escuchar",
                                    'imagen' => ""
                                ],
                                [
                                    'titulo' => "Arte y Deportes",
                                    'desc' => "Clubes deportivos, grupos artísticos y actividades recreativas",
                                    'btn_texto' => "Conocer Más",
                                    'imagen' => "img/bienestar_estudiantil_1.png",
                                    'imagen_fija' => '0'
                                ]
                            ]
                        ],
                    ],
                ],
                'admision' => [
                    'label' => 'ADMISIÓN',
                    'fields' => [
                        'div_textos' => [
                            'type' => 'divider',
                            'label' => 'Textos de la Sección',
                        ],
                        'titulo' => [
                            'type' => 'textarea',
                            'label' => 'Título de la sección',
                            'default' => 'Inicia tu proceso de admisión',
                            'help' => 'Título grande animado que cruza la sección de lado a lado.',
                        ],
                        'descripcion' => [
                            'type' => 'textarea',
                            'label' => 'Descripción',
                            'default' => 'Da el primer paso hacia tu futuro profesional. Déjanos tus datos y un asesor académico se contactará contigo para guiarte en la elección de tu carrera, becas y opciones de financiamiento.',
                            'help' => 'Párrafo que aparece a la izquierda del formulario.',
                        ],
                        'div_imagen' => [
                            'type' => 'divider',
                            'label' => 'Imagen (Izquierda)',
                        ],
                        'imagen_principal' => [
                            'type' => 'image',
                            'label' => 'Imagen principal',
                            'default' => 'img/admision1.png',
                            'help' => 'Fotografía que acompaña al formulario.',
                        ],
                        'div_video' => [
                            'type' => 'divider',
                            'label' => 'Botón de Video (círculo naranja)',
                        ],
                        'video_url' => [
                            'type' => 'text',
                            'label' => 'Link del video de YouTube',
                            'default' => 'https://www.youtube.com/watch?v=_arpKGQERXM',
                            'help' => 'Pega aquí el link normal de YouTube (ej: https://youtu.be/abc123 o https://www.youtube.com/watch?v=abc123). El video se abre en una ventana sobre la página. Si lo dejas vacío, el botón simplemente baja hasta el formulario.',
                        ],
                        'circular_text' => [
                            'type' => 'textarea',
                            'label' => 'Texto que gira alrededor del botón',
                            'default' => "¿CÓMO INSCRIBIRSE?\nHAZ CLIC AQUÍ",
                            'help' => 'Escribe UNA FRASE POR LÍNEA. Los puntitos separadores (•) se agregan solos, no los escribas. Cuidado: entre todas las frases no pases de unos 45 caracteres o las letras se amontonarán para caber en el círculo.',
                        ],
                        'div_form' => [
                            'type' => 'divider',
                            'label' => 'Formulario de Registro',
                        ],
                        'form_titulo' => [
                            'type' => 'text',
                            'label' => 'Título del Formulario',
                            'default' => 'Formulario de Registro',
                            'help' => 'Título que aparece arriba de los campos del formulario.',
                        ],
                        'form_subtitulo' => [
                            'type' => 'text',
                            'label' => 'Subtítulo del Formulario',
                            'default' => 'Los campos marcados con un asterisco (*) son obligatorios.',
                            'help' => 'Nota pequeña debajo del título. El asterisco se pinta en naranja automáticamente.',
                        ],
                        'btn_enviar' => [
                            'type' => 'text',
                            'label' => 'Texto del botón Enviar',
                            'default' => 'Completar registro',
                            'help' => 'Texto del botón naranja al final del formulario.',
                        ],
                        'div_programas' => [
                            'type' => 'divider',
                            'label' => 'Opciones de "Programa o Área de Interés"',
                        ],
                        'lista_programas_interes' => [
                            'type' => 'repeater',
                            'label' => '',
                            'item_label' => 'Opción',
                            'help' => 'Opciones de la lista desplegable "Programa o Área de Interés". Usa las flechas ↑ ↓ para cambiar el orden en que aparecen.',
                            'subfields' => [
                                'texto' => [
                                    'type' => 'text',
                                    'label' => 'Nombre de la opción',
                                ],
                            ],
                            'default' => [
                                ['texto' => 'Enfermería'],
                                ['texto' => 'Fisioterapia'],
                                ['texto' => 'Marketing Digital'],
                                ['texto' => 'Contabilidad'],
                                ['texto' => 'Logística y Transporte'],
                                ['texto' => 'Desarrollo de Software'],
                                ['texto' => 'Otra'],
                            ],
                        ],
                        'div_modalidades' => [
                            'type' => 'divider',
                            'label' => 'Opciones de "Modalidad Preferida"',
                        ],
                        'lista_modalidades' => [
                            'type' => 'repeater',
                            'label' => '',
                            'item_label' => 'Modalidad',
                            'help' => 'Opciones de la lista desplegable "Modalidad Preferida". Usa las flechas ↑ ↓ para cambiar el orden en que aparecen.',
                            'subfields' => [
                                'texto' => [
                                    'type' => 'text',
                                    'label' => 'Nombre de la modalidad',
                                ],
                            ],
                            'default' => [
                                ['texto' => 'Presencial'],
                                ['texto' => 'Online'],
                                ['texto' => 'Híbrida'],
                            ],
                        ],
                    ],
                ],
                'noticias' => [
                    'label' => 'NOTICIAS',
                    'fields' => [
                        'div_global' => [
                            'type' => 'divider',
                            'label' => 'Textos Generales de la Sección',
                        ],
                        'etiqueta_superior' => [
                            'type' => 'text',
                            'label' => 'Etiqueta superior (badge)',
                            'default' => 'Actualidad ITB',
                            'help' => 'Texto pequeño que aparece arriba del título.',
                        ],
                        'titulo_seccion' => [
                            'type' => 'textarea',
                            'label' => 'Título de la Sección',
                            'default' => 'Noticias y Eventos del ITB',
                            'help' => 'Título principal de la sección (soporta asteriscos *texto* para color naranja).',
                        ],
                        'boton_todas' => [
                            'type' => 'text',
                            'label' => 'Botón "Todas las noticias"',
                            'default' => 'Todas las noticias',
                            'help' => 'Texto del botón superior derecho.',
                        ],
                        'div_eventos' => [
                            'type' => 'divider',
                            'label' => 'Eventos',
                        ],
                        'info_eventos' => [
                            'type' => 'alert',
                            'alert_type' => 'info',
                            'label' => '<strong>EVENTOS:</strong> Ocupan la tarjeta grande de la izquierda. Se muestra uno a la vez y van pasando solos <strong>de izquierda a derecha</strong>. Agrega los que quieras con "Añadir Evento"; el orden es el que definas aquí con las flechas ↑ ↓.',
                        ],
                        'eventos' => [
                            'type' => 'repeater',
                            'label' => '',
                            'item_label' => 'Evento',
                            'help' => 'Cada evento es una diapositiva de la tarjeta grande.',
                            'default' => [
                                [
                                    'categoria' => 'Evento',
                                    'fecha' => '15 Sep 2025',
                                    'titulo' => '¡METAMORFOSIS CREATIVA está por comenzar!',
                                    'descripcion' => 'Lo mejor del Diseño de Modas y Maquillaje...',
                                    'imagen' => 'img/noticia_1.png',
                                ],
                            ],
                            'subfields' => [
                                'categoria' => [
                                    'type' => 'text',
                                    'label' => 'Categoría',
                                    'default' => 'Evento',
                                    'help' => 'Badge naranja de arriba (ej: Evento, Académico).',
                                ],
                                'fecha' => [
                                    'type' => 'text',
                                    'label' => 'Fecha',
                                    'default' => '',
                                    'help' => 'Ej: 15 Sep 2025.',
                                ],
                                'titulo' => [
                                    'type' => 'text',
                                    'label' => 'Título',
                                    'default' => '',
                                ],
                                'descripcion' => [
                                    'type' => 'textarea',
                                    'label' => 'Descripción / Resumen',
                                    'default' => '',
                                ],
                                'imagen' => [
                                    'type' => 'image',
                                    'label' => 'Imagen',
                                    'default' => '',
                                ],
                            ],
                        ],
                        'info_noticias' => [
                            'type' => 'alert',
                            'alert_type' => 'info',
                            'label' => '<strong>NOTICIAS SECUNDARIAS:</strong> Ocupan la tarjeta de la derecha. Se ven <strong>dos a la vez</strong> y avanzan <strong>de arriba hacia abajo</strong>: entra una nueva por arriba y sale la de abajo. Agrega las que quieras con "Añadir Noticia Secundaria".',
                        ],
                        'div_secundarias' => [
                            'type' => 'divider',
                            'label' => 'Noticias Secundarias',
                        ],
                        'secundarias' => [
                            'type' => 'repeater',
                            'label' => '',
                            'item_label' => 'Noticia Secundaria',
                            'help' => 'Agrega las noticias secundarias que aparecerán a la derecha.',
                            'default' => [
                                [
                                    'titulo' => 'Estudiantes de Diseño de Modas',
                                    'fecha' => 'Agosto 20, 2026',
                                    'imagen' => 'img/noticia_2.png',
                                ],
                                [
                                    'titulo' => 'ITB promovió una movilidad',
                                    'fecha' => 'Agosto 20, 2026',
                                    'imagen' => 'img/noticia_3.png',
                                ],
                            ],
                            'subfields' => [
                                'titulo' => [
                                    'type' => 'text',
                                    'label' => 'Título',
                                    'default' => '',
                                ],
                                'fecha' => [
                                    'type' => 'text',
                                    'label' => 'Fecha',
                                    'default' => '',
                                ],
                                'imagen' => [
                                    'type' => 'image',
                                    'label' => 'Imagen',
                                    'default' => '',
                                ],
                            ]
                        ],
                    ],
                ],
                'alianzas' => [
                    'label' => 'ALIANZAS',
                    'fields' => [
                        'div_textos' => [
                            'type' => 'divider',
                            'label' => 'Encabezado de la Sección',
                        ],
                        'titulo' => [
                            'type' => 'textarea',
                            'label' => 'Título de la sección',
                            'default' => 'Alianzas del ITB',
                            'help' => 'Si pones una palabra entre asteriscos *así*, se pinta en naranja.',
                        ],
                        'boton_todas' => [
                            'type' => 'text',
                            'label' => 'Texto del botón',
                            'default' => 'Ver Alianzas y Convenios',
                            'help' => 'Botón que aparece a la derecha del título.',
                        ],
                        'div_logos' => [
                            'type' => 'divider',
                            'label' => 'Logos del Carrusel',
                        ],
                        'lista_logos' => [
                            'type' => 'repeater',
                            'label' => '',
                            'item_label' => 'Logo',
                            'help' => 'Logos de las empresas aliadas. Se desplazan solos en bucle. Usa las flechas ↑ ↓ para cambiar el orden. Recomendado: logos con fondo transparente o blanco.',
                            'subfields' => [
                                'imagen' => [
                                    'type' => 'image',
                                    'label' => 'Logo',
                                ],
                                'nombre' => [
                                    'type' => 'text',
                                    'label' => 'Nombre de la empresa',
                                    'help' => 'No se ve en la página; sirve para accesibilidad y buscadores.',
                                ],
                            ],
                            'default' => [
                                ['imagen' => 'img/alianza_1.png', 'nombre' => 'Aliado 1'],
                                ['imagen' => 'img/alianza_2.jpg', 'nombre' => 'Aliado 2'],
                                ['imagen' => 'img/alianza_3.png', 'nombre' => 'Aliado 3'],
                                ['imagen' => 'img/alianza_4.png', 'nombre' => 'Aliado 4'],
                                ['imagen' => 'img/alianza_5.png', 'nombre' => 'Aliado 5'],
                            ],
                        ],
                        'div_fotos' => [
                            'type' => 'divider',
                            'label' => 'Galería de Fotos (carrusel de abajo)',
                        ],
                        'lista_fotos' => [
                            'type' => 'repeater',
                            'label' => '',
                            'item_label' => 'Foto',
                            'help' => 'Segunda fila de imágenes, debajo de los logos. Mismo carrusel en bucle que arriba. Usa las flechas ↑ ↓ para cambiar el orden.',
                            'subfields' => [
                                'imagen' => [
                                    'type' => 'image',
                                    'label' => 'Foto',
                                ],
                                'nombre' => [
                                    'type' => 'text',
                                    'label' => 'Descripción de la foto',
                                    'help' => 'No se ve en la página; sirve para accesibilidad y buscadores.',
                                ],
                            ],
                            'default' => [
                                ['imagen' => 'img/alianza_sub1.png', 'nombre' => 'Actividad ITB 1'],
                                ['imagen' => 'img/alianza_sub2.png', 'nombre' => 'Actividad ITB 2'],
                                ['imagen' => 'img/alianza_sub3.png', 'nombre' => 'Actividad ITB 3'],
                                ['imagen' => 'img/alianza_sub4.png', 'nombre' => 'Actividad ITB 4'],
                                ['imagen' => 'img/alianza_sub5.png', 'nombre' => 'Actividad ITB 5'],
                                ['imagen' => 'img/alianza_sub6.png', 'nombre' => 'Actividad ITB 6'],
                            ],
                        ],
                    ],
                ],
            ],
        ],
        'sobre_nosotros' => [
            'label' => 'Sobre Nosotros',
            'group' => 'paginas',
            'icon' => 'bi bi-info-circle-fill',
            'type' => 'singleton',
            'fields' => [],
        ],
        'himno_identidad' => [
            'label' => 'Himno e Identidad',
            'group' => 'paginas',
            'icon' => 'bi bi-flag-fill',
            'type' => 'singleton',
            'fields' => [],
        ],
        'transparencia_leyes' => [
            'label' => 'Transparencia / Leyes',
            'group' => 'paginas',
            'icon' => 'bi bi-bank2',
            'type' => 'singleton',
            'fields' => [],
        ],
        // Item con PÁGINA PROPIA: no es una colección ni un singleton de campos,
        // así que en vez de 'fields' declara la pantalla que lo atiende con
        // 'url'. El menú lateral y el escritorio ya saben leer esa clave, así
        // que agregar otra sección especial no obliga a tocar el motor.
        'registros' => [
            'label' => 'Registros del formulario',
            'group' => 'contenido',
            'icon' => 'bi bi-inbox-fill',
            'type' => 'custom',
            'url' => 'registros.php',
            'subtitulo' => 'Ver y descargar en CSV',
        ],
        'equipo' => [
            'label' => 'Equipo',
            'group' => 'contenido',
            'icon' => 'bi bi-people-fill',
            'type' => 'collection',
            'sortable' => true,
            'columns' => ['nombre_completo', 'cargo'],
            'fields' => [
                'nombre' => [
                    'type' => 'text',
                    'label' => 'Nombre *',
                    'help' => 'Nombre sin títulos',
                    'required' => true
                ],
                'nombre_completo' => [
                    'type' => 'text',
                    'label' => 'Nombre Completo con Títulos *',
                    'help' => 'Ej: PhD. Roberto Tolozano Benites',
                    'required' => true
                ],
                'cargo' => [
                    'type' => 'text',
                    'label' => 'Cargo *',
                    'required' => true
                ],
                'linkedin' => [
                    'type' => 'text',
                    'label' => 'URL de LinkedIn',
                    'help' => 'Enlace al perfil de LinkedIn (opcional)'
                ],
                'email' => [
                    'type' => 'text',
                    'label' => 'Email Institucional',
                    'help' => 'Correo electrónico oficial (opcional)'
                ],
                'foto' => [
                    'type' => 'image',
                    'label' => 'Foto',
                    'help' => 'Sube la fotografía de la autoridad. Tamaño recomendado 400x400px.'
                ],
                'mostrar_en_home' => [
                    'type' => 'bool',
                    'label' => 'Mostrar en Home',
                    'default' => false,
                ],
                'publicado' => [
                    'type' => 'bool',
                    'label' => 'Publicado',
                    'default' => true,
                ],
            ],
        ],
    ],
];
