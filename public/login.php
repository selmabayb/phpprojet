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
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion - CinéLove</title>
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
        .login-box {
            background: white;
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0 0 15px rgba(0,0,0,0.1);
            max-width: 400px;
            width: 100%;
        }
        .login-box h2 {
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
        .error {
            color: red;
            text-align: center;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
    <div class="login-box">
        <h2>Se connecter</h2>
        <?php if (isset($_GET['error'])): ?>
            <div class="error">Email ou mot de passe incorrect.</div>
        <?php endif; ?>
        <form method="POST" action="auth.php">
            <input type="hidden" name="action" value="login">
            <input type="email" name="email" placeholder="Adresse e-mail" required>
            <input type="password" name="password" placeholder="Mot de passe" required>
            <button type="submit">Se connecter</button>
        </form>
        <div class="nav">
            <a href="register.php">Créer un compte</a>
        </div>
    </div>
</body>
</html>
