<?php
require_once __DIR__ . '/includes/functions.php';
requerirLogin();
$esKardex = esRol('kardex');
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Diagramas de Flujo</title>
    <link rel="stylesheet" href="css/estilos.css">
</head>
<body>
<header class="topbar">
    <div class="brand">UMSA Digital</div>
    <div class="navlinks">
        <a href="index.php">Inicio</a>
        <?php if ($esKardex): ?>
            <a href="listar.php">Solicitudes</a>
            <a href="kardex.php">Panel Kardex</a>
        <?php else: ?>
            <a href="mis_solicitudes.php">Mis solicitudes</a>
        <?php endif; ?>
        <a href="workflow.php">Workflow</a>
        <a href="logout.php">Salir</a>
    </div>
</header>

<main class="container wide">
    <section class="section-title left">
        <h1>Modelado del proceso</h1>
        <p>Diagramas de flujo de los trámites y del control por roles.</p>
    </section>

    <section class="diagram-grid">
        <article class="diagram-card">
            <h2>Flujo general con login y roles</h2>
            <img src="assets/diagrama_login_roles.png" alt="Diagrama de login y roles">
        </article>
        <article class="diagram-card">
            <h2>Inscripción de materias con validación de matrícula</h2>
            <img src="assets/diagrama_inscripcion_matricula.png" alt="Diagrama de inscripción con matrícula">
        </article>
        <article class="diagram-card">
            <h2>Solicitud de certificado académico</h2>
            <img src="assets/diagrama_certificado_academico.png" alt="Diagrama de certificado académico">
        </article>
        <article class="diagram-card">
            <h2>Seguimiento de Kardex</h2>
            <img src="assets/diagrama_kardex_flujos.png" alt="Diagrama de panel Kardex">
        </article>
    </section>
</main>
</body>
</html>
