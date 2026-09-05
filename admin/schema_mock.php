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
                    'default' => 'img/logo-itb-white.png',
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
                        'imagen_1' => [
                            'type' => 'image',
                            'label' => 'Imagen de fondo 1',
                            'default' => 'img/salud.jpg',
                            'help' => 'Primera foto del carrusel de fondo. Tamaño recomendado: 1920x1080px.',
                        ],
                        'imagen_2' => [
                            'type' => 'image',
                            'label' => 'Imagen de fondo 2 (Opcional)',
                            'default' => 'img/student.jpg',
                            'help' => 'Si se sube, el fondo rotará.',
                        ],
                        'imagen_3' => [
                            'type' => 'image',
                            'label' => 'Imagen de fondo 3 (Opcional)',
                            'default' => 'img/student 2.jpg',
                            'help' => 'Tercera foto para el carrusel.',
                        ],
                        'imagen_video_thumb' => [
                            'type' => 'image',
                            'label' => 'Miniatura del Video (círculo)',
                            'default' => 'img/hero-video-thumb.jpg',
                            'help' => 'Foto circular del video. Tamaño recomendado: 400x400px.',
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
                            'label' => 'Foto del Canciller',
                            'default' => 'img/PHD_Roberto.jpg',
                        ],
                        'canciller_nombre' => [
                            'type' => 'text',
                            'label' => 'Nombre',
                            'default' => 'PhD. Roberto Tolozano Benites',
                        ],
                        'canciller_cargo' => [
                            'type' => 'text',
                            'label' => 'Cargo',
                            'default' => 'Canciller del ITB',
                        ],
                        'div_campus_img' => [
                            'type' => 'divider',
                            'label' => 'Foto del Campus (Centro)',
                        ],
                        'imagen_campus' => [
                            'type' => 'image',
                            'label' => 'Foto del Campus',
                            'default' => 'img/estudiantes1.png',
                            'help' => 'Imagen central con efecto Jarallax.',
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
                        'div_stats' => [
                            'type' => 'divider',
                            'label' => 'Estadísticas (Lado Derecho)',
                        ],
                        'stat_anios' => [
                            'type' => 'text',
                            'label' => 'Número: Años de Experiencia',
                            'default' => '29+',
                        ],
                        'stat_anios_label' => [
                            'type' => 'text',
                            'label' => 'Texto: Años de Experiencia',
                            'default' => 'Años de Experiencia',
                        ],
                        'stat_graduados' => [
                            'type' => 'text',
                            'label' => 'Número: Estudiantes Graduados',
                            'default' => '+17,000',
                        ],
                        'stat_graduados_label' => [
                            'type' => 'text',
                            'label' => 'Texto: Estudiantes Graduados',
                            'default' => 'Estudiantes Graduados',
                        ],
                        'stat_carreras' => [
                            'type' => 'text',
                            'label' => 'Número: Carreras Disponibles',
                            'default' => '+35',
                        ],
                        'stat_carreras_label' => [
                            'type' => 'text',
                            'label' => 'Texto: Carreras Disponibles',
                            'default' => 'Carreras Disponibles',
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
                            'default' => 'Nuestras Áreas de *Formación*',
                            'help' => 'Usa asteriscos *texto* para pintar una palabra de color naranja.',
                        ],
                        'descripcion' => [
                            'type' => 'textarea',
                            'label' => 'Subtítulo',
                            'default' => 'Descubre las áreas de estudio que ofrecemos para tu desarrollo profesional',
                        ],
                        'div_area1' => [
                            'type' => 'divider',
                            'label' => 'Área 1: Salud',
                        ],
                        'area1_icono' => [
                            'type' => 'image',
                            'label' => 'Ícono Área 1 (PNG blanco)',
                            'default' => 'img/doctor.png',
                        ],
                        'area1_titulo' => [
                            'type' => 'text',
                            'label' => 'Título',
                            'default' => "Facultad de Salud\ny Servicios Sociales (FASSS)",
                        ],
                        'area1_desc' => [
                            'type' => 'textarea',
                            'label' => 'Descripción',
                            'default' => 'Carreras técnicas y tecnológicas enfocadas en el cuidado de la salud, enfermería, rehabilitación y bienestar comunitario.',
                        ],
                        'area1_btn' => [
                            'type' => 'text',
                            'label' => 'Texto del botón',
                            'default' => 'Explorar programas',
                        ],
                        'div_area2' => [
                            'type' => 'divider',
                            'label' => 'Área 2: Ciencias Empresariales',
                        ],
                        'area2_icono' => [
                            'type' => 'image',
                            'label' => 'Ícono Área 2 (PNG blanco)',
                            'default' => 'img/laptop.png',
                        ],
                        'area2_bg' => [
                            'type' => 'image',
                            'label' => 'Imagen de Fondo de la Tarjeta',
                            'default' => 'img/estudiantes1.png',
                            'help' => 'Imagen fotográfica con filtro azul para la tarjeta destacada.',
                        ],
                        'area2_titulo' => [
                            'type' => 'text',
                            'label' => 'Título',
                            'default' => "Facultad de Ciencias Empresariales\ny Sistemas / Económicas y\nEmpresariales (FACES)",
                        ],
                        'area2_desc' => [
                            'type' => 'textarea',
                            'label' => 'Descripción',
                            'default' => 'Programas de gestión, contabilidad, marketing y comercio para liderar en el sector empresarial e industrial.',
                        ],
                        'area2_btn' => [
                            'type' => 'text',
                            'label' => 'Texto del botón',
                            'default' => 'Explorar programas',
                        ],
                        'div_area3' => [
                            'type' => 'divider',
                            'label' => 'Área 3: Transporte',
                        ],
                        'area3_icono' => [
                            'type' => 'image',
                            'label' => 'Ícono Área 3 (PNG blanco)',
                            'default' => 'img/coche.png',
                        ],
                        'area3_titulo' => [
                            'type' => 'text',
                            'label' => 'Título',
                            'default' => "Facultad de Transporte\ny Vialidad (FATV)",
                        ],
                        'area3_desc' => [
                            'type' => 'textarea',
                            'label' => 'Descripción',
                            'default' => 'Formación especializada en mecánica, gestión de transporte, seguridad vial y escuela de conducción',
                        ],
                        'area3_btn' => [
                            'type' => 'text',
                            'label' => 'Texto del botón',
                            'default' => 'Explorar programas',
                        ],
                    ],
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
                            'default' => 'Programas *Destacados*',
                            'help' => 'Usa asteriscos *texto* para pintar una palabra de color naranja.',
                        ],
                        'btn_ver_todos' => [
                            'type' => 'text',
                            'label' => 'Botón "Ver todos"',
                            'default' => 'Ver todos los programas',
                        ],
                        'div_prog1' => [
                            'type' => 'divider',
                            'label' => 'Programa 1 (Salud)',
                        ],
                        'prog1_imagen' => [
                            'type' => 'image',
                            'label' => 'Imagen',
                            'default' => 'img/enfermeria.jpg',
                        ],
                        'prog1_modalidad' => [
                            'type' => 'text',
                            'label' => 'Modalidad',
                            'default' => 'Presencial',
                            'help' => 'Ej: Presencial, Híbrido, En línea',
                        ],
                        'prog1_area' => [
                            'type' => 'text',
                            'label' => 'Área',
                            'default' => 'Salud',
                        ],
                        'prog1_titulo' => [
                            'type' => 'text',
                            'label' => 'Título del programa',
                            'default' => 'Tecnología Superior en Enfermería',
                        ],
                        'prog1_duracion' => [
                            'type' => 'text',
                            'label' => 'Duración',
                            'default' => '5 Semestres',
                        ],
                        'prog1_sede' => [
                            'type' => 'text',
                            'label' => 'Sede',
                            'default' => 'Guayaquil',
                        ],
                        'div_prog2' => [
                            'type' => 'divider',
                            'label' => 'Programa 2 (Empresariales)',
                        ],
                        'prog2_imagen' => [
                            'type' => 'image',
                            'label' => 'Imagen',
                            'default' => 'img/Mecanica.jpg',
                        ],
                        'prog2_modalidad' => [
                            'type' => 'text',
                            'label' => 'Modalidad',
                            'default' => 'Presencial',
                            'help' => 'Ej: Presencial, Híbrido, En línea',
                        ],
                        'prog2_area' => [
                            'type' => 'text',
                            'label' => 'Área',
                            'default' => 'Ciencias Empresariales',
                        ],
                        'prog2_titulo' => [
                            'type' => 'text',
                            'label' => 'Título del programa',
                            'default' => 'Tecnología Superior en Marketing Digital',
                        ],
                        'prog2_duracion' => [
                            'type' => 'text',
                            'label' => 'Duración',
                            'default' => '5 Semestres',
                        ],
                        'prog2_sede' => [
                            'type' => 'text',
                            'label' => 'Sede',
                            'default' => 'Guayaquil',
                        ],
                        'div_prog3' => [
                            'type' => 'divider',
                            'label' => 'Programa 3 (Logística)',
                        ],
                        'prog3_imagen' => [
                            'type' => 'image',
                            'label' => 'Imagen',
                            'default' => 'img/desarrollo_software.jpg',
                        ],
                        'prog3_modalidad' => [
                            'type' => 'text',
                            'label' => 'Modalidad',
                            'default' => 'Presencial',
                            'help' => 'Ej: Presencial, Híbrido, En línea',
                        ],
                        'prog3_area' => [
                            'type' => 'text',
                            'label' => 'Área',
                            'default' => 'Transporte',
                        ],
                        'prog3_titulo' => [
                            'type' => 'text',
                            'label' => 'Título del programa',
                            'default' => 'Tecnología Superior en Logística y Transporte',
                        ],
                        'prog3_duracion' => [
                            'type' => 'text',
                            'label' => 'Duración',
                            'default' => '5 Semestres',
                        ],
                        'prog3_sede' => [
                            'type' => 'text',
                            'label' => 'Sede',
                            'default' => 'Guayaquil',
                        ],
                        'div_prog4' => [
                            'type' => 'divider',
                            'label' => 'Programa 4 (Software)',
                        ],
                        'prog4_imagen' => [
                            'type' => 'image',
                            'label' => 'Imagen',
                            'default' => 'img/administracion.jpg',
                        ],
                        'prog4_modalidad' => [
                            'type' => 'text',
                            'label' => 'Modalidad',
                            'default' => 'Híbrido',
                            'help' => 'Ej: Presencial, Híbrido, En línea',
                        ],
                        'prog4_area' => [
                            'type' => 'text',
                            'label' => 'Área',
                            'default' => 'Tecnología',
                        ],
                        'prog4_titulo' => [
                            'type' => 'text',
                            'label' => 'Título del programa',
                            'default' => 'Tecnología Superior en Desarrollo de Software',
                        ],
                        'prog4_duracion' => [
                            'type' => 'text',
                            'label' => 'Duración',
                            'default' => '5 Semestres',
                        ],
                        'prog4_sede' => [
                            'type' => 'text',
                            'label' => 'Sede',
                            'default' => 'Guayaquil',
                        ],
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
                            'label' => 'Título de la sección',
                            'default' => 'Tu Experiencia *ITB*',
                            'help' => 'Usa asteriscos *texto* para pintar una palabra de color naranja.',
                        ],
                        'descripcion' => [
                            'type' => 'textarea',
                            'label' => 'Descripción',
                            'default' => 'Más allá de lo académico, el ITB te ofrece una experiencia universitaria completa con servicios y beneficios diseñados para tu bienestar.',
                            'help' => 'Párrafo bajo el título principal.',
                        ],
                        'div_c1' => [
                            'type' => 'divider',
                            'label' => 'Característica 1',
                        ],
                        'caract1_titulo' => [
                            'type' => 'text',
                            'label' => 'Título',
                            'default' => 'Servicios Médicos',
                        ],
                        'caract1_desc' => [
                            'type' => 'text',
                            'label' => 'Descripción',
                            'default' => 'Atención médica y odontológica gratuita para estudiantes.',
                        ],
                        'div_c2' => [
                            'type' => 'divider',
                            'label' => 'Característica 2',
                        ],
                        'caract2_titulo' => [
                            'type' => 'text',
                            'label' => 'Título',
                            'default' => 'Becas y Financiamiento',
                        ],
                        'caract2_desc' => [
                            'type' => 'text',
                            'label' => 'Descripción',
                            'default' => 'Programas de becas por excelencia académica y apoyo financiero.',
                        ],
                        'div_c3' => [
                            'type' => 'divider',
                            'label' => 'Característica 3',
                        ],
                        'caract3_titulo' => [
                            'type' => 'text',
                            'label' => 'Título',
                            'default' => 'Laboratorios Modernos',
                        ],
                        'caract3_desc' => [
                            'type' => 'text',
                            'label' => 'Descripción',
                            'default' => 'Tecnología de punta en todos nuestros laboratorios especializados.',
                        ],
                        'div_c4' => [
                            'type' => 'divider',
                            'label' => 'Característica 4',
                        ],
                        'caract4_titulo' => [
                            'type' => 'text',
                            'label' => 'Título',
                            'default' => 'Bolsa de Empleo',
                        ],
                        'caract4_desc' => [
                            'type' => 'text',
                            'label' => 'Descripción',
                            'default' => 'Conexión directa con empresas aliadas para tus prácticas y primer empleo.',
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
                            'default' => 'Lo que dicen nuestros *Graduados*',
                            'help' => 'Usa asteriscos *texto* para pintar una palabra de color naranja.',
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
                        'nombre' => [
                            'type' => 'text',
                            'label' => 'Nombre del Graduado',
                            'default' => 'María Fernanda Gómez',
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
                            'default' => 'Nuestras *Autoridades*',
                            'help' => 'Usa asteriscos *texto* para pintar una palabra de color naranja.',
                        ],
                        'btn_directorio' => [
                            'type' => 'text',
                            'label' => 'Botón "Ver Directorio"',
                            'default' => 'Ver Directorio',
                        ],
                        'subtitulo' => [
                            'type' => 'textarea',
                            'label' => 'Subtítulo',
                            'default' => 'Profesionales comprometidos con la excelencia académica, la innovación educativa y la gestión transparente de nuestra comunidad universitaria.',
                        ],
                        'div_aut1' => [
                            'type' => 'divider',
                            'label' => 'Autoridad 1: Canciller',
                        ],
                        'aut1_foto' => [
                            'type' => 'image',
                            'label' => 'Foto Autoridad 1',
                            'default' => 'img/PHD.Roberto_tolozano.jpg',
                        ],
                        'aut1_nombre' => [
                            'type' => 'text',
                            'label' => 'Nombre',
                            'default' => 'PhD. Roberto Tolozano Benites',
                        ],
                        'aut1_cargo' => [
                            'type' => 'text',
                            'label' => 'Cargo',
                            'default' => 'Canciller',
                        ],
                        'aut1_email' => [
                            'type' => 'text',
                            'label' => 'Correo',
                            'default' => '#',
                        ],
                        'aut1_telf' => [
                            'type' => 'text',
                            'label' => 'Teléfono',
                            'default' => '#',
                        ],
                        'div_aut2' => [
                            'type' => 'divider',
                            'label' => 'Autoridad 2: Rectora',
                        ],
                        'aut2_foto' => [
                            'type' => 'image',
                            'label' => 'Foto Autoridad 2',
                            'default' => 'img/PHD.Elena_Tolozano.jpg',
                        ],
                        'aut2_nombre' => [
                            'type' => 'text',
                            'label' => 'Nombre',
                            'default' => 'PhD. Elena Tolozano Benites',
                        ],
                        'aut2_cargo' => [
                            'type' => 'text',
                            'label' => 'Cargo',
                            'default' => 'Rectora',
                        ],
                        'aut2_email' => [
                            'type' => 'text',
                            'label' => 'Correo',
                            'default' => '#',
                        ],
                        'aut2_telf' => [
                            'type' => 'text',
                            'label' => 'Teléfono',
                            'default' => '#',
                        ],
                        'div_aut3' => [
                            'type' => 'divider',
                            'label' => 'Autoridad 3: Vicerrector Académico',
                        ],
                        'aut3_foto' => [
                            'type' => 'image',
                            'label' => 'Foto Autoridad 3',
                            'default' => 'img/PHD.Luis_alzate.jpg',
                        ],
                        'aut3_nombre' => [
                            'type' => 'text',
                            'label' => 'Nombre',
                            'default' => 'PhD. Luis Alzate Peralta',
                        ],
                        'aut3_cargo' => [
                            'type' => 'text',
                            'label' => 'Cargo',
                            'default' => 'Vicerrector Académico y de Investigación',
                        ],
                        'aut3_email' => [
                            'type' => 'text',
                            'label' => 'Correo',
                            'default' => '#',
                        ],
                        'aut3_telf' => [
                            'type' => 'text',
                            'label' => 'Teléfono',
                            'default' => '#',
                        ],
                        'div_aut4' => [
                            'type' => 'divider',
                            'label' => 'Autoridad 4: Vicerrectora de Extensión',
                        ],
                        'aut4_foto' => [
                            'type' => 'image',
                            'label' => 'Foto Autoridad 4',
                            'default' => 'img/PHD.Michelle_tolozano.webp',
                        ],
                        'aut4_nombre' => [
                            'type' => 'text',
                            'label' => 'Nombre',
                            'default' => 'PhD. Michelle Tolozano Lapierre',
                        ],
                        'aut4_cargo' => [
                            'type' => 'text',
                            'label' => 'Cargo',
                            'default' => 'Vicerrectora de Extensión y Gestión Administrativa',
                        ],
                        'aut4_email' => [
                            'type' => 'text',
                            'label' => 'Correo',
                            'default' => '#',
                        ],
                        'aut4_telf' => [
                            'type' => 'text',
                            'label' => 'Teléfono',
                            'default' => '#',
                        ],
                    ],
                ],
                'servicios' => [
                    'label' => 'SERVICIOS INSTITUCIONALES',
                    'fields' => [
                        'div_global' => [
                            'type' => 'divider',
                            'label' => 'Textos Generales',
                        ],
                        'etiqueta_superior' => [
                            'type' => 'text',
                            'label' => 'Etiqueta superior (badge)',
                            'default' => 'Servicios Institucionales',
                        ],
                        'titulo' => [
                            'type' => 'text',
                            'label' => 'Título de la sección',
                            'default' => 'Todo lo que necesitas en  *un solo lugar*',
                            'help' => 'Usa asteriscos *texto* para pintar una palabra de color naranja.',
                        ],
                        'div_serv1' => [
                            'type' => 'divider',
                            'label' => 'Servicio 1: Bienestar',
                        ],
                        'serv1_titulo' => [
                            'type' => 'text',
                            'label' => 'Título',
                            'default' => 'Bienestar Estudiantil',
                        ],
                        'serv1_desc' => [
                            'type' => 'text',
                            'label' => 'Descripción',
                            'default' => 'Servicios médicos, psicológicos y odontológicos gratuitos',
                        ],
                        'serv1_imagen' => [
                            'type' => 'image',
                            'label' => 'Imagen',
                            'default' => 'img/servicio-bienestar.jpg',
                        ],
                        'div_serv2' => [
                            'type' => 'divider',
                            'label' => 'Servicio 2: Campus',
                        ],
                        'serv2_titulo' => [
                            'type' => 'text',
                            'label' => 'Título',
                            'default' => 'Campus Virtual',
                        ],
                        'serv2_desc' => [
                            'type' => 'text',
                            'label' => 'Descripción',
                            'default' => 'Plataforma educativa 24/7',
                        ],
                        'serv2_imagen' => [
                            'type' => 'image',
                            'label' => 'Imagen',
                            'default' => 'img/servicio-campus.jpg',
                        ],
                        'div_serv3' => [
                            'type' => 'divider',
                            'label' => 'Servicio 3: Horarios',
                        ],
                        'serv3_titulo' => [
                            'type' => 'text',
                            'label' => 'Título',
                            'default' => 'Horarios',
                        ],
                        'serv3_desc' => [
                            'type' => 'text',
                            'label' => 'Descripción',
                            'default' => 'Consulta tus horarios de clase',
                        ],
                        'serv3_imagen' => [
                            'type' => 'image',
                            'label' => 'Imagen',
                            'default' => 'img/salud.jpg',
                        ],
                        'div_serv4' => [
                            'type' => 'divider',
                            'label' => 'Servicio 4: Digitales',
                        ],
                        'serv4_titulo' => [
                            'type' => 'text',
                            'label' => 'Título',
                            'default' => 'Servicios Digitales',
                        ],
                        'serv4_desc' => [
                            'type' => 'text',
                            'label' => 'Descripción',
                            'default' => 'Trámites en línea y gestión académica',
                        ],
                        'serv4_imagen' => [
                            'type' => 'image',
                            'label' => 'Imagen',
                            'default' => 'img/salud.jpg',
                        ],
                        'div_serv5' => [
                            'type' => 'divider',
                            'label' => 'Servicio 5: Podcast',
                        ],
                        'serv5_titulo' => [
                            'type' => 'text',
                            'label' => 'Título',
                            'default' => 'Podcast ITB',
                        ],
                        'serv5_desc' => [
                            'type' => 'text',
                            'label' => 'Descripción',
                            'default' => 'Escucha nuestro contenido educativo',
                        ],
                        'serv5_imagen' => [
                            'type' => 'image',
                            'label' => 'Imagen',
                            'default' => 'img/salud.jpg',
                        ],
                        'div_serv6' => [
                            'type' => 'divider',
                            'label' => 'Servicio 6: Deportes',
                        ],
                        'serv6_titulo' => [
                            'type' => 'text',
                            'label' => 'Título',
                            'default' => 'Arte y Deportes',
                        ],
                        'serv6_desc' => [
                            'type' => 'text',
                            'label' => 'Descripción',
                            'default' => 'Clubes deportivos, grupos artísticos y actividades recreativas',
                        ],
                        'serv6_imagen' => [
                            'type' => 'image',
                            'label' => 'Imagen',
                            'default' => 'img/estudiantes1.png',
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
                        'titulo' => [
                            'type' => 'text',
                            'label' => 'Título de la Noticia',
                            'default' => 'Casa Abierta ITB 2025: Descubre tu vocación profesional',
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
                            'default' => 'Visita nuestro campus y conoce de primera mano nuestras instalaciones, docentes y oferta académica en la Casa Abierta más grande del año.',
                            'help' => 'Resumen corto de la noticia.',
                        ],
                        'imagen' => [
                            'type' => 'image',
                            'label' => 'Imagen de la Noticia Principal',
                            'default' => 'img/noticia-principal.jpg',
                            'help' => 'Imagen destacada de la noticia.',
                        ],
                        'div_sec1' => [
                            'type' => 'divider',
                            'label' => 'Noticia Secundaria 1',
                        ],
                        'sec1_titulo' => [
                            'type' => 'text',
                            'label' => 'Título Noticia 1',
                            'default' => 'Convenio internacional con universidad de España',
                        ],
                        'sec1_fecha' => [
                            'type' => 'text',
                            'label' => 'Fecha Noticia 1',
                            'default' => '10 Sep 2025',
                        ],
                        'sec1_imagen' => [
                            'type' => 'image',
                            'label' => 'Imagen Noticia 1',
                            'default' => 'img/noticia-2.jpg',
                        ],
                        'div_sec2' => [
                            'type' => 'divider',
                            'label' => 'Noticia Secundaria 2',
                        ],
                        'sec2_titulo' => [
                            'type' => 'text',
                            'label' => 'Título Noticia 2',
                            'default' => 'Graduación de la promoción 2025: más de 500 nuevos profesionales',
                        ],
                        'sec2_fecha' => [
                            'type' => 'text',
                            'label' => 'Fecha Noticia 2',
                            'default' => '05 Sep 2025',
                        ],
                        'sec2_imagen' => [
                            'type' => 'image',
                            'label' => 'Imagen Noticia 2',
                            'default' => 'img/noticia-3.jpg',
                        ],
                        'div_sec3' => [
                            'type' => 'divider',
                            'label' => 'Noticia Secundaria 3',
                        ],
                        'sec3_titulo' => [
                            'type' => 'text',
                            'label' => 'Título Noticia 3',
                            'default' => 'ITB inaugura nuevo laboratorio de simulación clínica',
                        ],
                        'sec3_fecha' => [
                            'type' => 'text',
                            'label' => 'Fecha Noticia 3',
                            'default' => '01 Sep 2025',
                        ],
                        'sec3_imagen' => [
                            'type' => 'image',
                            'label' => 'Imagen Noticia 3',
                            'default' => 'img/noticia-4.jpg',
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
