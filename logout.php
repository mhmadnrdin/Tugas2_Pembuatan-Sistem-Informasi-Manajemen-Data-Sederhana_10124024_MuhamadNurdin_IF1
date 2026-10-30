<?php
session_start();
// Menghapus seluruh data session
session_unset();
session_destroy();

// Redirect kembali ke halaman login
header("Location: login.php");
exit();
?>