document.addEventListener('DOMContentLoaded', function() {
    // Seleciona todos os links do submenu
    const submenuLinks = document.querySelectorAll('.cat-sub-menu a');
    const mainContentArea = document.getElementById('main-content-area');

    // Adiciona evento de clique para cada link
    submenuLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            const section = this.getAttribute('data-section');
            
            // Atualiza o título da página
            document.querySelector('.container').firstElementChild.textContent = this.textContent;
            
            // Carrega o conteúdo correspondente
            loadSection(section);
        });
    });

    // Função para carregar o conteúdo da seção
    function loadSection(section) {
        const sectionContent = {
            'logo': `
                <div class="edit-section">
                    <h3>Editar Logo</h3>
                    <div class="logo-preview">
                        <img src="get_image.php?type=logo" alt="Logo atual" style="max-width: 200px;">
                        <div class="image-actions">
                            <a href="get_image.php?type=logo&download=true" class="btn btn-secondary">
                                <i data-feather="download"></i> Download Logo Atual
                            </a>
                        </div>
                    </div>
                    <form class="edit-form" id="logoForm" enctype="multipart/form-data">
                        <div class="form-group">
                            <label>Upload novo logo</label>
                            <input type="file" class="form-control" name="logo" accept="image/*" required>
                        </div>
                        <button type="submit" class="btn btn-primary">Salvar alterações</button>
                        <div class="alert" style="display: none; margin-top: 10px;"></div>
                    </form>
                </div>
            `,
            'capa': `
                <div class="edit-section">
                    <h3>Editar Capa</h3>
                    <div class="image-preview">
                        <img src="img/banner-bg.jpg" alt="Imagem de capa atual" style="max-width: 100%;">
                        <div class="image-actions">
                            <a href="img/banner-bg.jpg" download class="btn btn-secondary">
                                <i data-feather="download"></i> Download Imagem Atual
                            </a>
                        </div>
                    </div>
                    <form class="edit-form">
                        <div class="form-group">
                            <label>Título da Capa</label>
                            <input type="text" class="form-control" value="Bem-vindo ao AEBConecta">
                        </div>
                        <div class="form-group">
                            <label>Subtítulo</label>
                            <textarea class="form-control" rows="3">O portal que liga a comunidade escolar com informações úteis, respostas rápidas e apoio especializado.</textarea>
                        </div>
                        <div class="form-group">
                            <label>Nova Imagem de Fundo</label>
                            <input type="file" class="form-control" accept="image/*">
                        </div>
                        <button type="submit" class="btn btn-primary">Salvar alterações</button>
                    </form>
                </div>
            `,
            'links': `
                <div class="edit-section">
                    <h3>Editar Ligações Rápidas</h3>
                    <form class="edit-form">
                        <div class="links-container">
                            <div class="form-group">
                                <label>GIAE</label>
                                <input type="url" class="form-control" value="http://193.236.85.189/">
                            </div>
                            <div class="form-group">
                                <label>Moodle</label>
                                <input type="url" class="form-control" value="https://moodle.agbatalha.pt/">
                            </div>
                            <div class="form-group">
                                <label>Eu Sou Pro</label>
                                <input type="url" class="form-control" value="https://agbatalha.pt/eusoupro/">
                            </div>
                        </div>
                        <button type="button" class="btn btn-secondary">Adicionar novo link</button>
                        <button type="submit" class="btn btn-primary">Salvar alterações</button>
                    </form>
                </div>
            `,
            'sobre': `
                <div class="edit-section">
                    <h3>Editar Sobre Nós</h3>
                    <div class="image-preview">
                        <img src="img/about-img.png" alt="Imagem atual" style="max-width: 100%;">
                        <div class="image-actions">
                            <a href="img/about-img.png" download class="btn btn-secondary">
                                <i data-feather="download"></i> Download Imagem Atual
                            </a>
                        </div>
                    </div>
                    <form class="edit-form">
                        <div class="form-group">
                            <label>Texto Sobre Nós</label>
                            <textarea class="form-control" rows="6">Somos alunos do Agrupamento de Escolas da Batalha, comprometidos com a inovação e otimização dos processos tecnológicos nas escolas...</textarea>
                        </div>
                        <div class="form-group">
                            <label>Nova Imagem</label>
                            <input type="file" class="form-control" accept="image/*">
                        </div>
                        <button type="submit" class="btn btn-primary">Salvar alterações</button>
                    </form>
                </div>
            `
            // Adicione mais seções conforme necessário
        };

        // Atualiza o conteúdo da área principal
        mainContentArea.innerHTML = sectionContent[section] || '<p>Seção não encontrada</p>';
        
        // Reinicializa os ícones do Feather após carregar o conteúdo
        if (window.feather) {
            feather.replace();
        }

        // Após carregar o conteúdo, configura os event listeners dos formulários
        setupFormListeners();
    }

    // Função para configurar os event listeners dos formulários
    function setupFormListeners() {
        const logoForm = document.getElementById('logoForm');
        if (logoForm) {
            logoForm.addEventListener('submit', function(e) {
                e.preventDefault();
                
                const formData = new FormData(this);
                formData.append('action', 'update_logo');

                fetch('process_image.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    const alert = this.querySelector('.alert');
                    if (data.success) {
                        // Atualiza a imagem preview
                        const img = document.querySelector('.logo-preview img');
                        img.src = 'get_image.php?type=logo&t=' + new Date().getTime();
                        
                        // Mostra mensagem de sucesso
                        alert.textContent = 'Logo atualizado com sucesso!';
                        alert.className = 'alert alert-success';
                    } else {
                        // Mostra mensagem de erro
                        alert.textContent = data.message || 'Erro ao atualizar o logo.';
                        alert.className = 'alert alert-danger';
                    }
                    alert.style.display = 'block';
                })
                .catch(error => {
                    console.error('Erro:', error);
                    const alert = this.querySelector('.alert');
                    alert.textContent = 'Erro ao processar a requisição.';
                    alert.className = 'alert alert-danger';
                    alert.style.display = 'block';
                });
            });
        }
    }
}); 