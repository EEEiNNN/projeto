<?php

$tabela = 'usuarios';
require_once("../../../conexao.php");

$query = $pdo->query("SELECT * FROM $tabela ORDER BY id DESC");
$res = $query->fetchAll(PDO::FETCH_ASSOC);
$linhas = count($res);

if($linhas > 0){
    echo <<<HTML
    <small>
        <table class="table table-hover" id="tabela">
            <thead>
                <tr>
                    <th>Nome</th>
                    <th class="esc">Email</th>
                    <th class="esc">Telefone</th>
                    <th class="esc">nivel</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
    HTML;
    
    for($i = 0; $i < $linhas; $i++){
        $id = $res[$i]['id'] ?? '';
        $nome = $res[$i]['nome'] ?? '';
        $telefone = $res[$i]['telefone'] ?? '';
        $email = $res[$i]['email'] ?? '';
        $nivel = $res[$i]['nivel'] ?? '';
        $endereco = $res[$i]['endereco'] ?? '';
        $ativo = $res[$i]['ativo'] ?? '';
        $data = $res[$i]['data'] ?? '';
        $senha = $res[$i]['senha'] ?? '';
        $foto = $res[$i]['foto'] ?? '';

        $dataF = $data ? implode('/', array_reverse(explode('-', $data))) : '';

        // Botões para Ativar e Desativar
        if($ativo === 'Sim'){
            $icone = 'fa-check-square';
            $titulo_link = 'Desativar Usuário';
            $acao = 'Não';
            $class_ativo = '';
        } else {
            $icone = 'fa-check-o';
            $titulo_link = 'Ativar Usuário';
            $acao = 'Sim';
            $class_ativo = '#c4c4c4';
        }

        $mostrar_adm = '';
        if(strtolower($nivel) === 'admin'){
            $senha = '******';
            $mostrar_adm = 'ocultar';
        }

        // Escapar saída para segurança (exemplo simples, pode usar htmlspecialchars)
        $nome_esc = htmlspecialchars($nome, ENT_QUOTES);
        $email_esc = htmlspecialchars($email, ENT_QUOTES);
        $telefone_esc = htmlspecialchars($telefone, ENT_QUOTES);
        $endereco_esc = htmlspecialchars($endereco, ENT_QUOTES);
        $nivel_esc = htmlspecialchars($nivel, ENT_QUOTES);
        $foto_esc = htmlspecialchars($foto, ENT_QUOTES);
        $ativo_esc = htmlspecialchars($ativo, ENT_QUOTES);
        $dataF_esc = htmlspecialchars($dataF, ENT_QUOTES);
        $senha_esc = htmlspecialchars($senha, ENT_QUOTES);

        echo <<<HTML
            <tr>
                <td>{$nome_esc}</td>
                <td class="esc">{$email_esc}</td>
                <td class="esc">{$telefone_esc}</td>
                <td class="esc">{$nivel}</td>
                <td>
                    <big><a href="#" onclick="editar('{$id}', '{$nome_esc}', '{$email_esc}', '{$telefone_esc}', '{$endereco_esc}', '{$nivel_esc}')" title="Editar Dados"><i class="fa fa-edit text-primary"></i></a></big>

                    <li class="dropdown head-dpdn2" style="display: inline-block">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                            <big>
                                <i class="fa fa-trash-o text-danger"></i>
                            </big>
                        </a>

                        <ul class="dropdown-menu" style="margin-left: -230px;">
                            <li>
                                <div class="notification_desc2">
                                    <p>Confirmar Exclusão? <a href="#" onclick="excluir('{$id}')"><span class="text-danger">Sim</span></a></p>
                                </div>
                            </li>
                        </ul>
                    </li>

                    <big>
                        <a href="#" onclick="mostrar('{$nome_esc}', '{$email_esc}', '{$telefone_esc}', '{$endereco_esc}', '{$ativo_esc}', '{$dataF_esc}', '{$senha_esc}', '{$nivel_esc}', '{$foto_esc}')" title="Mostrar Dados">
                            <i class="fa fa-info-circle text-primary"></i>
                        </a>
                    </big>

                    <big>
                        <a href="#" onclick="ativar('{$id}', '{$acao}')" title="{$titulo_link}">
                            <i class="fa {$icone} text-success"></i>
                        </a>
                    </big>

                    <big>
                        <a class="{$mostrar_adm}" href="#" onclick="permissoes('{$id}', '{$nome_esc}')" title="Dar permissões">
                            <i class="fa fa-lock text-primary"></i>
                        </a>
                    </big>

                </td>
            </tr>
        HTML;
    } // fecha o for

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
            "language": {
                "url": "//cdn.datatables.net/plug-ins/2.3.1/i18n/pt-BR.json"
            },
            "ordering": false,
            "stateSave": true
        });
    });
</script>

<!-- Funções para usuários -->
<script type="text/javascript">
    function editar(id, nome, email, telefone, endereco, nivel){
        $('#mensagem').text('');
        $('#titulo_inserir').text('Editar Registro');

        $('#id').val(id);
        $('#nome').val(nome);
        $('#email').val(email);
        $('#telefone').val(telefone);
        $('#endereco').val(endereco);
        $('#nivel').val(nivel).change();

        $('#modalForm').modal('show');
    }

    function mostrar(nome, email, telefone, endereco, ativo, data, senha, nivel, foto){
        $('#titulo_dados').text(nome);
        $('#email_dados').text(email);
        $('#telefone_dados').text(telefone);
        $('#endereco_dados').text(endereco);
        $('#ativo_dados').text(ativo);
        $('#data_dados').text(data);
        $('#nivel_dados').text(nivel);
        $('#foto_dados').attr("src","images/perfil/" + foto);

        $('#modalDados').modal('show');
    }

    function limparCampos(){
        $('#id').val('');
        $('#nome').val('');
        $('#email').val('');
        $('#telefone').val('');
        $('#endereco').val('');
        $('#ids').val('');
        $('#btn-deletar').hide();
    }
    
    function selecionar(id){
        var ids = $('#ids').val();

        if($('#seletor-'+id).is(":checked") == true){
            var novo_id = ids + id + '-';
            $('#ids').val(novo_id);
        }else{
            var retirar = ids.replace(id + '-', '');
            $('#ids').val(retirar);
        }

        var ids_final = $('#ids').val();
        if(ids_final == ""){
            $('#btn-deletar').hide();
        }else{
            $('#btn-deletar').show();
        }
    }

    function deletarSel(){
        var ids = $('#ids').val();
        var id = ids.split("-");
        for(i=0; i < id.length - 1; i++){
            excluir(id[i]);
        }

        limparCampos();
    }

    function permissoes(id, nome){
        $('#id_permissoes').val(id);
        $('#nome_permissoes').text(nome);

        $('#modalPermissoes').modal('show');
        listarPermissoes(id);
    }
</script>