document.addEventListener('DOMContentLoaded', function() {
    const inicioForm = document.getElementById('inicioForm');
    if (inicioForm) {
        inicioForm.addEventListener('submit', function(event) {
            event.preventDefault();
            
            const formData = new FormData(this);
            formData.append('update_capa', '1');
            
            fetch('update_inicio.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Mostra mensagem de sucesso
                    const alert = document.createElement('div');
                    alert.className = 'alert alert-success alert-dismissible fade show';
                    alert.innerHTML = `
                        ${data.message}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    `;
                    inicioForm.insertBefore(alert, inicioForm.firstChild);
                    
                    // Remove a mensagem após 3 segundos
                    setTimeout(() => {
                        alert.remove();
                    }, 3000);

                    // Garante que a seção de início permanece visível
                    document.querySelectorAll('.content-section').forEach(section => {
                        section.style.display = 'none';
                    });
                    document.getElementById('inicio-section').style.display = 'block';

                    // Scroll suave para o topo da seção
                    document.getElementById('inicio-section').scrollIntoView({ 
                        behavior: 'smooth', 
                        block: 'start' 
                    });
                } else {
                    throw new Error(data.message);
                }
            })
            .catch(error => {
                // Mostra mensagem de erro
                const alert = document.createElement('div');
                alert.className = 'alert alert-danger alert-dismissible fade show';
                alert.innerHTML = `
                    Erro ao salvar: ${error.message}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                `;
                inicioForm.insertBefore(alert, inicioForm.firstChild);
                
                // Remove a mensagem após 5 segundos
                setTimeout(() => {
                    alert.remove();
                }, 5000);
            });
        });
    }
}); 