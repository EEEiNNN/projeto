<?php
include 'header.php';

// Inicia o autoload do Composer e as classes necessárias
require_once 'vendor/autoload.php';
require_once 'config.php'; // Incluindo o config.php

use Endroid\QrCode\QrCode;
use Endroid\QrCode\ErrorCorrectionLevel;

// Verifica se o usuário está logado e se existe um pedido pendente na sessão
if (!isLoggedIn() || !isset($_SESSION['pedido_pendente'])) {
    header('Location: index.php');
    exit;
}

// Pega os dados da sessão
$pedido_pendente = $_SESSION['pedido_pendente'];
$total = $pedido_pendente['total'];
$txid = 'PEDIDO' . $pedido_pendente['usuario_id'] . time(); // Cria um TXID único

$pixKey = PIX_KEY; 
$merchantName = 'MARCELO DE DAVID CEZIMBRA'; 
$merchantCity = 'PORTO ALEGRE'; 

function formatPixField($id, $value) {
    $len = str_pad(strlen($value), 2, '0', STR_PAD_LEFT);
    return $id . $len . $value;
}

$payload = '000201'
         . formatPixField('26', '0014br.gov.bcb.pix' . formatPixField('01', $pixKey))
         . '52040000' . '5303986'
         . formatPixField('54', number_format($total, 2, '.', ''))
         . '5802BR'
         . formatPixField('59', preg_replace('/[^a-zA-Z0-9\s]/', '', $merchantName))
         . formatPixField('60', preg_replace('/[^a-zA-Z0-9\s]/', '', $merchantCity))
         . formatPixField('62', formatPixField('05', '***'))
         . '6304';

function crc16($payload) {
    $polynomial = 0x1021; $result = 0xFFFF;
    for ($offset = 0; $offset < strlen($payload); $offset++) {
        $result ^= (ord($payload[$offset]) << 8);
        for ($bitwise = 0; $bitwise < 8; $bitwise++) {
            if (($result <<= 1) & 0x10000) $result ^= $polynomial;
            $result &= 0xFFFF;
        }
    }
    return $result;
}

$crc = crc16($payload);
$payload .= sprintf('%04X', $crc);
// --- Fim da lógica do Payload ---

// --- Gerando o QR Code ---
$qrCode = new QrCode($payload);
$qrCode->setSize(300);
$qrCode->setMargin(10);
$qrCode->setErrorCorrectionLevel(ErrorCorrectionLevel::HIGH());
$qrCodeDataUri = $qrCode->writeDataUri();
?>

<title>Pagamento PIX | Ben-David</title>
<link rel="stylesheet" href="_css/pix.css"> <main>
    <section class="pix-section">
        <div class="pix-container">
            <h1 class="pix-header">Finalize seu Pagamento</h1>
            <p>Para concluir, faça o pagamento de <strong>R$ <?php echo number_format($total, 2, ',', '.'); ?></strong> e clique no botão de confirmação abaixo.</p>

            <div class="pix-details">
                <div class="pix-qrcode">
                    <img src="<?php echo $qrCodeDataUri; ?>" alt="QR Code PIX">
                </div>
                <div class="pix-key">
                    <h2>PIX Copia e Cola</h2>
                    <textarea id="pix-key-input" readonly rows="4"><?php echo htmlspecialchars($payload); ?></textarea>
                    <button onclick="copyPixKey()">Copiar Código</button>
                    <p id="copy-success" style="display:none; color: green; margin-top: 10px;">Copiado!</p>
                </div>
            </div>

            <div class="pix-actions">
                <p>Após realizar o pagamento no app do seu banco, clique no botão abaixo para confirmar seu pedido.</p>
                <form action="confirmar_pagamento.php" method="POST">
                    <button type="submit" class="btn-pix-confirm">Confirmar Pedido</button>
                </form>
            </div>
        </div>
    </section>
</main>

<script>
function copyPixKey() {
    var copyText = document.getElementById("pix-key-input");
    copyText.select();
    document.execCommand("copy");
    
    var successMessage = document.getElementById("copy-success");
    successMessage.style.display = "block";
    setTimeout(function(){ successMessage.style.display = "none"; }, 2000);
}
</script>

<?php include 'footer.php'; ?>