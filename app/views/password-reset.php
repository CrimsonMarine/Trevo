<?php $this->layout('master', ['title' => $title, 'message' => $message, 'token' => $token]) ?>

<h1>Alteração de Senha</h1>
<div class="sub-container">
    <div class="flexo pag-liner">
        <div class="margin-to-left font-left sub-p4 flexo flexCom" style="gap: 5px;">
            <div class="marginV-top">
                <strong><b class="font-mid-large marginV-none" style="margin-right: 5px; color: red; text-decoration:underline;">NÃO</b>passe este link para ninguém.</strong>
            </div>
            <h4 style="color: red; text-decoration:underline;" class="marginV-none"><?= $message ?></h4>
            <form method="POST" class="pear-box-2 flexo flexCom">
                <div>
                    <label for="password">Nova Senha:</label>
                    <br>
                    <input class="round-b" type="password" name="password" id="password" required>
                    <br>
                    <label for="passwordRep">Repita a Senha:</label>
                    <br>
                    <input class="round-b" type="password" name="passwordRep" id="passwordRep" required>
                    <br>
                    <input type="hidden" name="token" value="<?= htmlspecialchars($token) ?>">
                    <input class="ser-button marginV-bottom" type="submit" value="Enviar">
                </div>
            </form>
            <a href="/login">Fazer Login</a>
        </div>
    </div>
    <div class="flexo pag-liner" style="gap: 10px;">
        <div class="font-left sub-p4">
            <div class="flexo flexCom" style="gap: 2px;">
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
    
</div>
