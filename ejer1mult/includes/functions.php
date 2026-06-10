<?php
declare(strict_types=1);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

date_default_timezone_set('America/La_Paz');

define('DATA_DIR', __DIR__ . '/../data');

function dataPath(string $file): string
{
    return DATA_DIR . '/' . $file;
}

function asegurarDataDir(): void
{
    if (!is_dir(DATA_DIR)) {
        mkdir(DATA_DIR, 0777, true);
    }
}

function leerJsonArchivo(string $archivo, array $default = []): array
{
    asegurarDataDir();

    if (!file_exists($archivo)) {
        guardarJsonArchivo($archivo, $default);
    }

    $contenido = file_get_contents($archivo);
    $datos = json_decode($contenido ?: '[]', true);

    return is_array($datos) ? $datos : $default;
}

function guardarJsonArchivo(string $archivo, array $datos): bool
{
    asegurarDataDir();
    $json = json_encode(array_values($datos), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    return file_put_contents($archivo, $json, LOCK_EX) !== false;
}

function limpiar(string $valor): string
{
    return trim($valor);
}

function e(string $valor): string
{
    return htmlspecialchars($valor, ENT_QUOTES, 'UTF-8');
}

function generarId(array $items): int
{
    $maximo = 0;
    foreach ($items as $item) {
        $maximo = max($maximo, (int)($item['id'] ?? 0));
    }
    return $maximo + 1;
}

function usuarios(): array
{
    return leerJsonArchivo(dataPath('usuarios.json'));
}

function guardarUsuarios(array $usuarios): bool
{
    return guardarJsonArchivo(dataPath('usuarios.json'), $usuarios);
}

function buscarUsuarioPorCorreo(string $correo): ?array
{
    foreach (usuarios() as $usuario) {
        if (strtolower((string)($usuario['correo'] ?? '')) === strtolower($correo)) {
            return $usuario;
        }
    }
    return null;
}

function buscarUsuarioPorCi(string $ci): ?array
{
    foreach (usuarios() as $usuario) {
        if ((string)($usuario['ci'] ?? '') === $ci) {
            return $usuario;
        }
    }
    return null;
}

function actualizarUsuarioPorCi(string $ci, array $nuevosDatos): bool
{
    $usuarios = usuarios();
    $actualizado = false;

    foreach ($usuarios as &$usuario) {
        if ((string)($usuario['ci'] ?? '') === $ci) {
            $usuario = array_merge($usuario, $nuevosDatos);
            $actualizado = true;
            break;
        }
    }
    unset($usuario);

    return $actualizado ? guardarUsuarios($usuarios) : false;
}

function loginUsuario(string $correo, string $password): bool
{
    $usuario = buscarUsuarioPorCorreo($correo);

    if (!$usuario) {
        return false;
    }

    $hash = (string)($usuario['password_hash'] ?? '');
    $passwordPlano = (string)($usuario['password'] ?? '');

    $ok = false;
    if ($hash !== '') {
        $ok = password_verify($password, $hash);
    } elseif ($passwordPlano !== '') {
        $ok = hash_equals($passwordPlano, $password);
    }

    if (!$ok) {
        return false;
    }

    $_SESSION['usuario'] = [
        'id' => (int)($usuario['id'] ?? 0),
        'nombre' => (string)($usuario['nombre'] ?? ''),
        'ci' => (string)($usuario['ci'] ?? ''),
        'correo' => (string)($usuario['correo'] ?? ''),
        'rol' => (string)($usuario['rol'] ?? 'estudiante'),
        'carrera' => (string)($usuario['carrera'] ?? ''),
        'matriculado' => (bool)($usuario['matriculado'] ?? false),
    ];

    registrarFlujo('Inicio de sesión', 'El usuario ingresó al sistema.', 'General', 'Completado');
    return true;
}

function usuarioActual(): ?array
{
    return $_SESSION['usuario'] ?? null;
}

function esRol(string $rol): bool
{
    $usuario = usuarioActual();
    return $usuario && (($usuario['rol'] ?? '') === $rol);
}

function requerirLogin(): void
{
    if (!usuarioActual()) {
        header('Location: login.php?mensaje=' . urlencode('Debe iniciar sesión para continuar.'));
        exit;
    }
}

function requerirRol(array $roles): void
{
    requerirLogin();
    $rol = (string)(usuarioActual()['rol'] ?? '');
    if (!in_array($rol, $roles, true)) {
        header('Location: index.php?mensaje=' . urlencode('No tiene permiso para acceder a esa sección.'));
        exit;
    }
}

function refrescarSesionDesdeUsuario(): void
{
    $actual = usuarioActual();
    if (!$actual) {
        return;
    }

    $usuario = buscarUsuarioPorCi((string)$actual['ci']);
    if ($usuario) {
        $_SESSION['usuario']['matriculado'] = (bool)($usuario['matriculado'] ?? false);
        $_SESSION['usuario']['carrera'] = (string)($usuario['carrera'] ?? '');
        $_SESSION['usuario']['nombre'] = (string)($usuario['nombre'] ?? '');
    }
}

function tramites(): array
{
    return [
        'inscripcion' => [
            'titulo' => 'Inscripción de Materias',
            'descripcion' => 'Solicitud digital para registrar o regularizar la inscripción de una materia.',
            'archivo' => dataPath('inscripcion_materias.json'),
            'requiere_matricula' => true,
            'campos' => [
                ['name' => 'semestre', 'label' => 'Semestre', 'type' => 'number', 'required' => true],
                ['name' => 'materia', 'label' => 'Materia solicitada', 'type' => 'text', 'required' => true],
                ['name' => 'grupo', 'label' => 'Grupo o paralelo', 'type' => 'text', 'required' => true],
                ['name' => 'motivo', 'label' => 'Motivo de la solicitud', 'type' => 'textarea', 'required' => false],
            ],
        ],
        'certificado' => [
            'titulo' => 'Solicitud de Certificado Académico',
            'descripcion' => 'Solicitud digital para emitir certificado de notas, egreso u otro documento académico.',
            'archivo' => dataPath('certificado_academico.json'),
            'requiere_matricula' => false,
            'campos' => [
                ['name' => 'tipo_certificado', 'label' => 'Tipo de certificado', 'type' => 'select', 'required' => true,
                    'opciones' => ['Certificado de notas', 'Certificado de egreso', 'Certificado de alumno regular']],
                ['name' => 'gestion', 'label' => 'Gestión académica', 'type' => 'text', 'required' => true],
                ['name' => 'motivo', 'label' => 'Motivo de la solicitud', 'type' => 'textarea', 'required' => false],
            ],
        ],
    ];
}

function obtenerTramite(string $tipo): ?array
{
    $tramites = tramites();
    return $tramites[$tipo] ?? null;
}

function leerSolicitudes(string $tipo): array
{
    $tramite = obtenerTramite($tipo);
    if (!$tramite) {
        return [];
    }
    return leerJsonArchivo((string)$tramite['archivo']);
}

function guardarSolicitudes(string $tipo, array $solicitudes): bool
{
    $tramite = obtenerTramite($tipo);
    if (!$tramite) {
        return false;
    }
    return guardarJsonArchivo((string)$tramite['archivo'], $solicitudes);
}

function todasLasSolicitudes(): array
{
    $resultado = [];

    foreach (array_keys(tramites()) as $tipo) {
        foreach (leerSolicitudes($tipo) as $solicitud) {
            $solicitud['_tipo_key'] = $tipo;
            $resultado[] = $solicitud;
        }
    }

    usort($resultado, function ($a, $b) {
        return strcmp((string)($b['fecha_registro'] ?? ''), (string)($a['fecha_registro'] ?? ''));
    });

    return $resultado;
}

function solicitudesDelEstudiante(string $ci): array
{
    return array_values(array_filter(todasLasSolicitudes(), function ($s) use ($ci) {
        return (string)($s['ci'] ?? '') === $ci;
    }));
}

function validarSolicitud(string $tipo, array $post): array
{
    $tramite = obtenerTramite($tipo);
    $errores = [];

    if (!$tramite) {
        return ['Tipo de trámite no válido.'];
    }

    foreach ($tramite['campos'] as $campo) {
        $name = $campo['name'];
        $label = $campo['label'];
        $required = (bool)($campo['required'] ?? false);
        $valor = limpiar((string)($post[$name] ?? ''));

        if ($required && $valor === '') {
            $errores[] = "El campo {$label} es obligatorio.";
        }

        if ($name === 'semestre' && $valor !== '' && ((int)$valor < 1 || (int)$valor > 12)) {
            $errores[] = 'El semestre debe estar entre 1 y 12.';
        }
    }

    return $errores;
}

function construirSolicitud(string $tipo, array $post, array $tramite, int $id): array
{
    $usuario = usuarioActual();

    $solicitud = [
        'id' => $id,
        'tipo' => $tipo,
        'tramite' => $tramite['titulo'],
        'estado' => 'Pendiente',
        'fecha_registro' => date('Y-m-d H:i:s'),
        'fecha_actualizacion' => date('Y-m-d H:i:s'),
        'ci' => (string)($usuario['ci'] ?? ''),
        'nombre' => (string)($usuario['nombre'] ?? ''),
        'correo' => (string)($usuario['correo'] ?? ''),
        'carrera' => (string)($usuario['carrera'] ?? ''),
        'registrado_por' => (string)($usuario['rol'] ?? 'estudiante'),
    ];

    foreach ($tramite['campos'] as $campo) {
        $name = $campo['name'];
        $solicitud[$name] = limpiar((string)($post[$name] ?? ''));
    }

    return $solicitud;
}

function estadosPermitidos(): array
{
    return ['Pendiente', 'En revisión', 'Aprobado', 'Rechazado'];
}

function claseEstado(string $estado): string
{
    return match ($estado) {
        'Aprobado' => 'estado aprobado',
        'Rechazado' => 'estado rechazado',
        'En revisión' => 'estado revision',
        default => 'estado pendiente',
    };
}

function registrarFlujo(string $accion, string $detalle, string $tramite = 'General', string $estado = 'Registrado', ?string $ciForzado = null, ?string $nombreForzado = null, ?string $codFlujo = null, ?string $codProceso = null): void
{
    $usuario = usuarioActual();
    $flujos = leerJsonArchivo(dataPath('flujos.json'));

    $flujos[] = [
        'id' => generarId($flujos),
        'fecha' => date('Y-m-d H:i:s'),
        'ci' => $ciForzado ?? (string)($usuario['ci'] ?? 'Sin CI'),
        'nombre' => $nombreForzado ?? (string)($usuario['nombre'] ?? 'Visitante'),
        'rol' => (string)($usuario['rol'] ?? 'visitante'),
        'tramite' => $tramite,
        'codFlujo' => $codFlujo ?? '',
        'codProceso' => $codProceso ?? '',
        'accion' => $accion,
        'detalle' => $detalle,
        'estado' => $estado,
    ];

    guardarJsonArchivo(dataPath('flujos.json'), $flujos);
}

function leerFlujos(?string $ci = null): array
{
    $flujos = leerJsonArchivo(dataPath('flujos.json'));

    if ($ci !== null && $ci !== '') {
        $flujos = array_filter($flujos, function ($flujo) use ($ci) {
            return str_contains((string)($flujo['ci'] ?? ''), $ci) || str_contains(strtolower((string)($flujo['nombre'] ?? '')), strtolower($ci));
        });
    }

    usort($flujos, function ($a, $b) {
        return strcmp((string)($b['fecha'] ?? ''), (string)($a['fecha'] ?? ''));
    });

    return array_values($flujos);
}

function matriculaciones(): array
{
    return leerJsonArchivo(dataPath('matriculaciones.json'));
}

function guardarMatriculaciones(array $matriculaciones): bool
{
    return guardarJsonArchivo(dataPath('matriculaciones.json'), $matriculaciones);
}

function estudianteEstaMatriculado(string $ci): bool
{
    $usuario = buscarUsuarioPorCi($ci);
    return (bool)($usuario['matriculado'] ?? false);
}


function workflowTramites(): array
{
    return leerJsonArchivo(dataPath('workflow_tramites.json'));
}

function procesosPorFlujo(): array
{
    $agrupado = [];
    foreach (workflowTramites() as $proceso) {
        $codFlujo = (string)($proceso['codFlujo'] ?? '');
        if ($codFlujo === '') {
            continue;
        }
        $agrupado[$codFlujo][] = $proceso;
    }
    return $agrupado;
}

function nombreRolWorkflow(string $codRol): string
{
    return match ($codRol) {
        'E' => 'Estudiante',
        'K' => 'Kardex',
        'S' => 'Sistema',
        default => $codRol,
    };
}

function nombreTipoProceso(string $tipo): string
{
    return match ($tipo) {
        'I' => 'Inicio',
        'P' => 'Proceso',
        'C' => 'Cierre',
        default => $tipo,
    };
}

function workflowCodigoPorTipo(string $tipo): string
{
    return match ($tipo) {
        'inscripcion' => 'F1',
        'certificado' => 'F2',
        default => '',
    };
}
