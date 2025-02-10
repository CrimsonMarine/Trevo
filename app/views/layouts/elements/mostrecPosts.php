<?php
use app\database\ConnectionSQL;

$pdo = ConnectionSQL::connect();

$limit = isset($_GET['limit']) ? intval($_GET['limit']) : 10;
$limit = max(1, min(4, $limit));

$stmt = $pdo->prepare("SELECT * FROM posts ORDER BY date DESC LIMIT :limite");
$stmt->bindValue(':limite', $limit, PDO::PARAM_INT);
$stmt->execute();

$posts = $stmt->fetchAll(PDO::FETCH_ASSOC);

foreach ($posts as &$post) {
    $Userstmt = $pdo->prepare("SELECT username, id, user_url FROM users WHERE id = :id");
    $Userstmt->execute(['id' => $post['idAuthor']]);
    
    $user = $Userstmt->fetch(PDO::FETCH_ASSOC);
    
    $post['author'] = $user ? $user : ['username' => 'Desconhecido', 'id' => null, 'user_url' => null];
}

unset($post);
?>

<div class="apple-box-1">
    <span class="apple-fac-1">Postagens Recentes</span>
    <div class="apple-content1">
        <?php foreach ($posts as $post): ?>
            <div class="post-container1">
                <a class="marginAll1" href="/user/<?= htmlspecialchars($post['author']['user_url']) ?>">
                    <?= htmlspecialchars($post['author']['username']) ?>
                </a>
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
    </div>
</div>
