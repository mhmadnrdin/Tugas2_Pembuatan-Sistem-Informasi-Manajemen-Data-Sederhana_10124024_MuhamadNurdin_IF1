<?php
require_once 'bahasa.php';
require_once 'koneksi.php';

if (!isset($_SESSION['user_logged_in'])) {
    header("Location: login.php");
    exit();
}

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$stmt = $conn->prepare("SELECT * FROM pengiriman WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$data = $stmt->get_result()->fetch_assoc();

if (!$data) {
    header("Location: index.php");
    exit();
}

$error = '';
$kurir_options = $conn->query("SELECT * FROM kurir ORDER BY nama_kurir ASC");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama_pengirim = htmlspecialchars(trim($_POST['nama_pengirim']));
    $no_hp_pengirim = htmlspecialchars(trim($_POST['no_hp_pengirim']));
    $nama_penerima = htmlspecialchars(trim($_POST['nama_penerima']));
    $no_hp_penerima = htmlspecialchars(trim($_POST['no_hp_penerima']));
    $alamat_tujuan = htmlspecialchars(trim($_POST['alamat_tujuan']));
    $nama_barang = htmlspecialchars(trim($_POST['nama_barang']));
    $quantity = (int)$_POST['quantity'];
    $berat_kg = (float)$_POST['berat_kg'];
    $id_kurir = !empty($_POST['id_kurir']) ? (int)$_POST['id_kurir'] : null;

    if (empty($nama_pengirim) || empty($no_hp_pengirim) || empty($nama_penerima) || empty($no_hp_penerima) || empty($alamat_tujuan) || empty($nama_barang)) {
        $error = t('err_empty');
    } else {
        $stmt_update = $conn->prepare("UPDATE pengiriman SET nama_pengirim = ?, no_hp_pengirim = ?, nama_penerima = ?, no_hp_penerima = ?, alamat_tujuan = ?, nama_barang = ?, quantity = ?, berat_kg = ?, id_kurir = ? WHERE id = ?");
        $stmt_update->bind_param("ssssssidii", $nama_pengirim, $no_hp_pengirim, $nama_penerima, $no_hp_penerima, $alamat_tujuan, $nama_barang, $quantity, $berat_kg, $id_kurir, $id);

        if ($stmt_update->execute()) {
            header("Location: index.php");
            exit();
        } else {
            $error = "Gagal memperbarui data!";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="<?= $lang ?>">
<head>
    <meta charset="UTF-8">
    <title><?= t('edit') ?> Paket</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 40px; background-color: #f9f9f9; }
        .form-container { width: 450px; background: #fff; padding: 20px; border-radius: 5px; box-shadow: 0 0 5px rgba(0,0,0,0.1); }
        .error { color: red; margin-bottom: 10px; font-size: 14px; }
        input[type="text"], input[type="number"], select, textarea { width: 95%; padding: 8px; margin-top: 5px; margin-bottom: 12px; }
        textarea { height: 60px; }
        button { padding: 10px 18px; background-color: #ffc107; color: black; border: none; border-radius: 3px; cursor: pointer; }
        a { text-decoration: none; color: #555; margin-left: 10px; }
    </style>
</head>
<body>

    <div class="form-container">
        <h2><?= t('edit') ?> Paket (Resi: <?= htmlspecialchars($data['no_resi']) ?>)</h2>
        <?php if ($error): ?>
            <div class="error"><?= $error ?></div>
        <?php endif; ?>

        <form method="POST" action="">
            <label><?= t('sender') ?> (Nama & No. HP):</label>
            <input type="text" name="nama_pengirim" value="<?= htmlspecialchars($data['nama_pengirim']) ?>" required>
            <input type="text" name="no_hp_pengirim" value="<?= htmlspecialchars($data['no_hp_pengirim']) ?>" required>

            <label><?= t('recipient') ?> (Nama & No. HP):</label>
            <input type="text" name="nama_penerima" value="<?= htmlspecialchars($data['nama_penerima']) ?>" required>
            <input type="text" name="no_hp_penerima" value="<?= htmlspecialchars($data['no_hp_penerima']) ?>" required>

            <label><?= t('address') ?>:</label>
            <textarea name="alamat_tujuan" required><?= htmlspecialchars($data['alamat_tujuan']) ?></textarea>

            <label>Detail <?= t('item') ?> (Nama, Jumlah, Berat Kg):</label>
            <input type="text" name="nama_barang" value="<?= htmlspecialchars($data['nama_barang']) ?>" required>
            <input type="number" name="quantity" value="<?= $data['quantity'] ?>" min="1" required>
            <input type="number" step="0.01" name="berat_kg" value="<?= $data['berat_kg'] ?>" min="0.1" required>

            <label>Ganti <?= t('courier') ?>:</label>
            <select name="id_kurir">
                <option value="">-- Tanpa Kurir --</option>
                <?php while ($k = $kurir_options->fetch_assoc()): ?>
                    <option value="<?= $k['id'] ?>" <?= $data['id_kurir'] == $k['id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($k['nama_kurir']) ?> (<?= htmlspecialchars($k['no_hp_kurir']) ?>)
                    </option>
                <?php endwhile; ?>
            </select>

            <button type="submit"><?= t('save') ?></button>
            <a href="index.php"><?= t('back') ?></a>
        </form>
    </div>

</body>
</html>