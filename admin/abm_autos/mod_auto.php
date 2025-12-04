<?php
// Verificar autenticación y rol
session_start();

if (!isset($_SESSION['id_usuario']) || $_SESSION['fk_rol'] != 1) {
    header("Location: ../../pages/inicio.php");
    exit();
}

include_once("../../components/header.php");
?>
<link rel="stylesheet" href="/AutosYA/css/admin.css">
<?php

// Obtener ID del auto
$id_auto = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id_auto <= 0) {
    header("Location: ../gestion_autos.php?error=id_invalido");
    exit();
}

// Obtener datos del auto
$stmt = $con->prepare("SELECT * FROM autos WHERE id_auto = ?");
$stmt->bind_param("i", $id_auto);
$stmt->execute();
$resultado = $stmt->get_result();

if ($resultado->num_rows === 0) {
    $stmt->close();
    mysqli_close($con);
    header("Location: ../gestion_autos.php?error=auto_no_encontrado");
    exit();
}

$auto = $resultado->fetch_assoc();
$stmt->close();
?>

<div class="row justify-content-center">
    <div class="col-md-8">

        <?php
        // Mostrar mensaje de éxito en actualización de imagen
        if (isset($_GET['img'])) {
            echo '<div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle"></i> Imagen actualizada con éxito!
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>';
        }

        // Mostrar mensaje de éxito en eliminación de imagen
        if (isset($_GET['img_deleted'])) {
            echo '<div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle"></i> Imagen eliminada con éxito!
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>';
        }
        ?>

        <div class="card shadow mt-4">
            <div class="card-header bg-primary text-white">
                <h4 class="mb-0">
                    <i class="bi bi-pencil-square"></i> Modificar Auto
                </h4>
            </div>
            <div class="card-body">
                <form action="mod_auto_ok.php" method="POST" class="needs-validation" novalidate>
                    <input type="hidden" name="id_auto" value="<?php echo $auto['id_auto']; ?>">

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="marca" class="form-label">
                                <i class="bi bi-tag"></i> Marca
                            </label>
                            <input type="text" class="form-control" id="marca" name="marca"
                                value="<?php echo htmlspecialchars($auto['marca']); ?>" required>
                            <div class="invalid-feedback">
                                La marca es requerida.
                            </div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="modelo" class="form-label">
                                <i class="bi bi-car-front"></i> Modelo
                            </label>
                            <input type="text" class="form-control" id="modelo" name="modelo"
                                value="<?php echo htmlspecialchars($auto['modelo']); ?>" required>
                            <div class="invalid-feedback">
                                El modelo es requerido.
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="anio" class="form-label">
                                <i class="bi bi-calendar3"></i> Año
                            </label>
                            <input type="number" class="form-control" id="anio" name="anio"
                                min="1900" max="2026" value="<?php echo $auto['anio']; ?>" required>
                            <div class="invalid-feedback">
                                Ingresa un año válido.
                            </div>
                        </div>

                        <div class="col-md-4 mb-3">
                            <label for="patente" class="form-label">
                                <i class="bi bi-postcard"></i> Patente
                            </label>
                            <input type="text" class="form-control" id="patente" name="patente"
                                value="<?php echo htmlspecialchars($auto['patente']); ?>" required>
                            <div class="invalid-feedback">
                                La patente es requerida.
                            </div>
                        </div>

                        <div class="col-md-4 mb-3">
                            <label for="precio_por_dia" class="form-label">
                                <i class="bi bi-cash"></i> Precio por Día ($)
                            </label>
                            <input type="number" class="form-control" id="precio_por_dia" name="precio_por_dia"
                                step="0.01" min="0" value="<?php echo $auto['precio_por_dia']; ?>" required>
                            <div class="invalid-feedback">
                                El precio es requerido.
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="estado" class="form-label">
                            <i class="bi bi-info-circle"></i> Estado
                        </label>
                        <select class="form-select" id="estado" name="estado" required>
                            <option value="disponible" <?php echo $auto['estado'] === 'disponible' ? 'selected' : ''; ?>>
                                Disponible
                            </option>
                            <option value="reservado" <?php echo $auto['estado'] === 'reservado' ? 'selected' : ''; ?>>
                                Reservado
                            </option>
                            <option value="mantenimiento" <?php echo $auto['estado'] === 'mantenimiento' ? 'selected' : ''; ?>>
                                Mantenimiento
                            </option>
                        </select>
                        <div class="invalid-feedback">
                            Selecciona un estado.
                        </div>
                    </div>

                    <div class="d-flex gap-2 justify-content-end">
                        <a href="../gestion_autos.php" class="btn btn-secondary">
                            <i class="bi bi-x-circle"></i> Cancelar
                        </a>
                        <button type="submit" class="btn btn-warning">
                            <i class="bi bi-save"></i> Guardar Cambios
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Card para gestionar imagen -->
        <div class="card shadow mt-4">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">
                    <i class="bi bi-image"></i> Gestionar Imagen
                </h5>
            </div>
            <div class="card-body">
                <?php if (!empty($auto['imagen'])): ?>
                    <div class="text-center mb-3">
                        <img src="../../auto_imgs/<?php echo htmlspecialchars($auto['imagen']); ?>"
                            alt="<?php echo htmlspecialchars($auto['marca'] . ' ' . $auto['modelo']); ?>"
                            class="img-fluid rounded shadow" style="max-height: 300px;">
                    </div>
                <?php else: ?>
                    <div class="alert alert-info mb-3 alert-persistent">
                        <i class="bi bi-info-circle"></i> Este auto no tiene imagen asignada.
                    </div>
                <?php endif; ?>

                <form action="mod_auto_img.php" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="id_auto" value="<?php echo $auto['id_auto']; ?>">

                    <div class="mb-3">
                        <label for="nueva_imagen" class="form-label">
                            <?php echo !empty($auto['imagen']) ? 'Cambiar Imagen' : 'Agregar Imagen'; ?>
                        </label>
                        <input type="file" class="form-control" id="nueva_imagen" name="nueva_imagen"
                            accept="image/*" required>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-info">
                            <i class="bi bi-upload"></i> <?php echo !empty($auto['imagen']) ? 'Actualizar Imagen' : 'Subir Imagen'; ?>
                        </button>

                        <?php if (!empty($auto['imagen'])): ?>
                            <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#eliminarImagenModal">
                                <i class="bi bi-trash"></i> Eliminar Imagen
                            </button>
                        <?php endif; ?>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal de confirmación para eliminar imagen -->
<div class="modal fade" id="eliminarImagenModal" tabindex="-1" aria-labelledby="eliminarImagenModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="eliminarImagenModalLabel">
                    <i class="bi bi-exclamation-triangle"></i> Confirmar Eliminación
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p class="mb-0">Estás seguro de que deseas eliminar la imagen de este auto?</p>
            </div>
            <div class="modal-footer">
                <div class="d-flex gap-2 w-100 justify-content-center">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="bi bi-x-circle"></i> Cancelar
                    </button>
                    <form action="baja_auto_img.php" method="POST" style="display: inline;">
                        <input type="hidden" name="id_auto" value="<?php echo $auto['id_auto']; ?>">
                        <button type="submit" class="btn btn-danger">
                            <i class="bi bi-trash"></i> Eliminar
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="../../js/form-validation.js"></script>

<?php include_once("../../components/footer.php"); ?>