<?php
require_once 'bahasa.php';
require_once 'koneksi.php';

if (!isset($_SESSION['user_logged_in'])) {
    header("Location: login.php");
    exit();
}

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$stmt = $conn->prepare("SELECT id, no_resi, status_pengiriman FROM pengiriman WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$paket = $stmt->get_result()->fetch_assoc();

if (!$paket) {
    header("Location: index.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $status_baru = htmlspecialchars($_POST['status_pengiriman']);

    $stmt_update = $conn->prepare("UPDATE pengiriman SET status_pengiriman = ? WHERE id = ?");
    $stmt_update->bind_param("si", $status_baru, $id);

    if ($stmt_update->execute()) {
        header("Location: index.php");
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="<?= $lang ?>">
<head>
    <meta charset="UTF-8">
    <title><?= t('update_status') ?></title>
    <style>
        body { font-family: Arial, sans-serif; margin: 40px; background-color: #f9f9f9; }
        .form-container { width: 350px; background: #fff; padding: 20px; border-radius: 5px; box-shadow: 0 0 5px rgba(0,0,0,0.1); }
        select { width: 100%; padding: 8px; margin-top: 10px; margin-bottom: 20px; }
        button { padding: 8px 15px; background-color: #17a2b8; color: white; border: none; border-radius: 3px; cursor: pointer; }
        a { text-decoration: none; color: #555; margin-left: 10px; }
    </style>
</head>
<body>

    <div class="form-container">
        <h2><?= t('update_status') ?></h2>
        <p>No. Resi: <strong><?= htmlspecialchars($paket['no_resi']) ?></strong></p>

        <form method="POST" action="">
            <label><?= t('status') ?> Saat Ini:</label>
            <select name="status_pengiriman">
                <option value="Pending" <?= $paket['status_pengiriman'] == 'Pending' ? 'selected' : '' ?>>Pending</option>
                <option value="Diproses" <?= $paket['status_pengiriman'] == 'Diproses' ? 'selected' : '' ?>>Diproses</option>
                <option value="Dalam Pengiriman" <?= $paket['status_pengiriman'] == 'Dalam Pengiriman' ? 'selected' : '' ?>>Dalam Pengiriman</option>
                <option value="Selesai" <?= $paket['status_pengiriman'] == 'Selesai' ? 'selected' : '' ?>>Selesai</option>
                <option value="Batal" <?= $paket['status_pengiriman'] == 'Batal' ? 'selected' : '' ?>>Batal</option>
            </select>

            <button type="submit"><?= t('save') ?></button>
            <a href="index.php"><?= t('back') ?></a>
        </form>
    </div>

</body>
</html>