<link href="_css/footer.css" rel="stylesheet"> 
<footer class="footer">
    <div class="row-container">
        <div class="footer-links">
            <div class="col">
                <h4>Fale Conosco</h4>
                <div class="d-flex"><div class="icon"><i class="bx bxs-map"></i></div><span>Avenida João Wallig - Porto Alegre, RS</span></div>
                <div class="d-flex"><div class="icon"><i class="bx bxs-envelope"></i></div><span>Ben-David@contato.com</span></div>
                <div class="d-flex"><div class="icon"><i class="bx bxs-phone"></i></div><span>+55 51 99999-0000</span></div>
                <div class="icons d-flex">
                    <div class="icon"><i class="bx bxl-facebook"></i></div>
                    <div class="icon"><i class="bx bxl-twitter"></i></div>
                    <div class="icon"><i class="bx bxl-instagram"></i></div>
                    <div class="icon"><i class="bx bxl-youtube"></i></div>
                </div>
            </div>
            <div class="col">
                <h4>Categorias</h4>
                <a href="produtos.php?categoria=aneis">Anéis</a>
                <a href="produtos.php?categoria=brincos">Brincos</a>
                <a href="produtos.php?categoria=colares">Colares</a>
                <a href="produtos.php?categoria=pulseiras">Pulseiras</a>
            </div>
            <div class="col">
                <h4>Minha Conta</h4>
                <a href="<?php echo isLoggedIn() ? 'perfil.php' : 'login.php'; ?>">Perfil</a>
                <a href="#">Trocas</a>
                <a href="meus-pedidos.php">Histórico de Compras</a>
            </div>
        </div>
    </div>
    <p class="color">&copy; <?php echo date('Y'); ?> Ben-David. Todos os direitos reservados.<br /></p>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>

<script src="_js/script.js"></script>

</body>
</html>