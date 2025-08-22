<?php
    $pag = 'usuarios';
?>
<a type="button" class="btn btn-primary" data-toggle="modal" data-target="#modalForm">
    <span class="fa fa-plus"></span> Novo Utilizador
</a>

<div class="bs-example widget-shadow" style="padding:15px" id="listar">
    </div>

<div class="modal fade" id="modalForm" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-lg"> <div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title" id="exampleModalLabel"><span id="titulo_inserir"></span></h4>
				<button id="btn-fechar" type="button" class="close" data-dismiss="modal" aria-label="Close" style="margin-top: -25px">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<form id="form">
				<div class="modal-body">
                    <h5>Dados do Utilizador</h5>
                    <hr>
					<div class="row">
						<div class="col-md-6">							
							<label>Nome</label>
							<input type="text" class="form-control" id="nome" name="nome" placeholder="Nome Completo" required>							
						</div>
						<div class="col-md-6">							
							<label>Email</label>
							<input type="email" class="form-control" id="email" name="email" placeholder="Email do Utilizador" required>							
						</div>
					</div>
					<div class="row">
						<div class="col-md-6">							
							<label>Telefone</label>
							<input type="text" class="form-control" id="telefone" name="telefone" placeholder="Telefone">							
						</div>
						<div class="col-md-6">							
							<label>Nível</label>
							<select class="form-control" id="nivel" name="nivel">
                                <option value="user">Utilizador</option>
                                <option value="admin">Administrador</option>
                            </select>
						</div>	
					</div>
                    <br>

                    <h5>Endereço</h5>
                    <hr>
                    <div class="row">
                        <div class="col-md-4">
                            <label>CEP</label>
                            <input type="text" class="form-control" id="cep" name="cep" placeholder="00000-000">
                        </div>
                        <div class="col-md-8">
                            <label>Rua</label>
                            <input type="text" class="form-control" id="rua" name="rua" placeholder="Nome da Rua">
                        </div>
                    </div>
                    <div class="row mt-2">
                        <div class="col-md-3">
                            <label>Número</label>
                            <input type="text" class="form-control" id="numero" name="numero" placeholder="Nº">
                        </div>
                        <div class="col-md-5">
                            <label>Complemento</label>
                            <input type="text" class="form-control" id="complemento" name="complemento" placeholder="Apto, Bloco, etc.">
                        </div>
                         <div class="col-md-4">
                            <label>Bairro</label>
                            <input type="text" class="form-control" id="bairro" name="bairro" placeholder="Bairro">
                        </div>
                    </div>
                    <div class="row mt-2">
                        <div class="col-md-8">
                            <label>Cidade</label>
                            <input type="text" class="form-control" id="cidade" name="cidade" placeholder="Cidade">
                        </div>
                        <div class="col-md-4">
                            <label>Estado</label>
                            <input type="text" class="form-control" id="estado" name="estado" placeholder="Ex: RS" maxlength="2">
                        </div>
                    </div>

					<input type="hidden" id="id" name="id">
                    <input type="hidden" id="endereco_id" name="endereco_id">

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

<script type="text/javascript"> var pag = "<?=$pag?>"; </script>
<script src="js/ajax.js"></script>

<script>
    // Função de editar atualizada para receber todos os novos campos de endereço
    function editar(id, nome, email, telefone, nivel, cep, rua, numero, complemento, bairro, cidade, estado, endereco_id){
        $('#mensagem').text('');
        $('#titulo_inserir').text('Editar Registo');

        $('#id').val(id);
        $('#nome').val(nome);
        $('#email').val(email);
        $('#telefone').val(telefone);
        $('#nivel').val(nivel).change();
        
        // Preenche os campos de endereço
        $('#cep').val(cep);
        $('#rua').val(rua);
        $('#numero').val(numero);
        $('#complemento').val(complemento);
        $('#bairro').val(bairro);
        $('#cidade').val(cidade);
        $('#estado').val(estado);
        $('#endereco_id').val(endereco_id);

        $('#modalForm').modal('show');
    }
</script>