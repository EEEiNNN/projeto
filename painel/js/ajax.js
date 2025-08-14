$(document).ready(function(){
    listar();
});

function listar(){

    $.ajax({
        url: 'paginas/'+ pag +'/listar.php',
        method: 'POST',
        data: {},
        dataType: "html",

        success: function(result){
            $("#listar").html(result);
            $('#mensagem-excluir').text('');
            
        }
    });
}

$(document).ready(function () {
    $("#form").submit(function (event) {
        event.preventDefault();

        const formData = new FormData(this);

        $.ajax({
            url: 'paginas/'+ pag +'/inserir.php',
            type: 'POST',
            data: formData,
            success: function (mensagem) {
                $('#mensagem').removeClass();

                if (mensagem.trim() === "Salvo com Sucesso") {
                    $('#form')[0].reset();
                    $('#btn-fechar').click();
                    listar();
                } else {
                    $('#mensagem').addClass('text-danger');
                    $('#mensagem').text(mensagem);
                }
            },
            cache: false,
            contentType: false,
            processData: false
        });
    });
});


function excluir(id) {
    if (confirm("Deseja realmente excluir este usuário?")) {
        $.ajax({
            url: 'paginas/'+ pag +'/excluir.php',
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
