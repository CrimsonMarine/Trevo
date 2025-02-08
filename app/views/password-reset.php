<?php $this->layout('master', ['title' => $title, 'message' => $message, 'token' => $token]) ?>

<div style="font-size:150%">
    <h2><?= $message ?></h2>
    <div class="flexRow flexoNoN">
        <b class="font-mid-large" style="margin-right: 5px; color: red; text-decoration:underline;">NÃO</b>
        <strong>passe este link para ninguém.</strong>
    </div>
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
</div>
