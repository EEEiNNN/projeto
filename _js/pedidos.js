// Seleciona o modal e o botão de fechar
const modal = document.getElementById('detalhes-modal');
const closeBtn = document.querySelector('.close-btn');
const detalhesContent = document.getElementById('detalhes-content');

// Função para abrir o modal e buscar os detalhes do pedido
async function verDetalhes(pedidoId) {
    if (!pedidoId) return;

    // Mostra uma mensagem de "a carregar"
    detalhesContent.innerHTML = '<p>A carregar detalhes do pedido...</p>';
    modal.style.display = 'block';

    try {
        // Faz um pedido AJAX para um novo script que buscará os dados
        const response = await fetch(`ver_detalhes_pedido.php?id=${pedidoId}`);
        if (!response.ok) {
            throw new Error('Falha na resposta do servidor.');
        }
        const htmlContent = await response.text();
        detalhesContent.innerHTML = htmlContent;

    } catch (error) {
        detalhesContent.innerHTML = '<p>Ocorreu um erro ao buscar os detalhes do pedido. Tente novamente.</p>';
        console.error('Erro:', error);
    }
}

// Função para fechar o modal
function fecharModal() {
    modal.style.display = 'none';
}

// Fecha o modal se o utilizador clicar fora do conteúdo
window.onclick = function(event) {
    if (event.target == modal) {
        fecharModal();
    }
}