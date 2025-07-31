<?php
session_start();
require_once '../db.php';

// Check if user is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'user') {
    header("Location: ../index.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$report_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if (!$report_id) {
    header("Location: laporan.php");
    exit();
}

// Get user data
$stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

// Get report data (all reports, but check if user can edit)
$stmt = $conn->prepare("SELECT l.*, u.username FROM laporan l JOIN users u ON l.user_id = u.id WHERE l.id = ?");
$stmt->bind_param("i", $report_id);
$stmt->execute();
$report = $stmt->get_result()->fetch_assoc();

if (!$report) {
    header("Location: laporan.php?error=not_found");
    exit();
}

// Get status text and class
$status_class = '';
$status_text = '';
switch($report['status']) {
    case 'pending':
        $status_class = 'badge bg-warning';
        $status_text = 'Menunggu Proses';
        break;
    case 'completed':
        $status_class = 'badge bg-success';
        $status_text = 'Selesai Diproses';
        break;
    default:
        $status_class = 'badge bg-secondary';
        $status_text = 'Tidak Diketahui';
}

// Get category text
$category_text = $report['kategori'] ?: 'Tidak Diketahui';
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Laporan - Laporin Lingkungan</title>
    <link href="../bootstrap-5.3.7-dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="../css/style.css" rel="stylesheet">
    <style>
        .page-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 2rem 0;
            margin-bottom: 2rem;
        }
        
        .detail-card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
            padding: 2rem;
            border: 1px solid rgba(0, 0, 0, 0.05);
            margin-bottom: 2rem;
        }
        
        .report-image {
            width: 100%;
            max-height: 400px;
            object-fit: cover;
            border-radius: 15px;
            margin-bottom: 1.5rem;
        }
        
        .info-item {
            display: flex;
            align-items: center;
            margin-bottom: 1rem;
            padding: 1rem;
            background: #f8f9fa;
            border-radius: 10px;
            border-left: 4px solid var(--primary-color);
        }
        
        .info-icon {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            margin-right: 1rem;
        }
        
        .info-content h6 {
            margin-bottom: 0.25rem;
            font-weight: 600;
        }
        
        .info-content p {
            margin-bottom: 0;
            color: #6c757d;
        }
        
        .status-badge {
            font-size: 1rem;
            padding: 0.5rem 1rem;
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
        
        .action-buttons {
            display: flex;
            gap: 0.5rem;
            flex-wrap: wrap;
        }
        
        .alert {
            border-radius: 10px;
            border: none;
        }
        
        @media (max-width: 768px) {
            .page-header {
                padding: 1rem 0;
            }
            
            .detail-card {
                padding: 1.5rem;
            }
            
            .action-buttons {
                flex-direction: column;
            }
            
            .action-buttons .btn {
                width: 100%;
            }
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
        <div class="container">
            <a class="navbar-brand" href="index.php">
                <i class="fas fa-leaf me-2"></i>Laporin Lingkungan
            </a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="index.php">
                            <i class="fas fa-home me-1"></i>Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="laporan.php">
                            <i class="fas fa-file-alt me-1"></i>Laporan Saya
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="buat_laporan.php">
                            <i class="fas fa-plus me-1"></i>Buat Laporan
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="profil.php">
                            <i class="fas fa-user me-1"></i>Profil
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
                        <ul class="dropdown-menu">
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

    <!-- Page Header -->
    <div class="page-header">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h1 class="display-6 fw-bold mb-2">
                        <i class="fas fa-file-alt me-2"></i>Detail Laporan
                    </h1>
                    <p class="lead mb-0">Informasi lengkap laporan Anda</p>
                </div>
                <div class="col-md-4 text-md-end">
                    <a href="laporan.php" class="btn btn-light btn-lg">
                        <i class="fas fa-arrow-left me-2"></i>Kembali
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="container">
        <!-- Alert Messages -->
        <?php if (isset($_GET['success']) && $_GET['success'] === 'updated'): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>
            Laporan berhasil diperbarui!
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php endif; ?>
        
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="detail-card">
                    <!-- Report Title and Status -->
                    <div class="d-flex justify-content-between align-items-start mb-4">
                        <div>
                            <h2 class="fw-bold mb-2"><?php echo htmlspecialchars($report['judul']); ?></h2>
                            <p class="text-muted mb-0">
                                <i class="fas fa-calendar me-1"></i>
                                Dibuat pada: <?php echo date('d F Y H:i', strtotime($report['created_at'])); ?>
                            </p>
                        </div>
                        <span class="<?php echo $status_class; ?> status-badge"><?php echo $status_text; ?></span>
                    </div>
                    
                    <!-- Report Image -->
                    <?php if ($report['foto']): ?>
                    <div class="text-center mb-4">
                        <img src="../uploads/<?php echo htmlspecialchars($report['foto']); ?>" 
                             alt="Foto Laporan" class="report-image">
                    </div>
                    <?php endif; ?>
                    
                    <!-- Report Information -->
                    <div class="row">
                        <div class="col-md-6">
                            <div class="info-item">
                                <div class="info-icon">
                                    <i class="fas fa-map-marker-alt"></i>
                                </div>
                                <div class="info-content">
                                    <h6>Lokasi</h6>
                                    <p><?php echo htmlspecialchars($report['lokasi']); ?></p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="info-item">
                                <div class="info-icon">
                                    <i class="fas fa-tags"></i>
                                </div>
                                <div class="info-content">
                                    <h6>Kategori</h6>
                                    <p><?php echo $category_text; ?></p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="info-item">
                                <div class="info-icon">
                                    <i class="fas fa-clock"></i>
                                </div>
                                <div class="info-content">
                                    <h6>Status</h6>
                                    <p><?php echo $status_text; ?></p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="info-item">
                                <div class="info-icon">
                                    <i class="fas fa-user"></i>
                                </div>
                                <div class="info-content">
                                    <h6>Pelapor</h6>
                                    <p><?php echo htmlspecialchars($report['username']); ?></p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Report Description -->
                    <div class="mt-4">
                        <h5 class="fw-bold mb-3">
                            <i class="fas fa-align-left me-2"></i>Deskripsi Masalah
                        </h5>
                        <div class="p-3 bg-light rounded">
                            <p class="mb-0"><?php echo nl2br(htmlspecialchars($report['deskripsi'])); ?></p>
                        </div>
                    </div>
                    
                    <!-- Action Buttons -->
                    <div class="mt-4 pt-3 border-top">
                        <div class="action-buttons">
                            <a href="laporan.php" class="btn btn-outline-secondary">
                                <i class="fas fa-list me-2"></i>Kembali ke Daftar
                            </a>
                            <?php if ($report['user_id'] == $user_id): ?>
                            <a href="edit_laporan.php?id=<?php echo $report['id']; ?>" class="btn btn-outline-primary">
                                <i class="fas fa-edit me-2"></i>Edit Laporan
                            </a>
                            <button type="button" class="btn btn-outline-danger" onclick="confirmDelete(<?php echo $report['id']; ?>)">
                                <i class="fas fa-trash me-2"></i>Hapus Laporan
                            </button>
                            <?php endif; ?>
                        </div>
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
    <script>
        function confirmDelete(reportId) {
            if (confirm('Apakah Anda yakin ingin menghapus laporan ini? Tindakan ini tidak dapat dibatalkan.')) {
                window.location.href = 'laporan.php?delete=' + reportId;
            }
        }
    </script>
</body>
</html> 