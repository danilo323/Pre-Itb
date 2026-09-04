<?php
// admin/schema_mock.php
// Schema completo basado en los archivos includes/ del frontend.
// Cada clave = una sección del frontend.
// 'type' => 'singleton' (único) | 'collection' (lista con alta/baja)
// 'default' = valor que ya está escrito en el HTML del frontend.

return [

    // =====================================================
    // HEADER (Singleton)
    // =====================================================
    'header' => [
        'label' => 'Encabezado / Menú',
        'type'  => 'singleton',
        'icon'  => '🔝',
        'fields' => [
            'logo' => [
                'type'    => 'image',
                'label'   => 'Logo principal',
                'default' => 'img/logo-itb.png',
                'help'    => 'Logo que aparece en la barra de navegación superior.',
            ],
            'cta_btn_1' => [
                'type'    => 'text',
                'label'   => 'Botón 1 (Solicitar)',
                'default' => 'Solicitar Información',
                'help'    => 'Texto del primer botón del menú superior.',
            ],
            'cta_btn_2' => [
                'type'    => 'text',
                'label'   => 'Botón 2 (Matricúlate)',
                'default' => 'Matricúlame',
                'help'    => 'Texto del segundo botón (CTA principal del menú).',
            ],
        ],
    ],

    // =====================================================
    // HERO (Singleton)
    // =====================================================
    'hero' => [
        'label' => 'Hero (Portada)',
        'type'  => 'singleton',
        'icon'  => '🦸',
        'fields' => [
            'div_left' => ['type' => 'divider', 'label' => 'Lado Izquierdo (Textos y Botón)'],
            'subtitulo' => [
                'type'    => 'text',
                'label'   => 'Subtítulo (etiqueta pequeña)',
                'default' => 'Educación Superior de Excelencia',
                'help'    => 'Texto pequeño naranja sobre el título principal.',
            ],
            'titulo' => [
                'type'    => 'textarea',
                'label'   => 'Título principal',
                'default' => 'Construye tu Futuro, Lidera el Mañana',
                'help'    => 'Título grande de la portada. Máx. 2 líneas.',
            ],
            'descripcion' => [
                'type'    => 'textarea',
                'label'   => 'Descripción',
                'default' => 'Bienvenida al ITB. Formamos profesionales de alto nivel con educación práctica, tecnología e innovación para ayudarte a alcanzar el éxito laboral.',
                'help'    => 'Párrafo de descripción debajo del título.',
            ],
            'cta_texto' => [
                'type'    => 'text',
                'label'   => 'Texto del botón',
                'default' => 'Explorar Programas',
                'help'    => 'Texto del botón principal de la portada.',
            ],

            'div_stats' => ['type' => 'divider', 'label' => 'Estadísticas (Debajo del botón)'],
            'stat_numero' => [
                'type'    => 'text',
                'label'   => 'Texto de estadística (ej: +20K...)',
                'default' => '+20K Estudiantes Graduados',
                'help'    => 'Dato destacado que aparece debajo del botón.',
            ],
            'rating' => [
                'type'    => 'text',
                'label'   => 'Calificación de estrellas',
                'default' => '4.9',
                'help'    => 'Número de calificación con estrellas. Solo números.',
            ],

            'div_right' => ['type' => 'divider', 'label' => 'Lado Derecho y Fondo (Multimedia)'],
            'circular_text' => [
                'type'    => 'text',
                'label'   => 'Texto circular (repite la frase)',
                'default' => 'ITB INSTITUTO UNIVERSITARIO • EST. 1995 • ITB INSTITUTO UNIVERSITARIO • EST. 1995 •',
            ],
            'video_url' => [
                'type'    => 'text',
                'label'   => 'URL del Video (YouTube)',
                'default' => 'https://youtu.be/eTgzLxWGgS4?si=itTyNJd-Es1E4f-B',
                'help'    => 'Enlace del video institucional que se abre al presionar play.',
            ],
            'imagen_bg' => [
                'type'    => 'image',
                'label'   => 'Imagen de fondo del Hero',
                'default' => 'img/hero-bg.jpg',
                'help'    => 'Foto de fondo principal. Tamaño recomendado: 1920x1080px.',
            ],
            'imagen_video_thumb' => [
                'type'    => 'image',
                'label'   => 'Miniatura del Video (círculo)',
                'default' => 'img/hero-video-thumb.jpg',
                'help'    => 'Foto circular del video. Tamaño recomendado: 400x400px.',
            ],
        ],
    ],

    // =====================================================
    // TRAYECTORIA (Singleton)
    // =====================================================
    'trayectoria' => [
        'label' => 'Trayectoria',
        'type'  => 'singleton',
        'icon'  => '📅',
        'fields' => [
            'div_global' => ['type' => 'divider', 'label' => 'Textos Generales'],
            'etiqueta_superior' => [
                'type'    => 'text',
                'label'   => 'Etiqueta superior',
                'default' => 'NUESTRA TRAYECTORIA',
            ],
            'titulo' => [
                'type'    => 'textarea',
                'label'   => 'Título de la sección',
                'default' => 'Trayectoria y Compromiso con la Educación',
            ],
            'descripcion' => [
                'type'    => 'textarea',
                'label'   => 'Descripción',
                'default' => 'Desde 1995, el Instituto Superior Tecnológico Bolivariano de Tecnología ha formado profesionales con una educación integral basada en valores, innovación y excelencia académica. Nuestro compromiso es transformar vidas a través del conocimiento.',
            ],
            
            'div_perfil' => ['type' => 'divider', 'label' => 'Perfil del Canciller'],
            'canciller_foto' => [
                'type'    => 'image',
                'label'   => 'Foto',
                'default' => 'img/canciller.jpg',
            ],
            'canciller_nombre' => [
                'type'    => 'text',
                'label'   => 'Nombre',
                'default' => 'PhD. Roberto Tolozano Benites',
            ],
            'canciller_cargo' => [
                'type'    => 'text',
                'label'   => 'Cargo',
                'default' => 'Canciller del ITB',
            ],

            'div_btn' => ['type' => 'divider', 'label' => 'Botón'],
            'btn_historia' => [
                'type'    => 'text',
                'label'   => 'Texto del botón',
                'default' => 'Nuestra Historia',
            ],

            'div_stats' => ['type' => 'divider', 'label' => 'Estadísticas (Lado Derecho)'],
            'stat_anios' => [
                'type'    => 'text',
                'label'   => 'Número: Años de Experiencia',
                'default' => '29+',
            ],
            'stat_anios_label' => [
                'type'    => 'text',
                'label'   => 'Texto: Años de Experiencia',
                'default' => 'Años de Experiencia',
            ],
            'stat_graduados' => [
                'type'    => 'text',
                'label'   => 'Número: Estudiantes Graduados',
                'default' => '+17,000',
            ],
            'stat_graduados_label' => [
                'type'    => 'text',
                'label'   => 'Texto: Estudiantes Graduados',
                'default' => 'Estudiantes Graduados',
            ],
            'stat_carreras' => [
                'type'    => 'text',
                'label'   => 'Número: Carreras Disponibles',
                'default' => '+35',
            ],
            'stat_carreras_label' => [
                'type'    => 'text',
                'label'   => 'Texto: Carreras Disponibles',
                'default' => 'Carreras Disponibles',
            ],
        ],
    ],

    // =====================================================
    // ÁREAS DE FORMACIÓN (Collection)
    // =====================================================
    'areas' => [
        'label' => 'Áreas de Formación',
        'type'  => 'singleton',
        'icon'  => '📚',
        'fields' => [
            'div_global' => ['type' => 'divider', 'label' => 'Textos Generales'],
            'etiqueta_superior' => ['type' => 'text', 'label' => 'Etiqueta superior', 'default' => 'Áreas de Conocimiento'],
            'titulo_seccion_1'  => ['type' => 'text', 'label' => 'Título (parte oscura)', 'default' => 'Nuestras Áreas de'],
            'titulo_seccion_2'  => ['type' => 'text', 'label' => 'Título (parte naranja)', 'default' => 'Formación'],
            'descripcion'       => ['type' => 'textarea', 'label' => 'Subtítulo', 'default' => 'Descubre las áreas de estudio que ofrecemos para tu desarrollo profesional'],

            'div_area1' => ['type' => 'divider', 'label' => 'Área 1'],
            'area1_titulo'    => ['type' => 'text', 'label' => 'Título', 'default' => 'Salud'],
            'area1_desc'      => ['type' => 'textarea', 'label' => 'Descripción', 'default' => 'Formación integral en ciencias de la salud con laboratorios especializados y prácticas clínicas reales.'],
            'area1_programas' => ['type' => 'textarea', 'label' => 'Programas (uno por línea)', 'default' => "Enfermería\nFisioterapia\nLaboratorio Clínico"],
            'area1_btn'       => ['type' => 'text', 'label' => 'Texto del botón', 'default' => 'Explorar programas'],

            'div_area2' => ['type' => 'divider', 'label' => 'Área 2'],
            'area2_titulo'    => ['type' => 'text', 'label' => 'Título', 'default' => 'Ciencias Empresariales'],
            'area2_desc'      => ['type' => 'textarea', 'label' => 'Descripción', 'default' => 'Desarrolla habilidades de liderazgo, gestión y emprendimiento con enfoque práctico y global.'],
            'area2_programas' => ['type' => 'textarea', 'label' => 'Programas (uno por línea)', 'default' => "Administración de Empresas\nContabilidad\nMarketing Digital"],
            'area2_btn'       => ['type' => 'text', 'label' => 'Texto del botón', 'default' => 'Explorar programas'],

            'div_area3' => ['type' => 'divider', 'label' => 'Área 3'],
            'area3_titulo'    => ['type' => 'text', 'label' => 'Título', 'default' => 'Transporte'],
            'area3_desc'      => ['type' => 'textarea', 'label' => 'Descripción', 'default' => 'Especialízate en logística y transporte marítimo, terrestre y multimodal con certificaciones internacionales.'],
            'area3_programas' => ['type' => 'textarea', 'label' => 'Programas (uno por línea)', 'default' => "Logística y Transporte\nComercio Exterior\nOperaciones Portuarias"],
            'area3_btn'       => ['type' => 'text', 'label' => 'Texto del botón', 'default' => 'Explorar programas'],
        ],
    ],

    // =====================================================
    // PROGRAMAS DESTACADOS (Collection)
    // =====================================================
    'programas' => [
        'label' => 'Programas Destacados',
        'type'  => 'singleton',
        'icon'  => '🎓',
        'fields' => [
            'div_global' => ['type' => 'divider', 'label' => 'Textos Generales'],
            'etiqueta_superior' => ['type' => 'text', 'label' => 'Etiqueta superior', 'default' => 'Oferta Académica'],
            'titulo_seccion_1'  => ['type' => 'text', 'label' => 'Título (parte oscura)', 'default' => 'Programas'],
            'titulo_seccion_2'  => ['type' => 'text', 'label' => 'Título (parte naranja)', 'default' => 'Destacados'],
            'btn_ver_todos'     => ['type' => 'text', 'label' => 'Botón "Ver todos"', 'default' => 'Ver todos los programas'],

            'div_prog1' => ['type' => 'divider', 'label' => 'Programa 1 (Salud)'],
            'prog1_imagen'    => ['type' => 'image', 'label' => 'Imagen', 'default' => 'img/programa-enfermeria.jpg'],
            'prog1_modalidad' => ['type' => 'text', 'label' => 'Modalidad', 'default' => 'Presencial', 'help' => 'Ej: Presencial, Híbrido, En línea'],
            'prog1_area'      => ['type' => 'text', 'label' => 'Área', 'default' => 'Salud'],
            'prog1_titulo'    => ['type' => 'text', 'label' => 'Título del programa', 'default' => 'Tecnología Superior en Enfermería'],
            'prog1_duracion'  => ['type' => 'text', 'label' => 'Duración', 'default' => '5 Semestres'],
            'prog1_sede'      => ['type' => 'text', 'label' => 'Sede', 'default' => 'Guayaquil'],

            'div_prog2' => ['type' => 'divider', 'label' => 'Programa 2 (Empresariales)'],
            'prog2_imagen'    => ['type' => 'image', 'label' => 'Imagen', 'default' => 'img/programa-marketing.jpg'],
            'prog2_modalidad' => ['type' => 'text', 'label' => 'Modalidad', 'default' => 'Presencial', 'help' => 'Ej: Presencial, Híbrido, En línea'],
            'prog2_area'      => ['type' => 'text', 'label' => 'Área', 'default' => 'Ciencias Empresariales'],
            'prog2_titulo'    => ['type' => 'text', 'label' => 'Título del programa', 'default' => 'Tecnología Superior en Marketing Digital'],
            'prog2_duracion'  => ['type' => 'text', 'label' => 'Duración', 'default' => '5 Semestres'],
            'prog2_sede'      => ['type' => 'text', 'label' => 'Sede', 'default' => 'Guayaquil'],

            'div_prog3' => ['type' => 'divider', 'label' => 'Programa 3 (Logística)'],
            'prog3_imagen'    => ['type' => 'image', 'label' => 'Imagen', 'default' => 'img/programa-logistica.jpg'],
            'prog3_modalidad' => ['type' => 'text', 'label' => 'Modalidad', 'default' => 'Presencial', 'help' => 'Ej: Presencial, Híbrido, En línea'],
            'prog3_area'      => ['type' => 'text', 'label' => 'Área', 'default' => 'Transporte'],
            'prog3_titulo'    => ['type' => 'text', 'label' => 'Título del programa', 'default' => 'Tecnología Superior en Logística y Transporte'],
            'prog3_duracion'  => ['type' => 'text', 'label' => 'Duración', 'default' => '5 Semestres'],
            'prog3_sede'      => ['type' => 'text', 'label' => 'Sede', 'default' => 'Guayaquil'],

            'div_prog4' => ['type' => 'divider', 'label' => 'Programa 4 (Software)'],
            'prog4_imagen'    => ['type' => 'image', 'label' => 'Imagen', 'default' => 'img/programa-software.jpg'],
            'prog4_modalidad' => ['type' => 'text', 'label' => 'Modalidad', 'default' => 'Híbrido', 'help' => 'Ej: Presencial, Híbrido, En línea'],
            'prog4_area'      => ['type' => 'text', 'label' => 'Área', 'default' => 'Tecnología'],
            'prog4_titulo'    => ['type' => 'text', 'label' => 'Título del programa', 'default' => 'Tecnología Superior en Desarrollo de Software'],
            'prog4_duracion'  => ['type' => 'text', 'label' => 'Duración', 'default' => '5 Semestres'],
            'prog4_sede'      => ['type' => 'text', 'label' => 'Sede', 'default' => 'Guayaquil'],
        ],
    ],

    // =====================================================
    // EXPERIENCIA ITB (Singleton)
    // =====================================================
    'experiencia' => [
        'label' => 'Experiencia ITB',
        'type'  => 'singleton',
        'icon'  => '⭐',
        'fields' => [
            'div_header' => ['type' => 'divider', 'label' => 'Cabecera (Textos Principales)'],
            'etiqueta_superior' => [
                'type'    => 'text',
                'label'   => 'Etiqueta superior (badge)',
                'default' => 'Vida Estudiantil',
            ],
            'titulo_seccion_1' => [
                'type'    => 'text',
                'label'   => 'Título (parte oscura)',
                'default' => 'Tu Experiencia',
            ],
            'titulo_seccion_2' => [
                'type'    => 'text',
                'label'   => 'Título (parte naranja)',
                'default' => 'ITB',
            ],
            'descripcion' => [
                'type'    => 'textarea',
                'label'   => 'Descripción',
                'default' => 'Más allá de lo académico, el ITB te ofrece una experiencia universitaria completa con servicios y beneficios diseñados para tu bienestar.',
                'help'    => 'Párrafo bajo el título principal.',
            ],
            
            'div_c1' => ['type' => 'divider', 'label' => 'Característica 1'],
            'caract1_titulo' => ['type' => 'text', 'label' => 'Título', 'default' => 'Servicios Médicos'],
            'caract1_desc'   => ['type' => 'text', 'label' => 'Descripción', 'default' => 'Atención médica y odontológica gratuita para estudiantes.'],
            
            'div_c2' => ['type' => 'divider', 'label' => 'Característica 2'],
            'caract2_titulo' => ['type' => 'text', 'label' => 'Título', 'default' => 'Becas y Financiamiento'],
            'caract2_desc'   => ['type' => 'text', 'label' => 'Descripción', 'default' => 'Programas de becas por excelencia académica y apoyo financiero.'],
            
            'div_c3' => ['type' => 'divider', 'label' => 'Característica 3'],
            'caract3_titulo' => ['type' => 'text', 'label' => 'Título', 'default' => 'Laboratorios Modernos'],
            'caract3_desc'   => ['type' => 'text', 'label' => 'Descripción', 'default' => 'Tecnología de punta en todos nuestros laboratorios especializados.'],
            
            'div_c4' => ['type' => 'divider', 'label' => 'Característica 4'],
            'caract4_titulo' => ['type' => 'text', 'label' => 'Título', 'default' => 'Bolsa de Empleo'],
            'caract4_desc'   => ['type' => 'text', 'label' => 'Descripción', 'default' => 'Conexión directa con empresas aliadas para tus prácticas y primer empleo.'],

            'div_btn' => ['type' => 'divider', 'label' => 'Botón'],
            'btn_texto' => ['type' => 'text', 'label' => 'Texto del botón', 'default' => 'Más beneficios'],

            'div_img' => ['type' => 'divider', 'label' => 'Imagen e Indicador (Derecha)'],
            'imagen' => [
                'type'    => 'image',
                'label'   => 'Imagen principal',
                'default' => 'img/experiencia-itb.jpg',
            ],
            'badge_numero' => [
                'type'    => 'text',
                'label'   => 'Número del indicador (ej: 115%)',
                'default' => '98%',
            ],
            'badge_texto' => [
                'type'    => 'text',
                'label'   => 'Texto del indicador',
                'default' => 'Satisfacción Estudiantil',
            ],
        ],
    ],

    // =====================================================
    // TESTIMONIOS (Collection)
    // =====================================================
    'testimonios' => [
        'label' => 'Testimonios',
        'type'  => 'singleton',
        'icon'  => '💬',
        'fields' => [
            'div_global' => [
                'type'  => 'divider',
                'label' => 'Textos Generales',
            ],
            'etiqueta_superior' => [
                'type'    => 'text',
                'label'   => 'Etiqueta superior (badge)',
                'default' => 'HISTORIAS DE ÉXITO',
            ],
            'titulo_seccion_1' => [
                'type'    => 'text',
                'label'   => 'Título (parte blanca)',
                'default' => 'Lo que dicen nuestros',
            ],
            'titulo_seccion_2' => [
                'type'    => 'text',
                'label'   => 'Título (parte naranja)',
                'default' => 'Graduados',
            ],
            'div_testimonio' => [
                'type'  => 'divider',
                'label' => 'Testimonio Principal',
            ],
            'imagen' => [
                'type'    => 'image',
                'label'   => 'Foto del Graduado',
                'default' => 'img/testimonio-estudiante.jpg',
                'help'    => 'Foto que aparece a la izquierda.',
            ],
            'cita' => [
                'type'    => 'textarea',
                'label'   => 'Testimonio',
                'default' => '"El ITB me brindó las herramientas y el conocimiento necesario para destacarme en el campo laboral. Los docentes y el enfoque práctico marcaron la diferencia en mi formación profesional. Hoy lidero un equipo de trabajo gracias a la preparación que recibí."',
                'help'    => 'Texto del testimonio. Máx. 3-4 oraciones.',
            ],
            'nombre' => [
                'type'    => 'text',
                'label'   => 'Nombre del Graduado',
                'default' => 'María Fernanda López',
                'help'    => 'Nombre que aparece bajo el testimonio.',
            ],
            'carrera' => [
                'type'    => 'text',
                'label'   => 'Carrera y Promoción',
                'default' => 'Graduada en Enfermería - Promoción 2022',
                'help'    => 'Aparece al final, bajo el nombre.',
            ],
        ],
    ],

    // =====================================================
    // AUTORIDADES (Collection)
    // =====================================================
    'autoridades' => [
        'label' => 'Autoridades',
        'type'  => 'singleton',
        'icon'  => '👤',
        'fields' => [
            'div_global' => ['type' => 'divider', 'label' => 'Textos Generales'],
            'etiqueta_superior' => ['type' => 'text', 'label' => 'Etiqueta superior', 'default' => 'Nuestro Equipo'],
            'titulo_seccion_1'  => ['type' => 'text', 'label' => 'Título (parte oscura)', 'default' => 'Nuestras'],
            'titulo_seccion_2'  => ['type' => 'text', 'label' => 'Título (parte naranja)', 'default' => 'Autoridades'],
            'btn_directorio'    => ['type' => 'text', 'label' => 'Botón "Ver Directorio"', 'default' => 'Ver Directorio'],

            'div_aut1' => ['type' => 'divider', 'label' => 'Autoridad 1'],
            'aut1_nombre' => ['type' => 'text', 'label' => 'Nombre completo', 'default' => 'PhD. Roberto Tolozano Benites'],
            'aut1_cargo'  => ['type' => 'text', 'label' => 'Cargo', 'default' => 'Canciller'],
            'aut1_imagen' => ['type' => 'image', 'label' => 'Foto', 'default' => 'img/autoridad-1.jpg'],
            'aut1_linkedin' => ['type' => 'text', 'label' => 'URL de LinkedIn', 'default' => '#'],
            'aut1_email'  => ['type' => 'text', 'label' => 'Email institucional', 'default' => '#'],

            'div_aut2' => ['type' => 'divider', 'label' => 'Autoridad 2'],
            'aut2_nombre' => ['type' => 'text', 'label' => 'Nombre completo', 'default' => 'Mgs. Nombre Apellido'],
            'aut2_cargo'  => ['type' => 'text', 'label' => 'Cargo', 'default' => 'Rector'],
            'aut2_imagen' => ['type' => 'image', 'label' => 'Foto', 'default' => 'img/autoridad-2.jpg'],
            'aut2_linkedin' => ['type' => 'text', 'label' => 'URL de LinkedIn', 'default' => '#'],
            'aut2_email'  => ['type' => 'text', 'label' => 'Email institucional', 'default' => '#'],

            'div_aut3' => ['type' => 'divider', 'label' => 'Autoridad 3'],
            'aut3_nombre' => ['type' => 'text', 'label' => 'Nombre completo', 'default' => 'Mgs. Nombre Apellido'],
            'aut3_cargo'  => ['type' => 'text', 'label' => 'Cargo', 'default' => 'Vicerrector Académico'],
            'aut3_imagen' => ['type' => 'image', 'label' => 'Foto', 'default' => 'img/autoridad-3.jpg'],
            'aut3_linkedin' => ['type' => 'text', 'label' => 'URL de LinkedIn', 'default' => '#'],
            'aut3_email'  => ['type' => 'text', 'label' => 'Email institucional', 'default' => '#'],
        ],
    ],

    // =====================================================
    // SERVICIOS (Collection)
    // =====================================================
    'servicios' => [
        'label' => 'Servicios Institucionales',
        'type'  => 'singleton',
        'icon'  => '🏛️',
        'fields' => [
            'div_global' => [
                'type'    => 'divider',
                'label'   => 'Textos Generales',
            ],
            'etiqueta_superior' => [
                'type'    => 'text',
                'label'   => 'Etiqueta superior (badge)',
                'default' => 'Servicios Institucionales',
            ],
            'titulo_seccion_1' => [
                'type'    => 'text',
                'label'   => 'Título (parte oscura)',
                'default' => 'Todo lo que necesitas en ',
            ],
            'titulo_seccion_2' => [
                'type'    => 'text',
                'label'   => 'Título (parte naranja)',
                'default' => 'un solo lugar',
            ],

            'div_serv1' => ['type' => 'divider', 'label' => 'Servicio 1: Bienestar'],
            'serv1_titulo' => ['type' => 'text', 'label' => 'Título', 'default' => 'Bienestar Estudiantil'],
            'serv1_desc'   => ['type' => 'text', 'label' => 'Descripción', 'default' => 'Servicios médicos, psicológicos y odontológicos gratuitos'],
            'serv1_imagen' => ['type' => 'image', 'label' => 'Imagen', 'default' => 'img/servicio-bienestar.jpg'],
            
            'div_serv2' => ['type' => 'divider', 'label' => 'Servicio 2: Campus'],
            'serv2_titulo' => ['type' => 'text', 'label' => 'Título', 'default' => 'Campus Virtual'],
            'serv2_desc'   => ['type' => 'text', 'label' => 'Descripción', 'default' => 'Plataforma educativa 24/7'],
            'serv2_imagen' => ['type' => 'image', 'label' => 'Imagen', 'default' => 'img/servicio-campus.jpg'],
            
            'div_serv3' => ['type' => 'divider', 'label' => 'Servicio 3: Horarios'],
            'serv3_titulo' => ['type' => 'text', 'label' => 'Título', 'default' => 'Horarios'],
            'serv3_desc'   => ['type' => 'text', 'label' => 'Descripción', 'default' => 'Consulta tus horarios de clase'],
            'serv3_imagen' => ['type' => 'image', 'label' => 'Imagen', 'default' => 'img/servicio-horarios.jpg'],
            
            'div_serv4' => ['type' => 'divider', 'label' => 'Servicio 4: Digitales'],
            'serv4_titulo' => ['type' => 'text', 'label' => 'Título', 'default' => 'Servicios Digitales'],
            'serv4_desc'   => ['type' => 'text', 'label' => 'Descripción', 'default' => 'Trámites en línea y gestión académica'],
            'serv4_imagen' => ['type' => 'image', 'label' => 'Imagen', 'default' => 'img/servicio-digital.jpg'],
            
            'div_serv5' => ['type' => 'divider', 'label' => 'Servicio 5: Podcast'],
            'serv5_titulo' => ['type' => 'text', 'label' => 'Título', 'default' => 'Podcast ITB'],
            'serv5_desc'   => ['type' => 'text', 'label' => 'Descripción', 'default' => 'Escucha nuestro contenido educativo'],
            'serv5_imagen' => ['type' => 'image', 'label' => 'Imagen', 'default' => 'img/servicio-podcast.jpg'],
            
            'div_serv6' => ['type' => 'divider', 'label' => 'Servicio 6: Deportes'],
            'serv6_titulo' => ['type' => 'text', 'label' => 'Título', 'default' => 'Arte y Deportes'],
            'serv6_desc'   => ['type' => 'text', 'label' => 'Descripción', 'default' => 'Clubes deportivos, grupos artísticos y actividades recreativas'],
            'serv6_imagen' => ['type' => 'image', 'label' => 'Imagen', 'default' => 'img/servicio-deportes.jpg'],
        ],
    ],

    // =====================================================
    // ADMISIÓN (Singleton)
    // =====================================================
    'admision' => [
        'label' => 'Admisión',
        'type'  => 'singleton',
        'icon'  => '📋',
        'fields' => [
            'etiqueta_superior' => [
                'type'    => 'text',
                'label'   => 'Etiqueta superior (badge)',
                'default' => 'Admisiones Abiertas',
                'help'    => 'Texto pequeño que aparece arriba del título.',
            ],
            'titulo' => [
                'type'    => 'textarea',
                'label'   => 'Título de la sección',
                'default' => 'Inicia tu proceso de admisión',
                'help'    => 'Título principal de la sección de admisiones.',
            ],
            'descripcion' => [
                'type'    => 'textarea',
                'label'   => 'Descripción',
                'default' => 'Da el primer paso hacia tu futuro profesional. Completa el formulario y un asesor académico se pondrá en contacto contigo para guiarte en todo el proceso de inscripción.',
                'help'    => 'Texto descriptivo del proceso de admisión.',
            ],
            'feature_1' => [
                'type'    => 'text',
                'label'   => 'Característica 1',
                'default' => 'Proceso 100% en línea',
                'help'    => 'Primera ventaja del proceso de admisión.',
            ],
            'feature_2' => [
                'type'    => 'text',
                'label'   => 'Característica 2',
                'default' => 'Asesoría personalizada',
                'help'    => 'Segunda ventaja del proceso de admisión.',
            ],
            'feature_3' => [
                'type'    => 'text',
                'label'   => 'Característica 3',
                'default' => 'Respuesta en 24 horas',
                'help'    => 'Tercera ventaja del proceso de admisión.',
            ],
            'btn_enviar' => [
                'type'    => 'text',
                'label'   => 'Texto del botón Enviar',
                'default' => 'Enviar Solicitud',
                'help'    => 'Texto del botón de envío del formulario de admisión.',
            ],
            'form_titulo' => [
                'type'    => 'text',
                'label'   => 'Título del Formulario',
                'default' => 'Solicita Información',
                'help'    => 'Título que aparece arriba de los campos del formulario.',
            ],
            'form_terminos' => [
                'type'    => 'text',
                'label'   => 'Texto de Términos',
                'default' => 'Al enviar este formulario, aceptas nuestra Política de Privacidad.',
                'help'    => 'Texto legal o nota pequeña debajo del botón.',
            ],
        ],
    ],

    // =====================================================
    // NOTICIAS (Collection)
    // =====================================================
    'noticias' => [
        'label' => 'Noticias',
        'type'  => 'singleton',
        'icon'  => '📰',
        'fields' => [
            'div_global' => [
                'type'    => 'divider',
                'label'   => 'Textos Generales de la Sección',
            ],
            'etiqueta_superior' => [
                'type'    => 'text',
                'label'   => 'Etiqueta superior (badge)',
                'default' => 'Actualidad ITB',
                'help'    => 'Texto pequeño que aparece arriba del título.',
            ],
            'titulo_seccion_1' => [
                'type'    => 'text',
                'label'   => 'Título (parte oscura)',
                'default' => 'Noticias y',
                'help'    => 'La primera parte del título, color oscuro.',
            ],
            'titulo_seccion_2' => [
                'type'    => 'text',
                'label'   => 'Título (parte naranja)',
                'default' => 'Eventos',
                'help'    => 'La segunda parte del título, aparecerá en color naranja.',
            ],
            'boton_todas' => [
                'type'    => 'text',
                'label'   => 'Botón "Todas las noticias"',
                'default' => 'Todas las noticias',
                'help'    => 'Texto del botón superior derecho.',
            ],
            'div_noticia' => [
                'type'    => 'divider',
                'label'   => 'Noticia Destacada Principal',
            ],
            'titulo' => [
                'type'    => 'text',
                'label'   => 'Título de la Noticia',
                'default' => 'Casa Abierta ITB 2025: Descubre tu vocación profesional',
                'help'    => 'Título principal de la noticia.',
            ],
            'categoria' => [
                'type'    => 'text',
                'label'   => 'Categoría',
                'default' => 'Evento',
                'help'    => 'Categoría que se muestra como badge (ej: Evento, Académico).',
            ],
            'fecha' => [
                'type'    => 'text',
                'label'   => 'Fecha de publicación',
                'default' => '15 Sep 2025',
                'help'    => 'Fecha de publicación (ej: 15 Sep 2025).',
            ],
            'descripcion' => [
                'type'    => 'textarea',
                'label'   => 'Descripción / Resumen',
                'default' => 'Visita nuestro campus y conoce de primera mano nuestras instalaciones, docentes y oferta académica en la Casa Abierta más grande del año.',
                'help'    => 'Resumen corto de la noticia.',
            ],
            'imagen' => [
                'type'    => 'image',
                'label'   => 'Imagen de la Noticia Principal',
                'default' => 'img/noticia-principal.jpg',
                'help'    => 'Imagen destacada de la noticia.',
            ],
            'div_sec1' => [
                'type'    => 'divider',
                'label'   => 'Noticia Secundaria 1',
            ],
            'sec1_titulo' => [
                'type'    => 'text',
                'label'   => 'Título Noticia 1',
                'default' => 'Convenio internacional con universidad de España',
            ],
            'sec1_fecha' => [
                'type'    => 'text',
                'label'   => 'Fecha Noticia 1',
                'default' => '10 Sep 2025',
            ],
            'sec1_imagen' => [
                'type'    => 'image',
                'label'   => 'Imagen Noticia 1',
                'default' => 'img/noticia-2.jpg',
            ],
            'div_sec2' => [
                'type'    => 'divider',
                'label'   => 'Noticia Secundaria 2',
            ],
            'sec2_titulo' => [
                'type'    => 'text',
                'label'   => 'Título Noticia 2',
                'default' => 'Graduación de la promoción 2025: más de 500 nuevos profesionales',
            ],
            'sec2_fecha' => [
                'type'    => 'text',
                'label'   => 'Fecha Noticia 2',
                'default' => '05 Sep 2025',
            ],
            'sec2_imagen' => [
                'type'    => 'image',
                'label'   => 'Imagen Noticia 2',
                'default' => 'img/noticia-3.jpg',
            ],
            'div_sec3' => [
                'type'    => 'divider',
                'label'   => 'Noticia Secundaria 3',
            ],
            'sec3_titulo' => [
                'type'    => 'text',
                'label'   => 'Título Noticia 3',
                'default' => 'ITB inaugura nuevo laboratorio de simulación clínica',
            ],
            'sec3_fecha' => [
                'type'    => 'text',
                'label'   => 'Fecha Noticia 3',
                'default' => '01 Sep 2025',
            ],
            'sec3_imagen' => [
                'type'    => 'image',
                'label'   => 'Imagen Noticia 3',
                'default' => 'img/noticia-4.jpg',
            ],
        ],
    ],

    // =====================================================
    // FOOTER (Singleton)
    // =====================================================
    'footer' => [
        'label' => 'Pie de Página',
        'type'  => 'singleton',
        'icon'  => '🔻',
        'fields' => [
            'logo_blanco' => [
                'type'    => 'image',
                'label'   => 'Logo (versión blanca)',
                'default' => 'img/logo-itb-white.png',
                'help'    => 'Logo en blanco para el fondo oscuro del footer.',
            ],
            'descripcion' => [
                'type'    => 'textarea',
                'label'   => 'Descripción institucional',
                'default' => 'Instituto Superior Tecnológico Bolivariano de Tecnología. Formando profesionales de excelencia desde 1995.',
                'help'    => 'Texto corto debajo del logo en el footer.',
            ],
            'div_titulos' => [
                'type'    => 'divider',
                'label'   => 'Títulos de Columnas',
            ],
            'col1_titulo' => [
                'type'    => 'text',
                'label'   => 'Columna 1',
                'default' => 'Enlaces Rápidos',
                'help'    => 'Título de la primera columna.',
            ],
            'col2_titulo' => [
                'type'    => 'text',
                'label'   => 'Columna 2',
                'default' => 'Contacto',
                'help'    => 'Título de la segunda columna.',
            ],
            'col3_titulo' => [
                'type'    => 'text',
                'label'   => 'Columna 3',
                'default' => 'Horarios de Atención',
                'help'    => 'Título de la tercera columna.',
            ],
            'div_contacto' => [
                'type'    => 'divider',
                'label'   => 'Datos de Contacto',
            ],
            'direccion' => [
                'type'    => 'text',
                'label'   => 'Dirección',
                'default' => 'Víctor Manuel Rendón 236 y Pedro Carbo, Guayaquil',
                'help'    => 'Dirección física del instituto.',
            ],
            'telefono' => [
                'type'    => 'text',
                'label'   => 'Teléfono',
                'default' => '(04) 2-566-800',
                'help'    => 'Número de teléfono de contacto.',
            ],
            'email' => [
                'type'    => 'text',
                'label'   => 'Email de contacto',
                'default' => 'info@bolivariano.edu.ec',
                'help'    => 'Correo electrónico institucional principal.',
            ],
            'facebook_url' => [
                'type'    => 'text',
                'label'   => 'URL Facebook',
                'default' => '#',
                'help'    => 'Enlace al perfil de Facebook institucional.',
            ],
            'instagram_url' => [
                'type'    => 'text',
                'label'   => 'URL Instagram',
                'default' => '#',
                'help'    => 'Enlace al perfil de Instagram institucional.',
            ],
            'youtube_url' => [
                'type'    => 'text',
                'label'   => 'URL YouTube',
                'default' => '#',
                'help'    => 'Enlace al canal de YouTube institucional.',
            ],
            'linkedin_url' => [
                'type'    => 'text',
                'label'   => 'URL LinkedIn',
                'default' => '#',
                'help'    => 'Enlace al perfil de LinkedIn institucional.',
            ],
            'horario_semana' => [
                'type'    => 'text',
                'label'   => 'Horario (Lunes - Viernes)',
                'default' => 'Lunes a Viernes: 08:00 - 17:00',
                'help'    => 'Horario de atención entre semana.',
            ],
            'horario_sabado' => [
                'type'    => 'text',
                'label'   => 'Horario (Sábado)',
                'default' => 'Sábados: 08:00 - 13:00',
                'help'    => 'Horario de atención los sábados.',
            ],
            'prefooter_titulo' => [
                'type'    => 'text',
                'label'   => 'Pre-footer: Título CTA',
                'default' => '¿Aún no decides qué carrera estudiar?',
                'help'    => 'Título de la banda naranja encima del footer.',
            ],
            'prefooter_texto' => [
                'type'    => 'text',
                'label'   => 'Pre-footer: Subtítulo',
                'default' => 'Te ayudamos a encontrar la carrera ideal para ti',
                'help'    => 'Texto de apoyo debajo del título del pre-footer.',
            ],
            'div_botones' => [
                'type'    => 'divider',
                'label'   => 'Botones del Pre-footer',
            ],
            'prefooter_btn_1' => [
                'type'    => 'text',
                'label'   => 'Botón 1',
                'default' => 'Chatea con nosotros',
                'help'    => 'Ej: Chatea con nosotros',
            ],
            'prefooter_btn_2' => [
                'type'    => 'text',
                'label'   => 'Botón 2',
                'default' => 'Llámanos',
                'help'    => 'Ej: Llámanos',
            ],
            'prefooter_btn_3' => [
                'type'    => 'text',
                'label'   => 'Botón 3',
                'default' => 'Visítanos',
                'help'    => 'Ej: Visítanos',
            ],
            'enlaces_rapidos' => [
                'type'    => 'textarea',
                'label'   => 'Enlaces Rápidos (uno por línea)',
                'default' => "Oferta Académica\nAdmisiones\nVida Estudiantil\nInvestigación\nEducación Continua",
                'help'    => 'Lista de enlaces rápidos. Escribe uno por línea.',
            ],
        ],
    ],

];
