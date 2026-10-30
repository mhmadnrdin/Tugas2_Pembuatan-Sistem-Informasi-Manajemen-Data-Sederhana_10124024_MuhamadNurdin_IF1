<?php
require_once 'bahasa.php';
require_once 'koneksi.php';

// 1. Proteksi Otentikasi Login (Session Guard)
if (!isset($_SESSION['user_logged_in'])) {
    header("Location: login.php");
    exit();
}

// 2. Query Read Data Paket Pengiriman
$sql_pengiriman = "SELECT p.*, k.nama_kurir, k.no_hp_kurir 
                   FROM pengiriman p 
                   LEFT JOIN kurir k ON p.id_kurir = k.id 
                   ORDER BY p.id DESC";
$result_pengiriman = $conn->query($sql_pengiriman);

// 3. Query Read Data Kurir
$sql_kurir = "SELECT * FROM kurir ORDER BY id ASC";
$result_kurir = $conn->query($sql_kurir);
?>
<!DOCTYPE html>
<html lang="<?= $lang ?>">
<head>
    <meta charset="UTF-8">
    <title><?= t('app_title') ?></title>
    <style>
        body { font-family: Arial, sans-serif; margin: 30px; background-color: #f9f9f9; }
        .top-bar { display: flex; justify-content: space-between; align-items: center; background: #fff; padding: 15px; border-radius: 5px; box-shadow: 0 0 5px rgba(0,0,0,0.1); }
        .card { background: #fff; padding: 20px; border-radius: 5px; margin-top: 20px; box-shadow: 0 0 5px rgba(0,0,0,0.1); }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th, td { border: 1px solid #ddd; padding: 10px; text-align: left; font-size: 14px; }
        th { background-color: #f2f2f2; }
        
        /* Container Tombol Sejajar */
        .action-toolbar { display: flex; align-items: center; gap: 10px; margin-bottom: 10px; }
        
        .btn { padding: 8px 14px; text-decoration: none; border-radius: 4px; font-size: 13px; color: white; border: none; cursor: pointer; display: inline-block; }
        .btn-add { background-color: #28a745; }
        .btn-status { background-color: #17a2b8; }
        .btn-edit { background-color: #ffc107; color: black; }
        .btn-delete { background-color: #dc3545; }
        
        .badge { padding: 4px 8px; border-radius: 3px; color: white; font-weight: bold; font-size: 12px; }
        .bg-pending { background-color: #6c757d; }
        .bg-proses { background-color: #ffc107; color: black; }
        .bg-kirim { background-color: #17a2b8; }
        .bg-selesai { background-color: #28a745; }
        .bg-batal { background-color: #dc3545; }
    </style>
</head>
<body>

    <!-- Navigasi Atas & Dual Bahasa -->
    <div class="top-bar">
        <div>
            <strong><?= t('app_title') ?></strong> | 
            <a href="?lang=id">Bahasa Indonesia</a> | 
            <a href="?lang=en">English</a>
        </div>
        <div>
            <?= t('username') ?>: <strong><?= htmlspecialchars($_SESSION['username']) ?></strong> | 
            <a href="logout.php" style="color: red;"><?= t('logout') ?></a>
        </div>
    </div>

    <!-- Section 1: Dashboard Pengiriman -->
    <div class="card">
        <h2><?= t('dashboard') ?></h2>

        <!-- Baris Tombol Aksi Paket Sejajar -->
        <div class="action-toolbar">
            <a href="tambah_paket.php" class="btn btn-add">+ <?= t('add_btn') ?></a>
            <button type="button" onclick="prosesAksiPaket('status')" class="btn btn-status"><?= t('update_status') ?></button>
            <button type="button" onclick="prosesAksiPaket('edit')" class="btn btn-edit"><?= t('edit') ?></button>
            <button type="button" onclick="prosesAksiPaket('delete')" class="btn btn-delete"><?= t('delete') ?></button>
        </div>

        <table>
            <thead>
                <tr>
                    <th style="width: 50px; text-align: center;"><?= t('select') ?></th>
                    <th><?= t('no_resi') ?></th>
                    <th><?= t('sender') ?></th>
                    <th><?= t('recipient') ?></th>
                    <th><?= t('address') ?></th>
                    <th><?= t('item') ?></th>
                    <th><?= t('courier') ?></th>
                    <th><?= t('status') ?></th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result_pengiriman->num_rows > 0): ?>
                    <?php while ($row = $result_pengiriman->fetch_assoc()): ?>
                        <tr>
                            <td style="text-align: center;">
                                <input type="radio" name="pilih_paket" value="<?= $row['id'] ?>">
                            </td>
                            <td><strong><?= htmlspecialchars($row['no_resi']) ?></strong></td>
                            <td>
                                <?= htmlspecialchars($row['nama_pengirim']) ?><br>
                                <small><?= htmlspecialchars($row['no_hp_pengirim']) ?></small>
                            </td>
                            <td>
                                <?= htmlspecialchars($row['nama_penerima']) ?><br>
                                <small><?= htmlspecialchars($row['no_hp_penerima']) ?></small>
                            </td>
                            <td><?= htmlspecialchars($row['alamat_tujuan']) ?></td>
                            <td>
                                <?= htmlspecialchars($row['nama_barang']) ?><br>
                                <small><?= $row['quantity'] ?> pcs / <?= $row['berat_kg'] ?> kg</small>
                            </td>
                            <td>
                                <?php if ($row['nama_kurir']): ?>
                                    <?= htmlspecialchars($row['nama_kurir']) ?><br>
                                    <small><?= htmlspecialchars($row['no_hp_kurir']) ?></small>
                                <?php else: ?>
                                    <em style="color: gray;">- <?= t('unassigned') ?> -</em>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php 
                                    $status = $row['status_pengiriman'];
                                    $badge_class = 'bg-pending';
                                    if ($status == 'Diproses') $badge_class = 'bg-proses';
                                    if ($status == 'Dalam Pengiriman') $badge_class = 'bg-kirim';
                                    if ($status == 'Selesai') $badge_class = 'bg-selesai';
                                    if ($status == 'Batal') $badge_class = 'bg-batal';
                                ?>
                                <span class="badge <?= $badge_class ?>"><?= t('status_' . strtolower(str_replace(' ', '_', $status))) ?></span>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="8" style="text-align: center;"><?= t('no_packages') ?></td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- Section 2: Daftar Kurir -->
    <div class="card">
        <h2><?= t('courier_list') ?></h2>
        
        <!-- Baris Tombol Aksi Kurir Sejajar -->
        <div class="action-toolbar">
            <a href="tambah_kurir.php" class="btn btn-add">+ <?= t('add_btn') ?></a>
            <button type="button" onclick="prosesAksiKurir('delete')" class="btn btn-delete"><?= t('delete') ?></button>
        </div>

        <table>
            <thead>
                <tr>
                    <th style="width: 50px; text-align: center;"><?= t('select') ?></th>
                    <th>ID</th>
                    <th><?= t('courier_name') ?></th>
                    <th><?= t('courier_phone') ?></th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result_kurir->num_rows > 0): ?>
                    <?php while ($k = $result_kurir->fetch_assoc()): ?>
                        <tr>
                            <td style="text-align: center;">
                                <input type="radio" name="pilih_kurir" value="<?= $k['id'] ?>">
                            </td>
                            <td><?= $k['id'] ?></td>
                            <td><?= htmlspecialchars($k['nama_kurir']) ?></td>
                            <td><?= htmlspecialchars($k['no_hp_kurir']) ?></td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="4" style="text-align: center;"><?= t('no_couriers') ?></td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- Script JavaScript untuk Menangani Aksi Tombol Toolbar -->
    <script>
        function prosesAksiPaket(tipeAksi) {
            const radioTerpilih = document.querySelector('input[name="pilih_paket"]:checked');
            
            if (!radioTerpilih) {
                alert('<?= t('select_package_alert') ?>');
                return;
            }

            const idPaket = radioTerpilih.value;

            if (tipeAksi === 'status') {
                window.location.href = 'status_paket.php?id=' + idPaket;
            } else if (tipeAksi === 'edit') {
                window.location.href = 'edit_paket.php?id=' + idPaket;
            } else if (tipeAksi === 'delete') {
                if (confirm('<?= t('confirm_delete') ?>')) {
                    window.location.href = 'hapus_paket.php?id=' + idPaket;
                }
            }
        }

        function prosesAksiKurir(tipeAksi) {
            const radioTerpilih = document.querySelector('input[name="pilih_kurir"]:checked');
            
            if (!radioTerpilih) {
                alert('<?= t('select_courier_alert') ?>');
                return;
            }

            const idKurir = radioTerpilih.value;

            if (tipeAksi === 'delete') {
                if (confirm('<?= t('confirm_delete') ?>')) {
                    window.location.href = 'hapus_kurir.php?id=' + idKurir;
                }
            }
        }
    </script>

</body>
</html>