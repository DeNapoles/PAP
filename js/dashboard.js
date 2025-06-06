// Função para mostrar alertas
function showAlert(message, type) {
    // Limitar o tamanho da mensagem e remover detalhes técnicos
    let cleanMessage = message;
    if (message.includes('Detalhes:')) {
        cleanMessage = message.split('Detalhes:')[0].trim();
    }
    if (cleanMessage.length > 200) {
        cleanMessage = cleanMessage.substring(0, 200) + '...';
    }
    
    const alertContainer = document.createElement('div');
    // Procurar por um container específico para alertas ou usar a área principal
    const container = document.querySelector('.main-wrapper') || document.getElementById('alert-container') || document.body;
    alertContainer.innerHTML = `
        <div class="alert alert-${type} alert-dismissible fade show mt-2" role="alert" style="position: fixed; top: 20px; right: 20px; z-index: 9999; max-width: 400px;">
            ${cleanMessage}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    `;
    container.appendChild(alertContainer);
    setTimeout(() => { 
        if (alertContainer.parentNode) {
            alertContainer.remove(); 
        }
    }, 4000);
}

// Função para mostrar/esconder seções
window.showSection = function(sectionId, event) {
    if (event) { event.preventDefault(); }
    document.querySelectorAll('.content-section').forEach(section => { section.style.display = 'none'; });
    const targetSection = document.getElementById(sectionId);
    if (targetSection) { 
        targetSection.style.display = 'block'; 
        
        // Se for a seção de utilizadores, carregar os dados
        if (sectionId === 'users-section') {
            console.log('📋 Carregando seção de utilizadores...');
            setTimeout(() => {
                window.loadUsers(1, '');
            }, 100);
        }
    }
};

// Variáveis globais para paginação e busca
let currentPage = 1;
let currentSearch = '';

window.loadUsers = function(page = 1, search = '') {
    currentPage = page; currentSearch = search;
    console.log(`🔄 Loading users - Page: ${currentPage}, Search: ${currentSearch}`);
    
    // Mostrar loading na tabela
    const usersTableBody = document.getElementById('usersTableBody');
    if (usersTableBody) {
        usersTableBody.innerHTML = '<tr><td colspan="5" class="text-center py-4"><div class="spinner-border spinner-border-sm me-2" role="status"></div>Carregando utilizadores...</td></tr>';
    }
    
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
            console.log('✅ Users data received:', data);
            const usersTableBody = document.getElementById('usersTableBody');
            const usersPagination = document.getElementById('usersPagination');
            if (data.success) {
                if (usersTableBody) usersTableBody.innerHTML = data.html;
                if (usersPagination) usersPagination.innerHTML = data.pagination;
                
                // Primeiro anexar os eventos de paginação
                setupUsersPaginationEvents();
                
                // Depois anexar os eventos dos botões de ação
                setTimeout(() => {
                    attachUserActionListeners();
                }, 50);
                
                console.log('✅ Tabela de utilizadores atualizada com sucesso');
            } else {
                showAlert('Erro ao carregar usuários: ' + data.message, 'danger');
                if (usersTableBody) usersTableBody.innerHTML = `<tr><td colspan="5" class="text-center">${data.message}</td></tr>`;
                if (usersPagination) usersPagination.innerHTML = '';
            }
        })
        .catch(error => {
            console.error('❌ Erro no fetch de usuários:', error);
            showAlert('Erro ao carregar usuários. Por favor, tente novamente. Detalhes: ' + error.message, 'danger');
            const usersTableBody = document.getElementById('usersTableBody');
            if (usersTableBody) usersTableBody.innerHTML = `<tr><td colspan="5" class="text-center">Erro ao carregar utilizadores.</td></tr>`;
            const usersPagination = document.getElementById('usersPagination');
            if (usersPagination) usersPagination.innerHTML = '';
        });
};

function setupUsersPaginationEvents() {
    // Event listeners para paginação
    document.querySelectorAll('#usersPagination .page-link').forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            const page = this.getAttribute('data-page');
            if (page) {
                window.loadUsers(parseInt(page), currentSearch);
            }
        });
    });
    console.log('✅ Event listeners de paginação anexados');
}

function attachUserActionListeners() {
    console.log('🔗 Configurando listeners para tipo de utilizador...');
    
    // Event delegation já está configurado para delete e status buttons
    // Apenas precisamos configurar os dropdowns de tipo
    
    // Attach listeners for Tipo select
    const typeSelects = document.querySelectorAll('#usersTableBody .user-type-select');
    console.log(`📋 Encontrados ${typeSelects.length} dropdowns de tipo`);
    
    typeSelects.forEach(select => {
        const userId = select.getAttribute('data-id');
        const originalValue = select.value;
        if (userId) {
            // Remover listeners anteriores clonando o elemento
            const newSelect = select.cloneNode(true);
            newSelect.value = originalValue; // Manter o valor selecionado
            select.parentNode.replaceChild(newSelect, select);
            
            // Adicionar novo listener
            newSelect.addEventListener('change', function() {
                const newType = this.value;
                const userName = this.closest('tr').querySelector('.user-name').textContent;
                
                if (newType === 'Admin') {
                    if (confirm(`Tem certeza que deseja tornar ${userName} um administrador? Administradores têm acesso total.`)) {
                        window.updateUserType(parseInt(userId), newType, this);
                    } else {
                        this.value = originalValue;
                    }
                } else {
                    window.updateUserType(parseInt(userId), newType, this);
                }
            });
        }
    });
    
    console.log('✅ Event listeners configurados para dropdowns de tipo');
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
    console.log(`🚀 INÍCIO - deleteUser chamado para ID: ${id}`);
    console.log(`🔍 Tipo do ID: ${typeof id}, Valor: ${id}, É NaN: ${isNaN(id)}`);
    
    if (!id || isNaN(id)) {
        console.error('❌ ID inválido para delete:', id);
        showAlert('Erro: ID de utilizador inválido', 'danger');
        return;
    }
    
    const confirmDelete = confirm('Tem certeza que deseja excluir este usuário? Esta ação não pode ser desfeita.');
    console.log(`🤔 Confirmação do utilizador: ${confirmDelete}`);
    
    if (confirmDelete) {
        console.log(`✅ Prosseguindo com delete do ID: ${id}`);
        
        // Encontrar o botão específico
        const deleteBtn = document.querySelector(`#usersTableBody .delete-user-btn[data-id="${id}"]`);
        console.log(`🔍 Botão encontrado:`, deleteBtn);
        
        let originalContent = '';
        
        if (deleteBtn) {
            originalContent = deleteBtn.innerHTML;
            deleteBtn.disabled = true;
            deleteBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
            console.log('🔄 Estado de loading aplicado ao botão');
        } else {
            console.warn('⚠️ Botão de delete não encontrado no DOM');
        }
        
        console.log('📡 Iniciando requisição fetch...');
        
        const requestBody = `action=delete&id=${encodeURIComponent(id)}`;
        console.log(`📤 Body da requisição: ${requestBody}`);
        
        fetch('manage_user.php', { 
            method: 'POST', 
            headers: { 
                'Content-Type': 'application/x-www-form-urlencoded',
            }, 
            body: requestBody
        })
        .then(response => { 
            console.log('📡 Resposta recebida:', {
                ok: response.ok,
                status: response.status,
                statusText: response.statusText,
                contentType: response.headers.get("content-type"),
                url: response.url
            });
            
            const contentType = response.headers.get("content-type");
            if (!contentType || !contentType.includes("application/json")) {
                return response.text().then(text => { 
                    console.error('❌ Resposta não é JSON:', text);
                    throw new Error(`Expected JSON, received ${contentType}. Content: ${text.substring(0, 500)}`); 
                });
            }
            return response.json();
        })
        .then(data => {
            console.log('📊 JSON de resposta recebido:', data);
            
            // Restaurar botão independentemente do resultado
            if (deleteBtn && deleteBtn.parentNode) {
                deleteBtn.disabled = false;
                deleteBtn.innerHTML = originalContent;
                console.log('🔄 Estado original do botão restaurado');
            }
            
            if (data && data.success === true) {
                console.log('✅ Delete bem-sucedido!');
                
                const successMessage = data.message || 'Utilizador excluído com sucesso!';
                showAlert(successMessage, 'success');
                
                // Encontrar e remover a linha da tabela
                const userRow = document.querySelector(`tr[data-user-id="${id}"]`);
                console.log(`🔍 Linha do utilizador encontrada:`, userRow);
                
                if (userRow) {
                    console.log('🎭 Aplicando animação de remoção...');
                    userRow.classList.add('removing');
                    
                    setTimeout(() => {
                        if (userRow.parentNode) {
                            userRow.remove();
                            console.log('❌ Linha removida da tabela');
                        }
                        
                        // Recarregar a tabela após pequeno delay
                        console.log('🔄 Iniciando recarga da tabela...');
                        setTimeout(() => {
                            window.loadUsers(currentPage, currentSearch);
                        }, 100);
                        
                    }, 400);
                } else {
                    console.warn('⚠️ Linha do utilizador não encontrada, recarregando tabela imediatamente');
                    window.loadUsers(currentPage, currentSearch);
                }
                
            } else { 
                console.error('❌ Erro no delete:', data);
                const errorMsg = (data && data.message) ? data.message : 'Erro desconhecido ao excluir usuário';
                showAlert(errorMsg, 'danger'); 
            }
        })
        .catch(error => {
            console.error('❌ Erro na requisição fetch:', error);
            
            // Restaurar botão em caso de erro
            if (deleteBtn && deleteBtn.parentNode) {
                deleteBtn.disabled = false;
                deleteBtn.innerHTML = originalContent;
                console.log('🔄 Estado do botão restaurado após erro');
            }
            
            showAlert('Erro ao excluir usuário. Por favor, tente novamente. Detalhes: ' + error.message, 'danger');
        });
    } else {
        console.log('❌ Delete cancelado pelo utilizador');
    }
    
    console.log(`🏁 FIM - deleteUser para ID: ${id}`);
};

// Função updateUserStatus removida - agora usa toggleUserStatus exclusivamente

// Função updateUserStatusBtn removida - agora usa toggleUserStatus com onclick inline

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

// Variável para prevenir chamadas simultâneas
window._statusUpdateInProgress = false;

// Função global para alternar status (disponível imediatamente)
window.toggleUserStatus = function(userId, currentStatus, element) {
    // Prevenir múltiplas chamadas simultâneas
    if (window._statusUpdateInProgress) {
        console.log('🛑 Update já em progresso, ignorando clique');
        return;
    }
    
    window._statusUpdateInProgress = true;
    
    console.log('🔥 Alterando status do utilizador', userId, 'de', currentStatus);
    
    const newStatus = currentStatus === 'Ativo' ? 'Inativo' : 'Ativo';
    
    // Guardar estado original para reversão em caso de erro
    const originalState = {
        text: element.textContent,
        className: element.className,
        dataStatus: element.getAttribute('data-current-status')
    };
    
    // Aplicar feedback visual imediato
    element.style.opacity = '0.6';
    element.disabled = true;
    element.textContent = 'Processando...';
    
    // Fazer a requisição
    fetch('manage_user.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: `action=update_status&id=${userId}&status=${encodeURIComponent(newStatus)}`
    })
    .then(response => response.json())
    .then(data => {
        window._statusUpdateInProgress = false;
        
        if (data && data.success) {
            console.log('✅ Status atualizado com sucesso');
            
            // Mostrar mensagem de sucesso
            if (typeof showAlert === 'function') {
                showAlert(data.message, 'success');
            }
            
            // Recarregar a tabela para mostrar o novo estado
            if (typeof loadUsers === 'function') {
                loadUsers(currentPage, currentSearch);
            } else {
                // Fallback: recarregar página
                location.reload();
            }
        } else {
            console.log('❌ Erro:', data ? data.message : 'Resposta inválida');
            
            // Reverter botão ao estado original
            element.textContent = originalState.text;
            element.className = originalState.className;
            element.setAttribute('data-current-status', originalState.dataStatus);
            element.style.opacity = '1';
            element.disabled = false;
            
            if (typeof showAlert === 'function') {
                showAlert(data ? data.message : 'Erro na comunicação com o servidor', 'danger');
            }
        }
    })
    .catch(error => {
        console.error('🚨 Erro na requisição:', error);
        
        window._statusUpdateInProgress = false;
        
        // Reverter botão ao estado original
        element.textContent = originalState.text;
        element.className = originalState.className;
        element.setAttribute('data-current-status', originalState.dataStatus);
        element.style.opacity = '1';
        element.disabled = false;
        
        if (typeof showAlert === 'function') {
            showAlert('Erro de conexão: ' + error.message, 'danger');
        }
    });
};

console.log('✅ Função toggleUserStatus definida globalmente!');

// Teste imediato da função
console.log('🧪 Testando função:', typeof window.toggleUserStatus);

document.addEventListener('DOMContentLoaded', function() {
    console.log('📋 Dashboard JS DOMContentLoaded executado');
    console.log('🧪 Função toggleUserStatus disponível:', typeof window.toggleUserStatus);
    console.log('🧪 Função deleteUser disponível:', typeof window.deleteUser);
    console.log('🧪 Função loadUsers disponível:', typeof window.loadUsers);
    
    // Event delegation para botões de delete - funciona mesmo quando tabela é recriada
    document.addEventListener('click', function(e) {
        if (e.target.closest('.delete-user-btn')) {
            e.preventDefault();
            e.stopPropagation();
            
            const button = e.target.closest('.delete-user-btn');
            const userId = button.getAttribute('data-id');
            
            if (userId) {
                console.log(`🗑️ Event delegation captou click no delete para userId: ${userId}`);
                window.deleteUser(parseInt(userId));
            } else {
                console.warn('⚠️ Botão delete sem data-id clicado');
            }
        }
    });
    
    // Event delegation para botões de status toggle
    document.addEventListener('click', function(e) {
        if (e.target.closest('.status-toggle-btn')) {
            e.preventDefault();
            e.stopPropagation();
            
            const button = e.target.closest('.status-toggle-btn');
            const userId = button.getAttribute('data-user-id');
            const currentStatus = button.getAttribute('data-current-status');
            
            if (userId && currentStatus) {
                console.log(`🔄 Event delegation captou click no status toggle para userId: ${userId}`);
                window.toggleUserStatus(parseInt(userId), currentStatus, button);
            }
        }
    });
    
    console.log('✅ Event delegation configurado para botões de ação');
    
    // Inicializa os ícones do Feather
    if (typeof feather !== 'undefined') {
        feather.replace();
    }
    
    // Event listeners para elementos que existem sempre
    const saveUserBtn = document.getElementById('saveUserBtn');
    if (saveUserBtn) { saveUserBtn.addEventListener('click', saveUser); }

    // Adiciona event listeners para os links do menu principal (fora dos submenus) se eles também usarem data-section-id
    const menuLinks = document.querySelectorAll('.navbar-nav .nav-link[data-section-id]:not(.dropdown-item)');
    menuLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            const sectionId = this.getAttribute('data-section-id');
            if (sectionId) { showSection(sectionId, e); }
        });
    });

    // Event listeners para links dos submenus
    const subMenuLinks = document.querySelectorAll('.dropdown-item[data-section-id]');
    subMenuLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            const sectionId = this.getAttribute('data-section-id');
            if (sectionId) { showSection(sectionId, e); }
        });
    });

    // Mostrar a seção inicial com base no hash da URL
    const currentHash = window.location.hash.substring(1);
    const initialSection = currentHash || 'inicio-section';
    if (document.getElementById(initialSection)) {
        showSection(initialSection);
    } else {
        showSection('inicio-section');
    }

    // Configurações de busca para utilizadores
    const userSearchInput = document.getElementById('userSearch');
    if (userSearchInput) {
        userSearchInput.addEventListener('input', function() {
            const search = this.value;
            window.loadUsers(1, search);
        });
        
        userSearchInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                const search = this.value;
                window.loadUsers(1, search);
            }
        });
    }
});

// Remove a função loadContent antiga se não for mais usada
/*
function loadContent(section) { ... }
*/
// Remove ou comenta a função loadContent se não for mais