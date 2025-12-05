<?php
// Verificar autenticación y rol
session_start();

if (!isset($_SESSION['id_usuario'])) {
    header("Location: ../pages/login.php");
    exit();
}

if ($_SESSION['fk_rol'] != 1) {
    header("Location: ../pages/inicio.php");
    exit();
}

include_once("../components/header.php");
?>
<link rel="stylesheet" href="/AutosYA/css/admin.css">

<?php
// Paginación
$registros_por_pagina = 10;
$pagina_actual = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
$offset = ($pagina_actual - 1) * $registros_por_pagina;

// Contar total de usuarios
$stmt_count = $con->prepare("SELECT COUNT(*) as total FROM usuarios");
$stmt_count->execute();
$result_count = $stmt_count->get_result();
$total_usuarios = $result_count->fetch_assoc()['total'];
$total_paginas = ceil($total_usuarios / $registros_por_pagina);
$stmt_count->close();

// Obtener usuarios con paginación
$stmt = $con->prepare("
    SELECT u.id_usuario, u.nombre, u.correo, r.nombre as rol 
    FROM usuarios u
    INNER JOIN roles r ON u.fk_rol = r.id_rol
    ORDER BY u.id_usuario DESC
    LIMIT ? OFFSET ?
");
$stmt->bind_param("ii", $registros_por_pagina, $offset);
$stmt->execute();
$result = $stmt->get_result();
?>

<div class="container my-4">
    <div class="row">
        <div class="col-12">
            <h1 class="admin-page-title mb-4">
                <i class="bi bi-people-fill"></i> Gestión de Usuarios
            </h1>

            <?php
            // Mostrar mensajes de éxito/error
            if (isset($_GET['mod']) && $_GET['mod'] === 'ok') {
                echo '<div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="bi bi-check-circle"></i> Usuario modificado exitosamente.
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                  </div>';
            }
            if (isset($_GET['baja']) && $_GET['baja'] === 'ok') {
                echo '<div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="bi bi-check-circle"></i> Usuario eliminado exitosamente.
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                  </div>';
            }
            if (isset($_GET['error']) && $_GET['error'] === 'self_delete') {
                echo '<div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="bi bi-exclamation-triangle"></i> No puedes eliminar tu propia cuenta.
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                  </div>';
            }
            ?>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card shadow-lg admin-table-card">
                <div class="card-header">
                    <h4 class="mb-0">
                        <i class="bi bi-list-ul"></i> Listado de Usuarios
                    </h4>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Nombre</th>
                                    <th>Correo</th>
                                    <th>Rol</th>
                                    <th class="text-center">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                if ($result->num_rows > 0) {
                                    while ($usuario = $result->fetch_assoc()) {
                                        $es_usuario_actual = ($usuario['id_usuario'] == $_SESSION['id_usuario']);
                                        $badge_class = ($usuario['rol'] === 'admin') ? 'bg-danger' : 'bg-primary';
                                        $badge_yo = $es_usuario_actual ? ' <span class="badge bg-secondary">Yo</span>' : '';

                                        echo "<tr>";
                                        echo "<td>" . htmlspecialchars($usuario['id_usuario']) . "</td>";
                                        echo "<td>" . htmlspecialchars($usuario['nombre']) . $badge_yo . "</td>";
                                        echo "<td>" . htmlspecialchars($usuario['correo']) . "</td>";
                                        echo "<td><span class='badge {$badge_class}'>" . htmlspecialchars(ucfirst($usuario['rol'])) . "</span></td>";
                                        echo "<td class='text-center'>
                                            <div class='btn-group' role='group'>
                                                <a href='/AutosYA/usuario/mod_usuario.php?id=" . $usuario['id_usuario'] . "&redirect=admin_gestion' 
                                                   class='btn btn-sm btn-warning' title='Modificar'>
                                                    <i class='bi bi-pencil'></i>
                                                </a>";

                                        if (!$es_usuario_actual) {
                                            echo "<button type='button' class='btn btn-sm btn-danger' 
                                                    data-bs-toggle='modal' 
                                                    data-bs-target='#modalEliminarUsuario{$usuario['id_usuario']}' 
                                                    title='Eliminar'>
                                                    <i class='bi bi-trash'></i>
                                                  </button>";
                                        } else {
                                            echo "<button type='button' class='btn btn-sm btn-secondary' disabled title='No puedes eliminar tu propia cuenta'>
                                                    <i class='bi bi-trash'></i>
                                                  </button>";
                                        }

                                        echo "</div>
                                        </td>";
                                        echo "</tr>";

                                        // Modal de confirmación de eliminación
                                        if (!$es_usuario_actual) {
                                            echo "
                                            <div class='modal fade' id='modalEliminarUsuario{$usuario['id_usuario']}' tabindex='-1'>
                                                <div class='modal-dialog'>
                                                    <div class='modal-content'>
                                                        <div class='modal-header bg-danger text-white'>
                                                            <h5 class='modal-title'>
                                                                <i class='bi bi-exclamation-triangle'></i> Confirmar Eliminación
                                                            </h5>
                                                            <button type='button' class='btn-close btn-close-white' data-bs-dismiss='modal'></button>
                                                        </div>
                                                        <div class='modal-body'>
                                                            <p>Estás seguro de que deseas eliminar al usuario:</p>
                                                            <p><strong>" . htmlspecialchars($usuario['nombre']) . "</strong></p>
                                                        </div>
                                                        <div class='modal-footer'>
                                                            <button type='button' class='btn btn-secondary' data-bs-dismiss='modal'>Cancelar</button>
                                                            <a href='abm_usuarios/baja_usuario.php?id={$usuario['id_usuario']}' 
                                                               class='btn btn-danger'>
                                                                <i class='bi bi-trash'></i> Eliminar
                                                            </a>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>";
                                        }
                                    }
                                } else {
                                    echo "<tr><td colspan='6' class='text-center'>No hay usuarios registrados</td></tr>";
                                }
                                $stmt->close();
                                ?>
                            </tbody>
                        </table>
                    </div>

                    <?php if ($total_paginas > 1): ?>
                        <div class="d-flex justify-content-between align-items-center mt-3 px-3 pb-3">
                            <div class="text-muted">
                                Mostrando <?php echo min($offset + 1, $total_usuarios); ?> - <?php echo min($offset + $registros_por_pagina, $total_usuarios); ?> de <?php echo $total_usuarios; ?> usuarios
                            </div>
                            <nav aria-label="Paginación">
                                <ul class="pagination mb-0">
                                    <!-- Botón Anterior -->
                                    <li class="page-item <?php echo $pagina_actual <= 1 ? 'disabled' : ''; ?>">
                                        <a class="page-link" href="?pagina=<?php echo $pagina_actual - 1; ?>">
                                            <i class="bi bi-chevron-left"></i>
                                        </a>
                                    </li>

                                    <?php
                                    // Mostrar páginas
                                    $rango = 2;

                                    for ($i = 1; $i <= $total_paginas; $i++) {
                                        if ($i == 1 || $i == $total_paginas || ($i >= $pagina_actual - $rango && $i <= $pagina_actual + $rango)) {
                                            $active = $i == $pagina_actual ? 'active' : '';
                                            echo '<li class="page-item ' . $active . '">
                                            <a class="page-link" href="?pagina=' . $i . '">' . $i . '</a>
                                          </li>';
                                        } elseif ($i == $pagina_actual - $rango - 1 || $i == $pagina_actual + $rango + 1) {
                                            echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
                                        }
                                    }
                                    ?>

                                    <!-- Botón Siguiente -->
                                    <li class="page-item <?php echo $pagina_actual >= $total_paginas ? 'disabled' : ''; ?>">
                                        <a class="page-link" href="?pagina=<?php echo $pagina_actual + 1; ?>">
                                            <i class="bi bi-chevron-right"></i>
                                        </a>
                                    </li>
                                </ul>
                            </nav>
                        </div>
                    <?php endif; ?>
                </div>
        </div>
    </div>
</div>

<?php include_once("components/modal_agregar_usuario.php"); ?>

</div>
</div>

<?php include_once("../components/footer.php"); ?>