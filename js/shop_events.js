document.addEventListener('DOMContentLoaded', () => {

    /**
     * Exibe uma notificação flutuante (toast).
     * @param {string} message - A mensagem a ser exibida.
     * @param {boolean} isSuccess - True para sucesso (verde), false para erro (vermelho).
     */
    function showToast(message, isSuccess = true) {
        // Garante que o container de toasts exista
        let toastContainer = document.getElementById('toast-container');
        if (!toastContainer) {
            toastContainer = document.createElement('div');
            toastContainer.id = 'toast-container';
            document.body.appendChild(toastContainer);
        }

        const toast = document.createElement('div');
        toast.className = `toast ${isSuccess ? 'success' : 'error'}`;
        toast.textContent = message;

        toastContainer.appendChild(toast);

        // Remove a notificação após 3 segundos
        setTimeout(() => {
            toast.classList.add('hide');
            // Remove o elemento do DOM após a animação de fade-out
            toast.addEventListener('transitionend', () => toast.remove());
        }, 3000);
    }

    /**
     * Adiciona um produto ao carrinho via AJAX.
     * @param {string} productId - O ID do produto a ser adicionado.
     */
    async function addToCart(productId) {
        const formData = new FormData();
        formData.append('action', 'adicionar');
        formData.append('produto_id', productId);

        try {
            const response = await fetch('carrinho.php', {
                method: 'POST',
                body: formData
            });

            const result = await response.json();

            if (result.success) {
                showToast(result.message || 'Produto adicionado com sucesso!');
                // Opcional: Atualizar o contador do carrinho no header aqui
            } else {
                showToast(result.message || 'Ocorreu um erro.', false);
            }
        } catch (error) {
            console.error('Erro ao adicionar ao carrinho:', error);
            showToast('Erro de conexão ao adicionar o produto.', false);
        }
    }

    // Adiciona o evento de clique a todos os botões "Adicionar ao carrinho"
    const addToCartButtons = document.querySelectorAll('.btn-add-carrinho');
    addToCartButtons.forEach(button => {
        button.addEventListener('click', (event) => {
            event.preventDefault();
            const productId = event.currentTarget.dataset.produtoId;
            if (productId) {
                addToCart(productId);
            }
        });
    });
});