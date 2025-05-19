import { initializeApp } from "https://www.gstatic.com/firebasejs/11.2.0/firebase-app.js";
import { getAuth, GoogleAuthProvider, signInWithPopup, signInWithEmailAndPassword, signOut } from "https://www.gstatic.com/firebasejs/11.2.0/firebase-auth.js";

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
    
    const dropdownHTML = `
        <div class="dropdown">
            <a class="nav-link dropdown-toggle logged-in-user" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                <i class="fa fa-user me-2"></i>${user.displayName || user.nome}
            </a>
            <ul class="dropdown-menu dropdown-menu-end">
                <li><a class="dropdown-item" href="#"><i class="fa fa-user me-2"></i>Ver Perfil</a></li>
                <li><a class="dropdown-item" href="#"><i class="fa fa-calendar me-2"></i>Calendário</a></li>
                <li><a class="dropdown-item" href="#"><i class="fa fa-envelope me-2"></i>Mensagens</a></li>
                <li><hr class="dropdown-divider"></li>
                <li><a class="dropdown-item text-danger fw-bold" href="#" id="logout-btn"><i class="fa fa-sign-out me-2"></i>Terminar Sessão</a></li>
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
        logoutBtn.addEventListener('click', async function(e) {
            e.preventDefault();
            try {
                await signOut(auth);
                localStorage.removeItem('user');
                localStorage.removeItem('loginOrigem');
                location.reload();
            } catch (error) {
                console.error('Erro ao terminar sessão:', error);
                // Forçar limpeza mesmo em caso de erro
                localStorage.removeItem('user');
                localStorage.removeItem('loginOrigem');
                location.reload();
            }
        });
    }
}

// Função para fechar o modal de login
function closeLoginModal() {
    const modal = document.getElementById('loginModal');
    if (modal) {
        const modalInstance = bootstrap.Modal.getInstance(modal);
        if (modalInstance) {
            modalInstance.hide();
        } else {
            modal.classList.remove('show');
            modal.style.display = 'none';
            document.body.classList.remove('modal-open');
            const backdrop = document.querySelector('.modal-backdrop');
            if (backdrop) backdrop.remove();
        }
    }
}

document.addEventListener("DOMContentLoaded", function () {
    const googleLogin = document.getElementById("google-login-b");
    const loginForm = document.getElementById("loginForm");
    const loginModal = document.getElementById("loginModal");

    if (!googleLogin || !loginModal || !loginForm) {
        console.error("Elementos necessários não foram encontrados.");
        return;
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
                closeLoginModal();

                // Atualizar a navbar
                updateNavbarForLogin(user);
            })
            .catch((error) => {
                console.error("Erro no login:", error);
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

    const errorMessage = document.getElementById("loginError");
    if (errorMessage) {
        errorMessage.style.display = "none";
        errorMessage.textContent = "";
    }

    googleLogin.addEventListener("click", function () {
        const errorMessage = document.getElementById("loginError");
        if (errorMessage) {
            errorMessage.style.display = "none";
            errorMessage.textContent = "";
        }
        signInWithPopup(auth, provider)
            .then((result) => {
                const user = result.user;
                const userData = {
                    nome: user.displayName,
                    email: user.email,
                    photoURL: user.photoURL,
                    uid: user.uid,
                    tipo: 'Google'
                };
                localStorage.setItem('user', JSON.stringify(userData));
                localStorage.setItem('loginOrigem', 'google');
                closeLoginModal();
                updateNavbarForLogin(userData);
                location.reload();
            })
            .catch((error) => {
                console.error("Erro no login:", error.message);
                if (errorMessage) {
                    errorMessage.textContent = "Erro ao iniciar sessão com o Google. Tente novamente.";
                    errorMessage.style.display = "block";
                }
            });
    });

    // Verificar estado de autenticação ao carregar a página
    auth.onAuthStateChanged((user) => {
        if (user) {
            const userData = {
                nome: user.displayName,
                email: user.email,
                photoURL: user.photoURL,
                uid: user.uid,
                tipo: 'Google'
            };
            updateNavbarForLogin(userData);
        }
    });

    // Adicionar evento de logout
    document.addEventListener('click', function(e) {
        if (e.target && e.target.id === 'logout-btn') {
            e.preventDefault();
            const auth = getAuth();
            signOut(auth).then(() => {
                localStorage.removeItem('user');
                localStorage.removeItem('loginOrigem');
                location.reload();
            }).catch((error) => {
                // Mesmo que dê erro, força a limpeza local
                localStorage.removeItem('user');
                localStorage.removeItem('loginOrigem');
                location.reload();
            });
        }
    });
});
