<?php
session_start();
require_once 'db.php';
require_once 'header.php';

if (!isset($_GET['id']) || !isset($_GET['platform'])) {
    echo "Film introuvable.";
    exit;
}

$show_id = $_GET['id'];
$platform = $_GET['platform'];

// Récupérer les infos du film
$stmt = $pdo->prepare("SELECT * FROM filmsa WHERE show_id = ? AND platform = ?");
$stmt->execute([$show_id, $platform]);
$film = $stmt->fetch();

if (!$film) {
    echo "Film introuvable.";
    exit;
}

// Avis
$stmt = $pdo->prepare("SELECT r.*, u.username FROM reviews r
                       JOIN users u ON r.user_id = u.user_id
                       WHERE r.show_id = ? AND r.platform = ?
                       ORDER BY r.review_date DESC");
$stmt->execute([$show_id, $platform]);
$reviews = $stmt->fetchAll();

// Likes
$likesStmt = $pdo->prepare("SELECT review_id, COUNT(*) as total FROM like_reviews GROUP BY review_id");
$likesStmt->execute();
$likes = [];
foreach ($likesStmt as $like) {
    $likes[$like['review_id']] = $like['total'];
}

// Réponses
$responseStmt = $pdo->query("SELECT r.*, u.username FROM responses r JOIN users u ON r.user_id = u.user_id");
$responses = [];
foreach ($responseStmt as $res) {
    $responses[$res['review_id']][] = $res;
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8" />
    <title><?= htmlspecialchars($film['title']) ?> - Détail</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background: linear-gradient(to bottom, #ffeaf1, #ffffff);
            margin: 0;
            padding: 20px;
        }

        .film-container {
            max-width: 900px;
            margin: auto;
            background: #fff;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 0 15px rgba(0,0,0,0.1);
        }

        .interact form {
            display: inline-block;
            margin: 5px 0;
        }

        textarea {
            width: 100%;
            height: 60px;
            padding: 8px;
            margin-bottom: 10px;
            border-radius: 6px;
            border: 1px solid #ccc;
        }

        button {
            background-color: #e91e63;
            color: white;
            border: none;
            padding: 10px 14px;
            border-radius: 6px;
            cursor: pointer;
        }

        button:hover {
            background-color: #d81b60;
        }

        .review-box {
            background: #f9f9f9;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
        }

        .response {
            background-color: #f2f2f2;
            padding: 8px;
            margin-top: 6px;
            border-radius: 6px;
            font-size: 0.9em;
        }

    </style>
</head>
<body>

<div class="film-container">
    <h2><?= htmlspecialchars($film['title']) ?></h2>
    <p><strong>Plateforme :</strong> <?= htmlspecialchars($film['platform']) ?></p>
    <p><strong>Année :</strong> <?= htmlspecialchars($film['release_year']) ?></p>
    <p><strong>Genre :</strong> <?= htmlspecialchars($film['listed_in']) ?></p>
    <p><strong>Description :</strong> <?= htmlspecialchars($film['description']) ?></p>

    <?php if (isset($_SESSION['user_id'])): ?>
    <div class="interact">
        <h3>🎬 Interagir avec ce film</h3>
        <form method="post" action="interaction.php">
            <input type="hidden" name="action" value="add_to_watchlist">
            <input type="hidden" name="show_id" value="<?= $show_id ?>">
            <input type="hidden" name="platform" value="<?= $platform ?>">
            <button type="submit">Ajouter à ma liste à voir</button>
        </form>

        <form method="post" action="interaction.php">
            <input type="hidden" name="action" value="mark_as_seen">
            <input type="hidden" name="show_id" value="<?= $show_id ?>">
            <input type="hidden" name="platform" value="<?= $platform ?>">
            <button type="submit">Marquer comme vu</button>
        </form>

        <form method="post" action="interaction.php">
            <input type="hidden" name="action" value="add_top3">
            <input type="hidden" name="show_id" value="<?= $show_id ?>">
            <input type="hidden" name="platform" value="<?= $platform ?>">
            <select name="position">
                <option value="1">Top 1</option>
                <option value="2">Top 2</option>
                <option value="3">Top 3</option>
            </select>
            <button type="submit">Ajouter au top 3</button>
        </form>

        <h4>Note :</h4>
        <form method="post" action="interaction.php">
            <input type="hidden" name="action" value="add_review">
            <input type="hidden" name="show_id" value="<?= $show_id ?>">
            <input type="hidden" name="platform" value="<?= $platform ?>">
            <select name="note">
                <?php for ($i=1; $i<=5; $i++): ?>
                    <option value="<?= $i ?>"><?= $i ?> ★</option>
                <?php endfor; ?>
            </select>
            <textarea name="avis" placeholder="Votre avis" required></textarea>
            <button type="submit">Envoyer l’avis</button>
        </form>
    </div>
    <?php endif; ?>

    <h3>💬 Avis des spectateurs</h3>
    <?php if (empty($reviews)): ?>
        <p>Aucun avis pour ce film pour l’instant.</p>
    <?php endif; ?>

    <?php foreach ($reviews as $review): ?>
        <div class="review-box">
            <p><strong><?= htmlspecialchars($review['username']) ?></strong> — Note : <?= str_repeat('★', $review['rating']) ?></p>
            <p><?= nl2br(htmlspecialchars($review['review'])) ?></p>
            <p>❤️ <?= $likes[$review['review_id']] ?? 0 ?> like(s)</p>

            <?php if (isset($_SESSION['user_id'])): ?>
                <form method="post" action="interaction.php">
                    <input type="hidden" name="action" value="like_review">
                    <input type="hidden" name="review_id" value="<?= $review['review_id'] ?>">
                    <input type="hidden" name="show_id" value="<?= $show_id ?>">
                    <input type="hidden" name="platform" value="<?= $platform ?>">
                    <button type="submit">Like</button>
                </form>

                <form method="post" action="interaction.php">
                    <input type="hidden" name="action" value="reply_review">
                    <input type="hidden" name="review_id" value="<?= $review['review_id'] ?>">
                    <input type="hidden" name="show_id" value="<?= $show_id ?>">
                    <input type="hidden" name="platform" value="<?= $platform ?>">
                    <textarea name="response" placeholder="Répondre à cet avis..." required></textarea>
                    <button type="submit">Répondre</button>
                </form>
            <?php endif; ?>

            <?php if (isset($responses[$review['review_id']])): ?>
                <?php foreach ($responses[$review['review_id']] as $res): ?>
                    <div class="response">
                        <strong><?= htmlspecialchars($res['username']) ?> :</strong>
                        <?= htmlspecialchars($res['response']) ?>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    <?php endforeach; ?>
</div>
</body>
</html>