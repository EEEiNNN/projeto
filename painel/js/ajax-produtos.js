function carregarCategoriasProduto(){
    $.ajax({
        url: 'paginas/' + pag + '/carregar_categorias.php',
        method: 'GET', // alterar para GET
        dataType: "html",
        success:function(result){
            $("#categoria_id").html(result);
        },
        error: function(xhr, status, error){
            console.log("Erro ao carregar categorias:", error);
        }
    });
}


// Listar produtos
function listarProdutos(){
    $.ajax({
        url: 'paginas/' + pag + '/listar_produtos.php',
        method: 'POST',
        dataType: "html",
        success:function(result){
            $("#listar").html(result);
        }
    });
}

// Enviar formulário
$("#form").submit(function(event){
    event.preventDefault();

    $.ajax({
        url: 'paginas/' + pag + '/inserir_produto.php',
        method: 'POST',
        data: $(this).serialize(),
        success:function(result){
            $("#mensagem").html(result);

            if(result.includes("sucesso")){
                $('#modalForm').modal('hide');
                listarProdutos();
            }
        }
    });
});

function excluirProduto(id) {
    if (confirm("Deseja realmente excluir este produto?")) {
        $.ajax({
            url: 'paginas/'+ pag +'/excluir_produto.php',
            type: 'POST',
            data: { id: id },
            success: function (mensagem) {
                if (mensagem.trim() === "Excluído com Sucesso") {
                    listar();
                } else {
                    alert("Erro: " + mensagem);
                }
            }
        });
    }
}