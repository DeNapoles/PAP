<!-- ----------------------------------- Modal de Login ----------------------------------- -->
<div class="modal fade" id="loginModal" tabindex="-1" aria-labelledby="loginModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content shadow">
            <div class="modal-header">
                <h5 class="modal-title fw-bold" id="loginModalLabel">Iniciar Sessão</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-danger" id="loginError" style="display: none;"></div>
                <form id="loginForm">
                    <div class="form-floating mb-3">
                        <input type="email" class="form-control" id="email" placeholder="name@example.com" required>
                        <label for="email" class="fw-bold">Email</label>
                    </div>
                    <div class="form-floating mb-3" id="login-password-container" style="position: relative;">
                        <input type="password" class="form-control" id="password" placeholder="Password" required>
                        <label for="password" class="fw-bold">Palavra-passe</label>
                    </div>
                    <button type="submit" class="btn btn-primary w-100 fw-bold">Entrar</button>
                </form>
                <hr>
                <button id="google-login-b" class="btn btn-danger w-100 fw-bold">
                    <i class="fa fa-google me-2"></i> Entrar com o Google
                </button>
            </div>
        </div>
    </div>
</div>

<!-- ----------------------------------- Modal de Registo ----------------------------------- -->
<div class="modal fade" id="registerModal" tabindex="-1" aria-labelledby="registerModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content shadow">
            <div class="modal-header">
                <h5 class="modal-title fw-bold" id="registerModalLabel">Criar conta</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-danger" id="registerError" style="display: none;"></div>
                <div class="alert alert-success" id="registerSuccess" style="display: none;"></div>
                <form id="registerForm">
                    <div class="form-floating mb-3">
                        <input type="text" class="form-control" id="reg_nome" placeholder="Nome" required>
                        <label for="reg_nome" class="fw-bold">Nome</label>
                    </div>
                    <div class="form-floating mb-3">
                        <input type="email" class="form-control" id="reg_email" placeholder="Email" required>
                        <label for="reg_email" class="fw-bold">Email</label>
                    </div>
                    <div class="form-floating mb-3" id="register-password-container" style="position: relative;">
                        <input type="password" class="form-control" id="reg_password" placeholder="Palavra-passe" required>
                        <label for="reg_password" class="fw-bold">Palavra-passe</label>
                        <div id="password-feedback" class="mt-2 small">
                            <div class="password-requirement" data-requirement="length">
                                <i class="fas fa-times text-danger"></i> Mínimo de 8 caracteres
                            </div>
                            <div class="password-requirement" data-requirement="numbers">
                                <i class="fas fa-times text-danger"></i> Pelo menos 2 números
                            </div>
                            <div class="password-requirement" data-requirement="special">
                                <i class="fas fa-times text-danger"></i> Pelo menos 1 caractere especial
                            </div>
                            <div class="password-requirement" data-requirement="uppercase">
                                <i class="fas fa-times text-danger"></i> Pelo menos 1 letra maiúscula
                            </div>
                            <div class="password-requirement" data-requirement="lowercase">
                                <i class="fas fa-times text-danger"></i> Pelo menos 1 letra minúscula
                            </div>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary w-100 fw-bold">Registar</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="js/password-validation.js"></script> 