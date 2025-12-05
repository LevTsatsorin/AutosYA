/**
 * Manejador de formulario de filtros con overlay de carga suave
 */

document.addEventListener("DOMContentLoaded", function () {
  const overlay = document.getElementById("loadingOverlay");
  const filterForm = document.getElementById("filterForm");
  const clearBtn = document.getElementById("clearFiltersBtn");
  const urlParams = new URLSearchParams(window.location.search);
  const scrollToAutos = urlParams.get("scrollTo") === "autos";

  // Ocultar overlay si está activo
  if (overlay.classList.contains("active")) {
    setTimeout(function () {
      overlay.classList.remove("active");
    }, 400);
  }

  // Manejar envío del formulario de filtros
  if (filterForm) {
    filterForm.addEventListener("submit", function (e) {
      e.preventDefault();
      overlay.classList.add("active");

      // Obtener datos del formulario y agregar parámetro scrollTo
      const formData = new FormData(filterForm);
      const params = new URLSearchParams(formData);
      params.set("scrollTo", "autos");

      window.location.href = window.location.pathname + "?" + params.toString();
    });
  }

  // Manejar botón de limpiar filtros
  if (clearBtn) {
    clearBtn.addEventListener("click", function (e) {
      e.preventDefault();
      overlay.classList.add("active");
      window.location.href = window.location.pathname + "?scrollTo=autos";
    });
  }

  // Auto-scroll a la sección de autos
  if (scrollToAutos) {
    window.addEventListener("load", function () {
      const autosSection = document.getElementById("autos");
      if (autosSection) {
        autosSection.scrollIntoView({ behavior: "smooth", block: "start" });
      }
    });
  }
});
