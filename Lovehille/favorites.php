<?php
declare(strict_types=1);

require_once __DIR__ . '/includes/auth.php';

header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store');

$user = current_user();

if ($user === null) {
    http_response_code(401);
    echo json_encode(['error' => 'Please sign in again to access your saved titles.']);
    exit;
}

$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';

if (!in_array($method, ['GET', 'POST', 'DELETE'], true)) {
    header('Allow: GET, POST, DELETE');
    http_response_code(405);
    echo json_encode(['error' => 'That action is not supported.']);
    exit;
}

try {
    $connection = database();

    if ($method === 'GET') {
        $query = $connection->prepare('SELECT movie_slug FROM favorites WHERE user_id = :user_id ORDER BY created_at, movie_slug');
        $query->execute(['user_id' => $user['id']]);
        echo json_encode(['favorites' => array_column($query->fetchAll(), 'movie_slug')]);
        exit;
    }

    $submittedToken = $_SERVER['HTTP_X_CSRF_TOKEN'] ?? null;

    if (!verify_csrf_token(is_string($submittedToken) ? $submittedToken : null)) {
        http_response_code(400);
        echo json_encode(['error' => 'Your session expired. Refresh the page and try again.']);
        exit;
    }

    $payload = json_decode(file_get_contents('php://input'), true);
    $movie = is_array($payload) ? ($payload['movie'] ?? null) : null;
    $allowedMovies = ['off-campus', 'sterling-point', 'last-sunrise'];

    if (!is_string($movie) || !in_array($movie, $allowedMovies, true)) {
        http_response_code(422);
        echo json_encode(['error' => 'Choose a title from the LoveFilm collection.']);
        exit;
    }

    if ($method === 'POST') {
        $query = $connection->prepare(
            'INSERT INTO favorites (user_id, movie_slug) VALUES (:user_id, :movie_slug) ON DUPLICATE KEY UPDATE movie_slug = VALUES(movie_slug)'
        );
        $query->execute(['user_id' => $user['id'], 'movie_slug' => $movie]);
    } else {
        $query = $connection->prepare('DELETE FROM favorites WHERE user_id = :user_id AND movie_slug = :movie_slug');
        $query->execute(['user_id' => $user['id'], 'movie_slug' => $movie]);
    }

    http_response_code(200);
    echo json_encode(['ok' => true]);
} catch (RuntimeException $exception) {
    error_log('LoveFilm saved titles are unavailable: ' . $exception->getMessage());
    http_response_code(503);
    echo json_encode(['error' => $exception->getMessage()]);
} catch (PDOException $exception) {
    error_log('LoveFilm saved title request failed: ' . $exception->getMessage());
    http_response_code(500);
    echo json_encode(['error' => 'Your saved titles could not be updated. Please try again.']);
}
