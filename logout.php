<?php
session_start();

// Hapus semua data sesi
$_SESSION = [];
session_unset();
session_destroy();

// Hapus cookie jika ada
setcookie('id', '', time() - 3600);
setcookie('key', '', time() - 3600);

// Redirect ke halaman login
header("Location: login.php");
exit;
