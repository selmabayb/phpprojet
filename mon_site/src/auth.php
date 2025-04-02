<?php
include 'database.php';

// Pour l'inscription
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['username'], $_POST['email'], $_POST['password'])) {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);

    // Insérer l'utilisateur dans la base de données
    $sql = "INSERT INTO users (username, email, password) VALUES ('$username', '$email', '$password')";
    if ($conn->query($sql) === TRUE) {
        echo "Inscription réussie !";
    } else {
        echo "Erreur: " . $conn->error;
    }
}

// Pour la connexion
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['email'], $_POST['password'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Vérifier l'email et le mot de passe
    $sql = "SELECT * FROM users WHERE email='$email'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        if (password_verify($password, $user['password'])) {
            session_start();
            $_SESSION['user_id'] = $user['user_id'];
            echo "Connexion réussie !";
        } else {
            echo "Mot de passe incorrect.";
        }
    } else {
        echo "Aucun utilisateur trouvé avec cet email.";
    }
}
?>
