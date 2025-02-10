<?php $this->layout('master', ['title' => $title])
?>

<?php $this->insert('layouts/type-1', ['hr' => $hh, 'mesg' => $message])?>

<div class="sub-container">
    <div class="pag-liner">
        <div class="margin-to-left">
            <h3 class="marginV-none">Já possui uma conta?</h3>
            <h4><a href="/account-recover">Não consigo acessar minha conta.</a></h4>
            <div class="pear-box-1">
                <form class="ins-padd1" method="post" action="">
                    <div>
                        <label for="email">Email:</label>
                        <br>
                        <input type="text" class="round-b" name="email" id="Email" autocomplete="off">
                    </div>
                    <div>
                        <label for="password">Senha:</label>
                        <br>
                        <input type="password" class="round-b" name="password" id="Password" autocomplete="off">
                    </div>
                    <div>
                        <input class="ser-button round-b type-b-a" type="submit" name="submit" id="SubmitForm" value="Entrar">
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="pag-liner">
        <div class="margin-to-right">
            <h4 class="marginV-none">Certifique-se de ler as <a href="#">Política de Privacidade</a> e <a href="#">Termos de Uso.</a></h4>
            <p>Não possue uma conta? <a href="/signup">Cadastre-Se.</a></p>
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



