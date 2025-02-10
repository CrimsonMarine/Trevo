<?php
namespace app\controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use app\classes\User;
use app\database\ConnectionSQL;
use app\database\ConnectionRedis;

class ProfileController {
    public function profile(Request $request, Response $response, array $args) {
        $userUrl = $args['userUrl'];
        $currentUserId = $_SESSION['user-info']['userId'] ?? '';

        $userInstance = new User();
        $user = $userInstance->DatabaseUser('user_url', $userUrl);

        if (!$user) {
            return $response->withHeader('Location', '/not-found')->withStatus(404);
        }

        $userCustomization = $userInstance->DatabaseUserCustomization();

        $pdo = ConnectionSQL::connect();

        $stmtPosts = $pdo->prepare("SELECT title, content, date, idAuthor, id, post_url 
                                    FROM posts 
                                    WHERE idAuthor = :idAuthor 
                                    ORDER BY date DESC LIMIT 3");
        $stmtPosts->execute(['idAuthor' => $user['id']]);
        $posts = $stmtPosts->fetchAll(\PDO::FETCH_ASSOC);

        $status = ConnectionRedis::getData('users', $user['id'], 'status');
        $lastActivity = ConnectionRedis::getData('users', $user['id'], 'last_activity');
        $lastActivityFormatted = is_array($lastActivity) ? implode('', $lastActivity) : $lastActivity;

        $stmtFollow = $pdo->prepare("SELECT COUNT(*) FROM followers 
                                     WHERE follower_id = :follower 
                                     AND following_id = :following");
        $stmtFollow->execute([
            'follower' => $currentUserId,
            'following' => $user['id']
        ]);
        $following = $stmtFollow->fetchColumn() > 0;

        view('profile', [
            'username' => $user['username'], 
            'title' => $user['username'], 
            'status' => is_array($status) ? implode(':', $status) : $status,
            'lastActivity' => $lastActivityFormatted, 
            'posts' => $posts,
            'userId' => $user['id'],
            'userUrl' => $user['user_url'],
            'currentUserId' => $currentUserId,
            'following' => $following,
            'user' => $user,
            'userCustomization' => $userCustomization
        ]);

        return $response;
    }

    public function deletePost(Request $request, Response $response, array $args) {
        $postId = $args['postId'];
        $AuthorId = $args['AuthorId'];
    
        $pdo = ConnectionSQL::connect();
        $stmt = $pdo->prepare("SELECT idAuthor, id FROM posts WHERE id = :id");
        $stmt->execute(['id' => $postId]);
        $post = $stmt->fetch(\PDO::FETCH_ASSOC);
    
        if (isset($_SESSION['user-info']['userId'], $post['idAuthor'], $post['id']) && 
            (string)$post['idAuthor'] === (string)$_SESSION['user-info']['userId'] && 
            $postId === $post['id'] &&
            $AuthorId === $post['idAuthor']) {

            $userIdLogged = htmlspecialchars($_SESSION['user-info']['userId'], ENT_QUOTES, 'UTF-8');
            $stmtDelete = $pdo->prepare("DELETE FROM posts WHERE id = :id");
            $stmtDelete->execute(['id' => $postId]);
            return $response->withHeader('Location', '/profile/' . urlencode($userIdLogged))->withStatus(302);
        } else {
            $userIdLogged = htmlspecialchars($_SESSION['user-info']['userId'], ENT_QUOTES, 'UTF-8');
            return $response->withHeader('Location', '/profile/' . urlencode($userIdLogged) . '?error=not_author')->withStatus(302);
        }
    }

    public function checkFollowStatus(Request $request, Response $response, array $args) {
        $currentUserId = $_SESSION['user-info']['userId'] ?? null;
        $targetUserId = $args['userId'];
    
        $pdo = ConnectionSQL::connect();
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM followers WHERE follower_id = :follower AND following_id = :following");
        $stmt->execute([
            'follower' => $currentUserId,
            'following' => $targetUserId
        ]);
    
        $isFollowing = $stmt->fetchColumn() > 0;
    
        $response->getBody()->write(json_encode(['following' => $isFollowing]));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
    }

    public function toggleFollow(Request $request, Response $response, array $args) {
        $currentUserId = $_SESSION['user-info']['userId'] ?? null;
        $targetUserId = $args['userId'];
    
        $pdo = ConnectionSQL::connect();
    
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM followers WHERE follower_id = :follower AND following_id = :following");
        $stmt->execute([
            'follower' => $currentUserId,
            'following' => $targetUserId
        ]);
    
        $isFollowing = $stmt->fetchColumn() > 0;
        
        if ($_SESSION['type-user'] === 'guest') {
            return $response->withHeader('Location', '/login')->withStatus(302);
        }

        if ($isFollowing) {
            $stmt = $pdo->prepare("DELETE FROM followers WHERE follower_id = :follower AND following_id = :following");
            $stmt->execute([
                'follower' => $currentUserId,
                'following' => $targetUserId
            ]);
            $response->getBody()->write(json_encode(['following' => false]));
        } else {
            $stmt = $pdo->prepare("INSERT INTO followers (follower_id, following_id) VALUES (:follower, :following)");
            $stmt->execute([
                'follower' => $currentUserId,
                'following' => $targetUserId
            ]);
            $response->getBody()->write(json_encode(['following' => true]));
        }
    
        return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
    }
}
