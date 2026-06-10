# Actividad 1A - Digitalización de Trámites Universitarios UMSA

Proyecto web académico desarrollado en **PHP + JSON**, sin base de datos tradicional.

## Objetivo

Digitalizar dos trámites universitarios:

1. Inscripción de materias.
2. Solicitud de certificado académico.

Además, se agregaron mejoras al flujo original:

- Login de usuarios.
- Roles: estudiante y Kardex.
- Verificación de matrícula antes de la inscripción de materias.
- Subproceso de matriculación si el estudiante no está matriculado.
- Registro de flujos para que Kardex revise los pasos realizados por cada estudiante.

## Usuarios de prueba

| Rol | Correo | Contraseña |
|---|---|---|
| Estudiante matriculado | estudiante@umsa.bo | 123456 |
| Estudiante no matriculado | no.matriculado@umsa.bo | 123456 |
| Kardex | kardex@umsa.bo | 123456 |

## Cómo ejecutar

### Opción 1: XAMPP

1. Copiar la carpeta `actividad_1A_digitalizacion_umsa_mejorada` dentro de `htdocs`.
2. Iniciar Apache.
3. Abrir en el navegador:

```text
http://localhost/actividad_1A_digitalizacion_umsa_mejorada/
```

### Opción 2: Servidor PHP integrado

Entrar a la carpeta del proyecto y ejecutar:

```bash
php -S localhost:8000
```

Luego abrir:

```text
http://localhost:8000
```

## Archivos JSON usados

Todos están en la carpeta `data/`:

- `usuarios.json`: usuarios del sistema y roles.
- `inscripcion_materias.json`: solicitudes de inscripción.
- `certificado_academico.json`: solicitudes de certificados.
- `matriculaciones.json`: registros del subproceso de matrícula.
- `flujos.json`: historial de acciones realizadas por estudiantes y Kardex.

## Flujo del estudiante

1. Inicia sesión.
2. Elige trámite.
3. Si elige inscripción de materias, el sistema verifica si está matriculado.
4. Si no está matriculado, lo envía al subproceso de matrícula.
5. Luego puede registrar la solicitud.
6. Puede revisar sus solicitudes en `Mis solicitudes`.

## Flujo de Kardex

1. Inicia sesión como Kardex.
2. Consulta todas las solicitudes.
3. Cambia estados: Pendiente, En revisión, Aprobado o Rechazado.
4. Revisa el flujo completo de cada estudiante desde el panel Kardex.

## Estructura

```text
actividad_1A_digitalizacion_umsa_mejorada/
├── index.php
├── login.php
├── logout.php
├── registrar.php
├── guardar.php
├── matriculacion.php
├── mis_solicitudes.php
├── listar.php
├── kardex.php
├── actualizar_estado.php
├── diagramas.php
├── includes/
│   └── functions.php
├── css/
│   └── estilos.css
├── data/
│   ├── usuarios.json
│   ├── inscripcion_materias.json
│   ├── certificado_academico.json
│   ├── matriculaciones.json
│   └── flujos.json
├── assets/
│   ├── diagrama_login_roles.svg
│   ├── diagrama_inscripcion_matricula.svg
│   ├── diagrama_certificado_academico.svg
│   └── diagrama_kardex_flujos.svg
└── docs/
    └── informe_tecnico_breve.md
```
