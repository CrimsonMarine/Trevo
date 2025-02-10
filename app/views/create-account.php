<?php $this->layout('master', ['title' => $title])
?>

<?php $this->insert('layouts/type-1', ['hr' => $hh, 'mesg' => $message])?>

<div class="sub-container">
    <div class="pag-liner">
        <h3 class="marginV-none">Não possui uma conta?</h3>
        <h4>Certifique-se de ler as <a href="#">Política de Privacidade</a> e <a href="#">Termos de Uso.</a></h4>
        <script src="assets/js/dropdownElement.js"></script>
        <div class="pear-box-1">
            <form class="ins-padd1" method="post" action="">
                <div>
                    <label for="username">Nome de Usuário:</label>
                    <br>
                    <input class="round-b" type="text" name="username" id="inputTo50" required autocomplete="off">
                    <b><span id="textTo50"></span>/50</b>
                    <script src="/assets/js/verifyString.js"></script>
                </div>
                <div>
                    <label for="email" autocomplete="off">Email:</label>
                    <br>
                    <input class="round-b" type="text" name="email" id="email" required autocomplete="off">
                </div>
                <div>
                    <label for="password">Senha:</label>
                    <br>
                    <input class="round-b" type="password" name="password" id="password" required>
                </div>
                <div>
                    <label for="confirmpassword">Confirmar Senha:</label>
                    <br>
                    <input class="round-b" type="password" name="confirmpassword" id="confirmpassword" required>
                </div>
                <div>
                    <label for="birthday">Aniversário:</label>
                    <br>
                    <input class="round-b" type="date" name="birthday" id="birthday" required>
                </div>
                <div>
                    <?php $this->insert('layouts/country-selc')?>
                </div>
                <div>
                    <input class="ser-button round-b type-b-a" type="submit" name="submit" id="SubmitForm" value="Cadastrar">
                </div>
            </form>
        </div>
        
    </div>
    <div class="pag-liner">
        <div class="margin-to-right">
            <p class="marginV-none">Já possue uma conta? <a href="/login">Entrar.</a></p>
            <h4>
            Por que criar uma conta?
            </h4>
            <nav>
                <ul>
                    <li>Compartilhar seus pensamentos e ideias por meio de postagens de texto curtas e dinâmicas.</li>
                </ul>   
                <ul>
                    <li>Fazer upload de vídeos, assistir conteúdos variados e interagir com outros criadores.</li>
                </ul>
                <ul>
                    <li>Participar de comunidades e grupos temáticos, conectando-se com pessoas que compartilham interesses semelhantes.</li>
                </ul>
                <ul>
                    <li>Trocar mensagens privadas com outros usuários de forma rápida.</li>
                </ul>
                <ul>
                    <li>Criar e gerenciar seu próprio blog pessoal, compartilhando artigos e histórias com sua audiência.</li>
                </ul>
                <ul>
                    <li>Personalizar amplamente seu perfil e configurações para refletir sua identidade e preferências.</li>
                </ul>
            </nav>
        </div>
    </div>
</div>