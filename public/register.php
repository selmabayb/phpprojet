<?php
session_start();
if (isset($_SESSION['user_id'])) {
    header("Location: profile.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Créer un compte - CinéLove</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background: linear-gradient(145deg, #ffeaf1, #f0f4ff);
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .register-box {
            background: white;
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0 0 15px rgba(0,0,0,0.1);
            max-width: 400px;
            width: 100%;
        }
        .register-box h2 {
            color: #e91e63;
            text-align: center;
            margin-bottom: 25px;
        }
        form {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }
        input {
            padding: 12px;
            border-radius: 6px;
            border: 1px solid #ccc;
            font-size: 1em;
        }
        button {
            padding: 12px;
            background-color: #e91e63;
            border: none;
            border-radius: 6px;
            color: white;
            font-size: 1em;
            cursor: pointer;
        }
        .nav {
            margin-top: 20px;
            text-align: center;
        }
        .nav a {
            color: #e91e63;
            text-decoration: none;
        }
    </style>
</head>
<body>
    <div class="register-box">
        <h2>Créer un compte</h2>
        <form method="POST" action="auth.php">
            <input type="hidden" name="action" value="register">
            <input type="text" name="username" placeholder="Nom d'utilisateur" required>
            <input type="email" name="email" placeholder="Adresse e-mail" required>
            <input type="password" name="password" placeholder="Mot de passe" required>
            <button type="submit">S'inscrire</button>
        </form>
        <div class="nav">
            <a href="login.php">Déjà un compte ? Se connecter</a>
        </div>
    </div>
</body>
</html>
