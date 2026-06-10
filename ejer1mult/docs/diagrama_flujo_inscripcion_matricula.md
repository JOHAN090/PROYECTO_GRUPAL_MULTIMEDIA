# Diagrama de flujo - Inscripción de materias con validación de matrícula

```text
Inicio
  ↓
Estudiante inicia sesión
  ↓
Selecciona trámite: Inscripción de Materias
  ↓
Sistema verifica si está matriculado
  ↓
¿Está matriculado?
  ├── No → Redirige al subproceso de matriculación
  │          ↓
  │      Estudiante llena datos de matrícula
  │          ↓
  │      Sistema guarda en matriculaciones.json
  │          ↓
  │      Registra movimiento en flujos.json
  │          ↓
  └── Sí / ya matriculado → Habilita formulario de inscripción
              ↓
Estudiante llena materia, grupo y motivo
              ↓
Sistema valida campos obligatorios
              ↓
¿Datos correctos?
  ├── No → Muestra errores y vuelve al formulario
  └── Sí → Guarda solicitud en inscripcion_materias.json
              ↓
Kardex revisa y actualiza estado
              ↓
Estudiante consulta resultado
              ↓
Fin
```
