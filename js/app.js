// Cerrar alertas automáticamente después de 5 segundos
document.addEventListener("DOMContentLoaded", function () {
  const alerts = document.querySelectorAll(".alert");

  alerts.forEach(function (alert) {
    setTimeout(function () {
      const bsAlert = new bootstrap.Alert(alert);
      bsAlert.close();
    }, 5000);
  });
});

function scrollToTop() {
  window.scrollTo({
    top: 0,
    behavior: "smooth",
  });
}

// Agregar botón de scroll hacia arriba
document.addEventListener("DOMContentLoaded", function () {
  const scrollBtn = document.createElement("button");
  scrollBtn.innerHTML = '<i class="bi bi-arrow-up-circle-fill"></i>';
  scrollBtn.className = "btn btn-primary scroll-to-top";
  scrollBtn.onclick = scrollToTop;
  scrollBtn.setAttribute("aria-label", "Volver arriba");

  document.body.appendChild(scrollBtn);

  window.addEventListener("scroll", function () {
    if (window.pageYOffset > 300) {
      scrollBtn.classList.add("show");
    } else {
      scrollBtn.classList.remove("show");
    }
  });
});

console.log("AutosYA Application Loaded Successfully");
