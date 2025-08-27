<?php
require_once("../../../conexao.php");

// [CORREÇÃO] A query agora usa LEFT JOIN para buscar os dados do endereço associado ao utilizador
$query = $pdo->query("
    SELECT 
        u.id, u.nome, u.email, u.telefone, u.nivel, u.ativo, u.data, u.senha,
        e.id as endereco_id, e.cep, e.rua, e.numero, e.complemento, e.bairro, e.cidade, e.estado 
    FROM 
        usuarios u 
    LEFT JOIN 
        endereco e ON u.endereco_id = e.id 
    ORDER BY u.id DESC
");

$res = $query->fetchAll(PDO::FETCH_ASSOC);
$linhas = count($res);

if ($linhas > 0) {
    echo <<<HTML
    <small>
        <table class="table table-hover" id="tabela">
            <thead>
                <tr>
                    <th>Nome</th>
                    <th class="esc">Email</th>
                    <th class="esc">Telefone</th>
                    <th class="esc">Nível</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
    HTML;
    
    foreach ($res as $item) {
        $id = $item['id'];
        $nome = htmlspecialchars($item['nome'], ENT_QUOTES);
        $email = htmlspecialchars($item['email'], ENT_QUOTES);
        $telefone = htmlspecialchars($item['telefone'] ?? '', ENT_QUOTES);
        $nivel = htmlspecialchars($item['nivel'], ENT_QUOTES);
        $ativo = $item['ativo'];
        $dataF = $item['data'] ? date('d/m/Y', strtotime($item['data'])) : '';
        $senha = '******'; // A senha nunca deve ser exposta

        // [CORREÇÃO] Pega os dados individuais do endereço
        $endereco_id = $item['endereco_id'] ?? '';
        $cep = htmlspecialchars($item['cep'] ?? '', ENT_QUOTES);
        $rua = htmlspecialchars($item['rua'] ?? '', ENT_QUOTES);
        $numero = htmlspecialchars($item['numero'] ?? '', ENT_QUOTES);
        $complemento = htmlspecialchars($item['complemento'] ?? '', ENT_QUOTES);
        $bairro = htmlspecialchars($item['bairro'] ?? '', ENT_QUOTES);
        $cidade = htmlspecialchars($item['cidade'] ?? '', ENT_QUOTES);
        $estado = htmlspecialchars($item['estado'] ?? '', ENT_QUOTES);

        // Lógica para ícone de ativação
        if ($ativo === 'Sim') {
            $icone = 'fa-check-square';
            $titulo_link = 'Desativar Utilizador';
            $acao = 'Não';
        } else {
            $icone = 'fa-square-o'; // Ícone diferente para 'Não'
            $titulo_link = 'Ativar Utilizador';
            $acao = 'Sim';
        }
        
        $ocultar_adm = (strtolower($nivel) === 'admin') ? 'ocultar' : '';

        // [CORREÇÃO] As funções onclick agora passam todos os parâmetros do endereço
        echo <<<HTML
            <tr>
                <td>{$nome}</td>
                <td class="esc">{$email}</td>
                <td class="esc">{$telefone}</td>
                <td class="esc">{$nivel}</td>
                <td>
                    <big><a href="#" onclick="editar('{$id}', '{$nome}', '{$email}', '{$telefone}', '{$nivel}', '{$cep}', '{$rua}', '{$numero}', '{$complemento}', '{$bairro}', '{$cidade}', '{$estado}', '{$endereco_id}')" title="Editar Dados"><i class="fa fa-edit text-primary"></i></a></big>

                    <li class="dropdown head-dpdn2" style="display: inline-block;">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-expanded="false"><big><i class="fa fa-trash-o text-danger"></i></big></a>
                        <ul class="dropdown-menu" style="margin-left: -230px;">
                            <li>
                                <div class="notification_desc2">
                                    <p>Confirmar Exclusão? <a href="#" onclick="excluir('{$id}')"><span class="text-danger">Sim</span></a></p>
                                </div>
                            </li>
                        </ul>
                    </li>

                    <big><a href="#" onclick="mostrar('{$nome}', '{$email}', '{$telefone}', '{$nivel}', '{$ativo}', '{$dataF}', '{$cep}', '{$rua}', '{$numero}', '{$bairro}', '{$cidade}', '{$estado}')" title="Mostrar Dados"><i class="fa fa-info-circle text-secondary"></i></a></big>

                    <big><a href="#" onclick="ativar('{$id}', '{$acao}')" title="{$titulo_link}"><i class="fa {$icone} text-success"></i></a></big>

                    <big><a class="{$ocultar_adm}" href="#" onclick="permissoes('{$id}', '{$nome}')" title="Dar permissões"><i class="fa fa-lock text-primary"></i></a></big>
                </td>
            </tr>
        HTML;
    }
    
    echo <<<HTML
            </tbody>
        </table>
    </small>
    HTML;

} else {
    echo '<small>Nenhum Registo Encontrado</small>';
}
?>

<script type="text/javascript">
    $(document).ready(function() {
        $('#tabela').DataTable({
            "ordering": false,
            "stateSave": true
        });
    });
</script>