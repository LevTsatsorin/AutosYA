document.addEventListener("DOMContentLoaded", function () {
  const fechaInicio = document.getElementById("fecha_inicio");
  const fechaFin = document.getElementById("fecha_fin");
  const reservasMesContainer = document.getElementById("reservasMesContainer");

  if (!fechaInicio || !fechaFin || !reservasMesContainer) return;

  function mostrarReservasDelPeriodo() {
    const inicio = fechaInicio.value;
    const fin = fechaFin.value;

    if (!inicio && !fin) {
      reservasMesContainer.classList.add("d-none");
      return;
    }

    // Determinar el rango de meses a mostrar
    const fechaRef = inicio || fin;
    const [year, month] = fechaRef.split("-");

    // Filtrar reservas que caen en el mes seleccionado
    const reservasDelMes = reservasExistentes.filter((reserva) => {
      const mesInicioReserva = reserva.fecha_inicio.substring(0, 7);
      const mesFinReserva = reserva.fecha_fin.substring(0, 7);
      const mesSeleccionado = `${year}-${month}`;

      // Mostrar si la reserva toca el mes seleccionado
      return (
        mesInicioReserva <= mesSeleccionado && mesFinReserva >= mesSeleccionado
      );
    });

    if (reservasDelMes.length > 0) {
      let html = `<div class="alert alert-info alert-persistent">
                <h6 class="mb-3">
                    <i class="bi bi-info-circle"></i> Reservas en el período seleccionado:
                </h6>
                <div class="row g-2">`;

      reservasDelMes.forEach((reserva) => {
        const fechaInicioFormat = new Date(
          reserva.fecha_inicio + "T00:00:00"
        ).toLocaleDateString("es-AR");
        const fechaFinFormat = new Date(
          reserva.fecha_fin + "T00:00:00"
        ).toLocaleDateString("es-AR");

        html += `
                    <div class="col-md-6">
                        <div class="p-2 bg-white rounded border border-info">
                            <small>
                                <i class="bi bi-calendar-event text-primary"></i>
                                <strong>${fechaInicioFormat}</strong>
                                <i class="bi bi-arrow-right"></i>
                                <strong>${fechaFinFormat}</strong>
                            </small>
                        </div>
                    </div>`;
      });

      html += `</div></div>`;

      reservasMesContainer.innerHTML = html;
      reservasMesContainer.classList.remove("d-none");
    } else {
      reservasMesContainer.classList.add("d-none");
    }
  }

  fechaInicio.addEventListener("change", mostrarReservasDelPeriodo);
  fechaFin.addEventListener("change", mostrarReservasDelPeriodo);
});
