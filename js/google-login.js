import { initializeApp } from "https://www.gstatic.com/firebasejs/11.2.0/firebase-app.js";
import { getAuth, GoogleAuthProvider, signInWithPopup, signInWithEmailAndPassword } from "https://www.gstatic.com/firebasejs/11.2.0/firebase-auth.js";

// Configuração do Firebase
const firebaseConfig = {
  apiKey: "AIzaSyBw149NYjEO2u3n4eREn7OEsswmT6Ews9I",
  authDomain: "login-aebconecta.firebaseapp.com",
  projectId: "login-aebconecta",
  storageBucket: "login-aebconecta.firebasestorage.app",
  messagingSenderId: "114762405232",
  appId: "1:114762405232:web:4deeb016d92911dead6bb5"
};

const app = initializeApp(firebaseConfig);
const auth = getAuth(app);
auth.languageCode = 'pt';
const provider = new GoogleAuthProvider();

// Variável global para guardar as informações do utilizador
let currentUserInfo = {};

document.addEventListener("DOMContentLoaded", function () {
  const googleLogin = document.getElementById("google-login-b");
  const loginForm = document.getElementById("loginForm");
  const loginModal = document.getElementById("loginModal");

  if (!googleLogin || !loginModal || !loginForm) {
    console.error("Elementos necessários não foram encontrados.");
    return;
  }

  // Função para atualizar a navbar para o estado de login
  function updateNavbarForLogin(user) {
    const loginLink = document.querySelector('a.nav-link[data-bs-toggle="modal"][data-bs-target="#loginModal"]');
    if (!loginLink) {
        console.log('Link de login não encontrado');
        return;
    }
    
    const navItem = loginLink.closest('li.nav-item');
    if (!navItem) {
        console.log('Nav item não encontrado');
        return;
    }
    
    navItem.innerHTML = `
        <div class="dropdown">
            <a class="nav-link dropdown-toggle logged-in-user" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                <i class="fa fa-user me-2"></i>${user.displayName}
            </a>
            <ul class="dropdown-menu dropdown-menu-end">
                <li><a class="dropdown-item" href="#"><i class="fa fa-user me-2"></i>Ver Perfil</a></li>
                <li><a class="dropdown-item" href="#"><i class="fa fa-calendar me-2"></i>Calendário</a></li>
                <li><a class="dropdown-item" href="#"><i class="fa fa-envelope me-2"></i>Mensagens</a></li>
                <li><hr class="dropdown-divider"></li>
                <li><a class="dropdown-item" href="#" id="logout-btn"><i class="fa fa-sign-out me-2"></i>Terminar Sessão</a></li>
            </ul>
        </div>
    `;

    // Adicionar evento de logout
    document.getElementById('logout-btn').addEventListener('click', function(e) {
        e.preventDefault();
        auth.signOut().then(() => {
            location.reload(); // Recarrega a página após logout
            console.log('Utilizador terminou sessão com sucesso');
        }).catch((error) => {
            console.error('Erro ao terminar sessão:', error);
        });
    });
  }

  // Função para atualizar a navbar para o estado de logout
  function updateNavbarForLogout() {
    location.reload(); // Recarrega a página para restaurar o estado original
  }

  // Adicionar evento de submit ao formulário de login
  loginForm.addEventListener("submit", function(e) {
    e.preventDefault();
    
    const email = document.getElementById("email").value;
    const password = document.getElementById("password").value;
    
    signInWithEmailAndPassword(auth, email, password)
        .then((userCredential) => {
            const user = userCredential.user;
            console.log("Login bem-sucedido:", user);
            
            // Fechar o modal
            $('#loginModal').modal('hide');
            $('.modal-backdrop').remove();
            $('body').removeClass('modal-open').css('padding-right', '');

            // Atualizar a navbar
            updateNavbarForLogin(user);
        })
        .catch((error) => {
            console.error("Erro no login:", error);
            // Mostrar mensagem de erro ao utilizador
            const errorMessage = document.getElementById("loginError");
            if (errorMessage) {
                switch (error.code) {
                    case 'auth/invalid-email':
                        errorMessage.textContent = "Email inválido.";
                        break;
                    case 'auth/user-not-found':
                        errorMessage.textContent = "Utilizador não encontrado.";
                        break;
                    case 'auth/wrong-password':
                        errorMessage.textContent = "Password incorreta.";
                        break;
                    default:
                        errorMessage.textContent = "Erro ao iniciar sessão. Tente novamente.";
                }
                errorMessage.style.display = "block";
            }
        });
  });

  googleLogin.addEventListener("click", function () {
    signInWithPopup(auth, provider)
      .then((result) => {
        const user = result.user;
        
        // Guardar as informações do utilizador
        currentUserInfo = {
          nome: user.displayName,
          email: user.email,
          photoURL: user.photoURL,
          uid: user.uid
        };

        console.log("Login bem-sucedido:", user);
        
        // Fechar o modal usando jQuery
        $('#loginModal').modal('hide');
        $('.modal-backdrop').remove();
        $('body').removeClass('modal-open').css('padding-right', '');

        // Atualizar a navbar com o menu dropdown
        updateNavbarForLogin(user);
      })
      .catch((error) => {
        console.error("Erro no login:", error.message);
      });
  });

  // Verificar estado de autenticação ao carregar a página
  auth.onAuthStateChanged((user) => {
    if (user) {
      updateNavbarForLogin(user);
    }
  });
});
