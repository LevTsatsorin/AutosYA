(function () {
  "use strict";

  const form = document.getElementById("reservaForm");
  if (!form) return;

  const fechaInicio = document.getElementById("fecha_inicio");
  const fechaFin = document.getElementById("fecha_fin");
  const disponibilidadMsg = document.getElementById("disponibilidadMsg");
  const submitBtn = document.getElementById("submitBtn");
  const totalDias = document.getElementById("total_dias");
  const precioTotal = document.getElementById("precio_total");

  let fechasValidas = false;

  const MS_POR_DIA = 1000 * 60 * 60 * 24;

  // Verificar si las fechas se superponen con reservas existentes
  function verificarSuperposicion(inicio, fin) {
    if (
      typeof reservasExistentes === "undefined" ||
      !reservasExistentes.length
    ) {
      return null;
    }

    const inicioSeleccionado = new Date(inicio + "T00:00:00");
    const finSeleccionado = new Date(fin + "T00:00:00");

    for (const reserva of reservasExistentes) {
      const inicioReserva = new Date(reserva.fecha_inicio + "T00:00:00");
      const finReserva = new Date(reserva.fecha_fin + "T00:00:00");

      if (
        !(finSeleccionado < inicioReserva || inicioSeleccionado > finReserva)
      ) {
        return reserva;
      }
    }

    return null;
  }

  // Validar fechas y verificar disponibilidad
  function validarYVerificar() {
    const inicio = fechaInicio.value;
    const fin = fechaFin.value;

    // Resetear estado
    submitBtn.disabled = true;
    fechasValidas = false;

    if (!inicio || !fin) {
      if (!disponibilidadMsg.classList.contains("alert-danger")) {
        disponibilidadMsg.classList.add("d-none");
      }
      totalDias.textContent = "-";
      precioTotal.textContent = "0.00";
      return;
    }

    // Validar que fecha_fin > fecha_inicio
    const [yearInicio, monthInicio, dayInicio] = inicio.split("-").map(Number);
    const [yearFin, monthFin, dayFin] = fin.split("-").map(Number);
    const dateInicio = new Date(yearInicio, monthInicio - 1, dayInicio);
    const dateFin = new Date(yearFin, monthFin - 1, dayFin);

    if (dateFin <= dateInicio) {
      disponibilidadMsg.textContent =
        "La fecha de fin debe ser posterior a la fecha de inicio";
      disponibilidadMsg.classList.remove("d-none", "alert-success");
      disponibilidadMsg.classList.add("alert-danger");
      totalDias.textContent = "-";
      precioTotal.textContent = "0.00";
      return;
    }

    // Validar que no sean fechas del pasado
    const manana = new Date();
    manana.setDate(manana.getDate() + 1);
    manana.setHours(0, 0, 0, 0);

    if (dateInicio < manana) {
      disponibilidadMsg.textContent =
        "Las reservas deben comenzar a partir de mañana";
      disponibilidadMsg.classList.remove("d-none", "alert-success");
      disponibilidadMsg.classList.add("alert-danger");
      totalDias.textContent = "-";
      precioTotal.textContent = "0.00";
      return;
    }

    // Verificar superposición con reservas existentes
    const conflicto = verificarSuperposicion(inicio, fin);
    if (conflicto) {
      const fechaInicioConflicto = new Date(
        conflicto.fecha_inicio + "T00:00:00"
      ).toLocaleDateString("es-AR");
      const fechaFinConflicto = new Date(
        conflicto.fecha_fin + "T00:00:00"
      ).toLocaleDateString("es-AR");
      disponibilidadMsg.innerHTML = `<i class="bi bi-x-circle"></i> <strong>Fechas no disponibles.</strong> Este auto ya está reservado del <strong>${fechaInicioConflicto}</strong> al <strong>${fechaFinConflicto}</strong>. Por favor, elige otras fechas.`;
      disponibilidadMsg.classList.remove("d-none", "alert-success");
      disponibilidadMsg.classList.add("alert-danger");
      totalDias.textContent = "-";
      precioTotal.textContent = "0.00";
      return;
    }

    // Calcular días
    const diffTime = Math.abs(dateFin - dateInicio);
    const diffDays = Math.ceil(diffTime / MS_POR_DIA);
    totalDias.textContent = diffDays;

    // Calcular precio total
    const total = diffDays * precioPorDia;
    precioTotal.textContent = total.toFixed(2);

    // Si llegamos aquí, las fechas son válidas
    disponibilidadMsg.classList.add("d-none");
    disponibilidadMsg.classList.remove("alert-danger", "alert-success");
    fechasValidas = true;
    submitBtn.disabled = false;
  }

  // Event listeners
  fechaInicio.addEventListener("input", validarYVerificar);
  fechaFin.addEventListener("input", validarYVerificar);
  fechaInicio.addEventListener("input", actualizarFechaFinMin);

  // Validación del formulario antes de enviar
  form.addEventListener("submit", function (event) {
    if (!form.checkValidity() || !fechasValidas) {
      event.preventDefault();
      event.stopPropagation();

      if (!fechasValidas && fechaInicio.value && fechaFin.value) {
        disponibilidadMsg.innerHTML =
          '<i class="bi bi-exclamation-triangle"></i> Por favor, verifica la disponibilidad antes de continuar.';
        disponibilidadMsg.classList.remove("d-none", "alert-success");
        disponibilidadMsg.classList.add("alert-danger");
      }
    }
    form.classList.add("was-validated");
  });

  function actualizarFechaFinMin() {
    if (fechaInicio.value) {
      const minFechaFin = new Date(fechaInicio.value);
      minFechaFin.setDate(minFechaFin.getDate() + 1);
      fechaFin.min = minFechaFin.toISOString().split("T")[0];

      if (
        fechaFin.value &&
        new Date(fechaFin.value) <= new Date(fechaInicio.value)
      ) {
        fechaFin.value = "";
      }
    }
  }
})();
