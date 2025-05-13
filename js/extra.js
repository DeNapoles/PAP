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

  // esconder/mostrar
  setupPasswordToggles();
});

// Função para configurar os botões de mostrar/esconder senha
function setupPasswordToggles() {
  const passwordFields = [
    { input: 'password', container: 'login-password-container' },  // Login
    { input: 'reg_password', container: 'register-password-container' }  // Registo
  ];

  passwordFields.forEach(field => {
    const inputElement = document.getElementById(field.input);
    const containerElement = document.getElementById(field.container);
    
    if (inputElement && containerElement) {
      const toggleButton = document.createElement('button');
      toggleButton.type = 'button';
      toggleButton.className = 'btn btn-outline-secondary password-toggle-btn';
      toggleButton.innerHTML = '<i class="fa fa-eye"></i>';
      toggleButton.setAttribute('aria-label', 'Mostrar/esconder palavra-passe');
      containerElement.appendChild(toggleButton);

      toggleButton.addEventListener('click', function() {
        // Alternar tipo do input entre password e text
        const type = inputElement.getAttribute('type') === 'password' ? 'text' : 'password';
        inputElement.setAttribute('type', type);
        
        // Mudar ícone
        const iconElement = this.querySelector('i');
        if (type === 'text') {
          iconElement.classList.remove('fa-eye');
          iconElement.classList.add('fa-eye-slash');
        } else {
          iconElement.classList.remove('fa-eye-slash');
          iconElement.classList.add('fa-eye');
        }
      });
    }
  });
}

// Configurar o modal de login para abrir ao clicar no botão de login
document.addEventListener("DOMContentLoaded", function () {
  const loginButton = document.getElementById("login-button"); 
  const loginModal = new bootstrap.Modal(document.getElementById("loginModal"), {}); 

  if (loginButton) {
      loginButton.addEventListener("click", function () {
          loginModal.show();
      });
  }
});

//register
document.addEventListener("DOMContentLoaded", function () {
  const registerForm = document.getElementById("registerForm");
  if (registerForm) {
    registerForm.addEventListener("submit", async function (e) {
      e.preventDefault();
      const nome = document.getElementById("reg_nome").value;
      const email = document.getElementById("reg_email").value;
      const senha = document.getElementById("reg_password").value;
      const tipo = document.getElementById("reg_tipo").value;

      const loadingIndicator = document.createElement('div');
      loadingIndicator.classList.add('alert', 'alert-info');
      loadingIndicator.innerText = 'A processar...';
      
      document.getElementById("registerError").style.display = "none";
      document.getElementById("registerSuccess").style.display = "none";
      
      registerForm.insertBefore(loadingIndicator, registerForm.querySelector('button[type="submit"]'));

      try {
        const response = await fetch('register.php', {
          method: 'POST',
          headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
          body: `nome=${encodeURIComponent(nome)}&email=${encodeURIComponent(email)}&senha=${encodeURIComponent(senha)}&tipo=${encodeURIComponent(tipo)}`
        });
        const data = await response.json();
        
        if (data.success) {
          document.getElementById("registerSuccess").innerText = data.message;
          document.getElementById("registerSuccess").style.display = "block";
          registerForm.reset();
        } else {
          document.getElementById("registerError").innerText = data.message;
          document.getElementById("registerError").style.display = "block";
        }
      } catch (error) {
        document.getElementById("registerError").innerText = "Erro ao comunicar com o servidor.";
        document.getElementById("registerError").style.display = "block";
        console.error("Erro:", error);
      } finally {
        if (loadingIndicator.parentNode) {
          loadingIndicator.parentNode.removeChild(loadingIndicator);
        }
      }
    });
  }
});
