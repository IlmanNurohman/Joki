<?php

session_start();

// Memeriksa apakah userId dan userType ada dalam session
if (!isset($_SESSION['user_id']) || !isset($_SESSION['userType'])) {
    // Jika tidak ada session userId atau userType, redirect ke halaman login
    header("Location: login.php");
    exit();
}

// Jika session ada, simpan userId ke dalam variabel
$userId = $_SESSION['user_id'];
$userType = $_SESSION['userType'];

$userType = $_SESSION['userType']; // Mendapatkan tipe user (admin atau kader)
$userId = $_SESSION['user_id']; // Mendapatkan user ID dari session

require 'config.php';

// Tentukan jumlah accordion (berdasarkan tanggal) per halaman
$accordionPerPage = 10;

// Ambil nomor halaman saat ini, jika tidak ada, default ke halaman 1
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;

// Tentukan offset berdasarkan halaman saat ini
$offset = ($page - 1) * $accordionPerPage;

// Proses pencarian user
$searchTerm = '';
if (isset($_GET['search'])) {
    $searchTerm = $_GET['search'];
    // Query untuk mendapatkan data bayi berdasarkan pencarian
    $stmt = $pdo->prepare("
        SELECT * FROM data_bumil 
        WHERE tanggal_pendataan LIKE :search AND user_id = :userId 
        ORDER BY tanggal_pendataan DESC
    ");
    $stmt->bindValue(':search', '%' . $searchTerm . '%');
    $stmt->bindValue(':userId', $userId); // Tambahkan filter user_id
} else {
    // Query untuk mendapatkan semua data bayi hanya untuk user yang sedang login
    $stmt = $pdo->prepare("
        SELECT * FROM data_bumil 
        WHERE user_id = :userId 
        ORDER BY tanggal_pendataan DESC
    ");
    $stmt->bindValue(':userId', $userId); // Tambahkan filter user_id
}
$stmt->execute();
$data_bumil = $stmt->fetchAll();

// Buat array untuk mengelompokkan data berdasarkan tanggal_pendataan
$groupedData = [];
foreach ($data_bumil as $data) {
    $tanggal = $data['tanggal_pendataan'];
    if (!isset($groupedData[$tanggal])) {
        $groupedData[$tanggal] = [];
    }
    $groupedData[$tanggal][] = $data;
}

// Total jumlah tanggal pendataan
$totalTanggal = count($groupedData);

// Tentukan jumlah halaman
$totalPages = ceil($totalTanggal / $accordionPerPage);

// Ambil data untuk halaman saat ini
$tanggalKeys = array_keys($groupedData);
$tanggalKeysPage = array_slice($tanggalKeys, $offset, $accordionPerPage);

// Hanya tampilkan data berdasarkan tanggal pada halaman saat ini
$groupedDataPage = [];
foreach ($tanggalKeysPage as $tanggal) {
    $groupedDataPage[$tanggal] = $groupedData[$tanggal];
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lihat Data Bumil</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.3/font/bootstrap-icons.css">
    <style>
    .navbar-brand img {
        height: 40px;
        /* Sesuaikan ukuran logo */
        width: auto;
    }

    .custom-container {
        background-color: #f1f1f1;
        padding: 20px;
        border-radius: 10px;
    }

    .table-responsive {
        width: 100%;
        overflow-x: auto;
    }

    .accordion-body {
        padding: 0;
    }

    .table {
        width: 100%;
        max-width: 100%;
    }

    /* Menengahkan isi kolom */
    .table td,
    .table th {
        text-align: center;
        /* Posisikan isi kolom di tengah */
        vertical-align: middle;
        /* Posisikan secara vertikal di tengah */
    }

    .accordion-container {
        border: 1px solid #000;
        /* Menambahkan border hitam pada container */
        border-radius: 8px;
        /* Membuat sudut melengkung */
        padding: 20px;
        /* Jarak antara isi container dengan border */
        background-color: #f9f9f9;
        /* Menambahkan latar belakang container */
    }

    .table-custom {
        border-collapse: collapse;
        width: 100%;
        margin-bottom: 1rem;
        margin-top: 1%;
        color: #212529;
        background-color: #fff;
        border-radius: 8px;
        overflow: hidden;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }

    .table-custom th {
        background-color: #007bff;
        color: white;
        text-align: center;
        padding: 12px;
        font-weight: bold;
    }

    .table-custom td {
        padding: 12px;
        text-align: center;
        vertical-align: middle;
    }

    .table-custom tbody tr {
        border-bottom: 1px solid #dee2e6;
        transition: background-color 0.3s;
    }

    .table-custom tbody tr:hover {
        background-color: #f1f1f1;
    }

    /* Responsive untuk mobile */
    .table-responsive-custom {
        display: block;
        width: 100%;
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
    }

    .custom-navbar {
        background-color: #66b3ff;
        /* Kode warna biru muda */
    }
    </style>
</head>

<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-light custom-navbar">
        <div class="container-fluid">
            <a class="navbar-brand" href="#" style="font-family: Georgia, 'Times New Roman', Times, serif;">
                <img src="img/posyandu.png" alt="" />
                POSMAP
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link active" aria-current="page" href="index.php">Dashboard</a>
                    </li>

                    <!-- Dropdown Pendataan -->
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="pendataanDropdown" role="button"
                            data-bs-toggle="dropdown" aria-expanded="false">
                            Pendataan
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="pendataanDropdown">
                            <li><a class="dropdown-item" href="lihat_data_bayi_balita.php">Data Bayi/Balita</a></li>
                            <li><a class="dropdown-item" href="lihat_data_bumil.php">Data Ibu Hamil</a></li>
                            <li><a class="dropdown-item" href="lihat_data_kb.php">Data Pengguna KB</a></li>
                            <li><a class="dropdown-item" href="data_vitamin.php">Data Sasaran Vitamin A</a></li>
                        </ul>
                    </li>

                    <!-- Dropdown Jadwal -->
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="jadwalDropdown" role="button"
                            data-bs-toggle="dropdown" aria-expanded="false">
                            Jadwal
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="jadwalDropdown">
                            <li><a class="dropdown-item" href="#">Jadwal Posyandu</a></li>
                            <li><a class="dropdown-item" href="#">Jadwal Penyuluhan</a></li>
                            <li><a class="dropdown-item" href="#">Jadwal Pemberian Makanan Tambahan</a></li>
                        </ul>
                    </li>

                    <!-- Dropdown Informasi -->
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="informasiDropdown" role="button"
                            data-bs-toggle="dropdown" aria-expanded="false">
                            Informasi
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="informasiDropdown">
                            <li><a class="dropdown-item" href="#">Informasi Kegiatan</a></li>
                            <li><a class="dropdown-item" href="#">Informasi Pendanaan Dana Sehat</a></li>
                            <?php if ($userType == 'kader'): ?>
                            <li><a class="dropdown-item" href="informasi_akun.php">Informasi Akun</a></li>

                            <?php endif; ?>
                            <li><a class="dropdown-item" href="#" data-bs-toggle="modal"
                                    data-bs-target="#logoutModal">Logout</a></li>
                        </ul>
                    </li>


                    <!-- Dropdown Data (Tampilkan jika userType adalah admin) -->
                    <?php if ($userType == 'admin'): ?>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="dataDropdown" role="button"
                            data-bs-toggle="dropdown" aria-expanded="false">
                            Data
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="dataDropdown">
                            <li><a class="dropdown-item" href="#">Data Pengguna</a></li>
                        </ul>
                    </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>
    <!-- Bootstrap Modal untuk konfirmasi logout -->
    <div class="modal fade" id="logoutModal" tabindex="-1" aria-labelledby="logoutModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="logoutModalLabel">Konfirmasi Logout</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Apakah Anda yakin ingin keluar?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <!-- Tombol Ya akan mengarahkan ke halaman logout.php -->
                    <a href="logout_proces.php" class="btn btn-primary">Ya</a>
                </div>
            </div>
        </div>
    </div>

    <div class="container mt-4">

        <h3><i class="bi bi-journals"></i>Data Bumil</h3>

        <form class="d-flex mb-3" method="GET" action="lihat_data_bumil.php">
            <input class="form-control me-2" type="search" placeholder="Cari..." aria-label="Search" name="search"
                value="<?php echo htmlspecialchars($searchTerm); ?>">
            <button class="btn btn-primary  btn-custom" type="submit">Cari</button>
        </form>
        <div class="custom-container">
            <a href="data_bumil.php" class="btn btn-primary mb-3">
                <i class="bi bi-plus-circle"></i> Tambah Pendataan
            </a>
            <button class="btn btn-danger mb-3" id="deleteSelectedBtn" onclick="confirmDelete()">
                <i class="bi bi-trash"></i> Hapus Terpilih
            </button>
            <!-- Membungkus accordion dengan container yang sudah diberi border dan padding -->
            <div class="accordion-container border p-3">
                <!-- Accordion for grouping data by 'tanggal_pendataan' -->
                <div class="accordion" id="dataAccordion">
                    <?php foreach ($groupedDataPage as $tanggal => $bumilList): ?>
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="heading-<?php echo $tanggal; ?>">
                            <button class="accordion-button" type="button" data-bs-toggle="collapse"
                                data-bs-target="#collapse-<?php echo $tanggal; ?>" aria-expanded="true"
                                aria-controls="collapse-<?php echo $tanggal; ?>">
                                Tanggal Pendataan: <?php echo $tanggal; ?>
                            </button>
                        </h2>
                        <div id="collapse-<?php echo $tanggal; ?>" class="accordion-collapse collapse"
                            aria-labelledby="heading-<?php echo $tanggal; ?>" data-bs-parent="#dataAccordion">
                            <div class="accordion-body">
                                <div class="table-responsive-custom">
                                    <table class="table table-custom">
                                        <thead>
                                            <tr>
                                                <th><input type="checkbox" id="selectAll-<?php echo $tanggal; ?>" />
                                                </th>
                                                <th>Nama</th>
                                                <th>Nama Suami</th>
                                                <th>Usia Ibu</th>
                                                <th>Usia Kandungan</th>
                                                <th>Alamat</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($bumilList as $data): ?>
                                            <tr>
                                                <td>
                                                    <input type="checkbox" name="selected[]"
                                                        value="<?php echo $data['id']; ?>" />
                                                </td>
                                                <td><?php echo $data['nama']; ?></td>
                                                <td><?php echo $data['nama_suami']; ?></td>
                                                <td><?php echo $data['usia_ibu'] . ' kg'; ?></td>
                                                <td><?php echo $data['usia_kandungan'] . ' cm'; ?></td>
                                                <td><?php echo $data['alamat']; ?></td>
                                            </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <!-- Modal Konfirmasi Hapus -->
        <div class="modal fade" id="deleteConfirmationModal" tabindex="-1" aria-labelledby="deleteConfirmationLabel"
            aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="deleteConfirmationLabel">Konfirmasi Hapus</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        Apakah Anda yakin ingin menghapus data yang dipilih?
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="button" class="btn btn-danger" id="confirmDeleteBtn">Hapus</button>
                    </div>
                </div>
            </div>
        </div>
        <!-- Tampilkan kontrol pagination -->
        <nav aria-label="Page navigation">
            <ul class="pagination justify-content-center">
                <!-- Tombol "Previous" -->
                <li class="page-item <?php echo ($page <= 1) ? 'disabled' : ''; ?>">
                    <a class="page-link"
                        href="?page=<?php echo $page - 1; ?><?php echo $searchTerm ? '&search=' . urlencode($searchTerm) : ''; ?>"
                        aria-label="Previous">
                        <span aria-hidden="true">&laquo;</span>
                    </a>
                </li>

                <!-- Tombol angka halaman -->
                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                <li class="page-item <?php echo ($i == $page) ? 'active' : ''; ?>">
                    <a class="page-link"
                        href="?page=<?php echo $i; ?><?php echo $searchTerm ? '&search=' . urlencode($searchTerm) : ''; ?>"><?php echo $i; ?></a>
                </li>
                <?php endfor; ?>

                <!-- Tombol "Next" -->
                <li class="page-item <?php echo ($page >= $totalPages) ? 'disabled' : ''; ?>">
                    <a class="page-link"
                        href="?page=<?php echo $page + 1; ?><?php echo $searchTerm ? '&search=' . urlencode($searchTerm) : ''; ?>"
                        aria-label="Next">
                        <span aria-hidden="true">&raquo;</span>
                    </a>
                </li>
            </ul>
        </nav>


        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
        <script>
        let selectedIds = [];

        function confirmDelete() {
            // Mencari semua checkbox yang terpilih
            const checkboxes = document.querySelectorAll('input[name="selected[]"]:checked');

            if (checkboxes.length === 0) {
                alert("Tidak ada data yang dipilih untuk dihapus.");
                return;
            }

            // Simpan ID yang dipilih dalam array
            selectedIds = Array.from(checkboxes).map(checkbox => checkbox.value);

            // Tampilkan modal konfirmasi
            const modal = new bootstrap.Modal(document.getElementById('deleteConfirmationModal'));
            modal.show();
        }

        // Tangani penghapusan setelah konfirmasi
        document.getElementById('confirmDeleteBtn').onclick = function() {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = 'hapus_data.php'; // Ganti dengan URL script PHP yang menangani penghapusan

            selectedIds.forEach(id => {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'selected[]';
                input.value = id;
                form.appendChild(input);
            });

            document.body.appendChild(form);
            form.submit();
        }
        </script>
</body>

</html>