<?php $this->layout('master', ['title' => $title]) ?>

<div class="flexo prof-container" style="gap: 10px;">
    <div class="margin-to-left font-left pear-box-3 sub-p3">
        <div>
            <div class="ins-padd2 flexo flexCom">
                <form class="flexo flexRow" method="get" id="toggle-form">
                    <input type="radio" id="checkbox-toggle" name="tipo" value="conta" style="display: none;"
                            <?php echo (isset($_GET['tipo']) && $_GET['tipo'] === 'conta') ? 'checked' : ''; ?>>
                    <label for="checkbox-toggle" class="cli-pan1">Conta</label>
                    
                    <input type="radio" id="checkbox-toggle1" name="tipo" value="personalizacao" style="display: none;"
                            <?php echo (isset($_GET['tipo']) && $_GET['tipo'] === 'personalizacao') ? 'checked' : ''; ?>>
                    <label for="checkbox-toggle1" class="cli-pan1">Personalização</label>

                    <input type="radio" id="checkbox-toggle2" name="tipo" value="seguranca" style="display: none;"
                            <?php echo (isset($_GET['tipo']) && $_GET['tipo'] === 'seguranca') ? 'checked' : ''; ?>>
                    <label for="checkbox-toggle2" class="cli-pan1">Segurança</label>
                </form>
                <div class="back-s">
                    <div class="ins-padd1">
                        <?php
                            if (isset($_GET['tipo']) && $_GET['tipo'] === 'conta') {
                                $hh = "Conta";
                                $this->insert('layouts/type-1', ['hr' => $hh, 'mesg' => $message]);
                                
                                $this->insert('layouts/forms/settings/accountSet', ['userInfo' => $userInfo, 'username' => $username]);
                                
                            } else if (isset($_GET['tipo']) && $_GET['tipo'] === 'personalizacao') {
                                $hh = "Personalização";
                                $this->insert('layouts/type-1', ['hr' => $hh, 'mesg' => $message]);

                                $this->insert('layouts/forms/settings/customSet', ['userCustomization' => $userCustomization]);
                            } else if (isset($_GET['tipo']) && $_GET['tipo'] === 'seguranca') {
                                if (isset($_GET['config']) && $_GET['config'] === 'passwordRes') {
                                    $hh = "Recuperação de Senha";
                                    $this->insert('layouts/type-1', ['hr' => $hh, 'mesg' => $message]);
                                    $this->insert('layouts/forms/settings/otherSettings/passwordReset');
                                    
                                } else {
                                    $hh = "Segurança";
                                    $this->insert('layouts/type-1', ['hr' => $hh, 'mesg' => $message]);
                                    $this->insert('layouts/forms/settings/securSet');
                                }
                            
                             } else {
                                $hh = "Configurações";
                                $this->insert('layouts/type-1', ['hr' => $hh, 'mesg' => $message]);
                                $message = 'Configure ou personalize detalhes da sua conta aqui.
                                <p style="font-weight: 100;">Acesse o <a href="#">Guia de Personalização</a> para ter mais ideias</p>';
                                echo "<b style='color: #525252;'>$message</b>";
                            }
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('toggle-form');

    document.querySelectorAll('input[type="radio"]').forEach(input => {
        input.addEventListener('change', () => {
            const url = new URL(window.location.href);
            url.searchParams.set('tipo', input.value);

            if (input.dataset.config === "passwordRes") {
                url.searchParams.set('config', 'passwordRes');
            } else {
                url.searchParams.delete('config');
            }

            window.location.href = url.toString();
        });
    });
});

</script>
