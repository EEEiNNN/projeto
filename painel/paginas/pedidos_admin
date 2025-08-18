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
                            <label>Usuário</label>
                            <select class="form-control" id="usuario_id" name="usuario_id" required>
                                <?php
                                // CORREÇÃO: Usa a variável $pdo já existente de 'index.php'
                                $query_usuarios = $pdo->query("SELECT id, nome FROM usuarios ORDER BY nome");
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
                            <input type="text" class="form-control" id="endereco_entrega" name="endereco_entrega" placeholder="Endereço Completo" required>
                        </div>
                    </div>
                    <hr>
                    <h5>Itens do Pedido</h5>
                    <div id="pedido_itens">
                        </div>
                    <button type="button" class="btn btn-success btn-sm" onclick="adicionarItem()">Adicionar Produto</button>
                    
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

<div class="modal fade" id="modalDados" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title" id="exampleModalLabel">Detalhes do Pedido: <span id="titulo_dados"></span></h4>
				<button id="btn-fechar-dados" type="button" class="close" data-dismiss="modal" aria-label="Close" style="margin-top: -25px">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
            <div class="modal-body" id="corpo_dados">
                </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    var pag = "<?=$pag?>";
</script>
<script src="js/ajax.js"></script> <script>
    let itemIndex = 0;

    function adicionarItem(produto_id = '', quantidade = 1, preco_unitario = 0) {
        itemIndex++;
        const div = document.createElement('div');
        div.className = 'row item-pedido mb-2';
        div.style.marginBottom = '10px';
        div.innerHTML = `
            <div class="col-md-5">
                <select class="form-control" name="produtos[${itemIndex}][produto_id]" onchange="atualizarPreco(this)" required>
                    <option value="">Selecione um Produto</option>
                    <?php
                    $query_produtos = $pdo->query("SELECT id, nome, preco FROM produtos WHERE ativo = TRUE ORDER BY nome");
                    $produtos = $query_produtos->fetchAll(PDO::FETCH_ASSOC);
                    foreach ($produtos as $p) {
                        echo "<option value='{$p['id']}' data-preco='{$p['preco']}'>{$p['nome']}</option>";
                    }
                    ?>
                </select>
            </div>
            <div class="col-md-2">
                <input type="number" class="form-control" name="produtos[${itemIndex}][quantidade]" value="${quantidade}" min="1" required>
            </div>
            <div class="col-md-3">
                <input type="text" class="form-control preco-unitario" name="produtos[${itemIndex}][preco]" value="${preco_unitario.toFixed(2)}" readonly>
            </div>
            <div class="col-md-2">
                <button type="button" class="btn btn-danger btn-sm" onclick="removerItem(this)">Remover</button>
            </div>
        `;
        document.getElementById('pedido_itens').appendChild(div);

        // Se for edição, seleciona o produto correto
        if (produto_id) {
            div.querySelector('select').value = produto_id;
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

    // Limpa o formulário ao fechar o modal
    $('#modalForm').on('hidden.bs.modal', function () {
        $('#form')[0].reset();
        $('#pedido_itens').empty();
        itemIndex = 0;
        $('#mensagem').text('').removeClass('text-danger');
    });
</script>