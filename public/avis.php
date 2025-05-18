<?php
session_start();
require_once 'db.php';
require_once 'header.php';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Avis des spectateurs</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background: linear-gradient(to bottom, #ffeaf1, #ffffff);
            margin: 0;
            padding: 0 20px;
        }
        .container {
            max-width: 900px;
            margin: 40px auto;
            background: #fff;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 0 12px rgba(0,0,0,0.08);
        }
        .avis-box {
            background: #fafafa;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 20px;
        }
        textarea {
            width: 100%;
            height: 50px;
            padding: 10px;
            margin-top: 5px;
            margin-bottom: 10px;
            border-radius: 6px;
            border: 1px solid #ccc;
        }
        button {
            background-color: #e91e63;
            color: white;
            border: none;
            padding: 8px 15px;
            border-radius: 6px;
            cursor: pointer;
        }
        button:hover {
            background-color: #d81b60;
        }
        .response {
            background-color: #f2f2f2;
            padding: 8px;
            margin-top: 8px;
            border-radius: 6px;
            font-size: 0.9em;
        }
    </style>
</head>
<body>
<div class="container">
    <h2>📝 Tous les avis des spectateurs</h2>
    <?php
    // Récupérer tous les avis avec utilisateur
    $stmt = $pdo->query("SELECT r.*, u.username, f.title FROM reviews r
                         JOIN users u ON r.user_id = u.user_id
                         JOIN filmsa f ON r.show_id = f.show_id AND r.platform = f.platform
                         ORDER BY r.review_date DESC");
    $reviews = $stmt->fetchAll();

    // Récupérer les likes
    $likesStmt = $pdo->query("SELECT review_id, COUNT(*) as total FROM like_reviews GROUP BY review_id");
    $likes = [];
    foreach ($likesStmt as $like) {
        $likes[$like['review_id']] = $like['total'];
    }

    // Récupérer les réponses
    $responseStmt = $pdo->query("SELECT r.*, u.username FROM responses r JOIN users u ON r.user_id = u.user_id");
    $responses = [];
    foreach ($responseStmt as $res) {
        $responses[$res['review_id']][] = $res;
    }

    foreach ($reviews as $review):
    ?>
    <div class="avis-box">
        <p><strong><?= htmlspecialchars($review['username']) ?></strong> sur <em><?= htmlspecialchars($review['title']) ?></em></p>
        <p>Note : <?= str_repeat('★', $review['rating']) ?></p>
        <p><?= nl2br(htmlspecialchars($review['review'])) ?></p>
        <p>❤️ <?= $likes[$review['review_id']] ?? 0 ?> like(s)</p>

        <?php if (isset($_SESSION['user_id'])): ?>
        <form method="post" action="interaction.php" style="display:inline;">
            <input type="hidden" name="action" value="like_review">
            <input type="hidden" name="review_id" value="<?= $review['review_id'] ?>">
            <input type="hidden" name="show_id" value="<?= $review['show_id'] ?>">
            <input type="hidden" name="platform" value="<?= $review['platform'] ?>">
            <input type="hidden" name="return_to" value="avis.php">
            <button type="submit">Like</button>
        </form>

        <form method="post" action="interaction.php">
            <input type="hidden" name="action" value="reply_review">
            <input type="hidden" name="review_id" value="<?= $review['review_id'] ?>">
            <input type="hidden" name="show_id" value="<?= $review['show_id'] ?>">
            <input type="hidden" name="platform" value="<?= $review['platform'] ?>">
            <input type="hidden" name="return_to" value="avis.php">
            <textarea name="response" placeholder="Répondre à cet avis..." required></textarea>
            <button type="submit">Répondre</button>
        </form>
        <?php endif; ?>

        <?php if (isset($responses[$review['review_id']])): ?>
        <div>
            <?php foreach ($responses[$review['review_id']] as $rep): ?>
            <div class="response">
                <strong><?= htmlspecialchars($rep['username']) ?></strong> : <?= htmlspecialchars($rep['response']) ?>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </div>
    <?php endforeach; ?>
</div>
</body>
</html>