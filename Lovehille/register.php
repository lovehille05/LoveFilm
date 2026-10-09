<?php
declare(strict_types=1);

require_once __DIR__ . '/includes/auth.php';
redirect_authenticated_user();

$name = '';
$email = '';
$error = '';
$databaseError = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim((string) ($_POST['name'] ?? ''));
    $email = mb_strtolower(trim((string) ($_POST['email'] ?? '')), 'UTF-8');
    $password = (string) ($_POST['password'] ?? '');
    $confirmation = (string) ($_POST['password_confirmation'] ?? '');

    if (!verify_csrf_token(isset($_POST['csrf_token']) ? (string) $_POST['csrf_token'] : null)) {
        http_response_code(400);
        $error = 'Your session expired. Refresh the page and try again.';
    } elseif (
        $name === ''
        || mb_strlen($name, 'UTF-8') > 80
        || !filter_var($email, FILTER_VALIDATE_EMAIL)
        || strlen($email) > 254
        || strlen($password) < 10
        || strlen($password) > 72
        || $password !== $confirmation
    ) {
        $error = 'Enter your name, a valid email, and a matching password of 10–72 characters.';
    } else {
        try {
            $insert = database()->prepare(
                'INSERT INTO users (name, email, password_hash) VALUES (:name, :email, :password_hash)'
            );
            $insert->execute([
                'name' => $name,
                'email' => $email,
                'password_hash' => password_hash($password, PASSWORD_DEFAULT),
            ]);
            $_SESSION['flash_success'] = 'Your account is ready. Sign in to start watching.';
            redirect_to('/login.php');
        } catch (PDOException $exception) {
            if ($exception->getCode() === '23000') {
                $error = 'There is already an account with that email. Try signing in instead.';
            } else {
                error_log('LoveFilm registration failed: ' . $exception->getMessage());
                $databaseError = 'We could not create your account right now. Please try again.';
            }
        } catch (RuntimeException $exception) {
            $databaseError = $exception->getMessage();
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
  <title>Create your account · LoveFilm</title>
  <link rel="stylesheet" href="auth.css">
</head>
<body>
  <main class="auth-shell">
    <a class="auth-brand" href="login.php" aria-label="LoveFilm">LOVEFILM<span>.</span></a>
    <section class="auth-card" aria-labelledby="auth-title">
      <p class="auth-kicker">MAKE YOURSELF AT HOME</p>
      <h1 id="auth-title">Join LoveFilm</h1>
      <p class="auth-subtitle">Save your favorites. Make movie night yours.</p>
      <?php if ($error !== ''): ?><p class="message message-error" role="alert"><?= escape_html($error) ?></p><?php endif; ?>
      <?php if ($databaseError !== ''): ?><p class="message message-error" role="alert"><?= escape_html($databaseError) ?></p><?php endif; ?>
      <form method="post" action="register.php" class="auth-form">
        <input type="hidden" name="csrf_token" value="<?= escape_html(csrf_token()) ?>">
        <label for="name">Your name</label>
        <input id="name" name="name" type="text" autocomplete="name" maxlength="80" placeholder="How should we call you?" value="<?= escape_html($name) ?>" required autofocus>
        <label for="email">Email address</label>
        <input id="email" name="email" type="email" autocomplete="email" maxlength="254" placeholder="you@example.com" value="<?= escape_html($email) ?>" required>
        <label for="password">Password <span class="field-hint">At least 10 characters</span></label>
        <input id="password" name="password" type="password" autocomplete="new-password" minlength="10" maxlength="72" placeholder="Create a password" required>
        <label for="password_confirmation">Confirm password</label>
        <input id="password_confirmation" name="password_confirmation" type="password" autocomplete="new-password" minlength="10" maxlength="72" placeholder="Enter it one more time" required>
        <button class="auth-submit" type="submit">Create account <span aria-hidden="true">→</span></button>
      </form>
      <p class="auth-switch">Already part of the story? <a href="login.php">Sign in</a></p>
    </section>
    <p class="auth-footnote">Your watchlist is saved privately to your account.</p>
  </main>
</body>
</html>
