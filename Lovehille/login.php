<?php
declare(strict_types=1);

require_once __DIR__ . '/includes/auth.php';
redirect_authenticated_user();

$email = '';
$error = '';
$notice = '';
$databaseError = '';

if (isset($_SESSION['flash_success']) && is_string($_SESSION['flash_success'])) {
    $notice = $_SESSION['flash_success'];
    unset($_SESSION['flash_success']);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = mb_strtolower(trim((string) ($_POST['email'] ?? '')), 'UTF-8');
    $password = (string) ($_POST['password'] ?? '');

    if (!verify_csrf_token(isset($_POST['csrf_token']) ? (string) $_POST['csrf_token'] : null)) {
        http_response_code(400);
        $error = 'Your session expired. Refresh the page and try again.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($email) > 254 || $password === '') {
        $error = 'Email or password is incorrect.';
    } else {
        try {
            $query = database()->prepare('SELECT id, name, email, password_hash FROM users WHERE email = :email LIMIT 1');
            $query->execute(['email' => $email]);
            $user = $query->fetch();

            if ($user && password_verify($password, $user['password_hash'])) {
                if (password_needs_rehash($user['password_hash'], PASSWORD_DEFAULT)) {
                    $update = database()->prepare('UPDATE users SET password_hash = :password_hash WHERE id = :id');
                    $update->execute([
                        'password_hash' => password_hash($password, PASSWORD_DEFAULT),
                        'id' => $user['id'],
                    ]);
                }

                session_regenerate_id(true);
                $_SESSION['user'] = [
                    'id' => (int) $user['id'],
                    'name' => $user['name'],
                    'email' => $user['email'],
                ];
                unset($_SESSION['csrf_token']);
                redirect_to('/index.php');
            }

            $error = 'Email or password is incorrect.';
        } catch (RuntimeException $exception) {
            $databaseError = $exception->getMessage();
        } catch (PDOException $exception) {
            error_log('LoveFilm sign-in failed: ' . $exception->getMessage());
            $databaseError = 'We could not sign you in right now. Please try again.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="theme-color" content="#688E5A">
  <title>Sign in · LoveFilm</title>
  <link rel="stylesheet" href="auth.css">
</head>
<body>
  <main class="auth-shell">
    <a class="auth-brand" href="login.php" aria-label="LoveFilm">LOVEFILM<span>.</span></a>
    <section class="auth-card" aria-labelledby="auth-title">
      <p class="auth-kicker">YOUR NEXT FAVORITE STORY</p>
      <h1 id="auth-title">Welcome back</h1>
      <p class="auth-subtitle">Sign in to settle in and enjoy your collection.</p>
      <?php if ($notice !== ''): ?><p class="message message-success" role="status"><?= escape_html($notice) ?></p><?php endif; ?>
      <?php if ($error !== ''): ?><p class="message message-error" role="alert"><?= escape_html($error) ?></p><?php endif; ?>
      <?php if ($databaseError !== ''): ?><p class="message message-error" role="alert"><?= escape_html($databaseError) ?></p><?php endif; ?>
      <form method="post" action="login.php" class="auth-form">
        <input type="hidden" name="csrf_token" value="<?= escape_html(csrf_token()) ?>">
        <label for="email">Email address</label>
        <input id="email" name="email" type="email" autocomplete="email" maxlength="254" placeholder="you@example.com" value="<?= escape_html($email) ?>" required autofocus>
        <label for="password">Password</label>
        <input id="password" name="password" type="password" autocomplete="current-password" placeholder="Your password" required>
        <button class="auth-submit" type="submit">Sign in <span aria-hidden="true">→</span></button>
      </form>
      <p class="auth-switch">New to LoveFilm? <a href="register.php">Create an account</a></p>
    </section>
    <p class="auth-footnote">A little picture story, made for movie night.</p>
  </main>
</body>
</html>
