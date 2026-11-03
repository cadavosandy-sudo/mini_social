<?php
function pe($value) {
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}
$isOwner = (int)$profile['id'] === (int)$_SESSION['user_id'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>THOUGHTS | Profile</title>
    <link rel="stylesheet" href="style.css?v=100">
</head>
<body>

<header class="topbar">
    <a class="brand" href="index.php?page=feed">THOUGHTS.</a>
    <a class="logout" href="index.php?page=feed">Newsfeed</a>
</header>

<main class="profile-page">
    <section class="profile-card">
        <?php if (!empty($profile['profile_image'])): ?>
            <img class="profile-large-pic"
                 src="<?= pe($profile['profile_image']) ?>"
                 alt="Profile picture">
        <?php else: ?>
            <div class="profile-large-pic profile-initial">
                <?= pe(strtoupper(substr($profile['username'], 0, 1))) ?>
            </div>
        <?php endif; ?>

        <h1><?= pe($profile['full_name']) ?></h1>
        <p>@<?= pe($profile['username']) ?></p>
        <p><?= nl2br(pe($profile['bio'] ?? 'No bio yet.')) ?></p>
        <p><?= count($posts) ?> posts</p>

        <?php if ($isOwner): ?>
            <hr>
            <h2>Change Profile Picture</h2>
            <form method="POST" enctype="multipart/form-data">
                <input type="hidden" name="action" value="upload_pfp">
                <div class="form-group">
                    <input type="file" name="profile_image" accept="image/jpeg,image/png,image/webp" required>
                </div>
                <button type="submit">Upload PFP</button>
            </form>

            <hr>
            <h2>Edit Profile</h2>
            <form method="POST">
                <input type="hidden" name="action" value="update_profile">

                <div class="form-group">
                    <label>Display Name</label>
                    <input type="text" name="full_name" maxlength="100" value="<?= pe($profile['full_name']) ?>" required>
                </div>

                <div class="form-group">
                    <label>Bio</label>
                    <textarea name="bio" maxlength="500" rows="4"><?= pe($profile['bio'] ?? '') ?></textarea>
                </div>

                <button type="submit">Save Profile</button>
            </form>
        <?php endif; ?>

        <?php if (!empty($error)): ?>
            <p class="error"><?= pe($error) ?></p>
        <?php endif; ?>

        <?php if (!empty($success)): ?>
            <p class="success"><?= pe($success) ?></p>
        <?php endif; ?>
    </section>

    <section class="profile-posts">
        <h2><?= $isOwner ? 'Your Posts' : 'Posts by ' . pe($profile['username']) ?></h2>

        <?php if (empty($posts)): ?>
            <p>No posts yet.</p>
        <?php endif; ?>

        <?php foreach ($posts as $post): ?>
            <article class="post-card">
                <small>
                    <?= pe(date('M j, Y g:i A', strtotime($post['created_at']))) ?>
                </small>

                <?php if (!empty($post['content'])): ?>
                    <div class="post-text"><?= pe($post['content']) ?></div>
                <?php endif; ?>

                <?php if (!empty($post['file_path'])): ?>
                    <?php
                    $ext = strtolower(pathinfo($post['file_path'], PATHINFO_EXTENSION));
                    ?>
                    <?php if (in_array($ext, ['jpg','jpeg','png','gif','webp'])): ?>
                        <img class="post-attachment"
                             src="<?= pe($post['file_path']) ?>"
                             alt="Post attachment">
                    <?php else: ?>
                        <p>
                            <a href="<?= pe($post['file_path']) ?>"
                               target="_blank" rel="noopener">
                                Open attached file
                            </a>
                        </p>
                    <?php endif; ?>
                <?php endif; ?>
            </article>
        <?php endforeach; ?>
    </section>
</main>

</body>
</html>