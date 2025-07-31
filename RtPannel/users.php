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
$current_user = $stmt->get_result()->fetch_assoc();

// Get all users (RT can only view, not manage)
$query = "SELECT id, username, email, role, created_at FROM users ORDER BY created_at DESC";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data User - RT Dashboard</title>
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
        
        .content-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            padding: 2rem;
        }
        
        .table {
            background: white;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
        }
        
        .table thead th {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            font-weight: 600;
        }
        
        .badge-role {
            padding: 8px 12px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
        }
        
        .badge-admin {
            background: linear-gradient(135deg, #ff6b6b 0%, #ee5a24 100%);
            color: white;
        }
        
        .badge-rt {
            background: linear-gradient(135deg, #4ecdc4 0%, #44a08d 100%);
            color: white;
        }
        
        .badge-user {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
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
                        <a class="nav-link active" href="users.php">
                            <i class="fas fa-user-cog me-1"></i>Data User
                        </a>
                    </li>
                </ul>
                
                <div class="navbar-nav">
                    <div class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" role="button" data-bs-toggle="dropdown">
                            <div class="user-avatar me-2">
                                <?php echo strtoupper(substr($current_user['username'], 0, 1)); ?>
                            </div>
                            <span><?php echo htmlspecialchars($current_user['username']); ?></span>
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
                        <i class="fas fa-users-cog me-2"></i>Data User
                    </h1>
                    <p class="lead mb-0">Lihat data user dan role dalam sistem</p>
                </div>
                <div class="col-md-4 text-md-end">
                    <div class="user-info">
                        <div class="user-avatar">
                            <?php echo strtoupper(substr($current_user['username'], 0, 1)); ?>
                        </div>
                        <div>
                            <div class="fw-bold"><?php echo htmlspecialchars($current_user['username']); ?></div>
                            <small class="text-light">Ketua RT</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="container">
        <div class="content-card">
            <div class="row align-items-center mb-4">
                <div class="col-md-8">
                    <h2 class="fw-bold text-dark mb-2">
                        <i class="fas fa-users me-2"></i>Daftar User
                    </h2>
                    <p class="text-muted mb-0">Informasi user dan role dalam sistem</p>
                </div>
                <div class="col-md-4 text-end">
                    <div class="alert alert-info mb-0">
                        <i class="fas fa-info-circle me-2"></i>
                        <small>RT hanya dapat melihat data user</small>
                    </div>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Username</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Tanggal Dibuat</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $no = 1;
                        while ($user = $result->fetch_assoc()): 
                        ?>
                        <tr>
                            <td><?php echo $no++; ?></td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center me-2" style="width: 35px; height: 35px;">
                                        <?php echo strtoupper(substr($user['username'], 0, 1)); ?>
                                    </div>
                                    <span class="fw-semibold"><?php echo htmlspecialchars($user['username']); ?></span>
                                </div>
                            </td>
                            <td><?php echo htmlspecialchars($user['email'] ?? '-'); ?></td>
                            <td>
                                <?php
                                $role_class = '';
                                switch($user['role']) {
                                    case 'admin':
                                        $role_class = 'badge-admin';
                                        break;
                                    case 'rt':
                                        $role_class = 'badge-rt';
                                        break;
                                    case 'user':
                                        $role_class = 'badge-user';
                                        break;
                                }
                                ?>
                                <span class="badge-role <?php echo $role_class; ?>">
                                    <?php echo strtoupper($user['role']); ?>
                                </span>
                            </td>

                            <td><?php echo date('d/m/Y H:i', strtotime($user['created_at'])); ?></td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>

            <?php if ($result->num_rows === 0): ?>
            <div class="text-center py-5">
                <i class="fas fa-users text-muted" style="font-size: 4rem;"></i>
                <h4 class="text-muted mt-3">Belum ada data user</h4>
                <p class="text-muted">Sistem belum memiliki user terdaftar</p>
            </div>
            <?php endif; ?>

            <!-- Statistics -->
            <div class="row mt-4">
                <div class="col-12">
                    <h4 class="fw-bold mb-3">
                        <i class="fas fa-chart-pie me-2"></i>Statistik User
                    </h4>
                </div>
                <?php
                // Get user statistics
                $total_users = $conn->query("SELECT COUNT(*) as total FROM users")->fetch_assoc()['total'];
                $admin_count = $conn->query("SELECT COUNT(*) as total FROM users WHERE role = 'admin'")->fetch_assoc()['total'];
                $rt_count = $conn->query("SELECT COUNT(*) as total FROM users WHERE role = 'rt'")->fetch_assoc()['total'];
                $user_count = $conn->query("SELECT COUNT(*) as total FROM users WHERE role = 'user'")->fetch_assoc()['total'];
                ?>
                <div class="col-md-2 mb-3">
                    <div class="card text-center">
                        <div class="card-body">
                            <h5 class="card-title text-primary"><?php echo $total_users; ?></h5>
                            <p class="card-text text-muted">Total User</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-2 mb-3">
                    <div class="card text-center">
                        <div class="card-body">
                            <h5 class="card-title text-danger"><?php echo $admin_count; ?></h5>
                            <p class="card-text text-muted">Admin</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-2 mb-3">
                    <div class="card text-center">
                        <div class="card-body">
                            <h5 class="card-title text-info"><?php echo $rt_count; ?></h5>
                            <p class="card-text text-muted">RT</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-2 mb-3">
                    <div class="card text-center">
                        <div class="card-body">
                            <h5 class="card-title text-success"><?php echo $user_count; ?></h5>
                            <p class="card-text text-muted">User</p>
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
</body>
</html> 