// Função para mostrar alertas
function showAlert(message, type) {
    const alertContainer = document.createElement('div');
    const container = document.getElementById('alert-container') || document.body;
    alertContainer.innerHTML = `
        <div class="alert alert-${type} alert-dismissible fade show mt-2" role="alert">
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    `;
    container.appendChild(alertContainer);
    setTimeout(() => { alertContainer.remove(); }, 5000);
}

// Função para mostrar/esconder seções
function showSection(sectionId, event) {
    if (event) { event.preventDefault(); }
    document.querySelectorAll('.content-section').forEach(section => { section.style.display = 'none'; });
    const selectedSection = document.getElementById(sectionId);
    if (selectedSection) {
        selectedSection.style.display = 'block';
        console.log('Seção mostrada:', sectionId);
        switch(sectionId) {
            case 'posts-section': loadPostsPage(1); break;
            case 'users-section': loadUsers(); break;
            case 'user-logs-section': loadUserLogs(); break;
        }
    } else { console.error('Seção não encontrada:', sectionId); }
}

// Funções para gerenciar usuários
let currentPage = 1;
let currentSearch = '';

function loadUsers(page = 1, search = '') {
    currentPage = page; currentSearch = search;
    console.log(`Loading users - Page: ${currentPage}, Search: ${currentSearch}`);
    fetch(`get_users.php?page=${page}&search=${encodeURIComponent(search)}`)
        .then(response => { 
            // Check if response is JSON
            const contentType = response.headers.get("content-type");
            if (!contentType || !contentType.includes("application/json")) {
                return response.text().then(text => { throw new Error(`Expected JSON, received ${contentType}. Content: ${text}`); });
            }
            return response.json();
        })
        .then(data => {
            console.log('Users data received:', data);
            const usersTableBody = document.getElementById('usersTableBody');
            const usersPagination = document.getElementById('usersPagination');
            if (data.success) {
                if (usersTableBody) usersTableBody.innerHTML = data.html;
                if (usersPagination) usersPagination.innerHTML = data.pagination;
                setupUsersPaginationEvents();
                 // Attach event listeners to Edit, Delete, Tipo and Status buttons AFTER loading HTML
                attachUserActionListeners();
            } else {
                showAlert('Erro ao carregar usuários: ' + data.message, 'danger');
                if (usersTableBody) usersTableBody.innerHTML = `<tr><td colspan="6" class="text-center">${data.message}</td></tr>`;
                if (usersPagination) usersPagination.innerHTML = '';
            }
        })
        .catch(error => {
            console.error('Erro no fetch de usuários:', error);
            showAlert('Erro ao carregar usuários. Por favor, tente novamente. Detalhes: ' + error.message, 'danger');
            const usersTableBody = document.getElementById('usersTableBody');
            if (usersTableBody) usersTableBody.innerHTML = `<tr><td colspan="6" class="text-center">Erro ao carregar utilizadores.</td></tr>`;
            const usersPagination = document.getElementById('usersPagination');
            if (usersPagination) usersPagination.innerHTML = '';
        });
}

function setupUsersPaginationEvents() {
    // Remove previous listeners to avoid duplicates
    document.querySelectorAll('#usersPagination .page-link').forEach(oldLink => {
        const newLink = oldLink.cloneNode(true);
        oldLink.parentNode.replaceChild(newLink, oldLink);
    });

    document.querySelectorAll('#usersPagination .page-link').forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            const page = this.getAttribute('data-page');
            if (page) loadUsers(parseInt(page), currentSearch);
        });
    });
}

function attachUserActionListeners() {
    console.log('Anexando event listeners para ações de utilizador...');
    // Attach listeners for Edit buttons
    document.querySelectorAll('#usersTableBody .edit-user-btn').forEach(button => {
        const userId = button.getAttribute('data-id');
        if (userId) {
             // Remove any existing inline onclick (shouldn't be there if PHP is correct, but as safeguard)
            button.removeAttribute('onclick'); 
            button.addEventListener('click', () => editUser(parseInt(userId)));
        }
    });

    // Attach listeners for Delete buttons
    document.querySelectorAll('#usersTableBody .delete-user-btn').forEach(button => {
         const userId = button.getAttribute('data-id');
         if (userId) {
            // Remove any existing inline onclick
            button.removeAttribute('onclick'); 
            button.addEventListener('click', () => deleteUser(parseInt(userId)));
        }
    });

    // Attach listeners for Estado toggle button (update to target the button class)
    const statusButtons = document.querySelectorAll('#usersTableBody .btn-estado-toggle');
    console.log('Encontrados ' + statusButtons.length + ' botões de estado.');
    statusButtons.forEach(button => {
         const userId = button.getAttribute('data-id');
         if (userId) {
            button.addEventListener('click', function() {
                const currentEstado = this.getAttribute('data-estado');
                const newEstado = currentEstado === 'Ativo' ? 'Inativo' : 'Ativo';
                updateUserStatusBtn(parseInt(userId), newEstado, this);
            });
         }
    });

    // Attach listeners for Tipo select
    const typeSelects = document.querySelectorAll('#usersTableBody .user-type-select');
    console.log('Encontrados ' + typeSelects.length + ' dropdowns de tipo.');
    typeSelects.forEach(select => {
        const userId = select.getAttribute('data-id');
        const originalValue = select.value; // Store the original value
        if (userId) {
            select.addEventListener('change', function() {
                const newType = this.value;
                const userName = this.closest('tr').querySelector('.user-name').textContent; // Get user name for confirmation
                
                if (newType === 'Admin') {
                    // Confirm if changing to Admin
                    if (confirm(`Tem certeza que deseja tornar ${userName} um administrador? Administradores têm acesso total.`)) {
                        updateUserType(parseInt(userId), newType, this);
                    } else {
                        // Revert to original value if cancelled
                        this.value = originalValue;
                    }
                } else {
                     // No confirmation needed for other types
                    updateUserType(parseInt(userId), newType, this);
                }
            });
        }
    });
}

function loadUserLogs(page = 1, search = '') {
    console.log(`Loading user logs - Page: ${page}, Search: ${search}`);
    fetch(`get_user_logs.php?page=${page}&search=${encodeURIComponent(search)}`)
        .then(response => { 
             // Check if response is JSON
            const contentType = response.headers.get("content-type");
            if (!contentType || !contentType.includes("application/json")) {
                return response.text().then(text => { throw new Error(`Expected JSON, received ${contentType}. Content: ${text}`); });
            }
            return response.json();
        })
        .then(data => {
            console.log('User logs data received:', data);
            const logsTableBody = document.querySelector('#user-logs-section #logsTableBody');
            const logsPagination = document.querySelector('#user-logs-section #logsPagination');
            if (data.success) {
                if (logsTableBody) logsTableBody.innerHTML = data.html;
                if (logsPagination) logsPagination.innerHTML = data.pagination;
                setupLogsPaginationEvents();
            } else {
                showAlert('Erro ao carregar logs: ' + data.message, 'danger');
                if (logsTableBody) logsTableBody.innerHTML = `<tr><td colspan="5" class="text-center">${data.message}</td></tr>`;
                if (logsPagination) logsPagination.innerHTML = '';
            }
        })
        .catch(error => {
            console.error('Erro no fetch de logs:', error);
            showAlert('Erro ao carregar logs. Por favor, tente novamente. Detalhes: ' + error.message, 'danger');
            const logsTableBody = document.querySelector('#user-logs-section #logsTableBody');
            if (logsTableBody) logsTableBody.innerHTML = `<tr><td colspan="5" class="text-center">Erro ao carregar histórico de alterações.</td></tr>`;
            const logsPagination = document.querySelector('#user-logs-section #logsPagination');
            if (logsPagination) logsPagination.innerHTML = '';
        });
}

function setupLogsPaginationEvents() {
     // Remove previous listeners to avoid duplicates
    document.querySelectorAll('#logsPagination .page-link').forEach(oldLink => {
        const newLink = oldLink.cloneNode(true);
        oldLink.parentNode.replaceChild(newLink, oldLink);
    });

    document.querySelectorAll('#logsPagination .page-link').forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            const page = this.getAttribute('data-page');
            if (page) loadUserLogs(parseInt(page), currentSearch);
        });
    });
}

window.showNewUserForm = function() {
    console.log('Showing new user form');
    const userModal = document.getElementById('user-modal');
    if (userModal) {
        const form = userModal.querySelector('#user-form');
        if (form) form.reset();
        const userIdInput = userModal.querySelector('#user-id');
        if (userIdInput) userIdInput.value = '';
        const modalTitle = userModal.querySelector('#user-modal-title');
        if (modalTitle) modalTitle.textContent = 'Novo Usuário';
        const senhaInput = userModal.querySelector('#user-senha');
        if (senhaInput) senhaInput.required = true;
        new bootstrap.Modal(userModal).show();
    }
};

window.editUser = function(id) {
    console.log('Editing user with ID:', id);
    fetch(`get_users.php?id=${id}`)
        .then(response => { 
             // Check if response is JSON
            const contentType = response.headers.get("content-type");
            if (!contentType || !contentType.includes("application/json")) {
                return response.text().then(text => { throw new Error(`Expected JSON, received ${contentType}. Content: ${text}`); });
            }
            return response.json();
        })
        .then(data => {
            console.log('User data for editing:', data);
            if (data.success && data.user) {
                const user = data.user;
                const userModal = document.getElementById('user-modal');
                if (userModal) {
                    const form = userModal.querySelector('#user-form');
                    if (form) {
                        form.querySelector('#user-id').value = user.ID_Utilizador;
                        form.querySelector('#user-nome').value = user.Nome;
                        form.querySelector('#user-email').value = user.Email;
                        form.querySelector('#user-tipo').value = user.Tipo_Utilizador;
                        const senhaInput = form.querySelector('#user-senha');
                        if (senhaInput) { senhaInput.value = ''; senhaInput.required = false; }
                        userModal.querySelector('#user-modal-title').textContent = 'Editar Usuário';
                        new bootstrap.Modal(userModal).show();
                    }
                }
            } else { showAlert('Erro ao carregar usuário para edição: ' + (data.message || 'Usuário não encontrado.'), 'danger'); }
        })
        .catch(error => {
            console.error('Erro no fetch para editar usuário:', error);
            showAlert('Erro ao carregar usuário para edição. Por favor, tente novamente. Detalhes: ' + error.message, 'danger');
        });
};

window.saveUser = function() {
    console.log('Saving user...');
    const form = document.getElementById('user-form');
    if (!form) { console.error('User form not found'); showAlert('Erro interno: Formulário de usuário não encontrado.', 'danger'); return; }
    const formData = new FormData(form);
    const userId = document.getElementById('user-id').value;
    formData.append('action', userId ? 'update' : 'create');
    fetch('manage_user.php', { method: 'POST', body: formData })
        .then(response => { 
             // Check if response is JSON
            const contentType = response.headers.get("content-type");
            if (!contentType || !contentType.includes("application/json")) {
                return response.text().then(text => { throw new Error(`Expected JSON, received ${contentType}. Content: ${text}`); });
            }
            return response.json();
        })
        .then(data => {
            console.log('Save user response:', data);
            if (data.success) {
                const userModal = document.getElementById('user-modal');
                if (userModal) bootstrap.Modal.getInstance(userModal)?.hide();
                showAlert(data.message, 'success');
                loadUsers(currentPage, currentSearch);
            } else { showAlert(data.message, 'danger'); }
        })
        .catch(error => {
            console.error('Erro no fetch para salvar usuário:', error);
            showAlert('Erro ao salvar usuário. Por favor, tente novamente. Detalhes: ' + error.message, 'danger');
        });
};

window.deleteUser = function(id) {
    console.log('Deleting user with ID:', id);
    if (confirm('Tem certeza que deseja excluir este usuário? Esta ação não pode ser desfeita.')) {
        fetch('manage_user.php', { method: 'POST', headers: { 'Content-Type': 'application/x-www-form-urlencoded', }, body: 'action=delete&id=' + id })
            .then(response => { 
                 // Check if response is JSON
                const contentType = response.headers.get("content-type");
                if (!contentType || !contentType.includes("application/json")) {
                    return response.text().then(text => { throw new Error(`Expected JSON, received ${contentType}. Content: ${text}`); });
                }
                return response.json();
            })
            .then(data => {
                console.log('Delete user response:', data);
                if (data.success) {
                    showAlert(data.message, 'success');
                    loadUsers(currentPage, currentSearch);
                } else { showAlert(data.message, 'danger'); }
            })
            .catch(error => {
                console.error('Erro no fetch para apagar usuário:', error);
                showAlert('Erro ao excluir usuário. Por favor, tente novamente. Detalhes: ' + error.message, 'danger');
            });
    }
};

window.updateUserStatus = function(id, status) {
    fetch('manage_user.php', { method: 'POST', headers: { 'Content-Type': 'application/x-www-form-urlencoded', }, body: 'action=update_status&id=' + id + '&status=' + status })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showAlert(data.message, 'success');
                // Atualiza visualmente o switch e badge imediatamente
                const toggles = document.querySelectorAll('#usersTableBody .status-toggle');
                toggles.forEach(toggle => {
                    if (parseInt(toggle.getAttribute('data-id')) === id) {
                        toggle.checked = (status === 'Ativo');
                        // Atualiza badge
                        const badge = toggle.parentNode.querySelector('.badge');
                        if (badge) {
                            badge.textContent = status;
                            badge.className = 'badge ' + (status === 'Ativo' ? 'bg-success-subtle text-success' : 'bg-secondary-subtle text-secondary');
                        }
                    }
                });
            } else {
                showAlert(data.message, 'danger');
            }
        })
        .catch(error => {
            showAlert('Erro ao atualizar status. Por favor, tente novamente. Detalhes: ' + error.message, 'danger');
            // Reverter o switch se houver erro
            const toggles = document.querySelectorAll('#usersTableBody .status-toggle');
            toggles.forEach(toggle => {
                if (parseInt(toggle.getAttribute('data-id')) === id) {
                    toggle.checked = !(status === 'Ativo');
                }
            });
        });
};

// Função para atualizar o estado via botão
window.updateUserStatusBtn = function(id, status, btn) {
    fetch('manage_user.php', { method: 'POST', headers: { 'Content-Type': 'application/x-www-form-urlencoded', }, body: 'action=update_status&id=' + id + '&status=' + status })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showAlert(data.message, 'success');
                // Não atualizar o botão localmente, apenas recarregar a tabela com o valor real da BD
                const searchInput = document.getElementById('userSearchInput');
                const searchValue = searchInput ? searchInput.value : '';
                loadUsers(1, searchValue);
            } else {
                showAlert(data.message, 'danger');
            }
        })
        .catch(error => {
            showAlert('Erro ao atualizar status. Por favor, tente novamente. Detalhes: ' + error.message, 'danger');
        });
};

// Função para atualizar o tipo de utilizador
window.updateUserType = function(id, tipo, selectElement) {
    fetch('manage_user.php', { method: 'POST', headers: { 'Content-Type': 'application/x-www-form-urlencoded', }, body: 'action=update_type&id=' + id + '&tipo=' + tipo })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showAlert(data.message, 'success');
                // O select já tem o valor correto devido ao event listener
                // Nenhuma atualização visual extra é necessária para o select em si
            } else {
                showAlert(data.message, 'danger');
                // Reverter o select se houver erro (opcional, dependendo da UX desejada)
                // selectElement.value = selectElement.getAttribute('data-original-value'); // Precisaria armazenar original-value
            }
        })
        .catch(error => {
            showAlert('Erro ao atualizar tipo de utilizador. Por favor, tente novamente. Detalhes: ' + error.message, 'danger');
            // Reverter o select se houver erro
             // selectElement.value = selectElement.getAttribute('data-original-value'); // Precisaria armazenar original-value
        });
};

document.addEventListener('DOMContentLoaded', function() {
    console.log('Dashboard JS carregado');
    
    // Inicializa os ícones do Feather
    if (typeof feather !== 'undefined') { feather.replace(); }

    // Adiciona event listeners para os botões de categoria que expandem/contraem submenus
    document.querySelectorAll('.show-cat-btn').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            const subMenu = this.nextElementSibling;
            const arrow = this.querySelector('.icon.arrow-down');
            if (subMenu.style.display === 'block') {
                subMenu.style.display = 'none';
                if (arrow) { arrow.style.transform = 'rotate(0deg)'; }
            } else {
                document.querySelectorAll('.cat-sub-menu').forEach(menu => { menu.style.display = 'none'; });
                document.querySelectorAll('.show-cat-btn .icon.arrow-down').forEach(arr => { arr.style.transform = 'rotate(0deg)'; });
                subMenu.style.display = 'block';
                if (arrow) { arrow.style.transform = 'rotate(180deg)'; }
            }
        });
    });

    // Adiciona event listeners aos links do menu usando data-section-id
    document.querySelectorAll('.cat-sub-menu a[data-section-id]').forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            const sectionId = this.getAttribute('data-section-id');
            if (sectionId) {
                showSection(sectionId, e);
                document.querySelectorAll('.cat-sub-menu a').forEach(a => a.classList.remove('active'));
                this.classList.add('active');
            }
        });
    });

    // Mostra a seção de boas-vindas por padrão ao carregar a página
    showSection('welcome-section');

    // --- Event Listeners para a seção de Usuários ---
    const userSearchInput = document.getElementById('userSearchInput');
    if (userSearchInput) { userSearchInput.addEventListener('input', function() { loadUsers(1, this.value); }); }
    
    // Corrigido ID do botão Novo Utilizador
    const addNewUserBtn = document.getElementById('addNewUserBtn'); 
    if (addNewUserBtn) { addNewUserBtn.addEventListener('click', showNewUserForm); }
    
    const saveUserBtn = document.getElementById('saveUserBtn');
    if (saveUserBtn) { saveUserBtn.addEventListener('click', saveUser); }

    // --- Event Listeners para a seção de Histórico de Alterações ---
    // Corrigido ID do input de pesquisa de logs
    const logSearchInput = document.getElementById('logSearch'); 
    if (logSearchInput) { logSearchInput.addEventListener('input', function() { loadUserLogs(1, this.value); }); }

    // Adiciona event listeners para os links do menu principal (fora dos submenus) se eles também usarem data-section-id
    document.querySelectorAll('.sidebar-body-menu > li > a[data-section-id]').forEach(link => {
         link.addEventListener('click', function(e) {
            e.preventDefault();
            const sectionId = this.getAttribute('data-section-id');
             if (sectionId) {
                showSection(sectionId, e);
                document.querySelectorAll('.sidebar-body-menu a').forEach(a => a.classList.remove('active'));
                this.classList.add('active');
             }
         });
    });

});

// Remove a função loadContent antiga se não for mais usada
/*
function loadContent(section) { ... }
*/
// Remove ou comenta a função loadContent se não for mais