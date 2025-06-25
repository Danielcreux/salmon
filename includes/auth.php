<?php
// includes/auth.php

session_start();

function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function getUserRole() {
    return $_SESSION['user_rol'] ?? null;
}

function redirectIfNotLoggedIn() {
    if (!isLoggedIn()) {
        header("Location: login.php");
        exit();
    }
}

function redirectBasedOnRole() {
    if (isLoggedIn()) {
        $role = getUserRole();
        if ($role === 'admin') {
            header("Location: usuarios.php");
        } elseif ($role === 'jefe') {
            header("Location: asistencias.php");
        } elseif ($role === 'cliente') {
            header("Location: checkin.php");
        }
        exit();
    }
}

function checkRole($allowedRoles) {
    if (!isLoggedIn() || !in_array(getUserRole(), $allowedRoles)) {
        header("Location: panel.php");
        exit();
    }
}
?>