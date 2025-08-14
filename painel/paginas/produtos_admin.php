<?php
    $pag = 'produtos_admin';
?>
<a type="button" class="btn btn-primary" data-toggle="modal" data-target="#modalForm" onclick="limparCamposProduto()">
    <span class="fa fa-plus"></span> Produtos
</a>

<div class="bs-example widget-shadow" style="padding:15px" id="listar">
    <!-- Chamando o ajax.js -->
    <script src="js/ajax-produtos.js"></script>
</div>

<!-- Modal Formulário -->
<div class="modal fade" id="modalForm" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="exampleModalLabel"><span id="titulo_inserir">Inserir Produto</span></h4>
                <button id="btn-fechar" type="button" class="close" data-dismiss="modal" aria-label="Close" style="margin-top: -25px">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="form">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <label>Nome</label>
                            <input type="text" class="form-control" id="nome" name="nome" placeholder="Nome do Produto" required>
                        </div>
                        <div class="col-md-6">
                            <label>Preço</label>
                            <input type="number" step="0.01" class="form-control" id="preco" name="preco" placeholder="0.00" required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <label>Categoria</label>
                            <select class="form-control" id="categoria_id" name="categoria_id" required>
                                <option value="">Selecione uma categoria</option>
                                <!-- Opções serão preenchidas via AJAX -->
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label>Descrição</label>
                            <textarea class="form-control" id="descricao" name="descricao" placeholder="Descrição do produto" rows="3"></textarea>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <label>Imagem URL</label>
                            <input type="text" class="form-control" id="imagem" name="imagem" placeholder="URL da imagem">
                        </div>
                        <div class="col-md-6">
                            <label>Estoque</label>
                            <input type="number" class="form-control" id="estoque" name="estoque" placeholder="Quantidade em estoque" value="0">
                        </div>
                    </div>
                    <input type="hidden" class="form-control" id="id" name="id">
                    <br>
                    <small><div id="mensagem" align="center"></div></small>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Salvar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script type="text/javascript">
    var pag = "<?php echo $pag; ?>";
    
    $(document).ready(function(){
        // Carregar categorias ao abrir o modal
        $('#modalForm').on('show.bs.modal', function (e) {
            carregarCategoriasProduto();
        });
    });
    
    // Funções específicas para produtos
    function carregarCategoriasProduto(){
        $.ajax({
            url: 'paginas/' + pag + '/carregar_categorias.php',
            method: 'POST',
            dataType: "html",
            success:function(result){
                $("#categoria_id").html(result);
            }
        });
    }
    
    function limparCamposProduto(){
        $('#id').val('');
        $('#nome').val('');
        $('#preco').val('');
        $('#categoria_id').val('');
        $('#descricao').val('');
        $('#imagem').val('');
        $('#estoque').val('');
        $('#titulo_inserir').text('Inserir Produto');
        $('#mensagem').text('');
    }
</script>
