<?php
session_start();
require_once 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'login') {
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';

        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['username'] = $user['username'];
            header("Location: profile.php");
            exit;
        } else {
            header("Location: login.php?error=1");
            exit;
        }
    }

    if ($action === 'register') {
        $username = trim($_POST['username']);
        $email = trim($_POST['email']);
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

        $check = $pdo->prepare("SELECT * FROM users WHERE email = ?");
        $check->execute([$email]);

        if ($check->fetch()) {
            header("Location: register.php?exists=1");
            exit;
        }

        $stmt = $pdo->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
        $stmt->execute([$username, $email, $password]);

        $new_id = $pdo->lastInsertId();
        $_SESSION['user_id'] = $new_id;
        $_SESSION['username'] = $username;
        header("Location: profile.php");
        exit;
    }
}

header("Location: index.php");
exit;
