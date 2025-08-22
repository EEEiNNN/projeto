<?php
// Inclui o header, que já inicia a sessão e a conexão
include 'header.php';

// Segurança: Garante que apenas utilizadores logados acedam a esta página
if (!isLoggedIn()) {
    header('Location: login.php');
    exit;
}

// Busca os dados atuais do utilizador na base de dados
$user_id = $_SESSION['id'];
$stmt = $pdo->prepare("SELECT nome, email, endereco, telefone FROM usuarios WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// Pega mensagens de feedback da sessão (se existirem)
$feedback = $_SESSION['feedback'] ?? null;
unset($_SESSION['feedback']); // Limpa a mensagem após lê-la
?>

<title>Meu Perfil | Ben-David</title>
<link rel="stylesheet" href="_css/perfil.css">

<main>
    <section class="profile-section">
        <div class="profile-container">
            <h1 class="profile-header">Meu Perfil</h1>

            <?php if ($feedback): ?>
                <div class="feedback-message <?php echo $feedback['type'] === 'success' ? 'feedback-success' : 'feedback-error'; ?>">
                    <?php echo htmlspecialchars($feedback['message']); ?>
                </div>
            <?php endif; ?>

            <form action="atualizar_perfil.php" method="POST" class="profile-form">
                <input type="hidden" name="action" value="update_details">
                <h2>Dados Pessoais</h2>
                <div class="form-grid">
                    <div class="form-group">
                        <label for="nome">Nome Completo</label>
                        <input type="text" id="nome" name="nome" value="<?php echo htmlspecialchars($user['nome']); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" readonly>
                    </div>
                    <div class="form-group">
                        <label for="telefone">Telefone</label>
                        <input type="text" id="telefone" name="telefone" value="<?php echo htmlspecialchars($user['telefone'] ?? ''); ?>">
                    </div>
                    <div class="form-group full-width">
                        <label for="endereco">Endereço</label>
                        <input type="text" id="endereco" name="endereco" value="<?php echo htmlspecialchars($user['endereco'] ?? ''); ?>">
                    </div>
                </div>
                <button type="submit" class="btn-submit">Salvar Alterações</button>
            </form>

            <form action="atualizar_perfil.php" method="POST" class="profile-form">
                <input type="hidden" name="action" value="change_password">
                <h2>Alterar Senha</h2>
                <div class="form-grid">
                    <div class="form-group full-width">
                        <label for="senha_atual">Senha Atual</label>
                        <input type="password" id="senha_atual" name="senha_atual" required>
                    </div>
                    <div class="form-group">
                        <label for="nova_senha">Nova Senha</label>
                        <input type="password" id="nova_senha" name="nova_senha" minlength="6" required>
                    </div>
                    <div class="form-group">
                        <label for="confirma_senha">Confirmar Nova Senha</label>
                        <input type="password" id="confirma_senha" name="confirma_senha" required>
                    </div>
                </div>
                <button type="submit" class="btn-submit">Alterar Senha</button>
            </form>
        </div>
    </section>
</main>

<?php
// Inclui o rodapé, que fecha o documento HTML e carrega os scripts globais
include 'footer.php';
?>