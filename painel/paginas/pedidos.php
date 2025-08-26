<?php
$pag = 'pedidos';
?>
<a type="button" class="btn btn-primary" data-toggle="modal" data-target="#modalForm">
    <span class="fa fa-plus"></span> Novo Pedido
</a>

<div class="bs-example widget-shadow" style="padding:15px; margin-top:15px;" id="listar">
    </div>

<div class="modal fade" id="modalForm" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="exampleModalLabel"><span id="titulo_inserir"></span></h4>
                <button id="btn-fechar" type="button" class="close" data-dismiss="modal" aria-label="Close" style="margin-top: -25px">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="form">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <label>Cliente</label>
                            <select class="form-control" id="usuario_id" name="usuario_id" required>
                                <option value="">Selecione um Cliente</option>
                                <?php
                                $query_usuarios = $pdo->query("SELECT id, nome FROM usuarios WHERE nivel = 'user' ORDER BY nome");
                                foreach ($query_usuarios->fetchAll(PDO::FETCH_ASSOC) as $usuario) {
                                    echo "<option value='{$usuario['id']}'>{$usuario['nome']}</option>";
                                }
                                ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label>Status do Pedido</label>
                            <select class="form-control" id="status" name="status">
                                <option value="pendente">Pendente</option>
                                <option value="processando">Processando</option>
                                <option value="enviado">Enviado</option>
                                <option value="entregue">Entregue</option>
                                <option value="cancelado">Cancelado</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="row mt-3">
                        <div class="col-md-12">
                             <label>Endereço de Entrega</label>
                             <select class="form-control" id="endereco_id" name="endereco_id" required>
                                <option value="">Selecione um cliente primeiro</option>
                            </select>
                        </div>
                    </div>
                    
                    <hr>
                    <h5>Itens do Pedido</h5>
                    <div id="pedido_itens"></div>
                    <button type="button" class="btn btn-success btn-sm mt-2" onclick="adicionarItem()">Adicionar Produto</button>
                    
                    <input type="hidden" id="id" name="id">
                    <br><br>
                    <small><div id="mensagem" align="center"></div></small>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Salvar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script type="text/javascript"> var pag = "<?=$pag?>"; </script>
<script src="js/ajax.js"></script>

<script>
    let itemIndex = 0;
    const usuarioSelect = document.getElementById('usuario_id');
    const enderecoSelect = document.getElementById('endereco_id');

    // --- LÓGICA PARA CARREGAR ENDEREÇOS DINAMICAMENTE ---
    const carregarEnderecos = async (usuarioId) => {
        enderecoSelect.innerHTML = '<option value="">A carregar endereços...</option>';
        if (!usuarioId) {
            enderecoSelect.innerHTML = '<option value="">Selecione um cliente primeiro</option>';
            return;
        }
        try {
            const formData = new FormData();
            formData.append('usuario_id', usuarioId);
            const response = await fetch(`paginas/${pag}/buscar_enderecos.php`, {
                method: 'POST',
                body: formData
            });
            const data = await response.json();
            enderecoSelect.innerHTML = '';
            if (data.success && data.enderecos.length > 0) {
                data.enderecos.forEach(endereco => {
                    const displayText = `${endereco.rua}, ${endereco.numero} - ${endereco.cidade}`;
                    const option = new Option(displayText, endereco.id);
                    enderecoSelect.add(option);
                });
            } else {
                enderecoSelect.innerHTML = '<option value="">Nenhum endereço encontrado</option>';
            }
        } catch (error) {
            console.error('Erro ao buscar endereços:', error);
            enderecoSelect.innerHTML = '<option value="">Erro ao carregar endereços</option>';
        }
    };

    usuarioSelect.addEventListener('change', () => carregarEnderecos(usuarioSelect.value));

    // --- [CORREÇÃO] FUNÇÃO EDITAR REESCRITA COM FETCH ---
    async function editar(id) {
        try {
            const formData = new FormData();
            formData.append('id', id);

            const response = await fetch(`paginas/${pag}/editar.php`, {
                method: 'POST',
                body: formData
            });
            const res = await response.json();

            if (res.success) {
                const data = res.data;
                $('#titulo_inserir').text('Editar Pedido');
                $('#id').val(data.id);
                $('#status').val(data.status);
                
                // 1. Define o valor do cliente
                $('#usuario_id').val(data.usuario_id);
                
                // 2. Carrega os endereços deste cliente
                await carregarEnderecos(data.usuario_id);
                
                // 3. Seleciona o endereço correto do pedido
                $('#endereco_id').val(data.endereco_id);
                
                // 4. Limpa e adiciona os itens do pedido
                $('#pedido_itens').empty();
                itemIndex = 0;
                if (data.itens) {
                    data.itens.forEach(item => {
                        adicionarItem(item.produto_id, item.quantidade, item.preco);
                    });
                }
                
                // 5. Mostra o modal
                $('#modalForm').modal('show');
            } else {
                alert(res.message || 'Erro ao buscar dados do pedido.');
            }
        } catch (error) {
            console.error("Erro na função editar:", error);
            alert('Erro de comunicação com o servidor.');
        }
    }

    // Limpa o formulário ao fechar o modal
    $('#modalForm').on('hidden.bs.modal', function () {
        $('#form')[0].reset();
        $('#pedido_itens').empty();
        itemIndex = 0;
        $('#id').val('');
        $('#titulo_inserir').text('Novo Pedido');
        // Limpa e reseta o select de endereços
        enderecoSelect.innerHTML = '<option value="">Selecione um cliente primeiro</option>';
    });

    // As funções para adicionar/remover itens não precisam de alteração
    function adicionarItem(produto_id = '', quantidade = 1, preco_unitario = 0) {
        itemIndex++;
        const div = document.createElement('div');
        div.className = 'row item-pedido align-items-end mb-2';
        div.innerHTML = `
            <div class="col-md-5"><label class="form-label">Produto</label><select class="form-control" name="produtos[${itemIndex}][produto_id]" onchange="atualizarPreco(this)" required><option value="">Selecione...</option><?php $query_produtos = $pdo->query("SELECT id, nome, preco FROM produto WHERE ativo = 1 ORDER BY nome"); foreach ($query_produtos->fetchAll(PDO::FETCH_ASSOC) as $p) { echo "<option value='{$p['id']}' data-preco='{$p['preco']}'>{$p['nome']}</option>"; } ?></select></div>
            <div class="col-md-2"><label class="form-label">Qtd.</label><input type="number" class="form-control" name="produtos[${itemIndex}][quantidade]" value="${quantidade}" min="1" required></div>
            <div class="col-md-3"><label class="form-label">Preço Unit.</label><input type="text" class="form-control preco-unitario" name="produtos[${itemIndex}][preco_unitario]" value="${parseFloat(preco_unitario).toFixed(2)}" readonly></div>
            <div class="col-md-2"><button type="button" class="btn btn-danger btn-sm" onclick="removerItem(this)">Remover</button></div>
        `;
        document.getElementById('pedido_itens').appendChild(div);
        if (produto_id) { div.querySelector('select').value = produto_id; }
    }
    function removerItem(button) { button.closest('.item-pedido').remove(); }
    function atualizarPreco(select) {
        const option = select.options[select.selectedIndex];
        const preco = option.getAttribute('data-preco') || '0.00';
        select.closest('.item-pedido').querySelector('.preco-unitario').value = parseFloat(preco).toFixed(2);
    }
</script>