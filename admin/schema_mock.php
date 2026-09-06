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
                'logo_blanco' => [
                    'type' => 'image',
                    'label' => 'Logo Blanco (Footer)',
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
                        ['texto' => 'Inicio', 'url' => '#', 'nivel' => 'padre'],
                        ['texto' => 'Nuestra Institución', 'url' => '#', 'nivel' => 'padre'],
                        ['texto' => 'Quienes Somos', 'url' => '#', 'nivel' => 'hijo'],
                        ['texto' => 'Misión y Visión', 'url' => '#', 'nivel' => 'hijo'],
                        ['texto' => 'Valores Institucionales', 'url' => '#', 'nivel' => 'hijo'],
                        ['texto' => 'Autoridades', 'url' => '#', 'nivel' => 'hijo'],
                        ['texto' => 'Preguntas Frecuentes', 'url' => '#', 'nivel' => 'hijo'],
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
                'descripcion' => [
                    'type' => 'textarea',
                    'label' => 'Lema / Descripción Institucional',
                    'default' => 'Instituto Superior Tecnológico Bolivariano de Tecnología. Formando profesionales de excelencia desde 1995.',
                ],
                'div_redes' => [
                    'type' => 'divider',
                    'label' => 'Redes Sociales',
                ],
                'facebook_url' => [
                    'type' => 'text',
                    'label' => 'Facebook URL',
                    'default' => '#',
                ],
                'instagram_url' => [
                    'type' => 'text',
                    'label' => 'Instagram URL',
                    'default' => '#',
                ],
                'twitter_url' => [
                    'type' => 'text',
                    'label' => 'Twitter / X URL',
                    'default' => '#',
                ],
                'youtube_url' => [
                    'type' => 'text',
                    'label' => 'YouTube URL',
                    'default' => '#',
                ],
                'linkedin_url' => [
                    'type' => 'text',
                    'label' => 'LinkedIn URL',
                    'default' => '#',
                ],
                'div_contacto' => [
                    'type' => 'divider',
                    'label' => 'Datos de Contacto',
                ],
                'telefono' => [
                    'type' => 'text',
                    'label' => 'Teléfono',
                    'default' => '(04) 2-566-800',
                ],
                'email' => [
                    'type' => 'text',
                    'label' => 'Correo de Contacto',
                    'default' => 'info@bolivariano.edu.ec',
                ],
                'direccion' => [
                    'type' => 'text',
                    'label' => 'Dirección',
                    'default' => 'Víctor Manuel Rendón 236 y Pedro Carbo, Guayaquil',
                ],
                'div_horarios' => [
                    'type' => 'divider',
                    'label' => 'Horarios de Atención',
                ],
                'horario_semana' => [
                    'type' => 'text',
                    'label' => 'Horario (Lunes - Viernes)',
                    'default' => 'Lunes a Viernes: 08:00 - 17:00',
                ],
                'horario_sabado' => [
                    'type' => 'text',
                    'label' => 'Horario (Sábado)',
                    'default' => 'Sábados: 08:00 - 13:00',
                ],
                'div_prefooter' => [
                    'type' => 'divider',
                    'label' => 'Pre-footer (Banner CTA)',
                ],
                'prefooter_titulo' => [
                    'type' => 'text',
                    'label' => 'Título CTA',
                    'default' => '¿Aún no decides qué carrera estudiar?',
                ],
                'prefooter_texto' => [
                    'type' => 'text',
                    'label' => 'Subtítulo CTA',
                    'default' => 'Te ayudamos a encontrar la carrera ideal para ti',
                ],
                'prefooter_btn_1' => [
                    'type' => 'text',
                    'label' => 'Botón 1',
                    'default' => 'Chatea con nosotros',
                ],
                'prefooter_btn_2' => [
                    'type' => 'text',
                    'label' => 'Botón 2',
                    'default' => 'Llámanos',
                ],
                'prefooter_btn_3' => [
                    'type' => 'text',
                    'label' => 'Botón 3',
                    'default' => 'Visítanos',
                ],
                'div_columnas' => [
                    'type' => 'divider',
                    'label' => 'Columnas',
                ],
                'col1_titulo' => [
                    'type' => 'text',
                    'label' => 'Título Columna 1',
                    'default' => 'Enlaces Rápidos',
                ],
                'enlaces_rapidos' => [
                    'type' => 'textarea',
                    'label' => 'Enlaces Rápidos (uno por línea)',
                    'default' => "Oferta Académica
Admisiones
Vida Estudiantil
Investigación
Educación Continua",
                ],
                'col2_titulo' => [
                    'type' => 'text',
                    'label' => 'Título Columna 2',
                    'default' => 'Contacto',
                ],
                'col3_titulo' => [
                    'type' => 'text',
                    'label' => 'Título Columna 3',
                    'default' => 'Horarios de Atención',
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
                            'type' => 'text',
                            'label' => 'Texto circular (repite la frase)',
                            'default' => '• EST. 1995 • ITB INSTITUTO UNIVERSITARIO ',
                            'help' => 'Cuidado: No lo hagas muy largo (máximo 45 caracteres aprox) o las letras se amontonarán para caber en el círculo.',
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
                        'imagenes_fondo' => [
                            'type' => 'repeater',
                            'label' => '',
                            'item_label' => 'Imagen',
                            'help' => 'Agrega tantas fotos de fondo como desees. El carrusel rotará automáticamente.',
                            'default' => [
                                ['archivo' => 'img/salud.jpg'],
                                ['archivo' => 'img/student.jpg'],
                                ['archivo' => 'img/student 2.jpg'],
                            ],
                            'subfields' => [
                                'archivo' => [
                                    'type' => 'image',
                                    'label' => 'Foto de Fondo',
                                    'help' => 'Tamaño recomendado: 1920x1080px',
                                ]
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
                            'default' => 'img/PHD_Roberto.jpg',
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
                            'default' => 'img/estudiantes1.png',
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
                                    'imagen_fondo' => 'img/student 2.jpg',
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
                        'lista_programas' => [
                            'type' => 'repeater',
                            'label' => 'Lista de Programas',
                            'item_label' => 'Programa',
                            'fixed_items' => true,
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
                            'label' => 'Imagen e Indicador (Derecha)',
                        ],
                        'imagen' => [
                            'type' => 'image',
                            'label' => 'Imagen principal',
                            'default' => 'img/experiencia.png',
                        ],
                        'badge_numero' => [
                            'type' => 'text',
                            'label' => 'Número del indicador (ej: 115%)',
                            'default' => '98%',
                        ],
                        'badge_texto' => [
                            'type' => 'text',
                            'label' => 'Texto del indicador',
                            'default' => 'Satisfacción Estudiantil',
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
                            'label' => 'Testimonio Principal',
                        ],
                        'imagen' => [
                            'type' => 'image',
                            'label' => 'Foto del Graduado',
                            'default' => 'img/MariaFernanda.png',
                            'help' => 'Foto que aparece a la izquierda.',
                        ],
                        'cita' => [
                            'type' => 'textarea',
                            'label' => 'Testimonio',
                            'default' => '"El ITB me brindó las herramientas y el conocimiento necesario para destacarme en el campo laboral. Los docentes y el enfoque práctico marcaron la diferencia en mi formación profesional. Hoy lidero un equipo de trabajo gracias a la preparación que recibí."',
                            'help' => 'Texto del testimonio. Máx. 3-4 oraciones.',
                        ],
                        'rol' => [
                            'type' => 'text',
                            'label' => 'Rol o Título',
                            'default' => 'Graduada',
                        ],
                        'nombre' => [
                            'type' => 'text',
                            'label' => 'Nombre del Graduado',
                            'default' => 'María Fernanda López',
                            'help' => 'Nombre que aparece bajo el testimonio.',
                        ],
                        'carrera' => [
                            'type' => 'text',
                            'label' => 'Carrera y Promoción',
                            'default' => 'Graduada en Enfermería - Promoción 2022',
                            'help' => 'Aparece al final, bajo el nombre.',
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
                        'btn_directorio' => [
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
                                'imagen' => ['type' => 'image', 'label' => 'Imagen de Fondo (Opcional)']
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
                                    'imagen' => "img/estudiantes1.png"
                                ]
                            ]
                        ],
                    ],
                ],
                'admision' => [
                    'label' => 'ADMISIÓN',
                    'fields' => [
                        'etiqueta_superior' => [
                            'type' => 'text',
                            'label' => 'Etiqueta superior (badge)',
                            'default' => 'Admisiones Abiertas',
                            'help' => 'Texto pequeño que aparece arriba del título.',
                        ],
                        'titulo' => [
                            'type' => 'textarea',
                            'label' => 'Título de la sección',
                            'default' => 'Inicia tu proceso de admisión',
                            'help' => 'Título principal de la sección de admisiones.',
                        ],
                        'descripcion' => [
                            'type' => 'textarea',
                            'label' => 'Descripción',
                            'default' => 'Da el primer paso hacia tu futuro profesional. Completa el formulario y un asesor académico se pondrá en contacto contigo para guiarte en todo el proceso de inscripción.',
                            'help' => 'Texto descriptivo del proceso de admisión.',
                        ],
                        'feature_1' => [
                            'type' => 'text',
                            'label' => 'Característica 1',
                            'default' => 'Proceso 100% en línea',
                            'help' => 'Primera ventaja del proceso de admisión.',
                        ],
                        'feature_2' => [
                            'type' => 'text',
                            'label' => 'Característica 2',
                            'default' => 'Asesoría personalizada',
                            'help' => 'Segunda ventaja del proceso de admisión.',
                        ],
                        'feature_3' => [
                            'type' => 'text',
                            'label' => 'Característica 3',
                            'default' => 'Respuesta en 24 horas',
                            'help' => 'Tercera ventaja del proceso de admisión.',
                        ],
                        'btn_enviar' => [
                            'type' => 'text',
                            'label' => 'Texto del botón Enviar',
                            'default' => 'Enviar Solicitud',
                            'help' => 'Texto del botón de envío del formulario de admisión.',
                        ],
                        'form_titulo' => [
                            'type' => 'text',
                            'label' => 'Título del Formulario',
                            'default' => 'Solicita Información',
                            'help' => 'Título que aparece arriba de los campos del formulario.',
                        ],
                        'form_terminos' => [
                            'type' => 'text',
                            'label' => 'Texto de Términos',
                            'default' => 'Al enviar este formulario, aceptas nuestra Política de Privacidad.',
                            'help' => 'Texto legal o nota pequeña debajo del botón.',
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
                        'titulo' => [
                            'type' => 'text',
                            'label' => 'Título de la Noticia',
                            'default' => '¡METAMORFOSIS CREATIVA está por comenzar!',
                            'help' => 'Título principal de la noticia.',
                        ],
                        'boton_todas' => [
                            'type' => 'text',
                            'label' => 'Botón "Todas las noticias"',
                            'default' => 'Todas las noticias',
                            'help' => 'Texto del botón superior derecho.',
                        ],
                        'div_noticia' => [
                            'type' => 'divider',
                            'label' => 'Noticia Destacada Principal',
                        ],
                        'categoria' => [
                            'type' => 'text',
                            'label' => 'Categoría',
                            'default' => 'Evento',
                            'help' => 'Categoría que se muestra como badge (ej: Evento, Académico).',
                        ],
                        'fecha' => [
                            'type' => 'text',
                            'label' => 'Fecha de publicación',
                            'default' => '15 Sep 2025',
                            'help' => 'Fecha de publicación (ej: 15 Sep 2025).',
                        ],
                        'descripcion' => [
                            'type' => 'textarea',
                            'label' => 'Descripción / Resumen',
                            'default' => 'Lo mejor del Diseño de Modas y Maquillaje...',
                            'help' => 'Resumen corto de la noticia.',
                        ],
                        'imagen' => [
                            'type' => 'image',
                            'label' => 'Imagen de la Noticia Principal',
                            'default' => 'img/noticia_1.png',
                            'help' => 'Imagen destacada de la noticia.',
                        ],
                        'info_noticias' => [
                            'type' => 'alert',
                            'alert_type' => 'info',
                            'label' => '<strong>NUEVA FUNCIÓN:</strong> Las noticias secundarias ahora son dinámicas. Puedes agregar las que quieras haciendo clic en "Añadir Noticia Secundaria".',
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
            ],
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
