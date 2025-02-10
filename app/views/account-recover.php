<?php $this->layout('master', ['title' => $title])?>

<?php $this->insert('layouts/type-1', ['hr' => $hh, 'mesg' => $message])?>
<div class="sub-container">
    <div class="pag-liner">
        <div class="margin-to-left flexo flexCom" style="gap: 15px;">
            <h4 class="marginV-none">Enviaremos um Link para redefinir a sua senha na sua caixa de email.</h4>
            <div class="pear-box-2">
                <form method="POST" class="flexo flexCom">
                    <label class="tLabel1" for="email">Insira o Email:</label>
                    <input type="email" class="round-b" name="email" id="forgotEmail">
                    <input type="hidden" name="form-type" value="recoverPassword">
                    <input class="ser-button round-b marginV-bottom" type="submit" value="Enviar">
                </form>
            </div>
            <a href="#">Esqueceu seu email?</a>
        </div>
    </div>
    <div class="pag-liner">
        <div class="margin-to-right flexo flexCom" style="gap: 2px;">
            <h3 class="marginV-none">Como evitar que roubem a sua conta?</h3>
            <nav>
                <ul>
                    <li>Não envie seus dados de Login para outros usuários.</li>
                </ul>
                <ul>
                    <li>Nós te comunicaremos através da própria plataforma, tome cuidado com emails maliciosos.</li>
                </ul>
                <ul>
                    <li>Não insira nada no console do site caso alguém solicitar.</li>
                </ul>
            </nav>
            <p class="marginV-none">Mais detalhes em <a href="#">Política de Privacidade</a></p>
        </div>
    </div>
    
</div>