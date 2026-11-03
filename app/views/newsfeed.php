<?php
function e($value) {
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>THOUGHTS | Newsfeed</title>
    <link rel="stylesheet" href="style.css?v=107">
</head>
<body>

<header class="topbar">
    <a class="brand" href="index.php?page=feed">THOUGHTS.</a>
    
    <div class="topbar-right">
        <a class="nav-link-profile" href="index.php?page=profile">👤 My Profile</a>
        <span class="welcome">Hi, <?= e($_SESSION['username']) ?>!</span>
        <a class="logout" href="index.php?page=logout">Log Out</a>
    </div>
</header>

<div class="social-layout">
    <main class="feed-content">
        <div class="feed-heading" id="create-post">
            <h1>Your Newsfeed</h1>
            <p>See what's on everyone's mind.</p>
        </div>

        <?php if (!empty($error)): ?>
            <p class="error"><?= e($error) ?></p>
        <?php endif; ?>

        <!-- Post Creation Form -->
        <form class="post-form" method="POST" action="index.php?page=feed" enctype="multipart/form-data">
            <input type="hidden" name="action" value="create">

            <textarea
                name="content"
                placeholder="Share your thoughts..."
                maxlength="2000"
                rows="4"
            ></textarea>

            <div class="composer-bottom">
                <label for="post_file" class="file-label">📎 Add photo or file</label>
                <input
                    type="file"
                    id="post_file"
                    name="post_file"
                    accept="image/jpeg,image/png,image/gif,image/webp,.pdf,.txt"
                >

                <button type="submit" class="btn-publish">Publish Post</button>
            </div>
        </form>

        <section class="posts-list">
            <h2>Recent Thoughts</h2>

            <?php if (empty($posts)): ?>
                <div class="empty-state">
                    <h3>No posts yet!</h3>
                    <p>Be the first to share a thought.</p>
                </div>
            <?php endif; ?>

            <?php foreach ($posts as $post): ?>
                <!-- Added id="post-X" so the page jumps back to this exact post -->
                <article class="post-card" id="post-<?= (int)$post['id'] ?>">
                    <div class="post-header">
                        <div class="avatar">
                            <?php if (!empty($post['profile_image'])): ?>
                                <img
                                    src="<?= e($post['profile_image']) ?>"
                                    alt="Profile picture"
                                    class="profile-pic"
                                >
                            <?php else: ?>
                                <?= e(strtoupper(substr($post['username'], 0, 1))) ?>
                            <?php endif; ?>
                        </div>

                        <div class="post-author">
                            <strong><?= e($post['full_name']) ?></strong>
                            <span>@<?= e($post['username']) ?></span>
                            <small>
                                <?= e(date(
                                    'M j, Y g:i A',
                                    strtotime($post['created_at'])
                                )) ?>
                            </small>
                        </div>
                    </div>

                    <?php if (!empty($post['content'])): ?>
                        <div class="post-text"><?= e($post['content']) ?></div>
                    <?php endif; ?>

                    <?php if (!empty($post['file_path'])): ?>
                        <?php
                        $file = $post['file_path'];
                        $extension = strtolower(pathinfo($file, PATHINFO_EXTENSION));
                        ?>

                        <?php if (in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'webp'])): ?>
                            <img
                                src="<?= e($file) ?>"
                                alt="Post attachment"
                                class="post-attachment"
                            >
                        <?php else: ?>
                            <p>
                                📎 <a href="<?= e($file) ?>" target="_blank" rel="noopener">
                                    Open attached file
                                </a>
                            </p>
                        <?php endif; ?>
                    <?php endif; ?>

                    <!-- Post Action Bar (Like & Comment Count) -->
                    <div class="post-actions">
                        <form method="POST" action="index.php?page=feed" style="display:inline;">
                            <input type="hidden" name="action" value="like">
                            <input type="hidden" name="post_id" value="<?= (int)$post['id'] ?>">
                            <button type="submit" class="like-btn <?= !empty($post['is_liked']) ? 'liked' : '' ?>">
                                <?= !empty($post['is_liked']) ? '❤️' : '🤍' ?> <?= $post['likes_count'] ?? 0 ?>
                            </button>
                        </form>
                        <span class="comment-count">💬 <?= count($post['comments'] ?? []) ?></span>
                    </div>

                    <!-- Comments Section -->
                    <div class="comments-section">
                        <?php if (!empty($post['comments'])): ?>
                            <div class="comments-list">
                                <?php foreach ($post['comments'] as $comment): ?>
                                    <div class="comment-item">
                                        <strong>@<?= e($comment['username'] ?? 'user') ?>:</strong>
                                        <span><?= e($comment['comment_text'] ?? '') ?></span>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>

                        <!-- Add Comment Form -->
                        <form method="POST" action="index.php?page=feed" class="comment-form">
                            <input type="hidden" name="action" value="comment">
                            <input type="hidden" name="post_id" value="<?= (int)$post['id'] ?>">
                            <input type="text" name="comment_text" placeholder="Write a comment..." required>
                            <button type="submit">Reply</button>
                        </form>
                    </div>

                    <?php if ((int)$post['user_id'] === (int)$_SESSION['user_id']): ?>
                        <form method="POST"
                              action="index.php?page=feed"
                              onsubmit="return confirm('Delete this post?');"
                              style="margin-top: 10px;">
                            <input type="hidden" name="action" value="delete">
                            <input type="hidden" name="post_id" value="<?= (int)$post['id'] ?>">
                            <button class="delete-btn" type="submit">
                                Delete Post
                            </button>
                        </form>
                    <?php endif; ?>
                </article>
            <?php endforeach; ?>
        </section>
    </main>
</div>

</body>
</html>