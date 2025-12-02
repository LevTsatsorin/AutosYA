(function () {
  "use strict";

  const forms = document.querySelectorAll(".needs-validation");
  const claveRepInput = document.getElementById("clave_rep");
  const feedbackDiv = claveRepInput.nextElementSibling;

  forms.forEach(function (form) {
    form.addEventListener(
      "submit",
      function (event) {
        // Validar que las contraseñas coincidan
        const clave = document.getElementById("clave").value;
        const claveRep = claveRepInput.value;

        if (clave !== claveRep) {
          event.preventDefault();
          event.stopPropagation();
          claveRepInput.setCustomValidity("Las contraseñas no coinciden");
          feedbackDiv.textContent = "Las contraseñas no coinciden";
        } else {
          claveRepInput.setCustomValidity("");
          feedbackDiv.textContent = "Repite tu contraseña.";
        }

        if (!form.checkValidity()) {
          event.preventDefault();
          event.stopPropagation();
        }
        form.classList.add("was-validated");
      },
      false
    );
  });

  // Limpiar validación personalizada cuando el usuario escribe
  claveRepInput.addEventListener("input", function () {
    this.setCustomValidity("");
    feedbackDiv.textContent = "Repite tu contraseña.";
  });
})();
