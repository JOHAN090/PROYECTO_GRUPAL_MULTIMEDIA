# Diagrama de flujo - Login y roles

```text
Inicio
  ↓
Usuario ingresa a la plataforma UMSA Digital
  ↓
Escribe correo y contraseña
  ↓
Sistema valida datos en usuarios.json
  ↓
¿Credenciales correctas?
  ├── No → Muestra error y vuelve al login
  └── Sí → Identifica rol
              ↓
¿Rol del usuario?
  ├── Estudiante → Panel de trámites y mis solicitudes
  └── Kardex → Panel para revisar solicitudes y flujos
              ↓
Sistema registra acceso/movimiento en flujos.json
              ↓
Fin
```
