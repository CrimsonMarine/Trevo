<?php $this->layout('master', ['title' => $title])?>

<div class="postPage flexo flexCom">
    <a class="marginAll1" href="/user/<?= $user['user_url']?>"><?= $user['username']?></a>
    <b class="font-large"><?php echo $this->e($post['title']) ?></b>
    <p><?php echo $this->e($post['content']) ?></p>
    <div class="flexRow flexoNoN" style="margin: 5px 0 5px 0;">
        <b>Publicado em: <?php echo $this->e(date('Y/m/d', strtotime($post['date']))) ?></b>
        <div class="flexRow flexoNoN margin-to-right" style="width: 100%;">
            <?php $this->insert('layouts/otherPostsElements', ['post' => $post])?>
        </div>
    </div>
</div>