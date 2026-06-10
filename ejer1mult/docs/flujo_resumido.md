# Flujo resumido del sistema

## 1. Login y roles

Inicio → Login → Validación en `usuarios.json` → Identificación de rol → Panel de estudiante o panel Kardex.

## 2. Inscripción de materias

Inicio → Login estudiante → Selecciona inscripción → Verifica matrícula → Si no está matriculado, realiza subproceso de matrícula → Guarda en `matriculaciones.json` → Registra inscripción → Guarda en `inscripcion_materias.json` → Kardex revisa → Estudiante consulta resultado.

## 3. Solicitud de certificado académico

Inicio → Login estudiante → Selecciona certificado → Llena formulario → Valida datos → Guarda en `certificado_academico.json` → Kardex revisa → Estudiante consulta resultado.

## 4. Kardex

Inicio → Login Kardex → Ver solicitudes → Revisar detalle → Ver historial desde `flujos.json` → Cambiar estado → Registrar movimiento → Fin.
