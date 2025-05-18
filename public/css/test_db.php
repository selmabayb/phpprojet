<?php
require_once 'db.php';

try {
    $stmt = $pdo->query("SELECT COUNT(*) FROM filmsa");
    $totalFilms = $stmt->fetchColumn();
    echo "Nombre total de films dans filmsa : $totalFilms<br>";

    if ($totalFilms > 0) {
        $stmt2 = $pdo->query("SELECT title, platform FROM filmsa LIMIT 5");
        $films = $stmt2->fetchAll();

        echo "<ul>";
        foreach ($films as $film) {
            echo "<li>" . htmlspecialchars($film['title']) . " (" . htmlspecialchars($film['platform']) . ")</li>";
        }
        echo "</ul>";
    } else {
        echo "La table filmsa est vide.";
    }
} catch (Exception $e) {
    echo "Erreur SQL : " . $e->getMessage();
}
