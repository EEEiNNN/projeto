<?php
require_once("conexao.php");

// Lógica para criar o usuário administrador padrão.
// Isso só será executado se a tabela de usuários estiver vazia.
try {
    $query = $pdo->query("SELECT id FROM usuarios LIMIT 1");
    if ($query->rowCount() == 0) {
        $senha_hash = password_hash('123', PASSWORD_DEFAULT);
        $pdo->prepare(
            "INSERT INTO usuarios (nome, email, senha, endereco, nivel, ativo, foto) 
             VALUES ('Administrador', 'admin@admin.com', ?, 'Endereço Padrão', 'admin', 'Sim', 'sem-foto.jpg')"
        )->execute([$senha_hash]);
    }
} catch (PDOException $e) {
    // Se a tabela ainda não existir, ignora o erro por enquanto.
    // O ideal seria ter um script de instalação separado.
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Acesso ao Sistema</title>
    <link rel="stylesheet" href="_css/login.css">
    <link rel="shortcut icon" type="image/x-icon" href="_img/icone.svg">
    <style>
        /* Estilos adicionais para a troca de abas */
        .form-container { display: none; }
        .form-container.active { display: block; }
        .tab-buttons { text-align: center; margin-bottom: 20px; }
        .tab-button {
            background: none; border: none; color: #8f8f8f;
            font-size: 18px; padding: 10px 15px; cursor: pointer;
            font-weight: 500; transition: 0.3s;
        }
        .tab-button.active { color: #fff; border-bottom: 2px solid #45f3ff; }
        .feedback-message { 
            text-align: center; margin-top: 15px; padding: 10px; border-radius: 5px;
            font-size: 0.9em; display: none;
        }
        .feedback-message.success { background-color: #28a745; color: white; }
        .feedback-message.error { background-color: #dc3545; color: white; }
    </style>
</head>
<body>
    <div class="box" style="height: auto; min-height: 450px;">
        <div class="figura"><a href="index.php">B</a></div>
        <span class="borderLine"></span>
        
        <div class="tab-buttons">
            <button class="tab-button active" data-form="login">Entrar</button>
            <button class="tab-button" data-form="register">Cadastrar</button>
        </div>

        <div id="login-form" class="form-container active">
            <form id="form-login">
                <input type="hidden" name="action" value="login">
                <div class="inputBox">
                    <input type="email" name="email" required="required">
                    <span>Email</span><i></i>
                </div>
                <div class="inputBox">
                    <input type="password" name="senha" required="required">
                    <span>Senha</span><i></i>
                </div>
                <div class="links">
                    <a href="#">Esqueci a senha</a>
                </div>
                <input type="submit" value="Login">
            </form>
        </div>

        <div id="register-form" class="form-container">
            <form id="form-register">
                <input type="hidden" name="action" value="register">
                <div class="inputBox">
                    <input type="text" name="usuario" required>
                    <span>Nome</span><i></i>
                </div>
                <div class="inputBox">
                    <input type="email" name="email" required>
                    <span>Email</span><i></i>
                </div>
                <div class="inputBox">
                    <input type="password" name="senha" required>
                    <span>Senha (mín. 6 caracteres)</span><i></i>
                </div>
                <input type="submit" value="Cadastrar">
            </form>
        </div>
        
        <div id="feedback" class="feedback-message"></div>
    </div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const tabButtons = document.querySelectorAll('.tab-button');
    const formContainers = document.querySelectorAll('.form-container');
    const feedbackDiv = document.getElementById('feedback');
    const formLogin = document.getElementById('form-login');
    const formRegister = document.getElementById('form-register');

    // Lógica para alternar entre abas de login e cadastro
    tabButtons.forEach(button => {
        button.addEventListener('click', () => {
            const formId = button.dataset.form;

            tabButtons.forEach(btn => btn.classList.remove('active'));
            button.classList.add('active');

            formContainers.forEach(container => {
                container.classList.remove('active');
                if (container.id === `${formId}-form`) {
                    container.classList.add('active');
                }
            });
            feedbackDiv.style.display = 'none'; // Esconde mensagens ao trocar de aba
        });
    });

    // Função para exibir mensagens de feedback
    const showFeedback = (message, isSuccess) => {
        feedbackDiv.textContent = message;
        feedbackDiv.className = 'feedback-message'; // Reseta as classes
        feedbackDiv.classList.add(isSuccess ? 'success' : 'error');
        feedbackDiv.style.display = 'block';
    };

    // Lógica para enviar o formulário de login via AJAX
    formLogin.addEventListener('submit', async (e) => {
        e.preventDefault();
        const formData = new FormData(formLogin);
        const submitButton = formLogin.querySelector('input[type="submit"]');
        submitButton.disabled = true;
        submitButton.value = 'Aguarde...';

        try {
            const response = await fetch('autenticar.php', {
                method: 'POST',
                body: formData
            });
            const data = await response.json();

            if (data.success) {
                showFeedback(data.message, true);
                window.location.href = data.redirect;
            } else {
                showFeedback(data.message, false);
            }
        } catch (error) {
            showFeedback('Erro de conexão. Verifique sua internet.', false);
        } finally {
            submitButton.disabled = false;
            submitButton.value = 'Login';
        }
    });

    // Lógica para enviar o formulário de cadastro via AJAX
    formRegister.addEventListener('submit', async (e) => {
        e.preventDefault();
        const formData = new FormData(formRegister);
        const submitButton = formRegister.querySelector('input[type="submit"]');
        submitButton.disabled = true;
        submitButton.value = 'Enviando...';

        try {
            const response = await fetch('autenticar.php', {
                method: 'POST',
                body: formData
            });
            const data = await response.json();

            showFeedback(data.message, data.success);
            if (data.success) {
                formRegister.reset();
                // Opcional: Mudar para a aba de login após o sucesso
                document.querySelector('.tab-button[data-form="login"]').click();
            }
        } catch (error) {
            showFeedback('Erro de conexão. Verifique sua internet.', false);
        } finally {
            submitButton.disabled = false;
            submitButton.value = 'Cadastrar';
        }
    });
});
</script>
</body>
</html>