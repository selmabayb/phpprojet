<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$stmt = $pdo->prepare("SELECT f.title, f.platform, f.show_id FROM films_vus v
JOIN filmsa f ON v.show_id = f.show_id AND v.platform = f.platform
WHERE v.user_id = ?
ORDER BY v.watched_date DESC");
$stmt->execute([$user_id]);
$films = $stmt->fetchAll();

include 'header.php';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Mes films vus - CinéLove</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        body {
            background: linear-gradient(180deg, #ffeaf1 0%, #fce4ec 100%);
            font-family: 'Segoe UI', sans-serif;
            margin: 0;
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

        .film {
            padding: 12px;
            background: #fff8fb;
            border-radius: 8px;
            border: 1px solid #eee;
            margin-bottom: 12px;
        }

        .film a {
            font-weight: bold;
            color: #e91e63;
            text-decoration: none;
        }
    </style>
</head>
<body>

<div class="container">
    <h2>🎞 Mes films vus</h2>
    <?php if (empty($films)): ?>
        <p style="text-align:center;">Aucun film vu pour l'instant.</p>
    <?php else: ?>
        <?php foreach ($films as $film): ?>
            <div class="film">
                <a href="film_detail.php?id=<?= urlencode($film['show_id']) ?>&platform=<?= urlencode($film['platform']) ?>">
                    <?= htmlspecialchars($film['title']) ?> (<?= htmlspecialchars($film['platform']) ?>)
                </a>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

</body>
</html>
