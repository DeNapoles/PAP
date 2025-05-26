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
                    </div>
                    <div class="form-floating mb-3">
                        <select class="form-select" id="reg_tipo" required>
                            <option value="">Selecione o tipo de utilizador</option>
                            <option value="Aluno">Aluno</option>
                            <option value="Professor">Professor</option>
                            <option value="Encarregado de Educação">Encarregado de Educação</option>
                            <option value="Admin">Admin</option>
                        </select>
                        <label for="reg_tipo" class="fw-bold">Tipo de Utilizador</label>
                    </div>
                    <button type="submit" class="btn btn-primary w-100 fw-bold">Registar</button>
                </form>
            </div>
        </div>
    </div>
</div> 