<?php
// Verificar autenticación y rol
session_start();

if (!isset($_SESSION['id_usuario']) || $_SESSION['fk_rol'] != 1) {
    header("Location: ../../pages/inicio.php");
    exit();
}

include_once("../../components/header.php");

// Obtener ID del auto
$id_auto = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id_auto <= 0) {
    header("Location: ../index.php?error=id_invalido");
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
    header("Location: ../index.php?error=auto_no_encontrado");
    exit();
}

$auto = $resultado->fetch_assoc();
$stmt->close();
?>

<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card shadow">
            <div class="card-header bg-warning text-dark">
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
                                <i class="bi bi-credit-card"></i> Patente
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
                        <a href="../index.php" class="btn btn-secondary">
                            <i class="bi bi-x-circle"></i> Cancelar
                        </a>
                        <button type="submit" class="btn btn-warning">
                            <i class="bi bi-save"></i> Guardar Cambios
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="../../js/form-validation.js"></script>

<?php include_once("../../components/footer.php"); ?>