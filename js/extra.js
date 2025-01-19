// Mostrar o botão quando o utilizador faz scroll para baixo
window.onscroll = function() {scrollFunction()};

function scrollFunction() {
  const button = document.getElementById("backToTopBtn");
  if (document.body.scrollTop > 200 || document.documentElement.scrollTop > 200) {
    button.style.display = "block";
  } else {
    button.style.display = "none";
  }
}

// Função para voltar ao topo da página
function topFunction() {
    window.scrollTo({ top: 0, behavior: 'smooth' });
}

// Inicializar os tooltips do Bootstrap (caso sejam utilizados)
document.addEventListener("DOMContentLoaded", function () {
  const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
  tooltipTriggerList.map(function (tooltipTriggerEl) {
      return new bootstrap.Tooltip(tooltipTriggerEl);
  });
});

// Configurar o modal de login para abrir ao clicar no botão de login
document.addEventListener("DOMContentLoaded", function () {
  const loginButton = document.getElementById("login-button"); // Botão Login
  const loginModal = new bootstrap.Modal(document.getElementById("loginModal"), {}); // Instância do modal

  // Abrir o modal ao clicar no botão
  if (loginButton) {
      loginButton.addEventListener("click", function () {
          loginModal.show();
      });
  }
});


