<?php
// admin/schema_mock.php
// Esquema temporal de prueba para desarrollar el motor visual independientemente del backend (Persona 3).
// Contiene los DOS tipos de contenido que define la guía: singleton y collection.

return [

    // =========================================================================
    // SINGLETON: Un único registro editable (no tiene listado, ni IDs, ni "Nuevo")
    // Ejemplo: La sección FAQ de la landing. Solo hay UNA.
    // =========================================================================
    'faq' => [
        'type' => 'singleton',
        'label' => 'Preguntas Frecuentes',
        'fields' => [
            'titulo' => [
                'type' => 'text',
                'label' => 'Título Principal',
                'help' => 'El título que aparecerá arriba de las preguntas (ej. "Preguntas Frecuentes").'
            ],
            'preguntas' => [
                'type' => 'repeater',
                'label' => 'Preguntas',
                'subfields' => [
                    'pregunta' => [
                        'type' => 'text',
                        'label' => 'Pregunta'
                    ],
                    'respuesta' => [
                        'type' => 'textarea',
                        'label' => 'Respuesta'
                    ]
                ]
            ]
        ]
    ],

    // =========================================================================
    // COLLECTION: Lista de N registros con alta/baja/modificación.
    // Ejemplo: Testimonios de estudiantes. Hay MUCHOS, cada uno con su ID.
    // Necesita: listado (tabla), botón "Nuevo", editar, eliminar, flag publicado.
    // =========================================================================
    'testimonios' => [
        'type' => 'collection',
        'label' => 'Testimonios',
        'columns' => ['nombre', 'carrera'],  // Columnas visibles en la tabla del listado
        'fields' => [
            'nombre' => [
                'type' => 'text',
                'label' => 'Nombre del Estudiante',
                'help' => 'Nombre completo del estudiante.'
            ],
            'carrera' => [
                'type' => 'text',
                'label' => 'Carrera',
                'help' => 'Carrera que estudia o estudió.'
            ],
            'testimonio' => [
                'type' => 'textarea',
                'label' => 'Testimonio',
                'help' => 'Lo que dice el estudiante sobre el instituto.'
            ]
        ]
    ]

];
