<?php
$tabela = 'produtos';
require_once("../../../conexao.php");

$query = $pdo->query("SELECT p.*, c.nome as categoria_nome
                      FROM produto p 
                      LEFT JOIN categoria c ON p.categoria_id = c.id 
                      ORDER BY p.id DESC");
$res = $query->fetchAll(PDO::FETCH_ASSOC);
$linhas = count($res);

if($linhas > 0){
    echo <<<HTML
    <small>
        <table class="table table-hover" id="tabela">
            <thead>
                <tr>
                    <th>Nome</th>
                    <th class="esc">Preço</th>
                    <th class="esc">Categoria</th>
                    <th class="esc">Estoque</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
    HTML;
    
    for($i = 0; $i < $linhas; $i++){
        $id = $res[$i]['id'] ?? '';
        $nome = $res[$i]['nome'] ?? '';
        $preco = $res[$i]['preco'] ?? '';
        $categoria_id = $res[$i]['categoria_id'] ?? '';
        $categoria_nome = $res[$i]['categoria_nome'] ?? '';
        $descricao = $res[$i]['descricao'] ?? '';
        $imagem = $res[$i]['imagem'] ?? '';
        $estoque = $res[$i]['estoque'] ?? '';
        $ativo = $res[$i]['ativo'] ?? '';
        $data_criacao = $res[$i]['data_criacao'] ?? '';

        $dataF = $data_criacao ? date('d/m/Y', strtotime($data_criacao)) : '';
        $precoF = 'R$ ' . number_format($preco, 2, ',', '.');

        // Botões para Ativar e Desativar
        if($ativo == 1 || $ativo == true){
            $icone = 'fa-check-square';
            $titulo_link = 'Desativar Produto';
            $acao = 0;
            $class_ativo = '';
        } else {
            $icone = 'fa-check-o';
            $titulo_link = 'Ativar Produto';
            $acao = 1;
            $class_ativo = '#c4c4c4';
        }

        // Escapar saída para segurança
        $nome_esc = htmlspecialchars($nome, ENT_QUOTES);
        $categoria_nome_esc = htmlspecialchars($categoria_nome, ENT_QUOTES);
        $descricao_esc = htmlspecialchars($descricao, ENT_QUOTES);
        $imagem_esc = htmlspecialchars($imagem, ENT_QUOTES);
        $ativo_text = ($ativo == 1 || $ativo == true) ? 'Sim' : 'Não';

        echo <<<HTML
            <tr style="color: {$class_ativo}">
                <td>{$nome_esc}</td>
                <td class="esc">{$precoF}</td>
                <td class="esc">{$categoria_nome_esc}</td>
                <td class="esc">{$estoque}</td>
                <td>
                    <big><a href="#" onclick="editarProduto('{$id}', '{$nome_esc}', '{$preco}', '{$categoria_id}', '{$descricao_esc}', '{$imagem_esc}', '{$estoque}')" title="Editar Dados"><i class="fa fa-edit text-primary"></i></a></big>

                    <li class="dropdown head-dpdn2" style="display: inline-block">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                            <big>
                                <i class="fa fa-trash-o text-danger"></i>
                            </big>
                        </a>

                        <ul class="dropdown-menu" style="margin-left: -230px;">
                            <li>
                                <div class="notification_desc2">
                                    <p>Confirmar Exclusão? <a href="#" onclick="excluirProduto('{$id}')"><span class="text-danger">Sim</span></a></p>
                                </div>
                            </li>
                        </ul>
                    </li>

                    <big>
                        <a href="#" onclick="mostrarProduto('{$nome_esc}', '{$precoF}', '{$categoria_nome_esc}', '{$estoque}', '{$ativo_text}', '{$dataF}', '{$descricao_esc}', '{$imagem_esc}')" title="Mostrar Dados">
                            <i class="fa fa-info-circle text-primary"></i>
                        </a>
                    </big>

                    <big>
                        <a href="#" onclick="ativarProduto('{$id}', '{$acao}')" title="{$titulo_link}">
                            <i class="fa {$icone} text-success"></i>
                        </a>
                    </big>

                </td>
            </tr>
        HTML;
    }

    echo <<<HTML
            </tbody>
            <small>
                <div align="center" id="mensagem-excluir"></div>
            </small>
        </table>
    HTML;
    
} else {
    echo '<small>Nenhum Registro Encontrado</small>';
}
?>

<script type="text/javascript">
    $(document).ready(function(){
        $('#tabela').DataTable({
            "ordering": false,
            "stateSave": true
        });
    });
</script>

<script type="text/javascript">
    // Funções específicas para PRODUTOS
    function editarProduto(id, nome, preco, categoria_id, descricao, imagem, estoque){
        $('#mensagem').text('');
        $('#titulo_inserir').text('Editar Produto');

        $('#id').val(id);
        $('#nome').val(nome);
        $('#preco').val(preco);
        $('#descricao').val(descricao);
        $('#imagem').val(imagem);
        $('#estoque').val(estoque);
        
        // Carregar categorias e tipos específicos para produtos
        carregarCategoriasProduto();
        
        // Aguardar um pouco para garantir que os selects foram carregados
        setTimeout(function(){
            $('#categoria_id').val(categoria_id);
        }, 500);

        $('#modalForm').modal('show');
    }

    function mostrarProduto(nome, preco, categoria, estoque, ativo, data, descricao, imagem){
        $('#titulo_dados').text(nome);
        $('#preco_dados').text(preco);
        $('#categoria_dados').text(categoria);
        $('#estoque_dados').text(estoque);
        $('#ativo_dados').text(ativo);
        $('#data_dados').text(data);
        $('#descricao_dados').text(descricao);
        $('#imagem_dados').attr("src", imagem);

        $('#modalDados').modal('show');
    }

    function excluirProduto(id){
        $('#mensagem-excluir').text('Excluindo...');
        
        $.ajax({
            url: 'paginas/' + pag + '/excluir.php',
            method: 'POST',
            data: {id},
            dataType: "html",

            success:function(mensagem){
                if(mensagem.trim() == "Excluído com Sucesso"){
                    listar();
                }else{
                    $('#mensagem-excluir').addClass('text-danger');
                    $('#mensagem-excluir').text(mensagem);
                }
            }
        });
    }

    function ativarProduto(id, acao){
        $.ajax({
            url: 'paginas/' + pag + '/ativar.php',
            method: 'POST',
            data: {id, acao},
            dataType: "html",

            success:function(mensagem){
                if(mensagem.trim() == "Ativado com Sucesso" || mensagem.trim() == "Desativado com Sucesso"){
                    listar();
                }else{
                    $('#mensagem-excluir').addClass('text-danger');
                    $('#mensagem-excluir').text(mensagem);
                }
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

    // Funções específicas para carregar dados de produtos
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
</script>