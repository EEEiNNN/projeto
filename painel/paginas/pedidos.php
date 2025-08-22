<?php
$pag = 'pedidos';
// Este ficheiro é incluído a partir do painel/index.php, que já tem a conexão.
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
                                <?php
                                $query_usuarios = $pdo->query("SELECT id, nome FROM usuarios WHERE nivel = 'user' ORDER BY nome");
                                $usuarios = $query_usuarios->fetchAll(PDO::FETCH_ASSOC);
                                foreach ($usuarios as $usuario) {
                                    echo "<option value='{$usuario['id']}'>{$usuario['nome']}</option>";
                                }
                                ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label>Status</label>
                            <select class="form-control" id="status" name="status">
                                <option value="pendente">Pendente</option>
                                <option value="processando">Processando</option>
                                <option value="enviado">Enviado</option>
                                <option value="entregue">Entregue</option>
                                <option value="cancelado">Cancelado</option>
                            </select>
                        </div>
                    </div>
                    <div class="row" style="margin-top: 10px;">
                         <div class="col-md-12">
                            <label>Endereço de Entrega</label>
                            <input type="text" class="form-control" name="endereco_entrega" id="endereco_entrega" placeholder="Endereço Completo">
                        </div>
                    </div>
                    <hr>
                    <h5>Itens do Pedido</h5>
                    <div id="pedido_itens">
                        </div>
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

<script>
    var pag = "<?=$pag?>";
    // Assumindo que o seu ajax.js já lida com o carregamento inicial da lista
</script>
<script src="js/ajax.js"></script>

<script>
    let itemIndex = 0;

    function adicionarItem(produto_id = '', quantidade = 1, preco_unitario = 0) {
        itemIndex++;
        const div = document.createElement('div');
        div.className = 'row item-pedido align-items-end'; // `align-items-end` para alinhar o botão
        div.style.marginBottom = '10px';
        div.innerHTML = `
            <div class="col-md-5">
                <label class="form-label">Produto</label>
                <select class="form-control" name="produtos[${itemIndex}][produto_id]" onchange="atualizarPreco(this)" required>
                    <option value="">Selecione um Produto</option>
                    <?php
                    // [CORREÇÃO] Nome da tabela é 'produto', não 'produtos'
                    $query_produtos = $pdo->query("SELECT id, nome, preco FROM produto WHERE ativo = 1 ORDER BY nome");
                    $produtos = $query_produtos->fetchAll(PDO::FETCH_ASSOC);
                    foreach ($produtos as $p) {
                        echo "<option value='{$p['id']}' data-preco='{$p['preco']}'>{$p['nome']}</option>";
                    }
                    ?>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label">Qtd.</label>
                <input type="number" class="form-control" name="produtos[${itemIndex}][quantidade]" value="${quantidade}" min="1" required>
            </div>
            <div class="col-md-3">
                <label class="form-label">Preço Unit.</label>
                <input type="text" class="form-control preco-unitario" name="produtos[${itemIndex}][preco_unitario]" value="${parseFloat(preco_unitario).toFixed(2)}" readonly>
            </div>
            <div class="col-md-2">
                <button type="button" class="btn btn-danger btn-sm" onclick="removerItem(this)">Remover</button>
            </div>
        `;
        document.getElementById('pedido_itens').appendChild(div);

        // Se for um item existente (na edição), seleciona o produto correto
        if (produto_id) {
            const select = div.querySelector('select');
            select.value = produto_id;
        }
    }

    function removerItem(button) {
        button.closest('.item-pedido').remove();
    }

    function atualizarPreco(select) {
        const option = select.options[select.selectedIndex];
        const preco = option.getAttribute('data-preco') || '0.00';
        const precoInput = select.closest('.item-pedido').querySelector('.preco-unitario');
        precoInput.value = parseFloat(preco).toFixed(2);
    }

    function editar(id) {
        $.ajax({
            url: `paginas/${pag}/editar.php`,
            method: 'POST',
            data: { id },
            dataType: 'json',
            success: function(res) {
                if (res.success) {
                    $('#titulo_inserir').text('Editar Pedido');
                    $('#id').val(res.data.id);
                    $('#usuario_id').val(res.data.usuario_id);
                    $('#status').val(res.data.status);
                    $('#endereco_entrega').val(res.data.endereco_entrega);

                    // Limpa itens antigos e adiciona os itens do pedido
                    $('#pedido_itens').empty();
                    itemIndex = 0;
                    res.data.itens.forEach(item => {
                        adicionarItem(item.produto_id, item.quantidade, item.preco);
                    });
                    
                    $('#modalForm').modal('show');
                } else {
                    alert(res.message || 'Erro ao buscar dados do pedido.');
                }
            },
            error: function() {
                alert('Erro de comunicação com o servidor.');
            }
        });
    }

    // Limpa o formulário ao fechar o modal
    $('#modalForm').on('hidden.bs.modal', function () {
        $('#form')[0].reset();
        $('#pedido_itens').empty();
        itemIndex = 0;
        $('#id').val('');
        $('#titulo_inserir').text('Novo Pedido');
    });
</script>