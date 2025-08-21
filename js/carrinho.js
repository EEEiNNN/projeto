document.addEventListener('DOMContentLoaded', () => {

    /**
     * Envia uma ação para o backend e recarrega a página em caso de sucesso.
     * @param {FormData} formData - Os dados a serem enviados (incluindo a ação).
     */
    const performCartAction = async (formData) => {
        try {
            const response = await fetch('carrinho.php', {
                method: 'POST',
                body: formData
            });

            const result = await response.json();

            if (result.success) {
                // A maneira mais simples e robusta de atualizar totais e itens é recarregar a página.
                window.location.reload();
            } else {
                // Exibe um alerta em caso de erro (ex: estoque insuficiente)
                alert(result.message || 'Ocorreu um erro na operação.');
            }
        } catch (error) {
            console.error('Erro na ação do carrinho:', error);
            alert('Não foi possível se comunicar com o servidor. Tente novamente.');
        }
    };

    // --- AÇÃO: ATUALIZAR QUANTIDADE ---
    const quantityInputs = document.querySelectorAll('.quantidade-input');
    quantityInputs.forEach(input => {
        // 'change' é acionado quando o valor do input muda e o usuário clica fora
        input.addEventListener('change', (e) => {
            const productId = e.target.dataset.produto;
            const quantity = parseInt(e.target.value, 10);

            if (productId && quantity > 0) {
                const formData = new FormData();
                formData.append('action', 'atualizar');
                formData.append('produto_id', productId);
                formData.append('quantidade', quantity);
                performCartAction(formData);
            } else if (quantity <= 0) {
                // Se a quantidade for 0 ou menos, remove o item
                removeItem(productId);
            }
        });
    });
    
    // --- AÇÃO: BOTÕES DE + E - ---
    const quantityButtons = document.querySelectorAll('.btn-quantidade');
    quantityButtons.forEach(button => {
        button.addEventListener('click', (e) => {
           const action = e.target.dataset.action;
           const productId = e.target.dataset.produto;
           const input = document.querySelector(`.quantidade-input[data-produto='${productId}']`);
           let currentValue = parseInt(input.value, 10);
           
           if(action === 'aumentar') {
               currentValue++;
           } else if(action === 'diminuir') {
               currentValue--;
           }
           
           // Dispara o evento 'change' para reutilizar a lógica de atualização
           if(currentValue >= 0){
               input.value = currentValue;
               input.dispatchEvent(new Event('change'));
           }
        });
    });


    /**
     * Função auxiliar para remover um item.
     * @param {string} productId - O ID do produto a ser removido.
     */
    const removeItem = (productId) => {
        if (!productId) return;

        // Confirmação antes de remover
        if (confirm('Tem certeza de que deseja remover este item?')) {
            const formData = new FormData();
            formData.append('action', 'remover');
            formData.append('produto_id', productId);
            performCartAction(formData);
        }
    };

    // --- AÇÃO: REMOVER ITEM (no botão de lixeira) ---
    const removeButtons = document.querySelectorAll('.btn-remover');
    removeButtons.forEach(button => {
        button.addEventListener('click', (e) => {
            // currentTarget garante que pegamos o botão, mesmo que o clique seja no ícone dentro dele
            const productId = e.currentTarget.dataset.produto;
            removeItem(productId);
        });
    });

    // --- AÇÃO: LIMPAR CARRINHO ---
    const clearCartButton = document.querySelector('.btn-limpar');
    if (clearCartButton) {
        clearCartButton.addEventListener('click', () => {
            if (confirm('Tem certeza de que deseja limpar todo o carrinho?')) {
                const formData = new FormData();
                formData.append('action', 'limpar');
                performCartAction(formData);
            }
        });
    }
});