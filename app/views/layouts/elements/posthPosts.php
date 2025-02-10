<?php
use app\database\ConnectionSQL;

$pdo = ConnectionSQL::connect();

$limit = isset($_GET['limit']) ? intval($_GET['limit']) : 10;
$limit = max(1, min(4, $limit));

$currentUser = $_SESSION['user-info']['userId'] ?? null;

$followedPerPost = $pdo->prepare("SELECT following_id FROM followers WHERE follower_id = :follower_id");
$followedPerPost->execute(['follower_id' => $currentUser]);
$followedPosts = $followedPerPost->fetchAll(PDO::FETCH_ASSOC);

$followingIds = array_column($followedPosts, 'following_id');

$followedUserIds = [];
if (!empty($followingIds)) {
    $placeholders = implode(',', array_fill(0, count($followingIds), '?'));
    
    $followedPerUserPost = $pdo->prepare("SELECT id FROM users WHERE id IN ($placeholders)");
    $followedPerUserPost->execute($followingIds);
    
    $followedUserPosts = $followedPerUserPost->fetchAll(PDO::FETCH_ASSOC);
    $followedUserIds = array_column($followedUserPosts, 'id');
}

$chanceOfFollowed = 50;
$randomChance = mt_rand(1, 100);

if ($randomChance <= $chanceOfFollowed && !empty($followedUserIds)) {
    $placeholders = implode(',', array_map(function ($i) { return ":id$i"; }, range(1, count($followedUserIds))));
    $stmt = $pdo->prepare("SELECT * FROM posts WHERE idAuthor IN ($placeholders) ORDER BY RAND() LIMIT :limit");

    foreach ($followedUserIds as $index => $id) {
        $stmt->bindValue(":id" . ($index + 1), $id, PDO::PARAM_INT);
    }
} else {
    $stmt = $pdo->prepare("SELECT * FROM posts ORDER BY RAND() LIMIT :limit");
}

$stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
$stmt->execute();

$posts = $stmt->fetchAll(PDO::FETCH_ASSOC);

foreach ($posts as &$post) {
    $Userstmt = $pdo->prepare("SELECT username, id, user_url FROM users WHERE id = :id");
    $Userstmt->bindValue(':id', $post['idAuthor'], PDO::PARAM_INT);
    $Userstmt->execute();
    
    $user = $Userstmt->fetch(PDO::FETCH_ASSOC);
    
    $post['author'] = $user ?: ['username' => 'Desconhecido', 'id' => null, 'user_url' => null];
}
unset($post);
?>

<div class="marginV">
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
