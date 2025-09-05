<?php
include 'header.php';

if (!isLoggedIn()) {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['id'];

// [CORREÇÃO] A query foi ajustada para fazer o JOIN da forma correta, usando e.usuario_id.
$stmt = $pdo->prepare("
    SELECT u.*, e.id as endereco_id, e.cep, e.rua, e.numero, e.complemento, e.bairro, e.cidade, e.estado 
    FROM usuarios u 
    LEFT JOIN endereco e ON u.id = e.usuario_id 
    WHERE u.id = ?
");
$stmt->execute([$user_id]);
$data = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$data) {
    echo "Utilizador não encontrado.";
    exit;
}
?>

<title>Meu Perfil | Ben-David</title>
<link rel="stylesheet" href="_css/perfil.css">

<main>
    <section class="profile-section">
        <div class="profile-container">
            <h1 class="profile-header">Meu Perfil</h1>

            <?php if (isset($_SESSION['feedback'])): ?>
                <div class="feedback-<?php echo $_SESSION['feedback']['type']; ?>">
                    <?php echo htmlspecialchars($_SESSION['feedback']['message']); unset($_SESSION['feedback']); ?>
                </div>
            <?php endif; ?>

            <div class="profile-content">
                <div class="profile-details">
                    <h2>Meus Dados</h2>
                    <form id="profile-form" action="atualizar_perfil.php" method="POST">
                        <input type="hidden" name="action" value="update_details">
                        <input type="hidden" name="endereco_id" value="<?php echo htmlspecialchars($data['endereco_id'] ?? ''); ?>">
                        
                        <div class="form-group">
                            <label>Nome</label>
                            <input type="text" name="nome" value="<?php echo htmlspecialchars($data['nome']); ?>" required>
                        </div>
                        <div class="form-group">
                            <label>Email</label>
                            <input type="email" value="<?php echo htmlspecialchars($data['email']); ?>" disabled>
                        </div>
                        <div class="form-group">
                            <label>Telefone</label>
                            <input type="text" name="telefone" value="<?php echo htmlspecialchars($data['telefone'] ?? ''); ?>">
                        </div>
                        
                        <h2>Meu Endereço</h2>
                        <div class="form-group">
                            <label>CEP</label>
                            <input type="text" id="cep" name="cep" value="<?php echo htmlspecialchars($data['cep'] ?? ''); ?>">
                        </div>
                        <div class="form-group">
                            <label>Rua</label>
                            <input type="text" id="rua" name="rua" value="<?php echo htmlspecialchars($data['rua'] ?? ''); ?>">
                        </div>
                        <div class="form-group">
                            <label>Número</label>
                            <input type="text" id="numero" name="numero" value="<?php echo htmlspecialchars($data['numero'] ?? ''); ?>">
                        </div>
                        <div class="form-group">
                            <label>Bairro</label>
                            <input type="text" id="bairro" name="bairro" value="<?php echo htmlspecialchars($data['bairro'] ?? ''); ?>">
                        </div>
                        <div class="form-group">
                            <label>Cidade</label>
                            <input type="text" id="cidade" name="cidade" value="<?php echo htmlspecialchars($data['cidade'] ?? ''); ?>">
                        </div>
                        <div class="form-group">
                            <label>Estado (UF)</label>
                            <input type="text" id="estado" name="estado" maxlength="2" value="<?php echo htmlspecialchars($data['estado'] ?? ''); ?>">
                        </div>

                        <button type="submit" class="btn-save">Salvar Alterações</button>
                    </form>
                </div>
            </div>
        </div>
    </section>
</main>
<script src="_js/getCEP.js"></script>

<?php include 'footer.php'; ?>