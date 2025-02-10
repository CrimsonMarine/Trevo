<script src="/assets/js/verifyString.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const pfpInput = document.getElementById('pfp');
        const pfpPreview = document.getElementById('pfpPreview');
        pfpInput.addEventListener('change', function () {
            const [file] = pfpInput.files;
            if (file) {
                pfpPreview.src = URL.createObjectURL(file);
            }
        });
    });
</script>
<form method="post" id="accountSet" class="flexo flexCom" enctype="multipart/form-data">
    <label class="tLabel1" for="username">Nome de Usuário:</label>
    <input style="max-width: 200px;" id="inputTo50" placeholder="<?= $username ?>" type="text" name="username" class="round-b">
    <b><span id="textTo50"></span>/50</b>
    <br>
    <label class="tLabel1" for="pfp">Foto de Perfil:</label>
    <input style="max-width: fit-content;" type="file" accept="image/jpeg, image/png, image/gif" name="pfp" id="pfp" class="round-b">
    <img class="marginV" id="pfpPreview" style="border: 2px gray dashed;" src="<?= $userInfo['profile_picture'] ?>" height="115px" width="112px">
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