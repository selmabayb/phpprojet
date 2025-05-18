<?php
session_start();
require_once 'db.php';

// Filtres
$titre = $_GET['titre'] ?? '';
$plateforme = $_GET['plateforme'] ?? '';
$annee = $_GET['annee'] ?? '';
$genre = $_GET['genre'] ?? '';

// Pagination
$filmsParPage = 20;
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
if ($page < 1) $page = 1;
$offset = ($page - 1) * $filmsParPage;

// Requête dynamique
$where = [];
$params = [];

if ($titre !== '') {
    $where[] = "title LIKE :titre";
    $params[':titre'] = "%$titre%";
}
if ($plateforme !== '') {
    $where[] = "platform = :platform";
    $params[':platform'] = $plateforme;
}
if ($annee !== '') {
    $where[] = "release_year = :annee";
    $params[':annee'] = $annee;
}
if ($genre !== '') {
    $where[] = "listed_in LIKE :genre";
    $params[':genre'] = "%$genre%";
}

$whereSQL = $where ? 'WHERE ' . implode(' AND ', $where) : '';

$totalStmt = $pdo->prepare("SELECT COUNT(*) FROM filmsa $whereSQL");
$totalStmt->execute($params);
$totalFilms = $totalStmt->fetchColumn();
$totalPages = ceil($totalFilms / $filmsParPage);

$sql = "SELECT show_id, title, platform FROM filmsa $whereSQL ORDER BY title LIMIT :limit OFFSET :offset";
$stmt = $pdo->prepare($sql);
foreach ($params as $key => $val) {
    $stmt->bindValue($key, $val);
}
$stmt->bindValue(':limit', $filmsParPage, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$films = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Films - CinéLove</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        body {
            background: linear-gradient(180deg, #ffeaf1 0%, #fce4ec 100%);
            margin: 0;
            font-family: 'Segoe UI', sans-serif;
        }

        .film-section {
            padding: 40px 20px;
            max-width: 1200px;
            margin: auto;
        }

        .filters {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-bottom: 30px;
            justify-content: center;
        }

        .filters input,
        .filters select {
            padding: 8px;
            border-radius: 5px;
            border: 1px solid #ccc;
        }

        .film-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-top: 30px;
        }

        .film-card {
            background: white;
            border-radius: 10px;
            padding: 15px;
            box-shadow: 0 2px 6px rgba(0,0,0,0.1);
            text-align: center;
        }

        .film-title {
            font-weight: bold;
            color: #e91e63;
            font-size: 1.1em;
            text-decoration: none;
        }

        .film-platform {
            font-size: 0.9em;
            color: #666;
            margin-top: 5px;
        }

        .pagination {
            text-align: center;
            margin-top: 40px;
        }

        .pagination a {
            margin: 0 10px;
            text-decoration: none;
            color: #e91e63;
            font-weight: bold;
        }
    </style>
</head>
<body>
<?php include 'header.php'; ?>

<div class="film-section">
    <h2>🎬 Liste des films</h2>

    <form method="get" class="filters">
        <input type="text" name="titre" placeholder="Titre du film" value="<?= htmlspecialchars($titre) ?>">
        <select name="plateforme">
            <option value="">Plateforme</option>
            <option value="netflix" <?= $plateforme === 'netflix' ? 'selected' : '' ?>>Netflix</option>
            <option value="disney" <?= $plateforme === 'disney' ? 'selected' : '' ?>>Disney+</option>
            <option value="prime" <?= $plateforme === 'prime' ? 'selected' : '' ?>>Prime Video</option>
        </select>
        <input type="number" name="annee" placeholder="Année" value="<?= htmlspecialchars($annee) ?>">
        <input type="text" name="genre" placeholder="Genre (comedy, drama...)" value="<?= htmlspecialchars($genre) ?>">
        <button type="submit">Filtrer</button>
    </form>

    <div class="film-grid">
        <?php foreach ($films as $film): ?>
            <div class="film-card">
                <a class="film-title" href="film_detail.php?id=<?= urlencode($film['show_id']) ?>&platform=<?= urlencode($film['platform']) ?>">
                    <?= htmlspecialchars($film['title']) ?>
                </a>
                <div class="film-platform"><?= htmlspecialchars($film['platform']) ?></div>
            </div>
        <?php endforeach; ?>
    </div>

    <div class="pagination">
        <?php if ($page > 1): ?>
            <a href="?<?= http_build_query(array_merge($_GET, ['page' => $page - 1])) ?>">&laquo; Précédent</a>
        <?php endif; ?>
        <span>Page <?= $page ?> / <?= $totalPages ?></span>
        <?php if ($page < $totalPages): ?>
            <a href="?<?= http_build_query(array_merge($_GET, ['page' => $page + 1])) ?>">Suivant &raquo;</a>
        <?php endif; ?>
    </div>
</div>

</body>
</html>
