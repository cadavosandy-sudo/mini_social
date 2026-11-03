
<?php
function escapeLogin($value) {
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | MiniSocial</title>
    <link rel="stylesheet" href="style.css?v=3">
</head>
<body>

<div class="auth-container">
    <div class="auth-card">
        <h1 class="logo">THOUGHTS.</h1>
        <p class="subtitle">Share what's on your mind.</p>

        <?php if (!empty($_GET['registered'])): ?>
            <p class="success">Account created! You can now log in.</p>
        <?php endif; ?>

        <?php if (!empty($error)): ?>
            <p class="error"><?= escapeLogin($error) ?></p>
        <?php endif; ?>

        <form method="POST" action="index.php?page=login">
            <label for="username">Username</label>
            <input
                type="text"
                id="username"
                name="username"
                maxlength="50"
                autocomplete="username"
                required
            >

            <label for="password">Password</label>
            <input
                type="password"
                id="password"
                name="password"
                autocomplete="current-password"
                required
            >

            <button type="submit">Log In</button>
        </form>

        <p class="switch">
            Don't have an account?
            <a href="index.php?page=register">Create one</a>
        </p>
    </div>
</div>

</body>
</html>