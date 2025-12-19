<?php
// core/auth.php
require_once __DIR__ . '/functions.php';

function login($username, $password)
{
    $user = db_fetch("SELECT * FROM users WHERE username = ?", [$username]);
    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user'] = $user;
        return true;
    }
    return false;
}

function register_user($username, $password, $fullname)
{
    $hash = password_hash($password, PASSWORD_DEFAULT);
    try {
        return db_execute(
            "INSERT INTO users (username, password, fullname, role) VALUES (?, ?, ?, 0)",
            [$username, $hash, $fullname]
        );
    } catch (Exception $e) {
        return false;
    }
}

function logout()
{
    unset($_SESSION['user']);
}
