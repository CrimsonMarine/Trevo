<script src="/assets/js/verifyString.js"></script>
<form method="post" id="accountSet" class="flexo flexCom">
    <label class="tLabel1" for="username">Nome de Usuário:</label>
    <input style="max-width: 200px;" id="inputTo50" placeholder="<?= $username ?>" type="text" name="username" class="round-b">
    <b><span id="textTo50"></span>/50</b>
    <br>
    <label class="tLabel1" for="birthday">Aniversário:</label>
    <input style="max-width: fit-content;" type="date" name="birthday" id="birthday" class="round-b">
    <br>
    <div>
        <?php $this->insert('layouts/country-selc1', ['userCountry' => $userInfo['country']]);?>
    </div>
    <br>
    <input type="hidden" name="form-type" value="accountSetUsername">
    <input class="ser-button round-b" type="submit" value="Aplicar">
</form>