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

// Função utilitária para fechar modais Bootstrap 5 de forma robusta
function closeModal(modalId) {
  const modalEl = document.getElementById(modalId);
  if (!modalEl) return;
  // Tenta fechar via Bootstrap
  if (window.bootstrap && typeof bootstrap.Modal === "function" && typeof bootstrap.Modal.getInstance === "function") {
    const modalInstance = bootstrap.Modal.getInstance(modalEl);
    if (modalInstance) {
      modalInstance.hide();
      return;
    }
  }
  // Fecho forçado (fallback)
  modalEl.classList.remove('show');
  modalEl.style.display = 'none';
  document.body.classList.remove('modal-open');
  document.body.style = '';
  const backdrop = document.querySelector('.modal-backdrop');
  if (backdrop) backdrop.remove();
}

// Função para configurar os botões de mostrar/esconder senha
function setupPasswordToggles() {
  const passwordFields = [
    { input: 'password', container: 'login-password-container' },  // Login
    { input: 'reg_password', container: 'register-password-container' }  // Registo
  ];

  passwordFields.forEach(field => {
    const inputElement = document.getElementById(field.input);
    const containerElement = document.getElementById(field.container);
    if (inputElement && containerElement && !containerElement.querySelector('.password-toggle-btn')) {
      const toggleButton = document.createElement('button');
      toggleButton.type = 'button';
      toggleButton.className = 'btn btn-outline-secondary password-toggle-btn';
      toggleButton.innerHTML = '<i class="fa fa-eye"></i>';
      toggleButton.setAttribute('aria-label', 'Mostrar/esconder palavra-passe');
      containerElement.appendChild(toggleButton);

      toggleButton.addEventListener('click', function() {
        const type = inputElement.getAttribute('type') === 'password' ? 'text' : 'password';
        inputElement.setAttribute('type', type);
        const iconElement = this.querySelector('i');
        iconElement.classList.toggle('fa-eye', type === 'password');
        iconElement.classList.toggle('fa-eye-slash', type === 'text');
      });
    }
  });
}

// Função única para atualizar a navbar após login
function updateNavbarForLogin(user) {
  const loginLink = document.querySelector('a.nav-link[data-bs-toggle="modal"][data-bs-target="#loginModal"]');
  if (!loginLink) return;

  const navItem = loginLink.closest('li.nav-item');
  if (!navItem) return;

  // Adiciona o botão Dashboard só para Admins
  const isAdmin = user.tipo === 'Admin';
  const dashboardBtn = isAdmin
    ? `<li><a class="dropdown-item text-primary fw-bold" href="index_Dashboard.php?user=${encodeURIComponent(user.nome || user.displayName)}" id="dashboard-btn"><i class="fa fa-cogs me-2"></i>Dashboard</a></li><li><hr class="dropdown-divider"></li>`
    : '';

  // Adiciona o botão Submeter Ticket só para Alunos
  const isAluno = user.tipo === 'Aluno';
  const submitTicketBtn = isAluno
    ? `<li><a class="dropdown-item" href="submit_ticket.php"><i class="fa fa-ticket me-2"></i>Submeter Ticket</a></li>`
    : '';

  const dropdownHTML = `
    <div class="dropdown">
      <a class="nav-link dropdown-toggle logged-in-user" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
        <i class="fa fa-user me-2"></i>${user.nome || user.displayName}
      </a>
      <ul class="dropdown-menu dropdown-menu-end" style="min-width: 220px;">
        ${dashboardBtn}
        ${submitTicketBtn}
        <li>
          <a class="dropdown-item" href="#"><i class="fa fa-user me-2"></i>Ver Perfil</a>
        </li>
        <li>
          <a class="dropdown-item" href="#"><i class="fa fa-calendar me-2"></i>Calendário</a>
        </li>
        <li>
          <a class="dropdown-item" href="#"><i class="fa fa-envelope me-2"></i>Mensagens</a>
        </li>
        <li><hr class="dropdown-divider"></li>
        <li>
          <a class="dropdown-item text-danger fw-bold" href="#" id="logout-btn">
            <i class="fa fa-sign-out me-2"></i>Terminar Sessão
          </a>
        </li>
      </ul>
    </div>
  `;

  navItem.innerHTML = dropdownHTML;

  // Inicializar o dropdown do Bootstrap
  const dropdownElement = navItem.querySelector('.dropdown-toggle');
  if (dropdownElement) {
    new bootstrap.Dropdown(dropdownElement);
  }

  // Adicionar evento de logout
  const logoutBtn = document.getElementById('logout-btn');
  if (logoutBtn) {
    logoutBtn.addEventListener('click', e => {
      e.preventDefault();
      localStorage.removeItem('user');
      localStorage.removeItem('loginOrigem');
      location.reload();
    });
  }
}

// Inicializar funcionalidades após DOM pronto
// Inclui tooltips, toggles, e navbar se user autenticado
// Também regista listeners para login e registo

document.addEventListener("DOMContentLoaded", () => {
  // Adicionar handler para todos os links que abrem o modal de login
  const loginLinks = document.querySelectorAll('a[data-bs-toggle="modal"][data-bs-target="#loginModal"]');
  loginLinks.forEach(link => {
    link.addEventListener('click', function(e) {
      e.preventDefault();
      const loginModal = new bootstrap.Modal(document.getElementById('loginModal'));
      loginModal.show();
    });
  });

  // Tooltips
  const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
  tooltipTriggerList.forEach(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl));

  // Mostrar/esconder palavra-passe
  setupPasswordToggles();

  // Atualizar navbar se já estiver autenticado
  const user = localStorage.getItem('user');
  if (user) {
    try {
      const userData = JSON.parse(user);
      updateNavbarForLogin(userData);
    } catch (e) {
      console.error('Erro ao processar dados do utilizador:', e);
    }
  }

  // Registo
  const registerForm = document.getElementById("registerForm");
  if (registerForm) {
    registerForm.addEventListener("submit", async e => {
      e.preventDefault();
      const nome = document.getElementById("reg_nome").value;
      const email = document.getElementById("reg_email").value;
      const senha = document.getElementById("reg_password").value;

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
          body: `nome=${encodeURIComponent(nome)}&email=${encodeURIComponent(email)}&senha=${encodeURIComponent(senha)}`
        });
        const data = await response.json();
        
        if (data.success) {
          document.getElementById("registerSuccess").innerText = data.message;
          document.getElementById("registerSuccess").style.display = "block";
          registerForm.reset();
          setTimeout(() => {
            closeModalById('registerModal');
          }, 1000);
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

  // Login
  // teste
  const loginForm = document.getElementById("loginForm");
  if (loginForm) {
    loginForm.addEventListener("submit", async e => {
      e.preventDefault();
      const email = document.getElementById("email").value;
      const senha = document.getElementById("password").value;

      const loginError = document.getElementById("loginError");
      loginError.style.display = "none";
      loginError.innerText = "";

      try {
        const response = await fetch('login.php', {
          method: 'POST',
          headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
          body: `email=${encodeURIComponent(email)}&senha=${encodeURIComponent(senha)}`
        });
        const data = await response.json();

        if (data.success) {
          localStorage.setItem('user', JSON.stringify(data.user));
          updateNavbarForLogin(data.user);
          closeLoginModal();
          location.reload();
        } else {
          loginError.innerText = data.message;
          loginError.style.display = "block";
        }
      } catch (error) {
        loginError.innerText = "Erro ao comunicar com o servidor.";
        loginError.style.display = "block";
      }
    });
  }

  // Garantia extra: sempre que o modal for fechado (por X, clique fora, ou JS), limpa overlays e scroll
  const loginModal = document.getElementById('loginModal');
  if (loginModal) {
    loginModal.addEventListener('hidden.bs.modal', () => {
      setTimeout(() => {
        document.body.classList.remove('modal-open');
        document.body.style = '';
        document.querySelectorAll('.modal-backdrop').forEach(b => b.remove());
      }, 100);
    });
  }

  // Limpar mensagens quando o modal de registo for fechado
  const registerModal = document.getElementById('registerModal');
  if (registerModal) {
    registerModal.addEventListener('hidden.bs.modal', () => {
      document.getElementById("registerError").style.display = "none";
      document.getElementById("registerSuccess").style.display = "none";
      document.getElementById("registerError").innerText = "";
      document.getElementById("registerSuccess").innerText = "";
    });
  }
});

// Torna a função mais genérica para fechar qualquer modal
export function closeModalById(modalId) {
  const modalEl = document.getElementById(modalId);
  if (!modalEl) return;
  // Tenta fechar via Bootstrap
  if (window.bootstrap && typeof bootstrap.Modal === "function" && typeof bootstrap.Modal.getInstance === "function") {
    const modalInstance = bootstrap.Modal.getInstance(modalEl);
    if (modalInstance) {
      modalInstance.hide();
      return;
    }
  }
  // Fecho forçado (fallback)
  modalEl.classList.remove('show');
  modalEl.style.display = 'none';
  document.body.classList.remove('modal-open');
  document.body.style = '';
  const backdrop = document.querySelector('.modal-backdrop');
  if (backdrop) backdrop.remove();

  // Garantia extra: limpa tudo após 300ms (caso o Bootstrap não limpe)
  setTimeout(() => {
    document.body.classList.remove('modal-open');
    document.body.style = '';
    document.querySelectorAll('.modal-backdrop').forEach(b => b.remove());
  }, 300);
}

// Mantém a função antiga para compatibilidade
export function closeLoginModal() {
  closeModalById('loginModal');
}
