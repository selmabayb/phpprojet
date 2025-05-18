<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

if (!isset($_POST['action']) || !isset($_POST['show_id']) || !isset($_POST['platform'])) {
    header("Location: index.php");
    exit;
}

$action = $_POST['action'];
$show_id = $_POST['show_id'];
$platform = $_POST['platform'];

// Définit la redirection par défaut
$return_to = isset($_POST['return_to']) && !empty($_POST['return_to'])
    ? $_POST['return_to']
    : "film_detail.php?id=$show_id&platform=$platform";

if ($action === 'like_review') {
    if (!isset($_POST['review_id'])) {
        header("Location: $return_to");
        exit;
    }

    $review_id = $_POST['review_id'];

    $stmt = $pdo->prepare("SELECT 1 FROM like_reviews WHERE user_id = ? AND review_id = ?");
    $stmt->execute([$user_id, $review_id]);

    if (!$stmt->fetch()) {
        $stmt = $pdo->prepare("INSERT INTO like_reviews (user_id, review_id) VALUES (?, ?)");
        $stmt->execute([$user_id, $review_id]);
    }

    header("Location: $return_to");
    exit;
}

if ($action === 'reply_review') {
    if (!isset($_POST['review_id']) || !isset($_POST['response'])) {
        header("Location: $return_to");
        exit;
    }

    $review_id = $_POST['review_id'];
    $response = $_POST['response'];

    $stmt = $pdo->prepare("INSERT INTO responses (review_id, user_id, response) VALUES (?, ?, ?)");
    $stmt->execute([$review_id, $user_id, $response]);

    header("Location: $return_to");
    exit;
}

// Autres interactions
switch ($action) {
    case 'add_to_watchlist':
        $stmt = $pdo->prepare("INSERT IGNORE INTO favori_films (user_id, show_id, platform) VALUES (?, ?, ?)");
        $stmt->execute([$user_id, $show_id, $platform]);
        break;

    case 'mark_as_seen':
        $stmt = $pdo->prepare("INSERT IGNORE INTO films_vus (user_id, show_id, platform) VALUES (?, ?, ?)");
        $stmt->execute([$user_id, $show_id, $platform]);

        $stmt = $pdo->prepare("DELETE FROM favori_films WHERE user_id = ? AND show_id = ? AND platform = ?");
        $stmt->execute([$user_id, $show_id, $platform]);
        break;

    case 'add_top3':
        if (!isset($_POST['position'])) break;
        $position = $_POST['position'];

        $stmt = $pdo->prepare("DELETE FROM top_3 WHERE user_id = ? AND position = ?");
        $stmt->execute([$user_id, $position]);

        $stmt = $pdo->prepare("INSERT INTO top_3 (user_id, show_id, platform, position) VALUES (?, ?, ?, ?)");
        $stmt->execute([$user_id, $show_id, $platform, $position]);
        break;

    case 'add_review':
        if (!isset($_POST['note']) || !isset($_POST['avis'])) break;
        $note = $_POST['note'];
        $avis = $_POST['avis'];

        $stmt = $pdo->prepare("DELETE FROM reviews WHERE user_id = ? AND show_id = ? AND platform = ?");
        $stmt->execute([$user_id, $show_id, $platform]);

        $stmt = $pdo->prepare("INSERT INTO reviews (user_id, show_id, platform, rating, review) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$user_id, $show_id, $platform, $note, $avis]);
        break;
}

header("Location: $return_to");
exit;
