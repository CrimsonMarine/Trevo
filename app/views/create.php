<?php $this->layout('master', ['title' => $title]) ?>

<div class="flexo prof-container" style="gap: 10px;">
    <div class="margin-to-left font-left pear-box-3 sub-p3">
        <div>
            <div class="ins-padd2 flexo flexCom">
                <form class="flexo flexRow" method="get" id="toggle-form">
                    <input type="radio" id="checkbox-toggle" name="tipo" value="postagem" style="display: none;"
                            <?php echo (isset($_GET['tipo']) && $_GET['tipo'] === 'postagem') ? 'checked' : ''; ?>>
                    <label for="checkbox-toggle" class="cli-pan1">Postagem</label>
                    
                    <input type="radio" id="checkbox-toggle1" name="tipo" value="video" style="display: none;"
                            <?php echo (isset($_GET['tipo']) && $_GET['tipo'] === 'video') ? 'checked' : ''; ?>>
                    <label for="checkbox-toggle1" class="cli-pan1">Vídeo</label>
                </form>
                <div class="back-s">
                    <div class="ins-padd1">
                        <?php
                            if (isset($_GET['tipo']) && $_GET['tipo'] === 'postagem') {
                                $hh = "Postagem";
                                $this->insert('layouts/type-1', ['hr' => $hh, 'mesg' => $message]);
                                
                                $this->insert('layouts/forms/postForm');
                                
                            } else if (isset($_GET['tipo']) && $_GET['tipo'] === 'video') {
                                $hh = "Vídeo";
                                $this->insert('layouts/type-1', ['hr' => $hh, 'mesg' => $message]);

                                $this->insert('layouts/forms/videoForm');
                            } else {
                                $hh = "Criar";
                                $this->insert('layouts/type-1', ['hr' => $hh, 'mesg' => $message]);
                                $message = 'Selecione o tipo de conteúdo que deseja criar.';
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

        document.getElementById('checkbox-toggle').addEventListener('change', () => {
            form.submit();
        });

        document.getElementById('checkbox-toggle1').addEventListener('change', () => {
            form.submit();
        });
    });
</script>
