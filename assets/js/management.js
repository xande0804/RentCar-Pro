document.addEventListener('DOMContentLoaded', function () {
    // --- LÓGICA PARA REABRIR MODAL COM ERRO ---
    function handleValidationError(validationData) {
        if (!validationData) return;

        const modalId = validationData.modal === 'create' ? 'createModal' : 'editModal';
        const errorContainerId = validationData.modal === 'create' ? 'createError' : 'editError';
        
        const modalElement = document.getElementById(modalId);
        const errorContainer = document.getElementById(errorContainerId);
        
        if (modalElement && errorContainer) {
            errorContainer.textContent = validationData.error;
            errorContainer.classList.remove('d-none');
            const modalInstance = new bootstrap.Modal(modalElement);
            modalInstance.show();
        }
    }

    if (typeof serverValidationData !== 'undefined') {
        handleValidationError(serverValidationData);
    }

    // --- LÓGICA PARA PREENCHER MODAL DE EDIÇÃO ---
    const editModal = document.getElementById('editModal');
    if(editModal) {
        editModal.addEventListener('show.bs.modal', function (event) {
            const button = event.relatedTarget;
            if (!button || !button.classList.contains('edit')) return;

            const id = button.getAttribute('data-id'), nome = button.getAttribute('data-nome'), email = button.getAttribute('data-email'), perfil = button.getAttribute('data-perfil');
            
            const form = this.querySelector('form');
            form.querySelector('#edit-id').value = id;
            form.querySelector('#edit-nome').value = nome;
            form.querySelector('#edit-email').value = email;
            form.querySelector('#edit-perfil').value = perfil;
        });
    }
});