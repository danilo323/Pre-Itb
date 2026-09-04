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
                'label'   => 'Texto del botón CTA',
                'default' => 'Explorar Programas',
                'help'    => 'Texto del botón principal de la portada.',
            ],
            'stat_numero' => [
                'type'    => 'text',
                'label'   => 'Estadística (ej: +20K Estudiantes)',
                'default' => '+20K Estudiantes Graduados',
                'help'    => 'Dato destacado que aparece debajo del botón.',
            ],
            'rating' => [
                'type'    => 'text',
                'label'   => 'Calificación (ej: 4.9)',
                'default' => '4.9',
                'help'    => 'Número de calificación con estrellas. Solo números.',
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
            'titulo' => [
                'type'    => 'textarea',
                'label'   => 'Título de la sección',
                'default' => 'Trayectoria y Compromiso con la Educación',
                'help'    => 'Título principal de la sección Trayectoria.',
            ],
            'descripcion' => [
                'type'    => 'textarea',
                'label'   => 'Descripción',
                'default' => 'Desde 1995, el Instituto Superior Tecnológico Bolivariano de Tecnología ha formado profesionales con una educación integral basada en valores, innovación y excelencia académica. Nuestro compromiso es transformar vidas a través del conocimiento.',
                'help'    => 'Párrafo de descripción institucional.',
            ],
            'canciller_nombre' => [
                'type'    => 'text',
                'label'   => 'Nombre del Canciller',
                'default' => 'PhD. Roberto Tolozano Benites',
                'help'    => 'Nombre completo con título académico.',
            ],
            'canciller_cargo' => [
                'type'    => 'text',
                'label'   => 'Cargo del Canciller',
                'default' => 'Canciller del ITB',
                'help'    => 'Cargo que aparece debajo del nombre.',
            ],
            'canciller_foto' => [
                'type'    => 'image',
                'label'   => 'Foto del Canciller',
                'default' => 'img/canciller.jpg',
                'help'    => 'Foto circular del perfil. Tamaño recomendado: 200x200px.',
            ],
            'stat_anios' => [
                'type'    => 'text',
                'label'   => 'Estadística: Años de Experiencia',
                'default' => '29+',
                'help'    => 'Número destacado (solo el número o número+símbolo).',
            ],
            'stat_graduados' => [
                'type'    => 'text',
                'label'   => 'Estadística: Estudiantes Graduados',
                'default' => '+17,000',
                'help'    => 'Número de estudiantes graduados.',
            ],
            'stat_carreras' => [
                'type'    => 'text',
                'label'   => 'Estadística: Carreras Disponibles',
                'default' => '+35',
                'help'    => 'Número de carreras disponibles.',
            ],
        ],
    ],

    // =====================================================
    // ÁREAS DE FORMACIÓN (Collection)
    // =====================================================
    'areas' => [
        'label' => 'Áreas de Formación',
        'type'  => 'collection',
        'icon'  => '📚',
        'fields' => [
            'nombre' => [
                'type'    => 'text',
                'label'   => 'Nombre del Área',
                'default' => 'Salud',
                'help'    => 'Ej: Salud, Ciencias Empresariales, Transporte.',
            ],
            'descripcion' => [
                'type'    => 'textarea',
                'label'   => 'Descripción',
                'default' => 'Formación integral en ciencias de la salud con laboratorios especializados y prácticas clínicas reales.',
                'help'    => 'Descripción corta del área de formación.',
            ],
            'programas_lista' => [
                'type'    => 'textarea',
                'label'   => 'Programas (uno por línea)',
                'default' => "Enfermería\nFisioterapia\nLaboratorio Clínico",
                'help'    => 'Lista de carreras. Escribe uno por línea.',
            ],
        ],
    ],

    // =====================================================
    // PROGRAMAS DESTACADOS (Collection)
    // =====================================================
    'programas' => [
        'label' => 'Programas Destacados',
        'type'  => 'collection',
        'icon'  => '🎓',
        'fields' => [
            'titulo' => [
                'type'    => 'text',
                'label'   => 'Nombre del Programa',
                'default' => 'Tecnología Superior en Enfermería',
                'help'    => 'Nombre completo del programa académico.',
            ],
            'area' => [
                'type'    => 'text',
                'label'   => 'Área',
                'default' => 'Salud',
                'help'    => 'Área a la que pertenece el programa (ej: Salud, Tecnología).',
            ],
            'modalidad' => [
                'type'    => 'select',
                'label'   => 'Modalidad',
                'default' => 'Presencial',
                'options' => ['Presencial', 'Híbrido', 'En línea'],
                'help'    => 'Modalidad de estudio del programa.',
            ],
            'duracion' => [
                'type'    => 'text',
                'label'   => 'Duración',
                'default' => '5 Semestres',
                'help'    => 'Duración del programa (ej: 5 Semestres).',
            ],
            'sede' => [
                'type'    => 'text',
                'label'   => 'Sede',
                'default' => 'Guayaquil',
                'help'    => 'Ciudad donde se imparte el programa.',
            ],
            'imagen' => [
                'type'    => 'image',
                'label'   => 'Imagen del Programa',
                'default' => 'img/programa-enfermeria.jpg',
                'help'    => 'Foto de portada del programa. Recomendado: 600x400px.',
            ],
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
            'titulo' => [
                'type'    => 'textarea',
                'label'   => 'Título de la sección',
                'default' => 'Tu Experiencia ITB',
                'help'    => 'Título principal de la sección.',
            ],
            'descripcion' => [
                'type'    => 'textarea',
                'label'   => 'Descripción',
                'default' => 'Más allá de lo académico, el ITB te ofrece una experiencia universitaria completa con servicios y beneficios diseñados para tu bienestar.',
                'help'    => 'Párrafo de descripción de la sección.',
            ],
            'badge_numero' => [
                'type'    => 'text',
                'label'   => 'Badge: Número (ej: 98%)',
                'default' => '98%',
                'help'    => 'Número del badge sobre la imagen. Solo texto corto.',
            ],
            'badge_texto' => [
                'type'    => 'text',
                'label'   => 'Badge: Texto (ej: Satisfacción)',
                'default' => 'Satisfacción Estudiantil',
                'help'    => 'Texto descriptivo del badge.',
            ],
            'imagen' => [
                'type'    => 'image',
                'label'   => 'Imagen de la sección',
                'default' => 'img/experiencia-itb.jpg',
                'help'    => 'Foto del lado derecho. Recomendado: 600x700px.',
            ],
        ],
    ],

    // =====================================================
    // TESTIMONIOS (Collection)
    // =====================================================
    'testimonios' => [
        'label' => 'Testimonios',
        'type'  => 'collection',
        'icon'  => '💬',
        'fields' => [
            'nombre' => [
                'type'    => 'text',
                'label'   => 'Nombre del Graduado',
                'default' => 'María Fernanda López',
                'help'    => 'Nombre completo del autor del testimonio.',
            ],
            'carrera' => [
                'type'    => 'text',
                'label'   => 'Carrera y Promoción',
                'default' => 'Graduada en Enfermería - Promoción 2022',
                'help'    => 'Ej: Graduado en Marketing Digital - Promoción 2023.',
            ],
            'cita' => [
                'type'    => 'textarea',
                'label'   => 'Testimonio',
                'default' => 'El ITB me brindó las herramientas y el conocimiento necesario para destacarme en el campo laboral. Los docentes y el enfoque práctico marcaron la diferencia en mi formación profesional.',
                'help'    => 'Texto del testimonio. Máx. 3-4 oraciones.',
            ],
            'imagen' => [
                'type'    => 'image',
                'label'   => 'Foto del Graduado',
                'default' => 'img/testimonio-estudiante.jpg',
                'help'    => 'Foto del graduado. Recomendado: 500x600px.',
            ],
        ],
    ],

    // =====================================================
    // AUTORIDADES (Collection)
    // =====================================================
    'autoridades' => [
        'label' => 'Autoridades',
        'type'  => 'collection',
        'icon'  => '👤',
        'fields' => [
            'nombre' => [
                'type'    => 'text',
                'label'   => 'Nombre completo',
                'default' => 'PhD. Roberto Tolozano Benites',
                'help'    => 'Nombre con título académico (ej: PhD., Mgs., Ing.).',
            ],
            'cargo' => [
                'type'    => 'text',
                'label'   => 'Cargo',
                'default' => 'Canciller',
                'help'    => 'Cargo institucional (ej: Rector, Vicerrector Académico).',
            ],
            'imagen' => [
                'type'    => 'image',
                'label'   => 'Foto',
                'default' => 'img/autoridad-1.jpg',
                'help'    => 'Foto formal. Recomendado: 400x500px.',
            ],
            'linkedin_url' => [
                'type'    => 'text',
                'label'   => 'URL de LinkedIn',
                'default' => '#',
                'help'    => 'Enlace al perfil de LinkedIn de la autoridad.',
            ],
            'email' => [
                'type'    => 'text',
                'label'   => 'Email institucional',
                'default' => '#',
                'help'    => 'Correo institucional de contacto.',
            ],
        ],
    ],

    // =====================================================
    // SERVICIOS (Collection)
    // =====================================================
    'servicios' => [
        'label' => 'Servicios Institucionales',
        'type'  => 'collection',
        'icon'  => '🏛️',
        'fields' => [
            'titulo' => [
                'type'    => 'text',
                'label'   => 'Título del Servicio',
                'default' => 'Bienestar Estudiantil',
                'help'    => 'Nombre del servicio (ej: Campus Virtual, Horarios).',
            ],
            'descripcion' => [
                'type'    => 'text',
                'label'   => 'Descripción corta',
                'default' => 'Servicios médicos, psicológicos y odontológicos gratuitos',
                'help'    => 'Descripción breve visible sobre la imagen del servicio.',
            ],
            'imagen' => [
                'type'    => 'image',
                'label'   => 'Imagen de fondo',
                'default' => 'img/servicio-bienestar.jpg',
                'help'    => 'Foto de fondo de la tarjeta. Recomendado: 800x600px.',
            ],
            'enlace_url' => [
                'type'    => 'text',
                'label'   => 'URL del enlace',
                'default' => '#',
                'help'    => 'URL a donde lleva al hacer clic en la tarjeta.',
            ],
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
        ],
    ],

    // =====================================================
    // NOTICIAS (Collection)
    // =====================================================
    'noticias' => [
        'label' => 'Noticias',
        'type'  => 'collection',
        'icon'  => '📰',
        'fields' => [
            'titulo' => [
                'type'    => 'text',
                'label'   => 'Título de la Noticia',
                'default' => 'Casa Abierta ITB 2025: Descubre tu vocación profesional',
                'help'    => 'Título principal de la noticia.',
            ],
            'categoria' => [
                'type'    => 'select',
                'label'   => 'Categoría',
                'default' => 'Evento',
                'options' => ['Evento', 'Académico', 'Convenio', 'Logro', 'Noticia'],
                'help'    => 'Categoría que se muestra como badge en la noticia.',
            ],
            'fecha' => [
                'type'    => 'date',
                'label'   => 'Fecha de publicación',
                'default' => '2025-09-15',
                'help'    => 'Fecha de publicación de la noticia.',
            ],
            'descripcion' => [
                'type'    => 'textarea',
                'label'   => 'Descripción / Resumen',
                'default' => 'Visita nuestro campus y conoce de primera mano nuestras instalaciones, docentes y oferta académica en la Casa Abierta más grande del año.',
                'help'    => 'Resumen corto de la noticia (máx. 2-3 oraciones).',
            ],
            'imagen' => [
                'type'    => 'image',
                'label'   => 'Imagen de la Noticia',
                'default' => 'img/noticia-principal.jpg',
                'help'    => 'Imagen destacada de la noticia. Recomendado: 800x500px.',
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
        ],
    ],

];
