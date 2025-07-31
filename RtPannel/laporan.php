<?php 
include '../db.php';
include '../adminpannel/auth.php'; 

// Fix: Make sure $user is an array, not a string
if (!isset($user) || !is_array($user)) {
    // Try to get user from session or database if not set
    if (isset($_SESSION['user_id'])) {
        $user_id = $_SESSION['user_id'];
        $user_query = $conn->query("SELECT * FROM users WHERE id = '$user_id' LIMIT 1");
        if ($user_query && $user_query->num_rows > 0) {
            $user = $user_query->fetch_assoc();
        } else {
            $user = [
                'username' => 'Admin'
            ];
        }
    } else {
        $user = [
            'username' => 'Admin'
        ];
    }
}

// Fungsi untuk menghasilkan warna berdasarkan username
function getProfileColor($username) {
    // Daftar warna pastel
    $colors = [
        '#667eea', // biru
        '#764ba2', // ungu
        '#f6d365', // kuning
        '#fda085', // oranye
        '#43cea2', // hijau
        '#ff6a6a', // merah muda
        '#36d1c4', // toska
        '#ffb86c', // peach
        '#a18cd1', // ungu muda
        '#fbc2eb', // pink muda
    ];
    // Hash username ke index warna
    $index = 0;
    if ($username) {
        $index = abs(crc32(strtolower($username))) % count($colors);
    }
    return $colors[$index];
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Laporan - Admin Dashboard</title>
    <link href="../bootstrap-5.3.7-dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
        }
        .navbar {
            background: rgba(255, 255, 255, 0.95) !important;
            backdrop-filter: blur(10px);
            box-shadow: 0 2px 20px rgba(0, 0, 0, 0.1);
        }
        .navbar-brand {
            font-weight: 700;
            color: #667eea !important;
        }
        .nav-link {
            font-weight: 500;
            color: #495057 !important;
            transition: all 0.3s ease;
        }
        .nav-link:hover {
            color: #667eea !important;
            transform: translateY(-1px);
        }
        .main-content {
            min-height: calc(100vh - 80px);
            padding: 2rem 0;
        }
        .content-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        .table {
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
        }
        .table thead th {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            font-weight: 600;
            padding: 1rem;
        }
        .table tbody tr {
            transition: all 0.3s ease;
        }
        .table tbody tr:hover {
            background-color: rgba(102, 126, 234, 0.05);
            transform: scale(1.01);
        }
        .page-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 15px;
            padding: 1.5rem;
            margin-bottom: 2rem;
        }
        .stats-card {
            background: white;
            border-radius: 15px;
            padding: 1.5rem;
            margin-bottom: 2rem;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
        }
        .status-badge {
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-weight: 600;
            font-size: 0.875rem;
        }
        .status-pending {
            background: #fff3cd;
            color: #856404;
        }
        .status-proses {
            background: #cce5ff;
            color: #004085;
        }
        .status-selesai {
            background: #d4edda;
            color: #155724;
        }
        .foto-thumbnail {
            width: 60px;
            height: 60px;
            object-fit: cover;
            border-radius: 10px;
            border: 2px solid #e9ecef;
        }
        .text-truncate-2 {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
        /* Avatar warna profil */
        .user-avatar {
            width: 38px;
            height: 38px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            font-size: 1.1rem;
            color: #fff;
            background: #667eea; /* default, akan di-overwrite inline */
            box-shadow: 0 2px 8px rgba(102,126,234,0.08);
        }
        /* Avatar warna profil untuk tabel laporan */
        .avatar-sm {
            width: 38px;
            height: 38px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            font-size: 1.1rem;
            color: #fff;
            background: #667eea; /* default, akan di-overwrite inline */
            box-shadow: 0 2px 8px rgba(102,126,234,0.08);
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
        <div class="container">
            <a class="navbar-brand fw-bold" href="home.php">
                <i class="fas fa-users-cog me-2"></i>RT Dashboard
            </a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="home.php">
                            <i class="fas fa-home me-1"></i>Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="laporan.php">
                            <i class="fas fa-file-alt me-1"></i>Laporan
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="warga.php">
                            <i class="fas fa-users me-1"></i>Data Warga
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="kk.php">
                            <i class="fas fa-id-card me-1"></i>Kartu Keluarga
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="users.php">
                            <i class="fas fa-user-cog me-1"></i>Data User
                        </a>
                    </li>
                </ul>
                
                <div class="navbar-nav">
                    <div class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" role="button" data-bs-toggle="dropdown">
                            <div class="user-avatar me-2"
                                <?php
                                // Tambahkan warna profil inline
                                if (is_array($user) && isset($user['username'])) {
                                    $color = getProfileColor($user['username']);
                                    echo 'style="background: ' . htmlspecialchars($color) . ';"';
                                }
                                ?>
                            >
                                <?php
                                if (is_array($user) && isset($user['username'])) {
                                    echo strtoupper(substr($user['username'], 0, 1));
                                } else {
                                    echo '<i class="fas fa-user"></i>';
                                }
                                ?>
                            </div>
                            <span>
                                <?php
                                // Fix: Only access $user['username'] if $user is array and has 'username'
                                if (is_array($user) && isset($user['username'])) {
                                    echo htmlspecialchars($user['username']);
                                } else {
                                    echo '';
                                }
                                ?>
                            </span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="profil.php">
                                <i class="fas fa-user me-2"></i>Profil
                            </a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="logout.php">
                                <i class="fas fa-sign-out-alt me-2"></i>Logout
                            </a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <div class="main-content">
        <div class="container">
            <div class="page-header">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <h1 class="fw-bold mb-2">
                            <i class="fas fa-clipboard-list me-3"></i>Data Laporan Warga
                        </h1>
                        <p class="mb-0 opacity-75">Kelola dan pantau laporan lingkungan dari warga</p>
                    </div>
                    <div class="col-md-4 text-end">
                        <div class="btn-group" role="group">
                            <button type="button" class="btn btn-light btn-outline-light">
                                <i class="fas fa-filter me-2"></i>Filter
                            </button>
                            <button type="button" class="btn btn-light btn-outline-light">
                                <i class="fas fa-download me-2"></i>Export
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <?php
            $total_laporan = $conn->query("SELECT COUNT(*) as total FROM laporan")->fetch_assoc()['total'];
            $laporan_pending = $conn->query("SELECT COUNT(*) as total FROM laporan WHERE status = 'pending'")->fetch_assoc()['total'];
            $laporan_selesai = $conn->query("SELECT COUNT(*) as total FROM laporan WHERE status = 'selesai'")->fetch_assoc()['total'];
            ?>
            
            <div class="row mb-4">
                <div class="col-12 col-md-4">
                    <div class="stats-card">
                        <div class="row align-items-center">
                            <div class="col-8">
                                <h3 class="fw-bold text-primary mb-1"><?php echo $total_laporan; ?></h3>
                                <p class="mb-0 text-muted">Total Laporan</p>
                            </div>
                            <div class="col-4 text-end">
                                <i class="fas fa-clipboard-list fa-2x text-primary opacity-25"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-md-4">
                    <div class="stats-card">
                        <div class="row align-items-center">
                            <div class="col-8">
                                <h3 class="fw-bold text-warning mb-1"><?php echo $laporan_pending; ?></h3>
                                <p class="mb-0 text-muted">Menunggu</p>
                            </div>
                            <div class="col-4 text-end">
                                <i class="fas fa-clock fa-2x text-warning opacity-25"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-md-4">
                    <div class="stats-card">
                        <div class="row align-items-center">
                            <div class="col-8">
                                <h3 class="fw-bold text-success mb-1"><?php echo $laporan_selesai; ?></h3>
                                <p class="mb-0 text-muted">Selesai</p>
                            </div>
                            <div class="col-4 text-end">
                                <i class="fas fa-check-circle fa-2x text-success opacity-25"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="content-card p-4">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th><i class="fas fa-user me-2"></i>Pelapor</th>
                                <th><i class="fas fa-file-alt me-2"></i>Laporan</th>
                                <th><i class="fas fa-map-marker-alt me-2"></i>Lokasi</th>
                                <th><i class="fas fa-image me-2"></i>Foto</th>
                                <th><i class="fas fa-calendar me-2"></i>Tanggal</th>
                                <th><i class="fas fa-info-circle me-2"></i>Status</th>
                                <th class="text-center"><i class="fas fa-cogs me-2"></i>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php
                        $result = $conn->query("SELECT l.*, u.username as nama_pelapor FROM laporan l 
                                              LEFT JOIN users u ON l.user_id = u.id 
                                              ORDER BY l.created_at DESC");
                        if ($result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()) {
                                $status_class = '';
                                switch($row['status']) {
                                    case 'pending':
                                        $status_class = 'status-pending';
                                        break;
                                    case 'proses':
                                        $status_class = 'status-proses';
                                        break;
                                    case 'selesai':
                                        $status_class = 'status-selesai';
                                        break;
                                }
                                
                                // Defensive: check if expected keys exist before using
                                $nama_pelapor = isset($row['nama_pelapor']) ? $row['nama_pelapor'] : '-';
                                $judul = isset($row['judul']) ? $row['judul'] : '';
                                $deskripsi = isset($row['deskripsi']) ? $row['deskripsi'] : '';
                                $lokasi = isset($row['lokasi']) ? $row['lokasi'] : '';
                                $foto = isset($row['foto']) ? $row['foto'] : '';
                                $created_at = isset($row['created_at']) ? $row['created_at'] : '';
                                $status = isset($row['status']) ? $row['status'] : '';
                                $id = isset($row['id']) ? $row['id'] : '';

                                // Warna avatar pelapor
                                $avatar_color = getProfileColor($nama_pelapor);

                                echo "<tr>
                                    <td class='align-middle'>
                                        <div class='d-flex align-items-center'>
                                            <div class='avatar-sm rounded-circle d-flex align-items-center justify-content-center me-3' style='background: " . htmlspecialchars($avatar_color) . ";'>
                                                " . (trim($nama_pelapor) && $nama_pelapor != '-' ? strtoupper(substr($nama_pelapor, 0, 1)) : "<i class='fas fa-user text-white'></i>") . "
                                            </div>
                                            <div>
                                                <strong>" . htmlspecialchars($nama_pelapor) . "</strong>
                                            </div>
                                        </div>
                                    </td>
                                    <td class='align-middle'>
                                        <div class='text-truncate-2'>" . htmlspecialchars($judul) . "</div>
                                        <small class='text-muted'>" . htmlspecialchars($deskripsi) . "</small>
                                    </td>
                                    <td class='align-middle'>" . htmlspecialchars($lokasi) . "</td>
                                    <td class='align-middle'>
                                        " . ($foto ? "<img src='../uploads/" . htmlspecialchars($foto) . "' class='foto-thumbnail' alt='Foto'>" : "<span class='text-muted'>-</span>") . "
                                    </td>
                                    <td class='align-middle'>" . ($created_at ? date('d/m/Y H:i', strtotime($created_at)) : '-') . "</td>
                                    <td class='align-middle'>
                                        <span class='status-badge {$status_class}'>
                                            " . ucfirst(htmlspecialchars($status)) . "
                                        </span>
                                    </td>
                                    <td class='align-middle text-center'>
                                        <div class='btn-group' role='group'>
                                            <a href='detail_laporan.php?id=" . urlencode($id) . "' class='btn btn-sm btn-info me-1' title='Detail'>
                                                <i class='fas fa-eye'></i>
                                            </a>
                                            <a href='edit_status_laporan.php?id=" . urlencode($id) . "' class='btn btn-sm btn-warning me-1' title='Edit Status'>
                                                <i class='fas fa-edit'></i>
                                            </a>
                                            <form method='POST' action='hapus_laporan.php' style='display:inline;' onsubmit='return confirm(\"Yakin ingin menghapus laporan ini?\");'>
                                                <input type='hidden' name='id' value='" . htmlspecialchars($id) . "'>
                                                <button type='submit' class='btn btn-sm btn-danger' title='Hapus'>
                                                    <i class='fas fa-trash'></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>";
                            }
                        } else {
                            echo "<tr>
                                <td colspan='7' class='text-center py-4'>
                                    <div class='text-muted'>
                                        <i class='fas fa-inbox fa-3x mb-3 opacity-25'></i>
                                        <p class='mb-0'>Belum ada laporan</p>
                                    </div>
                                </td>
                            </tr>";
                        }
                        ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script src="../bootstrap-5.3.7-dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
