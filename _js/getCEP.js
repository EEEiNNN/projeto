document.addEventListener('DOMContentLoaded', function() {
    const cepInput = document.getElementById('cep');

    // Adiciona um "ouvinte de evento" que é acionado quando o utilizador sai do campo do CEP
    cepInput.addEventListener('blur', function() {
        // Pega o valor do CEP e remove caracteres não numéricos
        const cep = this.value.replace(/\D/g, '');

        // Verifica se o CEP tem 8 dígitos
        if (cep.length === 8) {
            // Mostra um feedback visual de que está a carregar
            setAddressFieldsReadOnly(true, 'Carregando...');
            
            // Faz a chamada à API ViaCEP
            fetch(`https://viacep.com.br/ws/${cep}/json/`)
                .then(response => response.json())
                .then(data => {
                    if (!data.erro) {
                        // Se a API encontrou o CEP, preenche os campos
                        document.getElementById('rua').value = data.logradouro;
                        document.getElementById('bairro').value = data.bairro;
                        document.getElementById('cidade').value = data.localidade;
                        document.getElementById('estado').value = data.uf;

                        // Move o foco do utilizador para o campo do número
                        document.getElementById('numero').focus();
                    } else {
                        // Se a API retornou um erro (CEP não encontrado)
                        alert('CEP não encontrado. Por favor, verifique e tente novamente.');
                        clearAddressFields();
                    }
                })
                .catch(error => {
                    // Em caso de erro de rede
                    console.error('Erro ao buscar o CEP:', error);
                    alert('Não foi possível buscar o CEP. Verifique a sua conexão.');
                })
                .finally(() => {
                    // Independentemente do resultado, reativa os campos
                    setAddressFieldsReadOnly(false);
                });
        }
    });

    // Função para limpar os campos de endereço
    function clearAddressFields() {
        document.getElementById('rua').value = '';
        document.getElementById('bairro').value = '';
        document.getElementById('cidade').value = '';
        document.getElementById('estado').value = '';
    }

    // Função para bloquear/desbloquear os campos durante a pesquisa
    function setAddressFieldsReadOnly(isReadOnly, placeholder = '') {
        document.getElementById('rua').readOnly = isReadOnly;
        document.getElementById('bairro').readOnly = isReadOnly;
        document.getElementById('cidade').readOnly = isReadOnly;
        document.getElementById('estado').readOnly = isReadOnly;

        document.getElementById('rua').placeholder = placeholder;
        document.getElementById('bairro').placeholder = placeholder;
        document.getElementById('cidade').placeholder = placeholder;
        document.getElementById('estado').placeholder = placeholder;
    }
});