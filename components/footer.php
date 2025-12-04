    </main>


    <footer class="text-white text-center py-4 mt-5">
        <div class="container">
            <div class="row">
                <div class="col-md-4 mb-3 mb-md-0">
                    <h5 style="font-weight: 700;">
                        <i class="bi bi-car-front-fill"></i> Autos<span style="color: var(--rosy-taupe);">YA</span>
                    </h5>
                    <p class="mb-0 opacity-75">Tu mejor opción en alquiler de autos</p>
                </div>
                <div class="col-md-4 mb-3 mb-md-0">
                    <h6 style="font-weight: 600;">Enlaces Rápidos</h6>
                    <ul class="list-unstyled">
                        <li><a href="/AutosYA/pages/inicio.php" class="text-white opacity-75 text-decoration-none">Inicio</a></li>
                        <?php if (!isset($_SESSION['id_usuario'])): ?>
                            <li><a href="/AutosYA/pages/login.php" class="text-white opacity-75 text-decoration-none">Iniciar Sesión</a></li>
                            <li><a href="/AutosYA/pages/registro.php" class="text-white opacity-75 text-decoration-none">Registrarse</a></li>
                        <?php else: ?>
                            <?php if (isset($_SESSION['fk_rol']) && $_SESSION['fk_rol'] == 1): ?>
                                <li><a href="/AutosYA/admin/index.php" class="text-white opacity-75 text-decoration-none">Panel Admin</a></li>
                            <?php else: ?>
                                <li><a href="/AutosYA/cliente/perfil.php" class="text-white opacity-75 text-decoration-none">Mi Perfil</a></li>
                                <li><a href="/AutosYA/pages/mis_reservas.php" class="text-white opacity-75 text-decoration-none">Mis Reservas</a></li>
                            <?php endif; ?>
                        <?php endif; ?>
                    </ul>
                </div>
                <div class="col-md-4">
                    <h6 style="font-weight: 600;">Desarrolladores</h6>
                    <p class="mb-0 opacity-75">
                        <i class="bi bi-person-fill"></i> Lev Tsatsorin<br>
                        <i class="bi bi-person-fill"></i> Ainur Munasipov
                    </p>
                </div>
            </div>
            <hr class="my-3 opacity-25">
            <p class="mb-0 opacity-75">&copy; 2025 AutosYA - Proyecto Final</p>
            <p class="mb-0 small opacity-50">Programación Web II, ACT2AP</p>
        </div>
    </footer>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="/AutosYA/js/app.js"></script>
    </body>

    </html>