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
                                $usuarios = $query_usuarios->fetchAll(PDO::FETCH_ASSOC);
                                foreach ($usuarios as $usuario) {
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

    // --- [NOVO] LÓGICA PARA CARREGAR ENDEREÇOS DINAMICAMENTE ---
    document.getElementById('usuario_id').addEventListener('change', async function() {
        const usuarioId = this.value;
        const enderecoSelect = document.getElementById('endereco_id');
        
        // Limpa o select de endereços
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

            enderecoSelect.innerHTML = ''; // Limpa novamente antes de preencher
            if (data.success && data.enderecos.length > 0) {
                data.enderecos.forEach(endereco => {
                    const displayText = `${endereco.rua}, ${endereco.numero} - ${endereco.cidade}`;
                    const option = new Option(displayText, endereco.id);
                    enderecoSelect.add(option);
                });
            } else {
                enderecoSelect.innerHTML = '<option value="">Nenhum endereço encontrado para este cliente</option>';
            }
        } catch (error) {
            console.error('Erro ao buscar endereços:', error);
            enderecoSelect.innerHTML = '<option value="">Erro ao carregar endereços</option>';
        }
    });


    // As funções abaixo permanecem na sua maioria iguais
    function adicionarItem(produto_id = '', quantidade = 1, preco_unitario = 0) {
        // ... (código para adicionar item sem alterações) ...
    }
    function removerItem(button) {
        // ... (código para remover item sem alterações) ...
    }
    function atualizarPreco(select) {
        // ... (código para atualizar preço sem alterações) ...
    }

    function editar(id) {
        $.ajax({
            url: `paginas/${pag}/editar.php`,
            method: 'POST',
            data: { id },
            dataType: 'json',
            success: async function(res) {
                if (res.success) {
                    $('#titulo_inserir').text('Editar Pedido');
                    $('#id').val(res.data.id);
                    $('#status').val(res.data.status);

                    // Preenche o cliente e dispara o evento 'change' para carregar os endereços
                    $('#usuario_id').val(res.data.usuario_id).trigger('change');
                    
                    // Aguarda um momento para os endereços carregarem e depois seleciona o correto
                    // Esta é uma forma de garantir que o AJAX de endereços termine antes de selecionarmos o valor
                    setTimeout(() => {
                        $('#endereco_id').val(res.data.endereco_id);
                    }, 500); // 500ms de espera

                    $('#pedido_itens').empty();
                    itemIndex = 0;
                    res.data.itens.forEach(item => {
                        adicionarItem(item.produto_id, item.quantidade, item.preco);
                    });
                    
                    $('#modalForm').modal('show');
                } else {
                    alert(res.message || 'Erro ao buscar dados do pedido.');
                }
            }
        });
    }
    
    $('#modalForm').on('hidden.bs.modal', function () {
        $('#form')[0].reset();
        $('#pedido_itens').empty();
        itemIndex = 0;
        $('#id').val('');
        $('#titulo_inserir').text('Novo Pedido');
    });
</script>