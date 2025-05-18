<?php
session_start();
include 'header.php';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Accueil - CinéLove</title>
    <style>
        body {
            margin: 0;
            font-family: 'Segoe UI', sans-serif;
            background: linear-gradient(to bottom right, #ffeaf4, #ffe2ef);
        }
        main {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-align: center;
            padding: 100px 20px;
        }
        h1 {
            font-size: 48px;
            color: #e91e63;
            margin-bottom: 10px;
        }
        p {
            font-size: 18px;
            color: #444;
            margin-bottom: 30px;
        }
        a.button {
            background-color: #e91e63;
            color: white;
            padding: 15px 25px;
            font-size: 16px;
            border-radius: 30px;
            text-decoration: none;
            font-weight: bold;
        }
        a.button:hover {
            background-color: #d81b60;
        }
    </style>
</head>
<body>
    <main>
        <h1>Bienvenue sur CinéLove 💕</h1>
        <p>Explorez, notez et partagez vos films préférés</p>
        <a href="films.php" class="button">🎬 Voir les films</a>
    </main>
</body>
</html>
