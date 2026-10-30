<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (isset($_GET['lang'])) {
    if ($_GET['lang'] === 'en' || $_GET['lang'] === 'id') {
        $_SESSION['lang'] = $_GET['lang'];
    }
}

$lang = isset($_SESSION['lang']) ? $_SESSION['lang'] : 'id';

$translations = [
    'id' => [
        'app_title' => 'Sistem Informasi Pengiriman Barang & Kurir',
        'login_title' => 'Login Admin',
        'username' => 'Nama Pengguna',
        'password' => 'Kata Sandi',
        'login_btn' => 'Masuk',
        'logout' => 'Keluar',
        'dashboard' => 'Dashboard Pengiriman',
        'courier_list' => 'Daftar Kurir',
        'add_btn' => 'Tambah',
        'select' => 'Pilih',
        'no_resi' => 'No. Resi',
        'sender' => 'Pengirim',
        'recipient' => 'Penerima',
        'address' => 'Alamat Tujuan',
        'item' => 'Barang',
        'courier' => 'Kurir',
        'courier_name' => 'Nama Kurir',
        'courier_phone' => 'No. HP Kurir',
        'status' => 'Status Pengiriman',
        'action' => 'Aksi',
        'save' => 'Simpan',
        'back' => 'Kembali',
        'edit' => 'Ubah',
        'delete' => 'Hapus',
        'update_status' => 'Update Status',
        'unassigned' => 'Belum Ditentukan',
        'no_packages' => 'Belum ada data pengiriman.',
        'no_couriers' => 'Belum ada data kurir.',
        'select_package_alert' => 'Silakan pilih salah satu paket pada tabel terlebih dahulu!',
        'select_courier_alert' => 'Silakan pilih salah satu kurir pada tabel terlebih dahulu!',
        'err_empty' => 'Semua bidang wajib diisi!',
        'err_login' => 'Username atau password salah!',
        'confirm_delete' => 'Apakah Anda yakin ingin menghapus data ini?',
        // Status Translasi
        'status_pending' => 'Pending',
        'status_diproses' => 'Diproses',
        'status_dalam_pengiriman' => 'Dalam Pengiriman',
        'status_selesai' => 'Selesai',
        'status_batal' => 'Batal',
    ],
    'en' => [
        'app_title' => 'Courier & Parcel Delivery System',
        'login_title' => 'Admin Login',
        'username' => 'Username',
        'password' => 'Password',
        'login_btn' => 'Login',
        'logout' => 'Logout',
        'dashboard' => 'Delivery Dashboard',
        'courier_list' => 'Courier List',
        'add_btn' => 'Add',
        'select' => 'Select',
        'no_resi' => 'Tracking No.',
        'sender' => 'Sender',
        'recipient' => 'Recipient',
        'address' => 'Destination Address',
        'item' => 'Item',
        'courier' => 'Courier',
        'courier_name' => 'Courier Name',
        'courier_phone' => 'Phone Number',
        'status' => 'Delivery Status',
        'action' => 'Action',
        'save' => 'Save',
        'back' => 'Back',
        'edit' => 'Edit',
        'delete' => 'Delete',
        'update_status' => 'Update Status',
        'unassigned' => 'Unassigned',
        'no_packages' => 'No delivery data available.',
        'no_couriers' => 'No courier data available.',
        'select_package_alert' => 'Please select a parcel from the table first!',
        'select_courier_alert' => 'Please select a courier from the table first!',
        'err_empty' => 'All fields are required!',
        'err_login' => 'Invalid username or password!',
        'confirm_delete' => 'Are you sure you want to delete this item?',
        // Status Translation
        'status_pending' => 'Pending',
        'status_diproses' => 'In Process',
        'status_dalam_pengiriman' => 'In Transit',
        'status_selesai' => 'Completed',
        'status_batal' => 'Cancelled',
    ]
];

function t($key) {
    global $translations, $lang;
    return isset($translations[$lang][$key]) ? $translations[$lang][$key] : $key;
}
?>