# Diagrama de flujo - Seguimiento de Kardex

```text
Inicio
  ↓
Kardex inicia sesión
  ↓
Sistema valida rol Kardex
  ↓
Ingresa al panel de administración
  ↓
Consulta solicitudes de inscripción y certificado
  ↓
Selecciona una solicitud
  ↓
Visualiza detalle del trámite
  ↓
Revisa historial de acciones en flujos.json
  ↓
¿Cumple requisitos?
  ├── Sí → Cambia estado a Aprobado
  └── No → Cambia estado a Rechazado u Observado
              ↓
Sistema registra cambio en flujos.json
              ↓
Estudiante ve el resultado en Mis solicitudes
              ↓
Fin
```
