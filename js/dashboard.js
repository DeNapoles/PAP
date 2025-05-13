document.addEventListener('DOMContentLoaded', function() {
    console.log('Dashboard JS carregado');
    
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

function showSection(section) {
    // Oculta todas as seções
    const sections = ['capa-section'];
    sections.forEach(s => {
        const element = document.getElementById(s);
        if (element) {
            element.style.display = 'none';
        }
    });

    // Mostra a seção selecionada
    if (section === 'capa') {
        const capaSection = document.getElementById('capa-section');
        if (capaSection) {
            capaSection.style.display = 'block';
        }
    }
    // Adicione mais condições para outras seções conforme necessário
}

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