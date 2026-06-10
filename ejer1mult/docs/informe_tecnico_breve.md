# Informe técnico breve

## 1. Tema

Digitalización de trámites universitarios UMSA mediante una plataforma web con almacenamiento en archivos JSON.

## 2. Trámites seleccionados

- Inscripción de materias.
- Solicitud de certificado académico.

## 3. Metodología utilizada

Se analizó el flujo tradicional de atención de trámites universitarios y se propuso una versión digital con formularios web. El proceso se dividió en entrada de datos, validación, registro, seguimiento y actualización del estado del trámite.

Para mejorar el control del proceso se incorporó un sistema de login con dos roles:

- Estudiante: registra solicitudes y revisa sus propios trámites.
- Kardex: revisa solicitudes, cambia estados y consulta el historial de acciones realizadas por los estudiantes.

## 4. Herramientas y tecnologías

- PHP para la lógica del sistema.
- HTML y CSS para la interfaz.
- JSON como sistema de almacenamiento.
- SVG para los diagramas de flujo.
- Navegador web para la ejecución.

## 5. Funcionamiento del sistema

El estudiante ingresa al sistema con su cuenta. Si desea solicitar inscripción de materias, el sistema verifica primero si está matriculado. Si no lo está, se activa un subproceso de matrícula donde el estudiante registra gestión y comprobante. Luego puede volver al formulario de inscripción.

Para solicitudes de certificado académico, el estudiante puede registrar directamente la solicitud. Todas las solicitudes quedan en estado Pendiente y son revisadas por Kardex.

Kardex puede ver todas las solicitudes y actualizar el estado a Pendiente, En revisión, Aprobado o Rechazado. Además, el sistema guarda un historial en `flujos.json` para mostrar los pasos realizados por cada estudiante.

## 6. Archivos JSON principales

- `usuarios.json`: almacena usuarios, roles y estado de matrícula.
- `inscripcion_materias.json`: almacena solicitudes de inscripción.
- `certificado_academico.json`: almacena solicitudes de certificados.
- `matriculaciones.json`: almacena el subproceso de matrícula.
- `flujos.json`: almacena el historial de acciones del sistema.

## 7. Resultados obtenidos

El sistema permite registrar trámites universitarios sin usar base de datos tradicional. También permite controlar permisos mediante roles y realizar seguimiento de los procesos hechos por cada estudiante.

## 8. Conclusión

La propuesta cumple con la digitalización de trámites universitarios usando almacenamiento JSON. Además, mejora el flujo original al incluir login, validación de matrícula, subproceso de matrícula y trazabilidad para Kardex.
