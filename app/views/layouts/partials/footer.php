<footer class="footer-master">
    <div class="footer-main">
        <div class="footer-wrapper">
            <a class="a-stylezer2" href="#">Sobre</a>
            <a class="a-stylezer2" href="/contact">Contato</a>
            <a class="a-stylezer2" href="#">Api</a>
            <a class="a-stylezer2" href="#">Blog</a>
            <a class="a-stylezer2" href="#">Política de Privacidade</a>
            <a href="#">Termos de Uso</a>
        </div>
        <div class="footer-wrapper-1">
            <a href="/"><div class="logo-footer"></div></a>
            <div class="footer-wrapper-1-sub1 flexCom flexo back-2 ins-padd3">
                <nav>
                <?php 
                    if ($_SESSION['type-user'] == 'user') {
                        echo "
                        <a class='bold-t a-stylezer2' href='/logout'>Sair</a>
                        <a class='bold-t a-stylezer2' href='/settings'>Configurações</a>
                        ";
                        if (isset($_SESSION['user-info']['user_url'])) {
                            $username = htmlspecialchars($_SESSION['user-info']['username'] ?? '', ENT_QUOTES, 'UTF-8');
                            $userIdUrl = htmlspecialchars($_SESSION['user-info']['user_url'] ?? '', ENT_QUOTES, 'UTF-8');
                            echo "
                            <a class='bold-t' href='/user/{$userIdUrl}'>{$username}</a>
                            ";
                        }
                    } else if ($_SESSION['type-user'] == 'guest') {
                        echo '
                        <a class="a-stylezer2 bold-t" href="/signup">Cadastrar</a>
                        <a class="bold-t" href="/login">Entrar</a>
                        ';
                    }
                ?>
                </nav>
            </div>
        </div>
    </div>
</footer>