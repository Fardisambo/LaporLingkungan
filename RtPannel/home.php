<?php
session_start();
require_once '../db.php';

// Check if user is logged in and is RT
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'rt') {
    header("Location: ../index.php");
    exit();
}

// Get user data
$user_id = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

// Get reports count for RT's area
$stmt = $conn->prepare("SELECT COUNT(*) as total FROM laporan");
$stmt->execute();
$total_reports = $stmt->get_result()->fetch_assoc()['total'];

// Get pending reports count
$stmt = $conn->prepare("SELECT COUNT(*) as pending FROM laporan WHERE status = 'pending'");
$stmt->execute();
$pending_reports = $stmt->get_result()->fetch_assoc()['pending'];

// Get completed reports count
$stmt = $conn->prepare("SELECT COUNT(*) as completed FROM laporan WHERE status = 'completed'");
$stmt->execute();
$completed_reports = $stmt->get_result()->fetch_assoc()['completed'];

// Get residents count
$stmt = $conn->prepare("SELECT COUNT(*) as total FROM warga");
$stmt->execute();
$total_residents = $stmt->get_result()->fetch_assoc()['total'];
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard RT - Laporin Lingkungan</title>
    <link href="../bootstrap-5.3.7-dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="../css/style.css" rel="stylesheet">
    <style>
        .dashboard-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 2rem 0;
            margin-bottom: 2rem;
        }
        
        .welcome-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            padding: 2rem;
            margin-bottom: 2rem;
        }
        
        .stats-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
            padding: 1.5rem;
            text-align: center;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            border: 1px solid rgba(0, 0, 0, 0.05);
        }
        
        .stats-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
        }
        
        .stats-icon {
            font-size: 2.5rem;
            margin-bottom: 1rem;
        }
        
        .stats-number {
            font-size: 2rem;
            font-weight: bold;
            margin-bottom: 0.5rem;
        }
        
        .action-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
            padding: 1.5rem;
            text-align: center;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            border: 1px solid rgba(0, 0, 0, 0.05);
            height: 100%;
        }
        
        .action-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
        }
        
        .action-icon {
            font-size: 3rem;
            margin-bottom: 1rem;
            color: var(--primary-color);
        }
        
        .navbar-brand {
            font-weight: bold;
            font-size: 1.5rem;
        }
        
        .navbar-nav .nav-link {
            font-weight: 500;
            transition: color 0.3s ease;
        }
        
        .navbar-nav .nav-link:hover {
            color: var(--primary-color) !important;
        }
        
        .user-info {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .user-avatar {
            width: 35px;
            height: 35px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
        }
        
        @media (max-width: 768px) {
            .dashboard-header {
                padding: 1rem 0;
            }
            
            .welcome-card {
                padding: 1.5rem;
            }
            
            .stats-card {
                margin-bottom: 1rem;
            }
            
            .action-card {
                margin-bottom: 1rem;
            }
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
                        <a class="nav-link active" href="home.php">
                            <i class="fas fa-home me-1"></i>Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="laporan.php">
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
                            <div class="user-avatar me-2">
                                <?php echo strtoupper(substr($user['username'], 0, 1)); ?>
                            </div>
                            <span><?php echo htmlspecialchars($user['username']); ?></span>
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

    <!-- Dashboard Header -->
    <div class="dashboard-header">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h1 class="display-6 fw-bold mb-2">
                        <i class="fas fa-users me-2"></i>Dashboard RT
                    </h1>
                    <p class="lead mb-0">Selamat datang kembali, <?php echo htmlspecialchars($user['username']); ?>!</p>
                </div>
                <div class="col-md-4 text-md-end">
                    <div class="user-info">
                        <div class="user-avatar">
                            <?php echo strtoupper(substr($user['username'], 0, 1)); ?>
                        </div>
                        <div>
                            <div class="fw-bold"><?php echo htmlspecialchars($user['username']); ?></div>
                            <small class="text-light">Ketua RT</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="container">
        <!-- Welcome Card -->
        <div class="welcome-card">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h2 class="fw-bold mb-3">
                        <i class="fas fa-leaf text-success me-2"></i>
                        Laporin Lingkungan
                    </h2>
                    <p class="lead mb-3">Platform pelaporan lingkungan untuk mengelola dan memantau masalah lingkungan di wilayah RT Anda.</p>
                    <p class="mb-0">Bersama kita jaga lingkungan untuk masa depan yang lebih baik.</p>
                </div>
                <div class="col-md-4 text-md-end">
                    <a href="laporan.php" class="btn btn-primary btn-lg">
                        <i class="fas fa-eye me-2"></i>Lihat Laporan
                    </a>
                </div>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="row mb-4">
            <div class="col-md-3 mb-3">
                <div class="stats-card">
                    <div class="stats-icon text-primary">
                        <i class="fas fa-file-alt"></i>
                    </div>
                    <div class="stats-number text-primary"><?php echo $total_reports; ?></div>
                    <div class="text-muted">Total Laporan</div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="stats-card">
                    <div class="stats-icon text-warning">
                        <i class="fas fa-clock"></i>
                    </div>
                    <div class="stats-number text-warning"><?php echo $pending_reports; ?></div>
                    <div class="text-muted">Menunggu Proses</div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="stats-card">
                    <div class="stats-icon text-success">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <div class="stats-number text-success"><?php echo $completed_reports; ?></div>
                    <div class="text-muted">Selesai Diproses</div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="stats-card">
                    <div class="stats-icon text-info">
                        <i class="fas fa-users"></i>
                    </div>
                    <div class="stats-number text-info"><?php echo $total_residents; ?></div>
                    <div class="text-muted">Total Warga</div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="row">
            <div class="col-12 mb-4">
                <h3 class="fw-bold mb-3">
                    <i class="fas fa-bolt me-2"></i>Aksi Cepat
                </h3>
            </div>
        </div>
        
        <div class="row">
            <div class="col-md-6 col-lg-3 mb-3">
                <div class="action-card">
                    <div class="action-icon">
                        <i class="fas fa-file-alt"></i>
                    </div>
                    <h5 class="fw-bold mb-2">Kelola Laporan</h5>
                    <p class="text-muted mb-3">Lihat dan kelola semua laporan lingkungan</p>
                    <a href="laporan.php" class="btn btn-primary w-100">
                        <i class="fas fa-arrow-right me-1"></i>Lihat
                    </a>
                </div>
            </div>
            
            <div class="col-md-6 col-lg-3 mb-3">
                <div class="action-card">
                    <div class="action-icon">
                        <i class="fas fa-users"></i>
                    </div>
                    <h5 class="fw-bold mb-2">Data Warga</h5>
                    <p class="text-muted mb-3">Kelola data warga di wilayah RT</p>
                    <a href="warga.php" class="btn btn-outline-primary w-100">
                        <i class="fas fa-arrow-right me-1"></i>Kelola
                    </a>
                </div>
            </div>
            
            <div class="col-md-6 col-lg-3 mb-3">
                <div class="action-card">
                    <div class="action-icon">
                        <i class="fas fa-id-card"></i>
                    </div>
                    <h5 class="fw-bold mb-2">Kartu Keluarga</h5>
                    <p class="text-muted mb-3">Kelola data kartu keluarga</p>
                    <a href="kk.php" class="btn btn-outline-primary w-100">
                        <i class="fas fa-arrow-right me-1"></i>Kelola
                    </a>
                </div>
            </div>
            
            <div class="col-md-6 col-lg-3 mb-3">
                <div class="action-card">
                    <div class="action-icon">
                        <i class="fas fa-users-cog"></i>
                    </div>
                    <h5 class="fw-bold mb-2">Data User</h5>
                    <p class="text-muted mb-3">Lihat data user dan role</p>
                    <a href="users.php" class="btn btn-outline-primary w-100">
                        <i class="fas fa-arrow-right me-1"></i>Lihat
                    </a>
                </div>
            </div>
            <div class="col-md-6 col-lg-3 mb-3">
                <div class="action-card">
                    <div class="action-icon">
                        <i class="fas fa-user"></i>
                    </div>
                    <h5 class="fw-bold mb-2">Profil</h5>
                    <p class="text-muted mb-3">Kelola informasi profil Anda</p>
                    <a href="profil.php" class="btn btn-outline-primary w-100">
                        <i class="fas fa-arrow-right me-1"></i>Kelola
                    </a>
                </div>
            </div>
        </div>

        <!-- Recent Reports -->
        <div class="row mt-5">
            <div class="col-12">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">
                            <i class="fas fa-history me-2"></i>Laporan Terbaru
                        </h5>
                    </div>
                    <div class="card-body">
                        <?php
                        $stmt = $conn->prepare("SELECT l.*, u.username as reporter FROM laporan l JOIN users u ON l.user_id = u.id ORDER BY l.created_at DESC LIMIT 5");
                        $stmt->execute();
                        $recent_reports = $stmt->get_result();
                        
                        if ($recent_reports->num_rows > 0):
                        ?>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Judul</th>
                                        <th>Pelapor</th>
                                        <th>Lokasi</th>
                                        <th>Status</th>
                                        <th>Tanggal</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while ($report = $recent_reports->fetch_assoc()): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($report['judul']); ?></td>
                                        <td><?php echo htmlspecialchars($report['reporter']); ?></td>
                                        <td><?php echo htmlspecialchars($report['lokasi']); ?></td>
                                        <td>
                                            <?php
                                            $status_class = '';
                                            $status_text = '';
                                            switch($report['status']) {
                                                case 'pending':
                                                    $status_class = 'badge bg-warning';
                                                    $status_text = 'Menunggu';
                                                    break;
                                                case 'completed':
                                                    $status_class = 'badge bg-success';
                                                    $status_text = 'Selesai';
                                                    break;
                                                default:
                                                    $status_class = 'badge bg-secondary';
                                                    $status_text = 'Tidak Diketahui';
                                            }
                                            ?>
                                            <span class="<?php echo $status_class; ?>"><?php echo $status_text; ?></span>
                                        </td>
                                        <td><?php echo date('d/m/Y', strtotime($report['created_at'])); ?></td>
                                        <td>
                                            <a href="detail_laporan.php?id=<?php echo $report['id']; ?>" class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
                        <?php else: ?>
                        <div class="text-center py-4">
                            <i class="fas fa-file-alt text-muted" style="font-size: 3rem;"></i>
                            <p class="text-muted mt-2">Belum ada laporan</p>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-dark text-light py-4 mt-5">
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <h5><i class="fas fa-leaf me-2"></i>Laporin Lingkungan</h5>
                    <p class="mb-0">Platform pelaporan lingkungan yang memudahkan masyarakat melaporkan masalah lingkungan.</p>
                </div>
                <div class="col-md-6 text-md-end">
                    <p class="mb-0">&copy; 2024 Laporin Lingkungan. All rights reserved.</p>
                </div>
            </div>
        </div>
    </footer>

    <script src="../bootstrap-5.3.7-dist/js/bootstrap.bundle.min.js"></script>
    <script src="../js/app.js"></script>
</body>
</html> 