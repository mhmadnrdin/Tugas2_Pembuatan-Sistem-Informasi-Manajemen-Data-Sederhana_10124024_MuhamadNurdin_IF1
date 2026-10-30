<?php
require_once 'bahasa.php';
require_once 'koneksi.php';

// Proteksi Halaman
if (!isset($_SESSION['user_logged_in'])) {
    header("Location: login.php");
    exit();
}

$error = '';

// Ambil daftar kurir untuk dropdown opsi pilihan kurir
$kurir_options = $conn->query("SELECT * FROM kurir ORDER BY nama_kurir ASC");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 1. Sanitasi Data Input
    $no_resi = htmlspecialchars(trim($_POST['no_resi']));
    $nama_pengirim = htmlspecialchars(trim($_POST['nama_pengirim']));
    $no_hp_pengirim = htmlspecialchars(trim($_POST['no_hp_pengirim']));
    $nama_penerima = htmlspecialchars(trim($_POST['nama_penerima']));
    $no_hp_penerima = htmlspecialchars(trim($_POST['no_hp_penerima']));
    $alamat_tujuan = htmlspecialchars(trim($_POST['alamat_tujuan']));
    $nama_barang = htmlspecialchars(trim($_POST['nama_barang']));
    $quantity = (int)$_POST['quantity'];
    $berat_kg = (float)$_POST['berat_kg'];
    $id_kurir = !empty($_POST['id_kurir']) ? (int)$_POST['id_kurir'] : null;

    // 2. Validasi Kelengkapan Data
    if (empty($no_resi) || empty($nama_pengirim) || empty($no_hp_pengirim) || empty($nama_penerima) || empty($no_hp_penerima) || empty($alamat_tujuan) || empty($nama_barang) || $quantity <= 0 || $berat_kg <= 0) {
        $error = t('err_empty');
    } else {
        // 3. Simpan Data Paket ke Database
        $stmt = $conn->prepare("INSERT INTO pengiriman (no_resi, nama_pengirim, no_hp_pengirim, nama_penerima, no_hp_penerima, alamat_tujuan, nama_barang, quantity, berat_kg, id_kurir) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sssssssidi", $no_resi, $nama_pengirim, $no_hp_pengirim, $nama_penerima, $no_hp_penerima, $alamat_tujuan, $nama_barang, $quantity, $berat_kg, $id_kurir);

        if ($stmt->execute()) {
            header("Location: index.php");
            exit();
        } else {
            $error = "Gagal menyimpan data! No Resi mungkin sudah terdaftar.";
        }
    }
}

// Generate Nomor Resi Otomatis (Contoh: REG-20261030-XXXX)
$auto_resi = "REG-" . date("Ymd") . "-" . rand(1000, 9999);
?>
<!DOCTYPE html>
<html lang="<?= $lang ?>">
<head>
    <meta charset="UTF-8">
    <title><?= t('add_package') ?></title>
    <style>
        body { font-family: Arial, sans-serif; margin: 40px; background-color: #f9f9f9; }
        .form-container { width: 450px; background: #fff; padding: 20px; border-radius: 5px; box-shadow: 0 0 5px rgba(0,0,0,0.1); }
        .error { color: red; margin-bottom: 10px; font-size: 14px; }
        input[type="text"], input[type="number"], select, textarea { width: 95%; padding: 8px; margin-top: 5px; margin-bottom: 12px; }
        textarea { height: 60px; }
        button { padding: 10px 18px; background-color: #28a745; color: white; border: none; border-radius: 3px; cursor: pointer; }
        a { text-decoration: none; color: #555; margin-left: 10px; }
    </style>
</head>
<body>

    <div class="form-container">
        <h2><?= t('add_package') ?></h2>
        <?php if ($error): ?>
            <div class="error"><?= $error ?></div>
        <?php endif; ?>

        <form method="POST" action="">
            <label><?= t('no_resi') ?>:</label>
            <input type="text" name="no_resi" value="<?= $auto_resi ?>" required>

            <label><?= t('sender') ?> (Nama & No. HP):</label>
            <input type="text" name="nama_pengirim" placeholder="Nama Pengirim" required>
            <input type="text" name="no_hp_pengirim" placeholder="No. HP Pengirim" required>

            <label><?= t('recipient') ?> (Nama & No. HP):</label>
            <input type="text" name="nama_penerima" placeholder="Nama Penerima" required>
            <input type="text" name="no_hp_penerima" placeholder="No. HP Penerima" required>

            <label><?= t('address') ?>:</label>
            <textarea name="alamat_tujuan" required></textarea>

            <label>Detail <?= t('item') ?> (Nama, Jumlah, Berat Kg):</label>
            <input type="text" name="nama_barang" placeholder="Nama Barang" required>
            <input type="number" name="quantity" placeholder="Jumlah (Quantity)" value="1" min="1" required>
            <input type="number" step="0.01" name="berat_kg" placeholder="Berat (Kg)" value="1.00" min="0.1" required>

            <label>Pilih <?= t('courier') ?>:</label>
            <select name="id_kurir">
                <option value="">-- Pilih Kurir (Opsional) --</option>
                <?php while ($k = $kurir_options->fetch_assoc()): ?>
                    <option value="<?= $k['id'] ?>"><?= htmlspecialchars($k['nama_kurir']) ?> (<?= htmlspecialchars($k['no_hp_kurir']) ?>)</option>
                <?php endwhile; ?>
            </select>

            <button type="submit"><?= t('save') ?></button>
            <a href="index.php"><?= t('back') ?></a>
        </form>
    </div>

</body>
</html>