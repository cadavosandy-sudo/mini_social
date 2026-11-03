<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register | THOUGHTS</title>
    <link rel="stylesheet" href="style.css?v=102">
</head>
<body>

<div class="auth-container">
    <div class="auth-card">
        <h1 class="logo">join THOUGHTS.</h1>
        <p class="subtitle">Create your account and connect.</p>

        <?php if (!empty($error)): ?>
            <p class="error">
                <?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?>
            </p>
        <?php endif; ?>

        <form method="POST" action="index.php?page=register">
            <label for="full_name">Full Name</label>
            <input
                type="text"
                id="full_name"
                name="full_name"
                maxlength="100"
                autocomplete="name"
                required
            >

            <label for="username">Username</label>
            <input
                type="text"
                id="username"
                name="username"
                minlength="3"
                maxlength="50"
                autocomplete="username"
                required
            >

            <label for="password">Password</label>
            <input
                type="password"
                id="password"
                name="password"
                minlength="8"
                autocomplete="new-password"
                required
            >

            <label for="confirm_password">Confirm Password</label>
            <input
                type="password"
                id="confirm_password"
                name="confirm_password"
                minlength="8"
                autocomplete="new-password"
                required
            >

            <button type="submit">Create Account</button>
        </form>

        <p class="switch">
            Already have an account?
            <a href="index.php?page=login">Log in</a>
        </p>
    </div>
</div>

</body>
</html>