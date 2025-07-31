<?php
include 'auth.php';
include '../db.php';

// Query total KK
$totalKK = 0;
$resultKK = $conn->query("SELECT COUNT(*) AS total FROM keluarga");
if ($resultKK) {
    $rowKK = $resultKK->fetch_assoc();
    $totalKK = $rowKK['total'];
}

// Query total Warga
$totalWarga = 0;
$resultWarga = $conn->query("SELECT COUNT(*) AS total FROM warga");
if ($resultWarga) {
    $rowWarga = $resultWarga->fetch_assoc();
    $totalWarga = $rowWarga['total'];
}

// Query laporan baru (assuming status = 'baru' or similar)
$totalLaporanBaru = 0;
$resultLaporan = $conn->query("SELECT COUNT(*) AS total FROM laporan WHERE status = 'baru'");
if ($resultLaporan) {
    $rowLaporan = $resultLaporan->fetch_assoc();
    $totalLaporanBaru = $rowLaporan['total'];
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Laporin Lingkungan</title>
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
        .dashboard-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            transition: all 0.3s ease;
        }
        .dashboard-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
        }
        .stat-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 15px;
            padding: 1.5rem;
            margin-bottom: 1rem;
        }
        .stat-card .icon {
            font-size: 2.5rem;
            opacity: 0.8;
        }
        .quick-action-card {
            background: white;
            border-radius: 15px;
            padding: 1.5rem;
            text-align: center;
            transition: all 0.3s ease;
            border: none;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
        }
        .quick-action-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
        }
        .quick-action-card .icon {
            font-size: 2rem;
            margin-bottom: 1rem;
            color: #667eea;
        }
        .btn-custom {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            border-radius: 10px;
            padding: 10px 20px;
            color: white;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        .btn-custom:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
            color: white;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark" style="background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);">
        <div class="container">
            <a class="navbar-brand fw-bold" href="home.php">
                <i class="fas fa-shield-alt me-2"></i>Admin Dashboard
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" 
                aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
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
                        <a class="nav-link" href="kk.php">
                            <i class="fas fa-id-card me-1"></i>Data KK
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="warga.php">
                            <i class="fas fa-users me-1"></i>Data Warga
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="laporan.php">
                            <i class="fas fa-clipboard-list me-1"></i>Data Laporan
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="users.php">
                            <i class="fas fa-users-cog me-1"></i>Kelola User
                        </a>
                    </li>
                </ul>
                
                <div class="navbar-nav">
                    <div class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" role="button" data-bs-toggle="dropdown">
                            <div class="user-avatar me-2">
                                <i class="fas fa-user-shield"></i>
                            </div>
                            <span>Administrator</span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
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
            <div class="row mb-4">
                <div class="col-12">
                    <div class="dashboard-card p-4">
                        <div class="row align-items-center">
                            <div class="col-md-8">
                                <h1 class="fw-bold text-dark mb-2">Selamat Datang, Admin!</h1>
                                <p class="text-muted mb-0">Kelola data warga dan laporan lingkungan dengan mudah</p>
                            </div>
                            <div class="col-md-4 text-end">
                                <i class="fas fa-chart-line fa-4x text-primary opacity-25"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row mb-4">
                <div class="col-12 col-md-4">
                    <div class="stat-card">
                        <div class="row align-items-center">
                            <div class="col-8">
                                <h3 class="fw-bold mb-1"><?php echo $totalKK; ?></h3>
                                <p class="mb-0 opacity-75">Total KK</p>
                            </div>
                            <div class="col-4 text-end">
                                <i class="fas fa-users icon"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-md-4">
                    <div class="stat-card">
                        <div class="row align-items-center">
                            <div class="col-8">
                                <h3 class="fw-bold mb-1"><?php echo $totalWarga; ?></h3>
                                <p class="mb-0 opacity-75">Total Warga</p>
                            </div>
                            <div class="col-4 text-end">
                                <i class="fas fa-user icon"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-md-4">
                    <div class="stat-card">
                        <div class="row align-items-center">
                            <div class="col-8">
                                <h3 class="fw-bold mb-1"><?php echo $totalLaporanBaru; ?></h3>
                                <p class="mb-0 opacity-75">Laporan Baru</p>
                            </div>
                            <div class="col-4 text-end">
                                <i class="fas fa-clipboard-list icon"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-12 col-md-6 col-lg-3 mb-3">
                    <div class="quick-action-card">
                        <div class="icon">
                            <i class="fas fa-users"></i>
                        </div>
                        <h5 class="fw-bold mb-3">Data KK</h5>
                        <p class="text-muted mb-3">Kelola data kartu keluarga warga</p>
                        <a href="kk.php" class="btn btn-custom w-100">
                            <i class="fas fa-arrow-right me-2"></i>Kelola KK
                        </a>
                    </div>
                </div>
                <div class="col-12 col-md-6 col-lg-3 mb-3">
                    <div class="quick-action-card">
                        <div class="icon">
                            <i class="fas fa-user"></i>
                        </div>
                        <h5 class="fw-bold mb-3">Data Warga</h5>
                        <p class="text-muted mb-3">Kelola data warga perorangan</p>
                        <a href="warga.php" class="btn btn-custom w-100">
                            <i class="fas fa-arrow-right me-2"></i>Kelola Warga
                        </a>
                    </div>
                </div>
                <div class="col-12 col-md-6 col-lg-3 mb-3">
                    <div class="quick-action-card">
                        <div class="icon">
                            <i class="fas fa-clipboard-list"></i>
                        </div>
                        <h5 class="fw-bold mb-3">Data Laporan</h5>
                        <p class="text-muted mb-3">Lihat dan kelola laporan lingkungan</p>
                        <a href="laporan.php" class="btn btn-custom w-100">
                            <i class="fas fa-arrow-right me-2"></i>Lihat Laporan
                        </a>
                    </div>
                </div>
                <div class="col-12 col-md-6 col-lg-3 mb-3">
                    <div class="quick-action-card">
                        <div class="icon">
                            <i class="fas fa-users-cog"></i>
                        </div>
                        <h5 class="fw-bold mb-3">Kelola User</h5>
                        <p class="text-muted mb-3">Kelola data user dan role</p>
                        <a href="users.php" class="btn btn-custom w-100">
                            <i class="fas fa-arrow-right me-2"></i>Kelola User
                        </a>
                    </div>
                </div>
                <div class="col-12 col-md-6 col-lg-3 mb-3">
                    <div class="quick-action-card">
                        <div class="icon">
                            <i class="fas fa-chart-bar"></i>
                        </div>
                        <h5 class="fw-bold mb-3">Statistik</h5>
                        <p class="text-muted mb-3">Lihat statistik dan analisis data</p>
                        <a href="#" class="btn btn-custom w-100">
                            <i class="fas fa-arrow-right me-2"></i>Lihat Statistik
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="../bootstrap-5.3.7-dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
