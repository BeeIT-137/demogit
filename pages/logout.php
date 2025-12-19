<?php
require_once __DIR__ . '/../core/auth.php';
logout();
redirect(BASE_URL . '/index.php');
