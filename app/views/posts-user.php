<?php $this->layout('master', ['title' => $title, 'username' => $username])?>
<h1>Postagens de: <a href="/user/<?php echo $userUrl?>"><?php echo $username?></a></h1>
<?php if (!empty($posts)): ?>
    <?php foreach ($posts as $post): ?>
        <div class="post-container1">
            <div class="postProfile flexCom flexo " onclick="window.location.href='/post/<?= htmlspecialchars($post['post_url']) ?>'">
                <?php if ($post['title'] != ''):?>
                    <b class="font-large titlePost"><?php echo $this->e($post['title']) ?></b>
                <?php endif?>
                <p><?= htmlspecialchars($post['content']) ?></p>
            </div>
            <div class="flexo flexCom">
                <div class="flexRow flexoNoN" style="margin: 5px 0 5px 0">
                    <b>Publicado em: <?= date('Y/m/d', strtotime($post['date'])) ?></b>
                    <?php $this->insert('layouts/otherPostsElements1', ['post' => $post]) ?>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
<?php else: ?>
    <p style="font-weight: bold;">Esse usuário não possue postagens.</p>
<?php endif; ?>

