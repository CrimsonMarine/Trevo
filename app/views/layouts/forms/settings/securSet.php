<input type="radio" id="checkbox-toggle3" name="tipo" value="seguranca" style="display: none;"
    <?php echo (isset($_GET['tipo']) && $_GET['tipo'] === 'seguranca' && isset($_GET['config']) && $_GET['config'] === 'passwordRes') ? 'checked' : ''; ?>
    data-config="passwordRes">
<label for="checkbox-toggle3" class="a-f">Recuperação de Senha</label>
<?php 
