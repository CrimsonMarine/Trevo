<?php

use app\controllers\AccRecoverController;
use app\controllers\HomeController;
use app\controllers\ContactController;
use app\controllers\CreateAccController;
use app\controllers\CreateController;
use app\controllers\LoginController;
use app\controllers\PostsUserController;
use app\controllers\ProfileController;
use app\controllers\PostController;
use app\controllers\ResetPasswordController;
use app\controllers\SettingsController;
use Slim\Exception\HttpNotFoundException;
use app\database\ConnectionRedis;
use app\database\ConnectionSQL;

/* Routes: */

$app->get('/', [HomeController::class, 'index']);
$app->get('/contact', [ContactController::class, 'contact']);
$app->map(['GET', 'POST'], '/account-recover', [AccRecoverController::class, 'recover']);
$app->map(['GET', 'POST'],'/password-reset', [ResetPasswordController::class, 'index']);
$app->map(['GET', 'POST'], '/settings', [SettingsController::class, 'settings']);
$app->map(['GET', 'POST'], '/login', [LoginController::class, 'login']);
$app->map(['GET', 'POST'], '/signup', [CreateAccController::class, 'create']);
$app->map(['GET', 'POST'], '/create', [CreateController::class, 'create']);
$app->get('/post/{postUrl}', function ($request, $response, $args) {
    $postUrl = $args['postUrl'] ?? null;

    $pdo = ConnectionSQL::connect();

    $stmt = $pdo->prepare("SELECT COUNT(*) FROM posts WHERE post_url = :postUrl");
    $stmt->execute(['postUrl' => $postUrl]);
    $postExists = $stmt->fetchColumn();

    if (!$postExists) {
        return $response->withHeader('Location', '/')->withStatus(302);
    }

    return (new PostController())->post($request, $response, $args);
});
$app->get('/user/{userUrl}/posts', function ($request, $response, $args) {
    $userUrl = $args['userUrl'] ?? null;

    $pdo = ConnectionSQL::connect();

    $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE user_url = :userUrl");
    $stmt->execute(['userUrl' => $userUrl]);
    $userExists = $stmt->fetchColumn();

    if (!$userExists) {
        return $response->withHeader('Location', '/')->withStatus(302);
    }

    return (new PostsUserController())->postsu($request, $response, $args);
});
$app->get('/user/{userUrl}', function ($request, $response, $args) {

    $userUrl = $args['userUrl'] ?? null;

    $pdo = ConnectionSQL::connect();

    $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE user_url = :userUrl");
    $stmt->execute(['userUrl' => $userUrl]);
    $userExists = $stmt->fetchColumn();

    if (!$userExists) {
        return $response->withHeader('Location', '/')->withStatus(302);
    }

    return (new ProfileController())->profile($request, $response, $args);
});

/* Actions: */

$app->get('/api/follow-status/{userId}', ProfileController::class . ':checkFollowStatus');
$app->post('/api/toggle-follow/{userId}', ProfileController::class . ':toggleFollow');

$app->map(['POST', 'DELETE'], '/delete-post/{AuthorId}/{postId}', ProfileController::class . ':deletePost');

$app->get('/logout', function ($request, $response, $args) {
    session_unset();
    session_destroy();

    return $response->withHeader('Location', '/')->withStatus(302);
});
$app->post('/update-status', function ($request, $response, $args) {
    $data = $request->getParsedBody();

    $newStatus = $data['status'] ?? null;
    $userId = $data['userId'] ?? null;

    if ($newStatus && $userId) {
        $validStatuses = ['online', 'offline', 'absent'];
        if (in_array($newStatus, $validStatuses)) {
            ConnectionRedis::setData('users', $userId, 'status', [$newStatus]);

            $response->getBody()->write(json_encode($newStatus));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
        } else {
            $response->getBody()->write(json_encode('Status inválido.'));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
        }
    }

    $response->getBody()->write(json_encode('Parâmetros ausentes.'));
    return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
});
$app->post('/update-activity', function ($request, $response, $args) {
    $data = json_decode($request->getBody(), true);

    if (isset($data['userId'])) {
        $userId = $data['userId'];
        $time = time();
        $timestamp = date('Y/m/d', $time);

        ConnectionRedis::setData('users', $userId, 'last_activity', [$timestamp]);
        ConnectionRedis::setData('users', $userId, 'status', ['absent']);

        $response->getBody()->write(json_encode(['status' => 'success', 'message' => 'Activity updated']));

        return $response->withHeader('Content-Type', 'application/json');
    } else {
        $response->getBody()->write(json_encode(['status' => 'error', 'message' => 'Invalid data']));

        return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
    }
});

/* Errors: */

$app->addRoutingMiddleware();

$errorMiddleware = $app->addErrorMiddleware(true, true, true);

$errorMiddleware->setErrorHandler(HttpNotFoundException::class, function ($request, $exception) use ($app) {
    $response = $app->getResponseFactory()->createResponse();
    return $response->withHeader('Location', '/')->withStatus(302);
});
$errorMiddleware->setErrorHandler(Slim\Exception\HttpMethodNotAllowedException::class, function ($request, $exception) use ($app) {
    $response = $app->getResponseFactory()->createResponse();
    return $response->withHeader('Location', '/')->withStatus(302);
});