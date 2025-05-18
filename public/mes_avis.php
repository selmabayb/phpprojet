<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$stmt = $pdo->prepare("SELECT f.title, f.platform, r.rating, r.review, r.review_date
FROM reviews r
JOIN filmsa f ON r.show_id = f.show_id AND r.platform = f.platform
WHERE r.user_id = ?
ORDER BY r.review_date DESC");
$stmt->execute([$user_id]);
$avis = $stmt->fetchAll();

include 'header.php';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Mes avis - CinéLove</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        body {
            background: linear-gradient(180deg, #ffeaf1 0%, #fce4ec 100%);
            font-family: 'Segoe UI', sans-serif;
        }

        .container {
            max-width: 900px;
            margin: 40px auto;
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        h2 {
            text-align: center;
            color: #e91e63;
            margin-bottom: 30px;
        }

        .avis {
            padding: 15px;
            background: #fdfdfd;
            border: 1px solid #eee;
            border-radius: 8px;
            margin-bottom: 15px;
        }

        .avis strong {
            color: #e91e63;
        }

        .avis em {
            color: #555;
        }
    </style>
</head>
<body>

<div class="container">
    <h2>💬 Mes avis postés</h2>
    <?php if (empty($avis)): ?>
        <p style="text-align:center;">Aucun avis pour l'instant.</p>
    <?php else: ?>
        <?php foreach ($avis as $a): ?>
            <div class="avis">
                <strong><?= htmlspecialchars($a['title']) ?></strong> (<?= htmlspecialchars($a['platform']) ?>)
                <br>
                <?= htmlspecialchars($a['review']) ?>
                <br>
                Note : <?= str_repeat("⭐", $a['rating']) ?> — <em><?= $a['review_date'] ?></em>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

</body>
</html>
