<?php

/**
 * Página de inicio - Vista pública de autos disponibles
 */
include_once("../components/header.php");
?>
<link rel="stylesheet" href="../css/inicio.css">

<!-- Hero Section -->
<div class="hero-section">
    <div class="hero-background-text">
        <div class="text-line">Autos<span class="hero-ya-accent">YA</span>Autos<span class="hero-ya-accent">YA</span></div>
        <div class="text-line">Autos<span class="hero-ya-accent">YA</span>Autos<span class="hero-ya-accent">YA</span></div>
        <div class="text-line">Autos<span class="hero-ya-accent">YA</span>Autos<span class="hero-ya-accent">YA</span></div>
        <div class="text-line">Autos<span class="hero-ya-accent">YA</span>Autos<span class="hero-ya-accent">YA</span></div>
        <div class="text-line">Autos<span class="hero-ya-accent">YA</span>Autos<span class="hero-ya-accent">YA</span></div>
        <div class="text-line">Autos<span class="hero-ya-accent">YA</span>Autos<span class="hero-ya-accent">YA</span></div>
    </div>

    <div class="container h-100 py-5">
        <div class="row h-100 align-items-center">
            <div class="col-lg-7 hero-content">
                <div class="hero-text-block">
                    <h1 class="hero-title">
                        Tu Viaje<br>
                        <span class="hero-highlight">Comienza Aquí</span>
                    </h1>
                    <p class="lead mb-5 hero-lead">
                        Servicio premium de alquiler de autos con los mejores vehículos a precios competitivos
                    </p>
                </div>

                <!-- Feature Grid -->
                <div class="row g-4 mb-4">
                    <div class="col-sm-6">
                        <div class="feature-card">
                            <div class="d-flex align-items-start gap-3">
                                <div class="feature-icon">
                                    <i class="bi bi-car-front-fill"></i>
                                </div>
                                <div>
                                    <h5 class="mb-1 feature-title">Amplia Selección</h5>
                                    <p class="mb-0 feature-text">Flota diversa de vehículos</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-sm-6">
                        <div class="feature-card">
                            <div class="d-flex align-items-start gap-3">
                                <div class="feature-icon">
                                    <i class="bi bi-cash-coin"></i>
                                </div>
                                <div>
                                    <h5 class="mb-1 feature-title">Mejores Precios</h5>
                                    <p class="mb-0 feature-text">Sin cargos ocultos</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-sm-6">
                        <div class="feature-card">
                            <div class="d-flex align-items-start gap-3">
                                <div class="feature-icon">
                                    <i class="bi bi-clock-fill"></i>
                                </div>
                                <div>
                                    <h5 class="mb-1 feature-title">Reserva Fácil</h5>
                                    <p class="mb-0 feature-text">Proceso rápido</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-sm-6">
                        <div class="feature-card">
                            <div class="d-flex align-items-start gap-3">
                                <div class="feature-icon">
                                    <i class="bi bi-shield-check"></i>
                                </div>
                                <div>
                                    <h5 class="mb-1 feature-title">Seguro y Confiable</h5>
                                    <p class="mb-0 feature-text">Totalmente asegurado</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <?php if (!isset($_SESSION['id_usuario'])): ?>
                    <div class="d-flex gap-3 flex-wrap">
                        <a class="btn btn-light btn-lg px-4 btn-hero" href="registro.php">
                            <i class="bi bi-person-plus"></i> Regístrate Ahora
                        </a>
                        <a class="btn btn-outline-light btn-lg px-4 btn-hero" href="login.php">
                            <i class="bi bi-box-arrow-in-right"></i> Iniciar Sesión
                        </a>
                    </div>
                <?php else: ?>
                    <div class="d-flex gap-3 flex-wrap">
                        <a class="btn btn-light btn-lg px-4 btn-hero" href="<?php echo $_SESSION['fk_rol'] == 1 ? '../admin/index.php' : '../cliente/perfil.php'; ?>">
                            <i class="bi bi-speedometer2"></i> Mi Dashboard
                        </a>
                        <a class="btn btn-outline-light btn-lg px-4 btn-hero" href="../pages/inicio.php#autos">
                            <i class="bi bi-car-front"></i> Ver Autos
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Main Content -->
<div class="container my-5" id="autos">
    <h2 class="section-header mb-4">
        <i class="bi bi-car-front"></i> Autos Disponibles
    </h2>

    <div class="row">
        <!-- Filter Sidebar -->
        <div class="col-lg-3 mb-4">
            <div class="filter-panel p-4">
                <div class="filter-header">
                    <div class="filter-icon">
                        <i class="bi bi-funnel-fill"></i>
                    </div>
                    <h4 class="mb-0 filter-title">Filtrar Autos</h4>
                </div>

                <form method="GET" action="" id="filterForm">
                    <div class="mb-4">
                        <label for="marca" class="form-label">Marca</label>
                        <input type="text" class="form-control" id="marca" name="marca"
                            placeholder="Ej: Toyota"
                            value="<?php echo isset($_GET['marca']) ? htmlspecialchars($_GET['marca']) : ''; ?>">
                    </div>

                    <div class="mb-4">
                        <label for="modelo" class="form-label">Modelo</label>
                        <input type="text" class="form-control" id="modelo" name="modelo"
                            placeholder="Ej: Corolla"
                            value="<?php echo isset($_GET['modelo']) ? htmlspecialchars($_GET['modelo']) : ''; ?>">
                    </div>

                    <div class="mb-4">
                        <label for="anio_min" class="form-label">Año Mínimo</label>
                        <input type="number" class="form-control" id="anio_min" name="anio_min"
                            placeholder="2020"
                            value="<?php echo isset($_GET['anio_min']) ? htmlspecialchars($_GET['anio_min']) : ''; ?>">
                    </div>

                    <div class="mb-4">
                        <label for="precio_max" class="form-label">Precio Máx/Día ($)</label>
                        <input type="number" class="form-control" id="precio_max" name="precio_max"
                            placeholder="5000"
                            value="<?php echo isset($_GET['precio_max']) ? htmlspecialchars($_GET['precio_max']) : ''; ?>">
                    </div>

                    <button type="submit" class="btn btn-primary w-100 mb-2">
                        <i class="bi bi-search"></i> Buscar
                    </button>

                    <?php if (!empty($_GET)): ?>
                        <a href="inicio.php" class="btn btn-reset w-100" id="clearFiltersBtn">
                            <i class="bi bi-arrow-counterclockwise"></i> Limpiar Filtros
                        </a>
                    <?php endif; ?>
                </form>
            </div>
        </div>

        <!-- Cars Grid -->
        <div class="col-lg-9">
            <?php
            // Cargar lógica de filtrado de autos
            include_once("../filtros/obtener_autos_disponibles.php");
            ?>

            <p class="text-muted mb-3">
                <?php echo $totalCars; ?> <?php echo $totalCars == 1 ? 'auto disponible' : 'autos disponibles'; ?>
            </p>

            <div class="row g-4">
                <?php

                if ($resultado->num_rows > 0) {
                    while ($fila = $resultado->fetch_assoc()) {
                ?>
                        <div class="col-md-6 col-xl-4">
                            <div class="card h-100 shadow-sm">
                                <div class="car-card-image">
                                    <?php if (!empty($fila['imagen'])): ?>
                                        <img src="../auto_imgs/<?php echo htmlspecialchars($fila['imagen']); ?>"
                                            alt="<?php echo htmlspecialchars($fila['marca'] . ' ' . $fila['modelo']); ?>">
                                    <?php else: ?>
                                        <i class="bi bi-car-front-fill text-muted car-icon-large"></i>
                                    <?php endif; ?>
                                    <div class="car-card-price">
                                        $<?php echo number_format($fila['precio_por_dia'], 0); ?>/día
                                    </div>
                                </div>

                                <div class="card-body">
                                    <h5 class="card-title">
                                        <?php echo htmlspecialchars($fila['marca']) . ' ' . htmlspecialchars($fila['modelo']); ?>
                                    </h5>

                                    <div class="car-detail">
                                        <i class="bi bi-calendar3"></i>
                                        <span><?php echo $fila['anio']; ?></span>
                                    </div>

                                    <div class="car-detail mb-3">
                                        <i class="bi bi-hash"></i>
                                        <span><?php echo htmlspecialchars($fila['patente']); ?></span>
                                    </div>

                                    <span class="badge bg-success">
                                        <i class="bi bi-check-circle"></i> Disponible
                                    </span>
                                </div>

                                <div class="card-footer">
                                    <?php if (isset($_SESSION['id_usuario'])): ?>
                                        <?php if ($_SESSION['fk_rol'] == 2): ?>
                                            <a href="../cliente/abm_reservas/alta_reserva.php?auto=<?php echo $fila['id_auto']; ?>"
                                                class="btn btn-primary w-100">
                                                <i class="bi bi-calendar-check"></i> Reservar Ahora
                                            </a>
                                        <?php elseif ($_SESSION['fk_rol'] == 1): ?>
                                            <a href="../admin/abm_autos/mod_auto.php?id=<?php echo $fila['id_auto']; ?>"
                                                class="btn btn-warning w-100">
                                                <i class="bi bi-pencil-square"></i> Editar Auto
                                            </a>
                                        <?php endif; ?>
                                    <?php else: ?>
                                        <a href="login.php" class="btn btn-outline-primary w-100">
                                            <i class="bi bi-box-arrow-in-right"></i> Inicia Sesión
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                <?php
                    }
                } else {
                    echo '<div class="col-12">
                            <div class="alert alert-info text-center" role="alert">
                                <i class="bi bi-info-circle me-2"></i>
                                No hay autos disponibles con los filtros seleccionados.
                            </div>
                          </div>';
                }

                $stmt->close();
                ?>
            </div>
        </div>
    </div>
</div>

<?php
// Mostrar overlay inmediatamente si hay parámetros en la URL
$hasParams = !empty($_SERVER['QUERY_STRING']);
$overlayClass = $hasParams ? ' class="active"' : '';
?>
<div id="loadingOverlay" <?php echo $overlayClass; ?>>
    <div style="text-align: center;">
        <div class="spinner-border text-light" role="status" style="width: 3rem; height: 3rem;">
            <span class="visually-hidden">Cargando...</span>
        </div>
        <p style="color: white; margin-top: 1rem; font-size: 1.1rem; font-weight: 600;">Cargando resultados...</p>
    </div>
</div>

<script src="../js/filter-handler.js"></script>

<?php include_once("../components/footer.php"); ?>