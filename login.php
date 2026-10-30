<?php
require_once 'bahasa.php';
require_once 'koneksi.php';

// Jika admin sudah login, langsung arahkan ke index.php
if (isset($_SESSION['user_logged_in'])) {
    header("Location: index.php");
    exit();
}

$error = '';

// Memproses input form saat dikirim via metode POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 1. Sanitasi Input (menghapus spasi berlebih & mencegah tag HTML jahat)
    $username = htmlspecialchars(trim($_POST['username']));
    $password = trim($_POST['password']);

    // 2. Validasi Kelengkapan Data dengan percabangan (if-else)
    if (empty($username) || empty($password)) {
        $error = t('err_empty');
    } else {
        // Prepared Statement untuk mencegah SQL Injection
        $stmt = $conn->prepare("SELECT id, username, password FROM users WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($user = $result->fetch_assoc()) {
            // Verifikasi kecocokan password plaintext dengan hash password
            if ($password === $user['password']) {
                // Simpan status login ke dalam session
                $_SESSION['user_logged_in'] = true;
                $_SESSION['username'] = $user['username'];
                
                header("Location: index.php");
                exit();
            } else {
                $error = t('err_login');
            }
        } else {
            $error = t('err_login');
        }
    }
}
?>
<!DOCTYPE html>
<html lang="<?= $lang ?>">
<head>
    <meta charset="UTF-8">
    <title><?= t('login_title') ?></title>
    <style>
        body { font-family: Arial, sans-serif; margin: 50px; background-color: #f4f4f4; }
        .login-box { width: 320px; padding: 20px; background: #fff; border: 1px solid #ccc; margin: auto; border-radius: 5px; }
        .error { color: red; margin-bottom: 10px; font-size: 14px; }
        .lang-switch { text-align: right; margin-bottom: 10px; }
        input[type="text"], input[type="password"] { width: 93%; padding: 8px; margin-top: 5px; margin-bottom: 15px; }
        button { width: 100%; padding: 10px; background-color: #007bff; color: white; border: none; border-radius: 3px; cursor: pointer; }
    </style>
</head>
<body>

    <div class="login-box">
        <div class="lang-switch">
            <a href="?lang=id">ID</a> | <a href="?lang=en">EN</a>
        </div>
        <h2><?= t('login_title') ?></h2>
        
        <?php if ($error): ?>
            <div class="error"><?= $error ?></div>
        <?php endif; ?>

        <form method="POST" action="">
            <label><?= t('username') ?></label>
            <input type="text" name="username" required>

            <label><?= t('password') ?></label>
            <input type="password" name="password" required>

            <button type="submit"><?= t('login_btn') ?></button>
        </form>
    </div>

</body>
</html>