<?php
require_once 'bahasa.php';
require_once 'koneksi.php';

// Proteksi Halaman (Session Guard)
if (!isset($_SESSION['user_logged_in'])) {
    header("Location: login.php");
    exit();
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 1. Sanitasi Data Input
    $nama_kurir = htmlspecialchars(trim($_POST['nama_kurir']));
    $no_hp_kurir = htmlspecialchars(trim($_POST['no_hp_kurir']));

    // 2. Validasi Kelengkapan Data
    if (empty($nama_kurir) || empty($no_hp_kurir)) {
        $error = t('err_empty');
    } else {
        // 3. Simpan Data ke Database menggunakan Prepared Statement
        $stmt = $conn->prepare("INSERT INTO kurir (nama_kurir, no_hp_kurir) VALUES (?, ?)");
        $stmt->bind_param("ss", $nama_kurir, $no_hp_kurir);

        if ($stmt->execute()) {
            header("Location: index.php");
            exit();
        } else {
            $error = "Error: " . $stmt->error;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="<?= $lang ?>">
<head>
    <meta charset="UTF-8">
    <title><?= t('add_courier') ?></title>
    <style>
        body { font-family: Arial, sans-serif; margin: 40px; background-color: #f9f9f9; }
        .form-container { width: 350px; background: #fff; padding: 20px; border-radius: 5px; box-shadow: 0 0 5px rgba(0,0,0,0.1); }
        .error { color: red; margin-bottom: 10px; font-size: 14px; }
        input[type="text"] { width: 93%; padding: 8px; margin-top: 5px; margin-bottom: 15px; }
        button { padding: 8px 15px; background-color: #28a745; color: white; border: none; border-radius: 3px; cursor: pointer; }
        a { text-decoration: none; color: #555; margin-left: 10px; }
    </style>
</head>
<body>

    <div class="form-container">
        <h2><?= t('add_courier') ?></h2>
        <?php if ($error): ?>
            <div class="error"><?= $error ?></div>
        <?php endif; ?>

        <form method="POST" action="">
            <label>Nama Kurir:</label><br>
            <input type="text" name="nama_kurir" required>

            <label>No. HP Kurir:</label><br>
            <input type="text" name="no_hp_kurir" required>

            <button type="submit"><?= t('save') ?></button>
            <a href="index.php"><?= t('back') ?></a>
        </form>
    </div>

</body>
</html>