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
</div>