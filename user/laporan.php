<?php
session_start();
require_once '../db.php';

// Check if user is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'user') {
    header("Location: ../index.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Get user data
$stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

// Handle status filter
$status_filter = isset($_GET['status']) ? $_GET['status'] : '';
$where_clause = "";
$params = [];
$types = "";

if ($status_filter) {
    $where_clause = "WHERE l.status = ?";
    $params[] = $status_filter;
    $types = "s";
}

// Get reports with filter (all users)
$query = "SELECT l.*, u.username FROM laporan l JOIN users u ON l.user_id = u.id";
if ($where_clause) {
    $query .= " " . $where_clause;
}
$query .= " ORDER BY l.created_at DESC";

$stmt = $conn->prepare($query);
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$reports = $stmt->get_result();

// Get counts for filter (all users)
$stmt = $conn->prepare("SELECT COUNT(*) as total FROM laporan");
$stmt->execute();
$total_reports = $stmt->get_result()->fetch_assoc()['total'];

$stmt = $conn->prepare("SELECT COUNT(*) as pending FROM laporan WHERE status = 'pending'");
$stmt->execute();
$pending_reports = $stmt->get_result()->fetch_assoc()['pending'];

$stmt = $conn->prepare("SELECT COUNT(*) as completed FROM laporan WHERE status = 'completed'");
$stmt->execute();
$completed_reports = $stmt->get_result()->fetch_assoc()['completed'];

// Get user's own reports count
$stmt = $conn->prepare("SELECT COUNT(*) as my_total FROM laporan WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$my_total_reports = $stmt->get_result()->fetch_assoc()['my_total'];

// Handle report deletion
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $report_id = $_GET['delete'];
    
    // Check if the report belongs to the current user
    $stmt = $conn->prepare("SELECT user_id FROM laporan WHERE id = ?");
    $stmt->bind_param("i", $report_id);
    $stmt->execute();
    $report = $stmt->get_result()->fetch_assoc();
    
    if ($report && $report['user_id'] == $user_id) {
        // Get the photo filename to delete it
        $stmt = $conn->prepare("SELECT foto FROM laporan WHERE id = ?");
        $stmt->bind_param("i", $report_id);
        $stmt->execute();
        $photo = $stmt->get_result()->fetch_assoc();
        
        // Delete the report
        $stmt = $conn->prepare("DELETE FROM laporan WHERE id = ? AND user_id = ?");
        $stmt->bind_param("ii", $report_id, $user_id);
        
        if ($stmt->execute()) {
            // Delete the photo file if it exists
            if ($photo && $photo['foto'] && file_exists("../uploads/" . $photo['foto'])) {
                unlink("../uploads/" . $photo['foto']);
            }
            header("Location: laporan.php?success=deleted");
            exit();
        } else {
            header("Location: laporan.php?error=delete_failed");
            exit();
        }
    } else {
        header("Location: laporan.php?error=unauthorized");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Semua Laporan - Laporin Lingkungan</title>
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
        
        .stats-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
            padding: 1.5rem;
            text-align: center;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            border: 1px solid rgba(0, 0, 0, 0.05);
            margin-bottom: 1rem;
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
        
        .filter-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
            padding: 1.5rem;
            margin-bottom: 2rem;
            border: 1px solid rgba(0, 0, 0, 0.05);
        }
        
        .report-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
            padding: 1.5rem;
            margin-bottom: 1rem;
            border: 1px solid rgba(0, 0, 0, 0.05);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        
        .report-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
        }
        
        .report-image {
            width: 100%;
            height: 200px;
            object-fit: cover;
            border-radius: 10px;
            margin-bottom: 1rem;
        }
        
        .status-badge {
            font-size: 0.8rem;
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
        
        .empty-state {
            text-align: center;
            padding: 3rem 1rem;
        }
        
        .empty-state i {
            font-size: 4rem;
            color: #dee2e6;
            margin-bottom: 1rem;
        }
        
        .reporter-info {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            margin-bottom: 0.5rem;
        }
        
        .reporter-avatar {
            width: 25px;
            height: 25px;
            border-radius: 50%;
            background: linear-gradient(135deg, #667eea, #764ba2);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 0.7rem;
            font-weight: bold;
        }
        
        .my-report {
            border-left: 4px solid #28a745;
        }
        
        .alert {
            border-radius: 10px;
            border: none;
        }
        
        @media (max-width: 768px) {
            .page-header {
                padding: 1rem 0;
            }
            
            .stats-card {
                margin-bottom: 1rem;
            }
            
            .report-card {
                margin-bottom: 1rem;
            }
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark" style="background: linear-gradient(135deg, #28a745 0%, #20c997 100%);">
        <div class="container">
            <a class="navbar-brand fw-bold" href="index.php">
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
                        <a class="nav-link active" href="laporan.php">
                            <i class="fas fa-file-alt me-1"></i>Laporan Saya
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="buat_laporan.php">
                            <i class="fas fa-plus me-1"></i>Buat Laporan
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="bantuan.php">
                            <i class="fas fa-question-circle me-1"></i>Bantuan
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

    <!-- Page Header -->
    <div class="page-header">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h1 class="display-6 fw-bold mb-2">
                        <i class="fas fa-file-alt me-2"></i>Semua Laporan
                    </h1>
                    <p class="lead mb-0">Lihat semua laporan dari semua pengguna platform</p>
                </div>
                <div class="col-md-4 text-md-end">
                    <a href="buat_laporan.php" class="btn btn-light btn-lg">
                        <i class="fas fa-plus me-2"></i>Buat Laporan Baru
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="container">
        <!-- Alert Messages -->
        <?php if (isset($_GET['success']) && $_GET['success'] === 'deleted'): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>
            Laporan berhasil dihapus!
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php endif; ?>
        
        <?php if (isset($_GET['error'])): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-triangle me-2"></i>
            <?php 
            switch($_GET['error']) {
                case 'delete_failed':
                    echo 'Gagal menghapus laporan. Silakan coba lagi.';
                    break;
                case 'unauthorized':
                    echo 'Anda tidak memiliki izin untuk menghapus laporan ini.';
                    break;
                case 'not_found':
                    echo 'Laporan tidak ditemukan.';
                    break;
                case 'invalid_id':
                    echo 'ID laporan tidak valid.';
                    break;
                default:
                    echo 'Terjadi kesalahan. Silakan coba lagi.';
            }
            ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php endif; ?>

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
                        <i class="fas fa-user"></i>
                    </div>
                    <div class="stats-number text-info"><?php echo $my_total_reports; ?></div>
                    <div class="text-muted">Laporan Saya</div>
                </div>
            </div>
        </div>

        <!-- Filter Card -->
        <div class="filter-card">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <h5 class="fw-bold mb-0">
                        <i class="fas fa-filter me-2"></i>Filter Laporan
                    </h5>
                </div>
                <div class="col-md-6">
                    <div class="d-flex gap-2 flex-wrap">
                        <a href="laporan.php" class="btn btn-outline-primary <?php echo !$status_filter ? 'active' : ''; ?>">
                            Semua (<?php echo $total_reports; ?>)
                        </a>
                        <a href="laporan.php?status=pending" class="btn btn-outline-warning <?php echo $status_filter === 'pending' ? 'active' : ''; ?>">
                            Menunggu (<?php echo $pending_reports; ?>)
                        </a>
                        <a href="laporan.php?status=completed" class="btn btn-outline-success <?php echo $status_filter === 'completed' ? 'active' : ''; ?>">
                            Selesai (<?php echo $completed_reports; ?>)
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Reports List -->
        <?php if ($reports->num_rows > 0): ?>
        <div class="row">
            <?php while ($report = $reports->fetch_assoc()): ?>
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="report-card <?php echo ($report['user_id'] == $user_id) ? 'my-report' : ''; ?>">
                    <?php if ($report['foto']): ?>
                    <img src="../uploads/<?php echo htmlspecialchars($report['foto']); ?>" alt="Foto Laporan" class="report-image">
                    <?php else: ?>
                    <div class="report-image bg-light d-flex align-items-center justify-content-center">
                        <i class="fas fa-image text-muted" style="font-size: 3rem;"></i>
                    </div>
                    <?php endif; ?>
                    
                    <div class="reporter-info">
                        <div class="reporter-avatar">
                            <?php echo strtoupper(substr($report['username'], 0, 1)); ?>
                        </div>
                        <small class="text-muted"><?php echo htmlspecialchars($report['username']); ?></small>
                        <?php if ($report['user_id'] == $user_id): ?>
                        <span class="badge bg-success ms-2">Laporan Saya</span>
                        <?php endif; ?>
                    </div>
                    
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <h6 class="fw-bold mb-0"><?php echo htmlspecialchars($report['judul']); ?></h6>
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
                        <span class="<?php echo $status_class; ?> status-badge"><?php echo $status_text; ?></span>
                    </div>
                    
                    <p class="text-muted small mb-2">
                        <i class="fas fa-map-marker-alt me-1"></i>
                        <?php echo htmlspecialchars($report['lokasi']); ?>
                    </p>
                    
                    <p class="text-muted small mb-3">
                        <?php echo htmlspecialchars(substr($report['deskripsi'], 0, 100)); ?>
                        <?php if (strlen($report['deskripsi']) > 100): ?>...<?php endif; ?>
                    </p>
                    
                    <div class="d-flex justify-content-between align-items-center">
                        <small class="text-muted">
                            <i class="fas fa-calendar me-1"></i>
                            <?php echo date('d/m/Y H:i', strtotime($report['created_at'])); ?>
                        </small>
                        <div class="btn-group">
                            <a href="detail_laporan.php?id=<?php echo $report['id']; ?>" class="btn btn-sm btn-outline-primary" title="Lihat Detail">
                                <i class="fas fa-eye"></i>
                            </a>
                            <?php if ($report['user_id'] == $user_id): ?>
                            <a href="edit_laporan.php?id=<?php echo $report['id']; ?>" class="btn btn-sm btn-outline-secondary" title="Edit Laporan">
                                <i class="fas fa-edit"></i>
                            </a>
                            <button type="button" class="btn btn-sm btn-outline-danger" onclick="confirmDelete(<?php echo $report['id']; ?>)" title="Hapus Laporan">
                                <i class="fas fa-trash"></i>
                            </button>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
            <?php endwhile; ?>
        </div>
        <?php else: ?>
        <div class="empty-state">
            <i class="fas fa-file-alt"></i>
            <h4 class="fw-bold mb-3">Belum Ada Laporan</h4>
            <p class="text-muted mb-4">
                <?php if ($status_filter): ?>
                Tidak ada laporan dengan status "<?php echo $status_filter === 'pending' ? 'Menunggu' : 'Selesai'; ?>"
                <?php else: ?>
                Belum ada laporan yang dibuat oleh pengguna platform.
                <?php endif; ?>
            </p>
            <a href="buat_laporan.php" class="btn btn-primary btn-lg">
                <i class="fas fa-plus me-2"></i>Buat Laporan Pertama
            </a>
        </div>
        <?php endif; ?>
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