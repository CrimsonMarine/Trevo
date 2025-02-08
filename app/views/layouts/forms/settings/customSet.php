<script>
    document.addEventListener('DOMContentLoaded', function () {
        const elements = {
            maso: document.getElementById("maso"),
            maso1: document.getElementById("maso1"),
            maso2: document.getElementById("maso2"),
        };

        elements.maso.style.backgroundColor = '<?= $userCustomization['usern_color']?>';
        elements.maso1.style.backgroundColor = '<?= $userCustomization['pear_elementColor1']?>';
        elements.maso2.style.backgroundColor = '<?= $userCustomization['pear_elementColor2']?>';

        function handleColorChange(inputId, element) {
            document.getElementById(inputId).addEventListener("input", function () {
                const regex = /^#([A-Fa-f0-9]{6})$/;
                const errorMessage = document.getElementById("error-message");

                if (regex.test(this.value)) {
                    this.style.borderColor = "green";
                    errorMessage.style.display = "none";
                    element.style.backgroundColor = this.value;
                } else {
                    this.style.borderColor = "red";
                    errorMessage.style.display = "block";
                }
            });
        }

        handleColorChange("colorInput", elements.maso);
        handleColorChange("colorInput1", elements.maso1);
        handleColorChange("colorInput2", elements.maso2);
    });

</script>
<form method="post" id="customSet" class="flexo flexCom">
    <b class="font-mid" style="margin-bottom: 3px;">Cores</b>
    <small>(Apenas Hexadecimal)</small>
    <span id="error-message" style="color: red; display: none;">Formato inválido! Use #RRGGBB.</span>
    <br>
    <div>
        <div class="ins-padd1" style="border-left: 1px gray solid;">
            <label class="tLabel1" for="usern-color">Cor do Nome de Usuário:</label>
            <div class="flexo flexRow">
                <div id="maso" style="height: 20px; width: 35px;"></div>
                <input type="text" name="usern-color" class="round-dd" id="colorInput" placeholder="#RRGGBB" value="<?= $userCustomization['usern_color']?>" pattern="^#([A-Fa-f0-9]{6})$" required>
            </div>
            <br>
            <b class="font-small" style="margin-bottom: 3px;">Cor dos Containers do Perfil:</b>
            <label class="tLabel1" for="pear-element1">Inicio do gradiente:</label>
            <div class="flexo flexRow">
                <div id="maso2" style="height: 20px; width: 35px;"></div>
                <input type="text" name="pear-element1" class="round-dd" id="colorInput2" placeholder="#RRGGBB" value="<?= $userCustomization['pear_elementColor2']?>" pattern="^#([A-Fa-f0-9]{6})$" required>
            </div>
            <label class="tLabel1" for="pear-element">Fim do gradiente:</label>
            <div class="flexo flexRow">
                <div id="maso1" style="height: 20px; width: 35px;"></div>
                <input type="text" name="pear-element" class="round-dd" id="colorInput1" placeholder="#RRGGBB" value="<?= $userCustomization['pear_elementColor1']?>" pattern="^#([A-Fa-f0-9]{6})$" required>
            </div>
        </div>
    </div>
    
    <br>
    <input type="hidden" name="form-type" value="customSet">
    <input class="ser-button round-b" type="submit" value="Aplicar">
</form>