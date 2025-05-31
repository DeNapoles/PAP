document.addEventListener('DOMContentLoaded', function() {
    console.log('Dashboard JS carregado');
    
    // Inicializa os ícones do Feather
    if (typeof feather !== 'undefined') {
        feather.replace();
    }

    // Adiciona event listeners para os botões de categoria
    document.querySelectorAll('.show-cat-btn').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            const subMenu = this.nextElementSibling;
            const arrow = this.querySelector('.icon.arrow-down');
            
            // Toggle do submenu
            if (subMenu.style.display === 'block') {
                subMenu.style.display = 'none';
                if (arrow) {
                    arrow.style.transform = 'rotate(0deg)';
                }
            } else {
                subMenu.style.display = 'block';
                if (arrow) {
                    arrow.style.transform = 'rotate(180deg)';
                }
            }
        });
    });

    // Função para mostrar/esconder seções
    function showSection(sectionId, event) {
        // Previne o comportamento padrão do link se o evento existir
        if (event) {
            event.preventDefault();
        }
        
        // Esconde todas as seções
        document.querySelectorAll('.content-section').forEach(section => {
            section.style.display = 'none';
        });

        // Mostra a seção selecionada
        const selectedSection = document.getElementById(sectionId);
        if (selectedSection) {
            selectedSection.style.display = 'block';
            console.log('Seção mostrada:', sectionId);
            
            // Se for a seção de posts, carrega a primeira página via AJAX
            if (sectionId === 'posts-section') {
                loadPostsPage(1);
            }
        } else {
            console.error('Seção não encontrada:', sectionId);
        }
    }

    // Adiciona event listeners aos links do menu
    document.querySelectorAll('.nav-link, .cat-sub-menu a').forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            const sectionId = this.getAttribute('onclick')?.match(/'([^']+)'/)?.[1];
            if (sectionId) {
                showSection(sectionId, e);
                console.log('Link clicado:', sectionId);
            }
        });
    });

    // Mostra a seção de boas-vindas por padrão
    showSection('welcome-section');

    // Adiciona event listeners para todos os links com data-section
    document.querySelectorAll('[data-section]').forEach(link => {
        console.log('Encontrado link com data-section:', link.getAttribute('data-section'));
        
        link.addEventListener('click', function(e) {
            e.preventDefault();
            console.log('Clicou em:', this.getAttribute('data-section'));
            
            // Remove a classe active de todos os links
            document.querySelectorAll('.cat-sub-menu a').forEach(a => {
                a.classList.remove('active');
            });
            
            // Adiciona a classe active ao link clicado
            this.classList.add('active');
            
            // Obtém a seção a ser mostrada
            const sectionToShow = this.getAttribute('data-section');
            
            // Oculta todas as seções
            document.querySelectorAll('.content-section').forEach(section => {
                section.style.display = 'none';
            });
            
            // Mostra a seção selecionada
            const targetSection = document.getElementById(sectionToShow + '-section');
            if (targetSection) {
                targetSection.style.display = 'block';
            }
        });
    });
});

function loadContent(section) {
    console.log('Carregando seção:', section);
    const contentArea = document.getElementById('main-content-area');
    
    // Mostra um indicador de carregamento
    contentArea.innerHTML = '<div class="text-center mt-5"><div class="spinner-border" role="status"><span class="visually-hidden">Carregando...</span></div></div>';
    
    // Mapeia as seções para seus respectivos arquivos PHP
    const sectionFiles = {
        'capa': './capa_editor.php',
        'links': './links_editor.php',
        'sobre': './sobre_editor.php',
        'avaliacoes': './avaliacoes_editor.php',
        'login': './login_editor.php',
        'faqs': './faqs_editor.php',
        'aviso': './aviso_editor.php'
    };

    // Verifica se a seção existe no mapeamento
    if (sectionFiles[section]) {
        console.log('Fazendo fetch de:', sectionFiles[section]);
        
        fetch(sectionFiles[section])
            .then(response => {
                console.log('Status da resposta:', response.status);
                if (!response.ok) {
                    throw new Error('Erro na resposta do servidor: ' + response.status);
                }
                return response.text();
            })
            .then(html => {
                console.log('Conteúdo recebido, atualizando área principal');
                contentArea.innerHTML = html;
                
                // Recarrega os scripts do Bootstrap após inserir o novo conteúdo
                if (typeof bootstrap !== 'undefined') {
                    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
                    tooltipTriggerList.map(function (tooltipTriggerEl) {
                        return new bootstrap.Tooltip(tooltipTriggerEl);
                    });
                }
            })
            .catch(error => {
                console.error('Erro ao carregar conteúdo:', error);
                contentArea.innerHTML = `
                    <div class="alert alert-danger m-3" role="alert">
                        <h4 class="alert-heading">Erro ao carregar o conteúdo</h4>
                        <p>Ocorreu um erro ao tentar carregar a seção. Por favor, tente novamente.</p>
                        <hr>
                        <p class="mb-0">Detalhes técnicos: ${error.message}</p>
                    </div>`;
            });
    } else {
        console.error('Seção não encontrada:', section);
        contentArea.innerHTML = '<div class="alert alert-warning m-3">Seção não encontrada.</div>';
    }
}

// Inicializar o sistema de avaliação com estrelas
function initStarRating(container) {
    if (!container) return;
    
    const stars = container.querySelectorAll('.star');
    const hiddenInput = container.querySelector('input[type="hidden"]');
    const ratingValue = container.querySelector('.rating-value');
    let currentValue = parseInt(hiddenInput.value) || 0;

    // Atualiza visual inicial
    stars.forEach((star, index) => {
        if (index < currentValue) {
            star.classList.add('active');
        } else {
            star.classList.remove('active');
        }
    });

    // Atualiza o valor mostrado
    ratingValue.textContent = `${currentValue}/5`;

    // Adiciona eventos de clique
    stars.forEach((star, index) => {
        star.addEventListener('click', () => {
            const value = index + 1;
            currentValue = value;
            hiddenInput.value = value;
            
            // Atualiza visual
            stars.forEach((s, i) => {
                if (i < value) {
                    s.classList.add('active');
                } else {
                    s.classList.remove('active');
                }
            });
            
            // Atualiza o valor mostrado
            ratingValue.textContent = `${value}/5`;
        });

        // Efeito hover
        star.addEventListener('mouseover', () => {
            stars.forEach((s, i) => {
                if (i <= index) {
                    s.classList.add('active');
                } else {
                    s.classList.remove('active');
                }
            });
        });

        star.addEventListener('mouseout', () => {
            stars.forEach((s, i) => {
                if (i < currentValue) {
                    s.classList.add('active');
                } else {
                    s.classList.remove('active');
                }
            });
        });
    });
}

// Inicializar todas as avaliações com estrelas ao carregar a página
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.star-rating').forEach(initStarRating);
});

// Função utilitária para obter o user_id do PHP
function getCurrentUserId() {
    // Tenta obter do campo hidden
    const hidden = document.getElementById('postAutorId');
    if (hidden && hidden.value) return hidden.value;
    // Tenta obter de variável global
    if (window.currentUserId) return window.currentUserId;
    // Tenta obter de meta tag (caso queira implementar no futuro)
    const meta = document.querySelector('meta[name="user-id"]');
    if (meta) return meta.getAttribute('content');
    return '';
}

function showPostEditor(mode = 'create', postData = null) {
    // Esconde a section de listagem de posts
    document.getElementById('posts-section').style.display = 'none';
    // Mostra a section de edição/criação
    document.getElementById('post-editor-section').style.display = 'block';

    // Limpa todos os campos
    document.getElementById('postEditorForm').reset();
    // Limpa previews de imagens
    document.querySelectorAll('#post-editor-section .image-preview img').forEach(img => {
        img.src = '';
        img.style.display = 'none';
    });
    // Limpa campos de texto de imagens
    document.querySelectorAll('#post-editor-section input[type="text"]').forEach(input => {
        if (input.id.startsWith('postImg')) input.value = '';
    });
    // Data de criação
    document.getElementById('postDataCriacao').value = '';

    // Sempre garantir que o autor_id está correto
    const autorIdInput = document.getElementById('postAutorId');
    if (autorIdInput) {
        autorIdInput.value = getCurrentUserId();
    }

    if (mode === 'create') {
        document.getElementById('post-editor-title').textContent = 'Novo Post';
        document.getElementById('savePostBtn').textContent = 'Criar';
        document.getElementById('postId').value = '';
        // Já garantido acima: autorIdInput.value = getCurrentUserId();
        // Remove required das imagens adicionais
        for (let i = 1; i <= 5; i++) {
            const imgInput = document.getElementById(`postImg${i}`);
            if (imgInput) imgInput.removeAttribute('required');
        }
    } else if (mode === 'edit' && postData) {
        document.getElementById('post-editor-title').textContent = 'Editar Post';
        document.getElementById('savePostBtn').textContent = 'Salvar';
        document.getElementById('postId').value = postData.id;
        document.getElementById('postTitulo').value = postData.titulo || '';
        document.getElementById('postTexto').value = postData.texto || '';
        document.getElementById('postTags').value = postData.tags || '';
        // Já garantido acima: autorIdInput.value = getCurrentUserId();
        document.getElementById('postDataCriacao').value = postData.data_criacao || '';
        // Imagem principal
        if (postData.img_principal) {
            document.getElementById('postImgPrincipal').value = postData.img_principal;
            const img = document.querySelector('#postImgPrincipalPreview img');
            img.src = postData.img_principal;
            img.style.display = 'block';
        }
        // Imagens adicionais
        for (let i = 1; i <= 5; i++) {
            const imgInput = document.getElementById(`postImg${i}`);
            if (imgInput) {
                imgInput.removeAttribute('required');
                if (postData[`img_${i}`]) {
                    imgInput.value = postData[`img_${i}`];
                    const img = document.querySelector(`#postImg${i}Preview img`);
                    img.src = postData[`img_${i}`];
                    img.style.display = 'block';
                }
            }
        }
    }
}

// Botão Novo Post
window.showNewPostForm = function() {
    showPostEditor('create');
};

// Botão Editar Post
window.editPost = function(postId) {
    // Buscar dados do post via AJAX
    fetch(`get_posts.php?id=${postId}`)
        .then(res => res.json())
        .then(data => {
            if (data.success && data.post) {
                showPostEditor('edit', data.post);
            } else {
                alert('Erro ao buscar dados do post: ' + (data.message || 'Erro desconhecido'));
            }
        })
        .catch(error => {
            console.error('Erro:', error);
            alert('Erro ao buscar dados do post. Por favor, tente novamente.');
        });
};

// Botão Cancelar
if (document.getElementById('cancelPostEdit')) {
    document.getElementById('cancelPostEdit').onclick = function() {
        document.getElementById('post-editor-section').style.display = 'none';
        document.getElementById('posts-section').style.display = 'block';
    };
}

function deletePost(postId) {
    if (confirm('Tem certeza que deseja apagar este post? Esta ação não pode ser desfeita.')) {
        // Faz a requisição para apagar o post
        fetch('delete_post.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: 'post_id=' + postId
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Remove o card do post da interface
                const postCard = document.querySelector(`[data-post-id="${postId}"]`);
                if (postCard) {
                    postCard.remove();
                }
                // Mostra mensagem de sucesso
                alert('Post apagado com sucesso!');
                // Garante que a seção de posts está visível
                document.getElementById('post-editor-section').style.display = 'none';
                document.getElementById('posts-section').style.display = 'block';
                // Recarrega a lista de posts via AJAX
                loadPostsPage(1);
            } else {
                alert('Erro ao apagar o post: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Erro:', error);
            alert('Erro ao apagar o post. Por favor, tente novamente.');
        });
    }
}

// Função para carregar posts via AJAX
function loadPostsPage(page = 1) {
    fetch(`get_posts.php?page=${page}`)
        .then(response => response.text())
        .then(html => {
            // Espera que o PHP retorne o HTML dos cards + paginação
            const tempDiv = document.createElement('div');
            tempDiv.innerHTML = html;
            const posts = tempDiv.querySelector('#postsContainer');
            const pagination = tempDiv.querySelector('#paginationContainer');
            if (posts && pagination) {
                document.getElementById('postsContainer').innerHTML = posts.innerHTML;
                document.getElementById('paginationContainer').innerHTML = pagination.innerHTML;
                // Atualiza a URL
                window.history.pushState({}, '', `?page=${page}`);
                // Reatribui eventos aos botões de paginação
                setupPaginationEvents();
                // Scroll suave para o topo da section dos posts
                const section = document.getElementById('posts-section');
                if (section) {
                    section.scrollIntoView({ behavior: 'smooth', block: 'start' });
                }
            }
        });
}

// Função para atribuir eventos aos botões de paginação
function setupPaginationEvents() {
    document.querySelectorAll('#paginationContainer .page-link').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            const page = this.getAttribute('data-page');
            if (page) loadPostsPage(page);
        });
    });
}

// Inicializa paginação AJAX ao carregar a seção de posts
if (document.getElementById('paginationContainer')) {
    setupPaginationEvents();
}

// Processar o formulário de post
document.getElementById('postEditorForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    
    fetch('save_post.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert(data.message);
            // Voltar para a listagem de posts
            document.getElementById('post-editor-section').style.display = 'none';
            document.getElementById('posts-section').style.display = 'block';
            // Recarregar a lista de posts
            loadPostsPage(1);
        } else {
            alert('Erro: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Erro:', error);
        alert('Erro ao salvar o post. Por favor, tente novamente.');
    });
});

// Processar o formulário de ligações rápidas
document.getElementById('linksForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    
    fetch('update_links.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        const alertDiv = document.getElementById('linksAlert');
        alertDiv.innerHTML = `
            <div class="alert alert-${data.success ? 'success' : 'danger'} alert-dismissible fade show" role="alert">
                ${data.message}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        `;
        
        // Scroll para o topo da seção
        const section = document.getElementById('links-section');
        section.scrollIntoView({ behavior: 'smooth', block: 'start' });
        
        // Remove a mensagem após 3 segundos
        setTimeout(() => {
            alertDiv.innerHTML = '';
        }, 3000);
    })
    .catch(error => {
        console.error('Erro:', error);
        const alertDiv = document.getElementById('linksAlert');
        alertDiv.innerHTML = `
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                Erro ao salvar alterações. Por favor, tente novamente.
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        `;
        
        // Scroll para o topo da seção
        const section = document.getElementById('links-section');
        section.scrollIntoView({ behavior: 'smooth', block: 'start' });
    });
}); 