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

$success_message = '';
$error_message = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];
    
    // Validate required fields
    if (empty($username) || empty($email)) {
        $error_message = 'Username dan email wajib diisi!';
    } else {
        // Check if username already exists (excluding current user)
        $stmt = $conn->prepare("SELECT id FROM users WHERE username = ? AND id != ?");
        $stmt->bind_param("si", $username, $user_id);
        $stmt->execute();
        if ($stmt->get_result()->num_rows > 0) {
            $error_message = 'Username sudah digunakan!';
        } else {
            // Update basic info
            $stmt = $conn->prepare("UPDATE users SET username = ?, email = ? WHERE id = ?");
            $stmt->bind_param("ssi", $username, $email, $user_id);
            
            if ($stmt->execute()) {
                $success_message = 'Profil berhasil diperbarui!';
                // Update session username
                $_SESSION['username'] = $username;
                // Refresh user data
                $stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
                $stmt->bind_param("i", $user_id);
                $stmt->execute();
                $user = $stmt->get_result()->fetch_assoc();
            } else {
                $error_message = 'Gagal memperbarui profil!';
            }
            
            // Handle password change if provided
            if (!empty($current_password) && !empty($new_password)) {
                if (!password_verify($current_password, $user['password'])) {
                    $error_message = 'Password saat ini salah!';
                } elseif ($new_password !== $confirm_password) {
                    $error_message = 'Password baru dan konfirmasi tidak cocok!';
                } elseif (strlen($new_password) < 6) {
                    $error_message = 'Password minimal 6 karakter!';
                } else {
                    $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
                    $stmt = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
                    $stmt->bind_param("si", $hashed_password, $user_id);
                    
                    if ($stmt->execute()) {
                        $success_message = 'Profil dan password berhasil diperbarui!';
                    } else {
                        $error_message = 'Gagal memperbarui password!';
                    }
                }
            }
        }
    }
}

// Get user's reports count
$stmt = $conn->prepare("SELECT COUNT(*) as total FROM laporan WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$total_reports = $stmt->get_result()->fetch_assoc()['total'];

// Get user's recent reports
$stmt = $conn->prepare("SELECT * FROM laporan WHERE user_id = ? ORDER BY created_at DESC LIMIT 3");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$recent_reports = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil - Laporin Lingkungan</title>
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
        
        .profile-card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
            padding: 2rem;
            border: 1px solid rgba(0, 0, 0, 0.05);
            margin-bottom: 2rem;
        }
        
        .profile-avatar {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 3rem;
            font-weight: bold;
            margin: 0 auto 1.5rem;
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
        
        .form-group {
            margin-bottom: 1.5rem;
        }
        
        .form-label {
            font-weight: 600;
            margin-bottom: 0.5rem;
            color: #333;
        }
        
        .form-control {
            border-radius: 10px;
            border: 2px solid #e9ecef;
            padding: 0.75rem 1rem;
            transition: all 0.3s ease;
        }
        
        .form-control:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }
        
        .btn-submit {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            border: none;
            border-radius: 10px;
            padding: 0.75rem 2rem;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        
        .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
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
        
        .recent-report {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 1rem;
            margin-bottom: 1rem;
            border-left: 4px solid var(--primary-color);
        }
        
        @media (max-width: 768px) {
            .page-header {
                padding: 1rem 0;
            }
            
            .profile-card {
                padding: 1.5rem;
            }
            
            .stats-card {
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
                        <i class="fas fa-user me-2"></i>Profil Saya
                    </h1>
                    <p class="lead mb-0">Kelola informasi profil dan akun Anda</p>
                </div>
                <div class="col-md-4 text-md-end">
                    <a href="laporan.php" class="btn btn-light btn-lg">
                        <i class="fas fa-file-alt me-2"></i>Lihat Laporan Saya
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="container">
        <div class="row">
            <!-- Profile Information -->
            <div class="col-lg-8">
                <div class="profile-card">
                    <div class="profile-avatar">
                        <?php echo strtoupper(substr($user['username'], 0, 1)); ?>
                    </div>
                    
                    <h3 class="text-center mb-4"><?php echo htmlspecialchars($user['username']); ?></h3>
                    
                    <?php if ($success_message): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="fas fa-check-circle me-2"></i><?php echo $success_message; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                    <?php endif; ?>
                    
                    <?php if ($error_message): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="fas fa-exclamation-circle me-2"></i><?php echo $error_message; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                    <?php endif; ?>
                    
                    <form method="POST" id="profileForm">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="username" class="form-label">
                                        <i class="fas fa-user me-1"></i>Username
                                    </label>
                                    <input type="text" class="form-control" id="username" name="username" 
                                           value="<?php echo htmlspecialchars($user['username']); ?>" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="email" class="form-label">
                                        <i class="fas fa-envelope me-1"></i>Email
                                    </label>
                                    <input type="email" class="form-control" id="email" name="email" 
                                           value="<?php echo htmlspecialchars($user['email']); ?>" required>
                                </div>
                            </div>
                        </div>
                        
                        <hr class="my-4">
                        
                        <h5 class="fw-bold mb-3">
                            <i class="fas fa-lock me-2"></i>Ubah Password
                        </h5>
                        <p class="text-muted small mb-3">Kosongkan field password jika tidak ingin mengubah password</p>
                        
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="current_password" class="form-label">
                                        <i class="fas fa-key me-1"></i>Password Saat Ini
                                    </label>
                                    <input type="password" class="form-control" id="current_password" name="current_password" 
                                           placeholder="Masukkan password saat ini">
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="new_password" class="form-label">
                                        <i class="fas fa-lock me-1"></i>Password Baru
                                    </label>
                                    <input type="password" class="form-control" id="new_password" name="new_password" 
                                           placeholder="Masukkan password baru">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="confirm_password" class="form-label">
                                        <i class="fas fa-lock me-1"></i>Konfirmasi Password
                                    </label>
                                    <input type="password" class="form-control" id="confirm_password" name="confirm_password" 
                                           placeholder="Konfirmasi password baru">
                                </div>
                            </div>
                        </div>
                        
                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <button type="submit" class="btn btn-primary btn-submit">
                                <i class="fas fa-save me-2"></i>Simpan Perubahan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            
            <!-- Statistics and Recent Reports -->
            <div class="col-lg-4">
                <!-- Statistics -->
                <div class="stats-card">
                    <div class="stats-icon text-primary">
                        <i class="fas fa-file-alt"></i>
                    </div>
                    <div class="stats-number text-primary"><?php echo $total_reports; ?></div>
                    <div class="text-muted">Total Laporan Dibuat</div>
                </div>
                
                <!-- Recent Reports -->
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h6 class="mb-0">
                            <i class="fas fa-history me-2"></i>Laporan Terbaru
                        </h6>
                    </div>
                    <div class="card-body">
                        <?php if ($recent_reports->num_rows > 0): ?>
                            <?php while ($report = $recent_reports->fetch_assoc()): ?>
                            <div class="recent-report">
                                <h6 class="fw-bold mb-1"><?php echo htmlspecialchars($report['judul']); ?></h6>
                                <p class="text-muted small mb-1">
                                    <i class="fas fa-map-marker-alt me-1"></i>
                                    <?php echo htmlspecialchars($report['lokasi']); ?>
                                </p>
                                <div class="d-flex justify-content-between align-items-center">
                                    <small class="text-muted">
                                        <?php echo date('d/m/Y', strtotime($report['created_at'])); ?>
                                    </small>
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
                                </div>
                            </div>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <div class="text-center py-3">
                                <i class="fas fa-file-alt text-muted" style="font-size: 2rem;"></i>
                                <p class="text-muted mt-2 mb-0">Belum ada laporan</p>
                            </div>
                        <?php endif; ?>
                        
                        <div class="text-center mt-3">
                            <a href="laporan.php" class="btn btn-outline-primary btn-sm">
                                <i class="fas fa-eye me-1"></i>Lihat Semua
                            </a>
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
        // Form validation
        document.getElementById('profileForm').addEventListener('submit', function(e) {
            const username = document.getElementById('username').value.trim();
            const email = document.getElementById('email').value.trim();
            const currentPassword = document.getElementById('current_password').value;
            const newPassword = document.getElementById('new_password').value;
            const confirmPassword = document.getElementById('confirm_password').value;
            
            if (!username || !email) {
                e.preventDefault();
                alert('Username dan email wajib diisi!');
                return false;
            }
            
            // Check if password fields are filled
            if (currentPassword || newPassword || confirmPassword) {
                if (!currentPassword) {
                    e.preventDefault();
                    alert('Password saat ini wajib diisi jika ingin mengubah password!');
                    return false;
                }
                
                if (!newPassword) {
                    e.preventDefault();
                    alert('Password baru wajib diisi!');
                    return false;
                }
                
                if (newPassword !== confirmPassword) {
                    e.preventDefault();
                    alert('Password baru dan konfirmasi tidak cocok!');
                    return false;
                }
                
                if (newPassword.length < 6) {
                    e.preventDefault();
                    alert('Password minimal 6 karakter!');
                    return false;
                }
            }
        });
    </script>
</body>
</html> 