# Diagrama de flujo - Solicitud de certificado académico

```text
Inicio
  ↓
Estudiante inicia sesión
  ↓
Selecciona trámite: Solicitud de Certificado Académico
  ↓
Llena formulario con tipo de certificado, carrera, correo y motivo
  ↓
Sistema valida campos obligatorios y correo
  ↓
¿Datos correctos?
  ├── No → Muestra errores y vuelve al formulario
  └── Sí → Guarda solicitud en certificado_academico.json
              ↓
Sistema registra movimiento en flujos.json
              ↓
Kardex revisa la información académica
              ↓
Kardex actualiza estado
              ↓
Estudiante consulta resultado
              ↓
Fin
```
