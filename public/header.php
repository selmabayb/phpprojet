<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$userConnected = isset($_SESSION['user_id']) && !empty($_SESSION['username']);
$username = $userConnected ? htmlspecialchars($_SESSION['username']) : '';
?>
<header style="background: #fff; padding: 20px 0; border-bottom: 1px solid #eee; box-shadow: 0 2px 5px rgba(0,0,0,0.05);">
    <div style="max-width: 1200px; margin: 0 auto; text-align: center;">
        <div style="font-size: 28px; font-weight: bold; color: #e91e63; margin-bottom: 10px;">CinéLove</div>
        <nav style="display: inline-flex; gap: 25px;">
            <a href="index.php" style="text-decoration: none; font-weight: bold; color: #333;">Accueil</a>
            <a href="films.php" style="text-decoration: none; font-weight: bold; color: #333;">Films</a>
            <a href="avis.php" style="text-decoration: none; font-weight: bold; color: #333;">Avis</a>
            <a href="profile.php" style="text-decoration: none; font-weight: bold; color: #333;">Profil</a>
            <?php if ($userConnected): ?>
                <a href="logout.php" style="text-decoration: none; font-weight: bold; color: #e91e63;">Déconnexion</a>
            <?php else: ?>
                <a href="login.php" style="text-decoration: none; font-weight: bold; color: #e91e63;">Se connecter</a>
                <a href="register.php" style="text-decoration: none; font-weight: bold; color: #e91e63;">Créer un compte</a>
            <?php endif; ?>
        </nav>
        <?php if ($userConnected): ?>
            <div style="margin-top: 5px; font-size: 14px; color: #777;">Connecté en tant que <?= $username ?></div>
        <?php endif; ?>
    </div>
</header>
