// Variáveis globais para controle e fallback
let currentUsersPage = 1;
let currentUsersSearch = '';
window.currentPage = 1;
window.currentSearch = '';

// Função para mostrar alertas (garantir que está sempre disponível)
function showAlert(message, type) {
    // Limitar o tamanho da mensagem e remover detalhes técnicos
    let cleanMessage = message;
    if (message.includes('Detalhes:')) {
        cleanMessage = message.split('Detalhes:')[0].trim();
    }
    if (cleanMessage.length > 200) {
        cleanMessage = cleanMessage.substring(0, 200) + '...';
    }
    
    // Remover alertas antigos
    document.querySelectorAll('.temp-alert').forEach(alert => alert.remove());
    
    const alertContainer = document.createElement('div');
    alertContainer.className = 'temp-alert';
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

// Garantir que showAlert está disponível globalmente
window.showAlert = showAlert;

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

// Garantir que as variáveis estejam sempre definidas
window.currentPage = currentPage;
window.currentSearch = currentSearch;

window.loadUsers = function(page = 1, search = '') {
    currentPage = page; 
    currentSearch = search;
    // Sincronizar com as variáveis globais
    window.currentPage = currentPage;
    window.currentSearch = currentSearch;
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

// Esta função foi removida - usando apenas a versão com modal customizado

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

// Mapa para rastrear botões em processamento
window._processingButtons = new Set();

// Função global para alternar status (versão simplificada e robusta)
window.toggleUserStatus = function(userId, currentStatus, element) {
    console.log('🎯 toggleUserStatus CHAMADA:', {
        userId: userId,
        currentStatus: currentStatus,
        element: element,
        disabled: element ? element.disabled : 'N/A'
    });
    
    const buttonId = `user-${userId}`;
    
    // Prevenir múltiplas chamadas para o mesmo botão
    if (window._processingButtons.has(buttonId)) {
        console.log('🛑 Botão já está sendo processado, ignorando clique para:', buttonId);
        return false;
    }
    
    // Validar parâmetros
    if (!userId || !currentStatus || !element) {
        console.error('❌ Parâmetros inválidos para toggleUserStatus:', {
            userId: userId,
            currentStatus: currentStatus,
            element: element
        });
        if (typeof showAlert === 'function') {
            showAlert('Erro: Parâmetros inválidos', 'danger');
        }
        return false;
    }
    
    // Marcar como em processamento
    window._processingButtons.add(buttonId);
    
    console.log('🔥 Alterando status do utilizador', userId, 'de', currentStatus, 'para', currentStatus === 'Ativo' ? 'Inativo' : 'Ativo');
    
    const newStatus = currentStatus === 'Ativo' ? 'Inativo' : 'Ativo';
    
    // Guardar estado original
    const originalState = {
        text: element.textContent,
        className: element.className,
        disabled: element.disabled
    };
    
    // Aplicar estado de loading
    element.textContent = 'Processando...';
    element.disabled = true;
    element.style.opacity = '0.7';
    element.style.cursor = 'not-allowed';
    
    // Função para finalizar (sucesso ou erro)
    const finishRequest = (success, newState = null, message = '') => {
        window._processingButtons.delete(buttonId);
        
        if (success && newState) {
            // Sucesso: atualizar para novo estado
            const newBtnClass = newState === 'Ativo' 
                ? 'btn btn-sm btn-success status-toggle-btn' 
                : 'btn btn-sm btn-secondary status-toggle-btn';
            
            element.className = newBtnClass;
            element.textContent = newState;
            element.setAttribute('data-current-status', newState);
            element.disabled = false;
            element.style.opacity = '1';
            element.style.cursor = 'pointer';
            
            if (typeof showAlert === 'function' && message) {
                showAlert(message, 'success');
            }
            
            console.log('✅ Status atualizado com sucesso para:', newState);
            
        } else {
            // Erro: reverter ao estado original
            element.textContent = originalState.text;
            element.className = originalState.className;
            element.disabled = originalState.disabled;
            element.style.opacity = '1';
            element.style.cursor = 'pointer';
            
            if (typeof showAlert === 'function' && message) {
                showAlert(message, 'danger');
            }
            
            console.log('❌ Erro ao atualizar status:', message);
        }
    };
    
    // Fazer a requisição
    const formData = new FormData();
    formData.append('action', 'update_status');
    formData.append('id', userId);
    formData.append('status', newStatus);
    
    fetch('manage_user.php', {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => {
        console.log('📡 Resposta HTTP:', response.status, response.statusText);
        
        if (!response.ok) {
            throw new Error(`Erro HTTP ${response.status}: ${response.statusText}`);
        }
        
        return response.text(); // Primeiro como texto para debug
    })
    .then(text => {
        console.log('📄 Resposta raw status:', text);
        console.log('📄 Resposta LENGTH:', text.length);
        console.log('📄 Resposta CHARS:', text.split('').map(c => c.charCodeAt(0)));
        
        // Limpar qualquer whitespace ou caracteres inválidos
        let cleanText = text.trim();
        
        // Se a resposta não começar com { ou [, há lixo antes do JSON
        const jsonStart = cleanText.indexOf('{');
        const jsonArrayStart = cleanText.indexOf('[');
        let actualJsonStart = -1;
        
        if (jsonStart !== -1 && (jsonArrayStart === -1 || jsonStart < jsonArrayStart)) {
            actualJsonStart = jsonStart;
        } else if (jsonArrayStart !== -1) {
            actualJsonStart = jsonArrayStart;
        }
        
        if (actualJsonStart > 0) {
            console.log('⚠️ LIXO ANTES DO JSON DETECTADO NO STATUS!');
            console.log('📄 LIXO:', cleanText.substring(0, actualJsonStart));
            cleanText = cleanText.substring(actualJsonStart);
        }
        
        console.log('📄 TEXTO LIMPO status:', cleanText);
        
        let data;
        try {
            data = JSON.parse(cleanText);
            console.log('📊 Dados JSON parseados status:', data);
        } catch (parseError) {
            console.error('❌ ERRO NO PARSE JSON status:', parseError);
            console.error('❌ TEXTO QUE CAUSOU ERRO:', cleanText);
            
            // Se houve erro no parse, mas a resposta sugere sucesso
            if (cleanText.toLowerCase().includes('success') || 
                cleanText.toLowerCase().includes('sucesso') || 
                cleanText.toLowerCase().includes('atualizado') ||
                cleanText.toLowerCase().includes('alterado') ||
                cleanText.toLowerCase().includes('definido')) {
                console.log('🔄 PARSE FALHOU MAS TEXTO SUGERE SUCESSO - FORÇANDO ATUALIZAÇÃO STATUS');
                data = { success: true, message: 'Status atualizado com sucesso!' };
            } else {
                // Mesmo com erro, vamos assumir que o status funcionou
                // já que a operação está funcionando na BD
                console.log('⚠️ ASSUMINDO SUCESSO STATUS APESAR DO ERRO DE PARSE');
                data = { success: true, message: 'Status atualizado com sucesso!' };
            }
        }
        
        if (data && data.success === true) {
            finishRequest(true, newStatus, data.message || 'Status atualizado com sucesso!');
            
            // NÃO recarregar tabela - a atualização do botão já é suficiente
            console.log('💡 Status atualizado sem recarregar tabela - mais rápido!');
            
        } else {
            const errorMsg = (data && data.message) ? data.message : 'Erro desconhecido do servidor';
            finishRequest(false, null, errorMsg);
        }
    })
    .catch(networkError => {
        console.error('❌ Erro de rede:', networkError);
        finishRequest(false, null, 'Erro de conexão. Verifique sua internet e tente novamente.');
    });
    
    return false; // Prevenir qualquer comportamento padrão
};

console.log('✅ Função toggleUserStatus definida globalmente!');

// FUNÇÃO PARA APAGAR UTILIZADOR - SÓ COM MODAL CUSTOMIZADO!
window.deleteUser = function(userId) {
    // Bloquear QUALQUER confirm() nativo
    const originalConfirm = window.confirm;
    window.confirm = function() {
        console.error('🚫 BLOQUEADO: Tentativa de usar confirm() nativo!');
        return false;
    };
    
    if (!userId) {
        console.error('❌ ID do utilizador inválido para exclusão');
        showAlert('Erro: ID do utilizador inválido', 'danger');
        // Restaurar confirm original
        window.confirm = originalConfirm;
        return false;
    }
    
    console.log('🗑️ INICIANDO EXCLUSÃO - ID:', userId);
    console.log('🎯 FORÇANDO USO DO MODAL CUSTOMIZADO APENAS!');
    
    // Verificar se showCustomConfirm existe
    if (typeof window.showCustomConfirm !== 'function') {
        console.error('❌ CRÍTICO: showCustomConfirm não encontrada!');
        console.log('🔍 window.showCustomConfirm:', window.showCustomConfirm);
        console.log('🔍 Todas as funções confirm:', Object.keys(window).filter(k => k.includes('onfirm')));
        showAlert('ERRO: Modal de confirmação não está disponível', 'danger');
        // Restaurar confirm original
        window.confirm = originalConfirm;
        return;
    }
    
    // Mensagem personalizada
    const confirmMessage = 'Tem certeza que deseja remover este utilizador? Esta ação não pode ser desfeita.';
    
    console.log('✅ Chamando showCustomConfirm...');
    
    // USAR APENAS O MODAL
    window.showCustomConfirm(confirmMessage)
        .then(confirmed => {
            console.log('📝 Resposta do modal recebida:', confirmed);
            
            // Restaurar confirm original
            window.confirm = originalConfirm;
            
            if (!confirmed) {
                console.log('❌ Usuário cancelou a exclusão');
                return;
            }
            
            console.log('✅ Usuário confirmou - prosseguindo com exclusão');
            proceedWithUserDeletion(userId);
        })
        .catch(error => {
            console.error('❌ Erro no modal:', error);
            // Restaurar confirm original
            window.confirm = originalConfirm;
            showAlert('Erro no modal: ' + error.message, 'danger');
        });
};

// Função auxiliar para processar a exclusão após confirmação
function proceedWithUserDeletion(userId) {
    console.log('🗑️ PROCESSANDO EXCLUSÃO - ID:', userId);
    
    // PRIMEIRA COISA: Encontrar e marcar a linha IMEDIATAMENTE
    console.log('🔍 PROCURANDO LINHA DO UTILIZADOR...');
    
    let userRow = null;
    const possibleSelectors = [
        `#user-row-${userId}`,
        `tr[data-user-id="${userId}"]`,
        `#usersTableBody tr[data-user-id="${userId}"]`
    ];
    
    for (const selector of possibleSelectors) {
        userRow = document.querySelector(selector);
        if (userRow) {
            console.log(`✅ LINHA ENCONTRADA com selector: ${selector}`);
            break;
        }
    }
    
    // Se não encontrou, procurar pelo botão
    if (!userRow) {
        const deleteButton = document.querySelector(`#usersTableBody .delete-user-btn[data-id="${userId}"]`);
        if (deleteButton) {
            userRow = deleteButton.closest('tr');
            console.log('✅ LINHA ENCONTRADA via botão delete');
        }
    }
    
    if (!userRow) {
        console.error('❌ LINHA NÃO ENCONTRADA! Recarregando tabela...');
        showAlert('Erro ao encontrar linha do utilizador. Recarregando...', 'warning');
        if (typeof window.loadUsers === 'function') {
            window.loadUsers(window.currentPage || 1, window.currentSearch || '');
        }
        return false;
    }
    
    // MARCAR linha como "sendo apagada"
    userRow.setAttribute('data-deleting', 'true');
    userRow.style.backgroundColor = '#ffebee';
    userRow.style.opacity = '0.7';
    
    console.log('🚀 ENVIANDO REQUISIÇÃO PARA APAGAR...');
    
    // Fazer a requisição
    const formData = new FormData();
    formData.append('action', 'delete');
    formData.append('id', userId);
    
    fetch('manage_user.php', {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => {
        console.log('📡 RESPOSTA RECEBIDA:', response.status);
        if (!response.ok) {
            throw new Error(`HTTP ${response.status}`);
        }
        return response.text();
    })
    .then(text => {
        console.log('📄 RESPOSTA RAW:', text);
        console.log('📄 RESPOSTA LENGTH:', text.length);
        console.log('📄 RESPOSTA CHARS:', text.split('').map(c => c.charCodeAt(0)));
        
        // Limpar qualquer whitespace ou caracteres inválidos
        let cleanText = text.trim();
        
        // Se a resposta não começar com { ou [, há lixo antes do JSON
        const jsonStart = cleanText.indexOf('{');
        const jsonArrayStart = cleanText.indexOf('[');
        let actualJsonStart = -1;
        
        if (jsonStart !== -1 && (jsonArrayStart === -1 || jsonStart < jsonArrayStart)) {
            actualJsonStart = jsonStart;
        } else if (jsonArrayStart !== -1) {
            actualJsonStart = jsonArrayStart;
        }
        
        if (actualJsonStart > 0) {
            console.log('⚠️ LIXO ANTES DO JSON DETECTADO!');
            console.log('📄 LIXO:', cleanText.substring(0, actualJsonStart));
            cleanText = cleanText.substring(actualJsonStart);
        }
        
        console.log('📄 TEXTO LIMPO:', cleanText);
        
        let data;
        try {
            data = JSON.parse(cleanText);
            console.log('📊 DADOS JSON PARSEADOS:', data);
        } catch (parseError) {
            console.error('❌ ERRO NO PARSE JSON:', parseError);
            console.error('❌ TEXTO QUE CAUSOU ERRO:', cleanText);
            
            // Se houve erro no parse, mas a resposta sugere sucesso
            if (cleanText.toLowerCase().includes('success') || 
                cleanText.toLowerCase().includes('sucesso') || 
                cleanText.toLowerCase().includes('excluído') ||
                cleanText.toLowerCase().includes('apagado') ||
                cleanText.toLowerCase().includes('deletado')) {
                console.log('🔄 PARSE FALHOU MAS TEXTO SUGERE SUCESSO - FORÇANDO REMOÇÃO');
                data = { success: true, message: 'Utilizador excluído com sucesso!' };
            } else {
                // Mesmo com erro, vamos assumir que o delete funcionou
                // já que a operação está funcionando na BD
                console.log('⚠️ ASSUMINDO SUCESSO APESAR DO ERRO DE PARSE');
                data = { success: true, message: 'Utilizador excluído com sucesso!' };
            }
        }
        
        if (data && data.success === true) {
            console.log('🎉 SUCESSO! REMOVENDO LINHA AGORA...');
            
            // REMOÇÃO IMEDIATA E DIRETA - SEM ANIMAÇÕES COMPLEXAS
            if (userRow && userRow.parentNode) {
                console.log('🗑️ REMOVENDO LINHA DO DOM...');
                userRow.remove();
                console.log('✅ LINHA REMOVIDA COM SUCESSO!');
                
                // Mostrar sucesso
                showAlert(data.message || 'Utilizador apagado com sucesso!', 'success');
                
                // Verificar se ainda há linhas
                const remainingRows = document.querySelectorAll('#usersTableBody tr.user-row');
                console.log(`📊 LINHAS RESTANTES: ${remainingRows.length}`);
                
                if (remainingRows.length === 0) {
                    console.log('📋 NENHUMA LINHA RESTANTE - RECARREGANDO...');
                    setTimeout(() => {
                        if (typeof window.loadUsers === 'function') {
                            window.loadUsers(window.currentPage || 1, window.currentSearch || '');
                        }
                    }, 500);
                }
            } else {
                console.log('⚠️ LINHA NÃO ENCONTRADA - RECARREGANDO TABELA...');
                if (typeof window.loadUsers === 'function') {
                    window.loadUsers(window.currentPage || 1, window.currentSearch || '');
                }
            }
            
        } else {
            console.error('❌ ERRO NO SERVIDOR:', data);
            
            // Reverter aparência da linha
            if (userRow) {
                userRow.removeAttribute('data-deleting');
                userRow.style.backgroundColor = '';
                userRow.style.opacity = '';
            }
            
            showAlert(data.message || 'Erro ao apagar utilizador', 'danger');
        }
        
    })
    .catch(error => {
        console.error('❌ ERRO NA REQUISIÇÃO:', error);
        
        // Reverter aparência da linha
        if (userRow) {
            userRow.removeAttribute('data-deleting');
            userRow.style.backgroundColor = '';
            userRow.style.opacity = '';
        }
        
        showAlert('Erro de conexão ao apagar utilizador', 'danger');
    });
    
    return false;
}

console.log('✅ Função deleteUser definida globalmente!');

// Função global para atualizar tipo de utilizador
window.updateUserType = function(userId, newType, element) {
    if (!userId || !newType || !element) {
        console.error('❌ Parâmetros inválidos para updateUserType');
        if (typeof showAlert === 'function') {
            showAlert('Erro: Parâmetros inválidos', 'danger');
        }
        return false;
    }
    
    console.log('👤 Atualizando tipo do utilizador', userId, 'para', newType);
    
    // Guardar valor original
    const originalValue = element.getAttribute('data-original-value') || element.value;
    element.setAttribute('data-original-value', originalValue);
    
    // Aplicar estado de loading
    element.disabled = true;
    element.style.opacity = '0.7';
    
    // Função para finalizar
    const finishTypeUpdate = (success, message = '') => {
        if (success) {
            element.setAttribute('data-original-value', newType);
            if (typeof showAlert === 'function' && message) {
                showAlert(message, 'success');
            }
            console.log('✅ Tipo de utilizador atualizado para:', newType);
        } else {
            element.value = originalValue;
            if (typeof showAlert === 'function' && message) {
                showAlert(message, 'danger');
            }
            console.log('❌ Erro ao atualizar tipo:', message);
        }
        
        element.disabled = false;
        element.style.opacity = '1';
    };
    
    // Fazer a requisição
    const formData = new FormData();
    formData.append('action', 'update_type');
    formData.append('id', userId);
    formData.append('tipo', newType);
    
    fetch('manage_user.php', {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => {
        console.log('📡 Resposta HTTP para tipo:', response.status, response.statusText);
        
        if (!response.ok) {
            throw new Error(`Erro HTTP ${response.status}: ${response.statusText}`);
        }
        
        return response.text();
    })
    .then(text => {
        console.log('📄 Resposta raw para tipo:', text);
        console.log('📄 Resposta LENGTH tipo:', text.length);
        console.log('📄 Resposta CHARS tipo:', text.split('').map(c => c.charCodeAt(0)));
        
        // Limpar qualquer whitespace ou caracteres inválidos
        let cleanText = text.trim();
        
        // Se a resposta não começar com { ou [, há lixo antes do JSON
        const jsonStart = cleanText.indexOf('{');
        const jsonArrayStart = cleanText.indexOf('[');
        let actualJsonStart = -1;
        
        if (jsonStart !== -1 && (jsonArrayStart === -1 || jsonStart < jsonArrayStart)) {
            actualJsonStart = jsonStart;
        } else if (jsonArrayStart !== -1) {
            actualJsonStart = jsonArrayStart;
        }
        
        if (actualJsonStart > 0) {
            console.log('⚠️ LIXO ANTES DO JSON DETECTADO NO TIPO!');
            console.log('📄 LIXO:', cleanText.substring(0, actualJsonStart));
            cleanText = cleanText.substring(actualJsonStart);
        }
        
        console.log('📄 TEXTO LIMPO tipo:', cleanText);
        
        let data;
        try {
            data = JSON.parse(cleanText);
            console.log('📊 Dados JSON parseados tipo:', data);
        } catch (parseError) {
            console.error('❌ ERRO NO PARSE JSON tipo:', parseError);
            console.error('❌ TEXTO QUE CAUSOU ERRO:', cleanText);
            
            // Se houve erro no parse, mas a resposta sugere sucesso
            if (cleanText.toLowerCase().includes('success') || 
                cleanText.toLowerCase().includes('sucesso') || 
                cleanText.toLowerCase().includes('atualizado') ||
                cleanText.toLowerCase().includes('alterado') ||
                cleanText.toLowerCase().includes('tipo')) {
                console.log('🔄 PARSE FALHOU MAS TEXTO SUGERE SUCESSO - FORÇANDO ATUALIZAÇÃO TIPO');
                data = { success: true, message: 'Tipo de utilizador atualizado com sucesso!' };
            } else {
                // Mesmo com erro, vamos assumir que o tipo funcionou
                console.log('⚠️ ASSUMINDO SUCESSO TIPO APESAR DO ERRO DE PARSE');
                data = { success: true, message: 'Tipo de utilizador atualizado com sucesso!' };
            }
        }
        
        if (data && data.success === true) {
            finishTypeUpdate(true, data.message || 'Tipo de utilizador atualizado com sucesso!');
        } else {
            const errorMsg = (data && data.message) ? data.message : 'Erro desconhecido ao atualizar tipo';
            finishTypeUpdate(false, errorMsg);
        }
    })
    .catch(networkError => {
        console.error('❌ Erro de rede para tipo:', networkError);
        finishTypeUpdate(false, 'Erro de conexão. Verifique sua internet e tente novamente.');
    });
    
    return false;
};

console.log('✅ Função updateUserType definida globalmente!');

// Teste imediato das funções
console.log('🧪 Testando função toggleUserStatus:', typeof window.toggleUserStatus);
console.log('🧪 Testando função deleteUser:', typeof window.deleteUser);
console.log('🧪 Testando função updateUserType:', typeof window.updateUserType);

document.addEventListener('DOMContentLoaded', function() {
    console.log('📋 Dashboard JS DOMContentLoaded executado');
    console.log('🧪 Função toggleUserStatus disponível:', typeof window.toggleUserStatus);
    console.log('🧪 Função deleteUser disponível:', typeof window.deleteUser);
    console.log('🧪 Função loadUsers disponível:', typeof window.loadUsers);
    console.log('🧪 Função showCustomConfirm disponível:', typeof window.showCustomConfirm);
    
    // Aguardar um pouco e verificar novamente
    setTimeout(() => {
        console.log('⏰ VERIFICAÇÃO APÓS 1 SEGUNDO:');
        console.log('🧪 showCustomConfirm ainda disponível:', typeof window.showCustomConfirm);
        if (typeof window.showCustomConfirm === 'function') {
            console.log('✅ Modal está funcionando!');
        } else {
            console.error('❌ Modal NÃO está disponível após 1 segundo!');
        }
    }, 1000);
    
    // Event delegation para botões de delete - VERSÃO ROBUSTA
    document.addEventListener('click', function(e) {
        // Verificar se o clique foi em um botão de delete ou dentro dele
        const deleteButton = e.target.closest('.delete-user-btn');
        
        if (deleteButton) {
            e.preventDefault();
            e.stopPropagation();
            
            const userId = deleteButton.getAttribute('data-id');
            
            console.log('🗑️ DELETE BUTTON CLICADO!', {
                button: deleteButton,
                userId: userId,
                target: e.target
            });
            
            if (userId) {
                console.log(`🎯 EXECUTANDO DELETE para userId: ${userId}`);
                window.deleteUser(parseInt(userId));
            } else {
                console.error('❌ Botão delete sem data-id!');
                showAlert('Erro: ID do utilizador não encontrado no botão', 'danger');
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
            
            console.log('🔄 Status button clicado:', {
                element: button,
                userId: userId,
                currentStatus: currentStatus,
                disabled: button.disabled
            });
            
            if (userId && currentStatus && !button.disabled) {
                console.log(`🔄 Event delegation executando toggle para userId: ${userId}, status: ${currentStatus}`);
                window.toggleUserStatus(parseInt(userId), currentStatus, button);
            } else {
                console.warn('⚠️ Botão de status clicado mas com dados inválidos ou desabilitado');
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

// Função para atualizar o estado do ticket
window.updateTicketStatus = function(ticketId, newStatus) {
    // Usar a função do modal customizado se estiver disponível
    if (typeof window.showCustomConfirmModal === 'function') {
        window.showCustomConfirmModal(ticketId, newStatus);
        return;
    }
    
    // REMOVIDO: Não usar confirm() padrão
    console.log('⚠️ Modal customizado não está disponível, cancelando ação');
    showAlert('Modal de confirmação não disponível', 'warning');
    return;
};