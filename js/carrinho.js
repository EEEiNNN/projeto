document.addEventListener('DOMContentLoaded', () => {

    // --- FUNÇÃO PARA ATUALIZAR A INTERFACE DO CARRINHO ---
    const atualizarInterfaceCarrinho = (data) => {
        const subtotalEl = document.getElementById('subtotal');
        const freteEl = document.getElementById('frete');
        const totalEl = document.getElementById('total');

        if (subtotalEl) subtotalEl.textContent = `R$ ${data.novoTotalCarrinho}`;
        if (freteEl) freteEl.textContent = data.freteTexto;
        if (totalEl) totalEl.textContent = `R$ ${data.totalFinal}`;

        // Se o carrinho ficar vazio, mostra a mensagem de "carrinho vazio"
        if (data.itemCount === 0) {
            document.querySelector('.carrinho-content').style.display = 'none';
            document.querySelector('.carrinho-vazio').style.display = 'block';
        }
    };

    // --- FUNÇÃO PRINCIPAL PARA ENVIAR AÇÕES PARA O BACKEND ---
    const performCartAction = async (formData) => {
        try {
            const response = await fetch('carrinho.php', {
                method: 'POST',
                body: formData
            });
            const result = await response.json();

            if (result.success) {
                // Atualiza a interface com os novos dados recebidos do PHP
                atualizarInterfaceCarrinho(result);
            } else {
                alert(result.message || 'Ocorreu um erro na operação.');
            }
        } catch (error) {
            console.error('Erro na ação do carrinho:', error);
            alert('Não foi possível comunicar com o servidor. Tente novamente.');
        }
    };

    // --- ATUALIZAR QUANTIDADE AO MUDAR O VALOR NO INPUT ---
    document.querySelectorAll('.quantidade-input').forEach(input => {
        input.addEventListener('change', (e) => {
            const productId = e.target.dataset.produto;
            let quantity = parseInt(e.target.value, 10);

            // Validação
            const max = parseInt(e.target.max, 10);
            if(quantity > max) {
                alert(`Stock insuficiente. Máximo de ${max} unidades.`);
                quantity = max; // Corrige para o máximo
                e.target.value = max;
            }
            
            const formData = new FormData();
            formData.append('action', 'atualizar');
            formData.append('produto_id', productId);
            formData.append('quantidade', quantity);
            performCartAction(formData);

            // Se a quantidade for 0, o item será removido pelo backend
            if (quantity <= 0) {
                e.target.closest('.carrinho-item').remove();
            }
        });
    });
    
    // --- BOTÕES DE + E - ---
    document.querySelectorAll('.btn-quantidade').forEach(button => {
        button.addEventListener('click', (e) => {
           const action = e.target.dataset.action;
           const productId = e.target.dataset.produto;
           const input = document.querySelector(`.quantidade-input[data-produto='${productId}']`);
           let currentValue = parseInt(input.value, 10);
           
           if(action === 'aumentar') currentValue++;
           else if(action === 'diminuir') currentValue--;
           
           if(currentValue >= 0) {
               input.value = currentValue;
               input.dispatchEvent(new Event('change')); // Dispara o evento de mudança para reutilizar a lógica
           }
        });
    });

    // --- REMOVER ITEM (BOTÃO DE LIXEIRA) ---
    document.querySelectorAll('.btn-remover').forEach(button => {
        button.addEventListener('click', (e) => {
            const productId = e.currentTarget.dataset.produto;
            if (confirm('Tem a certeza de que deseja remover este item?')) {
                const formData = new FormData();
                formData.append('action', 'remover');
                formData.append('produto_id', productId);
                performCartAction(formData);
                // Remove o elemento visualmente de imediato
                e.currentTarget.closest('.carrinho-item').remove();
            }
        });
    });

    // --- LIMPAR CARRINHO ---
    const clearCartButton = document.querySelector('.btn-limpar');
    if (clearCartButton) {
        clearCartButton.addEventListener('click', () => {
            if (confirm('Tem a certeza de que deseja limpar todo o carrinho?')) {
                const formData = new FormData();
                formData.append('action', 'limpar');
                performCartAction(formData);
            }
        });
    }
});