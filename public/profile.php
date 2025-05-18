<?php
session_start();
require_once 'db.php';
include 'header.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$username = $_SESSION['username'];

// Récupérer les statistiques
$nb_vus = $pdo->query("SELECT COUNT(*) FROM films_vus WHERE user_id = $user_id")->fetchColumn();
$nb_avis = $pdo->query("SELECT COUNT(*) FROM reviews WHERE user_id = $user_id")->fetchColumn();

// Récupérer le top 3
$top3 = $pdo->query("SELECT title FROM filmsa f JOIN top_3 t ON f.show_id = t.show_id AND f.platform = t.platform WHERE t.user_id = $user_id ORDER BY position ASC")->fetchAll(PDO::FETCH_COLUMN);

// Récupérer la liste des films à voir
$sql = "SELECT f.show_id, f.title, f.platform FROM filmsa f
        JOIN favori_films fav ON f.show_id = fav.show_id AND f.platform = fav.platform
        WHERE fav.user_id = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$user_id]);
$films_a_voir = $stmt->fetchAll();
?>

<style>
    body {
        background: #ffe6f0;
        font-family: 'Segoe UI', sans-serif;
        margin: 0;
        padding: 0;
    }

    .profile-container {
        max-width: 1000px;
        margin: 40px auto;
        background: white;
        padding: 30px;
        border-radius: 15px;
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    }

    .profile-header {
        text-align: center;
        margin-bottom: 30px;
    }

    .profile-header h1 {
        color: #e91e63;
        font-size: 28px;
    }

    .stats {
        display: flex;
        justify-content: center;
        gap: 30px;
        margin-bottom: 40px;
        flex-wrap: wrap;
    }

    .stat-circle {
        width: 130px;
        height: 130px;
        border: 2px solid #e91e63;
        border-radius: 50%;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        font-weight: bold;
        color: #e91e63;
        font-size: 18px;
        background: #fff5f9;
    }

    .top3, .films-a-voir, .profile-actions {
        margin-bottom: 40px;
        text-align: center;
    }

    .top3 h2, .films-a-voir h2 {
        color: #e91e63;
        margin-bottom: 15px;
    }

    .top3 ul, .films-a-voir ul {
        list-style: none;
        padding: 0;
    }

    .top3 li, .films-a-voir li {
        margin: 8px 0;
        font-weight: bold;
    }

    .films-a-voir a {
        text-decoration: none;
        color: #e91e63;
        display: inline-block;
        padding: 4px 8px;
        border: 1px solid #e91e63;
        border-radius: 6px;
        margin-top: 5px;
        transition: background 0.3s;
    }

    .films-a-voir a:hover {
        background: #fce4ec;
    }

    .profile-actions a {
        display: block;
        margin: 10px auto;
        color: #e91e63;
        font-weight: bold;
        text-decoration: none;
    }

    .logout-btn {
        display: inline-block;
        padding: 10px 20px;
        background-color: #e91e63;
        color: white;
        border: none;
        border-radius: 6px;
        font-weight: bold;
        text-decoration: none;
        margin-top: 20px;
    }

    .logout-btn:hover {
        background-color: #d81b60;
    }
</style>

<div class="profile-container">
    <div class="profile-header">
        <h1>🎉 Bienvenue, <?= htmlspecialchars($username) ?> 💖</h1>
    </div>

    <div class="stats">
        <div class="stat-circle">
            🎬<br><?= $nb_vus ?><br>Films vus
        </div>
        <div class="stat-circle">
            💬<br><?= $nb_avis ?><br>Avis postés
        </div>
    </div>

    <div class="top3">
        <h2>🌟 Mon Top 3</h2>
        <ul>
            <?php foreach ($top3 as $film): ?>
                <li><?= htmlspecialchars($film) ?></li>
            <?php endforeach; ?>
            <?php if (empty($top3)) echo "<li>Aucun film pour l’instant</li>"; ?>
        </ul>
    </div>

    <div class="films-a-voir">
        <h2>📌 Ma liste à voir</h2>
        <ul>
            <?php foreach ($films_a_voir as $film): ?>
                <li>
                    <a href="film_detail.php?id=<?= urlencode($film['show_id']) ?>&platform=<?= urlencode($film['platform']) ?>">
                        <?= htmlspecialchars($film['title']) ?> (<?= $film['platform'] ?>)
                    </a>
                </li>
            <?php endforeach; ?>
            <?php if (empty($films_a_voir)) echo "<li>Aucun film enregistré</li>"; ?>
        </ul>
    </div>

    <div class="profile-actions">
        <a href="films_vus.php">🎞️ Voir tous mes films vus</a>
        <a href="avis.php">💬 Voir tous mes avis</a>
        <a class="logout-btn" href="logout.php">Se déconnecter</a>
    </div>
</div>
