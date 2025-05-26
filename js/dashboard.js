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
    function showSection(sectionId) {
        // Previne o comportamento padrão do link
        event.preventDefault();
        
        // Esconde todas as seções
        document.querySelectorAll('.content-section').forEach(section => {
            section.style.display = 'none';
        });

        // Mostra a seção selecionada
        const selectedSection = document.getElementById(sectionId);
        if (selectedSection) {
            selectedSection.style.display = 'block';
            console.log('Seção mostrada:', sectionId);
            
            // Se for a seção de posts, atualiza a URL sem recarregar a página
            if (sectionId === 'posts-section') {
                const currentUrl = new URL(window.location.href);
                currentUrl.searchParams.set('section', 'posts');
                window.history.pushState({}, '', currentUrl);
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
                showSection(sectionId);
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

// Funções para gerenciar posts
function showNewPostForm() {
    // Por enquanto, apenas mostra um alerta
    alert('Funcionalidade em desenvolvimento');
}

function editPost(postId) {
    // Por enquanto, apenas mostra um alerta
    alert('Funcionalidade em desenvolvimento');
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
                // Recarrega a página para atualizar a paginação
                location.reload();
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