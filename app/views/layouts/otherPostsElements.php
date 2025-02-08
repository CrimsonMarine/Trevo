<div class="flexo flexRow margin-to-right">
    <div class="icon comments flexo margin-to-right"></div>
    <?php
    if (isset($_SESSION['user-info']['userId'])) {
        $loggedId = htmlspecialchars($_SESSION['user-info']['userId'] ?? '', ENT_QUOTES, 'UTF-8');
        if ($loggedId == (string)$post['idAuthor']) : ?>
            <form class="margin-to-right" action="/delete-post/<?php echo $post['idAuthor'];?>/<?php echo $post['id']; ?>" method="POST">
                <input type="hidden" name="_method" value="DELETE">
                <input class="yen-button round-b" type="submit" value="Deletar">
            </form>
        <?php endif;
    }
    ?>
    <div style="margin-left: 5px;" id="dropdownPostMenu" class="circ-1 flexoNoN"><span>...</span></div>
    <span style="position: absolute;" class="dropOp" id="placerDrop"></span>
    <script>
        function copyClipboard() {
            var copy = window.location.href;

            navigator.clipboard.writeText(copy)
                .then(() => alert("Copiado!"))
        }

        document.addEventListener('DOMContentLoaded', function () {
            const dropDown = document.getElementById('dropdownPostMenu');
            const placer = document.getElementById('placerDrop');

            let menu = document.createElement('div');
            menu.classList.add('dropDown1', 'flexCom', 'flexo');
            menu.innerHTML = `
                <button class="semi-clickable">Compartilhar</button>
                <button class="semi-clickable">Reportar</button>
                <button class="semi-clickable" onclick="copyClipboard()">Copiar Link</button>
            `;

            dropDown.addEventListener('click', function (event) {
                event.stopPropagation();

                if (placer.contains(menu)) {
                    menu.style.maxHeight = "0";
                    menu.style.opacity = "0";
                    setTimeout(() => placer.removeChild(menu), 300);
                } else {
                    placer.appendChild(menu);
                    setTimeout(() => {
                        menu.style.maxHeight = "150px";
                        menu.style.opacity = "1";
                    }, 10);
                }
            });

            document.addEventListener('click', function () {
                if (placer.contains(menu)) {
                    menu.style.maxHeight = "0";
                    menu.style.opacity = "0";
                    setTimeout(() => placer.removeChild(menu), 300);
                }
            });
        });
    </script>
</div>