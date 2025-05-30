document.addEventListener('DOMContentLoaded', function() {
    const passwordInput = document.getElementById('reg_password');
    const requirements = {
        length: {
            regex: /.{8,}/,
            message: 'Mínimo de 8 caracteres'
        },
        numbers: {
            regex: /^(?=(?:.*\d){2,})/,
            message: 'Pelo menos 2 números'
        },
        special: {
            regex: /[!@#$%^&*(),.?":{}|<>]/,
            message: 'Pelo menos 1 caractere especial'
        },
        uppercase: {
            regex: /[A-Z]/,
            message: 'Pelo menos 1 letra maiúscula'
        },
        lowercase: {
            regex: /[a-z]/,
            message: 'Pelo menos 1 letra minúscula'
        }
    };

    function resetRequirements() {
        for (const requirement of Object.keys(requirements)) {
            const element = document.querySelector(`[data-requirement="${requirement}"]`);
            const icon = element.querySelector('i');
            icon.className = 'fas fa-times text-danger';
            element.classList.add('text-danger');
            element.classList.remove('text-success');
        }
    }

    function updateRequirementStatus(requirement, isValid) {
        const element = document.querySelector(`[data-requirement="${requirement}"]`);
        const icon = element.querySelector('i');
        
        if (isValid) {
            icon.className = 'fas fa-check text-success';
            element.classList.add('text-success');
            element.classList.remove('text-danger');
        } else {
            icon.className = 'fas fa-times text-danger';
            element.classList.add('text-danger');
            element.classList.remove('text-success');
        }
    }

    function validatePassword(password) {
        let isValid = true;
        
        for (const [requirement, { regex }] of Object.entries(requirements)) {
            const requirementValid = regex.test(password);
            updateRequirementStatus(requirement, requirementValid);
            if (!requirementValid) isValid = false;
        }
        
        return isValid;
    }

    passwordInput.addEventListener('input', function() {
        validatePassword(this.value);
    });

    // Adicionar validação ao formulário
    const registerForm = document.getElementById('registerForm');
    registerForm.addEventListener('submit', function(e) {
        const password = passwordInput.value;
        if (!validatePassword(password)) {
            e.preventDefault();
            const registerError = document.getElementById('registerError');
            registerError.textContent = 'A senha não cumpre todos os requisitos de segurança.';
            registerError.style.display = 'block';
        }
    });

    // Resetar os ícones quando o modal for fechado
    const registerModal = document.getElementById('registerModal');
    registerModal.addEventListener('hidden.bs.modal', function() {
        resetRequirements();
        passwordInput.value = '';
    });
}); 