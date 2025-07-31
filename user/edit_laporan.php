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

// Check if report ID is provided
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: laporan.php?error=invalid_id");
    exit();
}

$report_id = $_GET['id'];

// Get report data and check if it belongs to the current user
$stmt = $conn->prepare("SELECT * FROM laporan WHERE id = ? AND user_id = ?");
$stmt->bind_param("ii", $report_id, $user_id);
$stmt->execute();
$report = $stmt->get_result()->fetch_assoc();

if (!$report) {
    header("Location: laporan.php?error=unauthorized");
    exit();
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $judul = trim($_POST['judul']);
    $lokasi = trim($_POST['lokasi']);
    $deskripsi = trim($_POST['deskripsi']);
    $kategori = $_POST['kategori'];
    
    // Validation
    $errors = [];
    
    if (empty($judul)) {
        $errors[] = "Judul laporan harus diisi";
    }
    
    if (empty($lokasi)) {
        $errors[] = "Lokasi harus diisi";
    }
    
    if (empty($deskripsi)) {
        $errors[] = "Deskripsi harus diisi";
    }
    
    if (empty($kategori)) {
        $errors[] = "Kategori harus dipilih";
    }
    
    // Handle file upload
    $foto = $report['foto']; // Keep existing photo by default
    
    if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
        $allowed_types = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
        $max_size = 5 * 1024 * 1024; // 5MB
        
        if (!in_array($_FILES['foto']['type'], $allowed_types)) {
            $errors[] = "Tipe file tidak didukung. Gunakan JPG, PNG, atau GIF";
        }
        
        if ($_FILES['foto']['size'] > $max_size) {
            $errors[] = "Ukuran file terlalu besar. Maksimal 5MB";
        }
        
        if (empty($errors)) {
            $file_extension = pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION);
            $new_filename = uniqid() . '.' . $file_extension;
            $upload_path = "../uploads/" . $new_filename;
            
            if (move_uploaded_file($_FILES['foto']['tmp_name'], $upload_path)) {
                // Delete old photo if exists
                if ($report['foto'] && file_exists("../uploads/" . $report['foto'])) {
                    unlink("../uploads/" . $report['foto']);
                }
                $foto = $new_filename;
            } else {
                $errors[] = "Gagal mengupload file";
            }
        }
    }
    
    // Update report if no errors
    if (empty($errors)) {
        $stmt = $conn->prepare("UPDATE laporan SET judul = ?, lokasi = ?, deskripsi = ?, kategori = ?, foto = ? WHERE id = ? AND user_id = ?");
        $stmt->bind_param("sssssii", $judul, $lokasi, $deskripsi, $kategori, $foto, $report_id, $user_id);
        
        if ($stmt->execute()) {
            header("Location: detail_laporan.php?id=" . $report_id . "&success=updated");
            exit();
        } else {
            $errors[] = "Gagal memperbarui laporan";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Laporan - Laporin Lingkungan</title>
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
        
        .form-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
            padding: 2rem;
            border: 1px solid rgba(0, 0, 0, 0.05);
        }
        
        .form-label {
            font-weight: 600;
            color: #495057;
        }
        
        .form-control, .form-select {
            border-radius: 10px;
            border: 1px solid #dee2e6;
            padding: 0.75rem 1rem;
            transition: border-color 0.3s ease, box-shadow 0.3s ease;
        }
        
        .form-control:focus, .form-select:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            border-radius: 10px;
            padding: 0.75rem 2rem;
            font-weight: 600;
            transition: transform 0.3s ease;
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
        }
        
        .current-photo {
            max-width: 200px;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }
        
        .alert {
            border-radius: 10px;
            border: none;
        }
        
        .navbar-brand {
            font-weight: bold;
            font-size: 1.5rem;
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
                            <i class="fas fa-file-alt me-1"></i>Semua Laporan
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
                        <i class="fas fa-edit me-2"></i>Edit Laporan
                    </h1>
                    <p class="lead mb-0">Perbarui informasi laporan Anda</p>
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
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="form-card">
                    <?php if (!empty($errors)): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <strong>Terjadi kesalahan:</strong>
                        <ul class="mb-0 mt-2">
                            <?php foreach ($errors as $error): ?>
                            <li><?php echo htmlspecialchars($error); ?></li>
                            <?php endforeach; ?>
                        </ul>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                    <?php endif; ?>

                    <form method="POST" enctype="multipart/form-data">
                        <div class="row">
                            <div class="col-md-8">
                                <div class="mb-3">
                                    <label for="judul" class="form-label">
                                        <i class="fas fa-heading me-1"></i>Judul Laporan
                                    </label>
                                    <input type="text" class="form-control" id="judul" name="judul" 
                                           value="<?php echo htmlspecialchars($report['judul']); ?>" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="kategori" class="form-label">
                                        <i class="fas fa-tag me-1"></i>Kategori
                                    </label>
                                    <select class="form-select" id="kategori" name="kategori" required>
                                        <option value="">Pilih Kategori</option>
                                        <option value="Sampah" <?php echo $report['kategori'] === 'Sampah' ? 'selected' : ''; ?>>Sampah</option>
                                        <option value="Pencemaran Air" <?php echo $report['kategori'] === 'Pencemaran Air' ? 'selected' : ''; ?>>Pencemaran Air</option>
                                        <option value="Pencemaran Udara" <?php echo $report['kategori'] === 'Pencemaran Udara' ? 'selected' : ''; ?>>Pencemaran Udara</option>
                                        <option value="Kerusakan Lingkungan" <?php echo $report['kategori'] === 'Kerusakan Lingkungan' ? 'selected' : ''; ?>>Kerusakan Lingkungan</option>
                                        <option value="Lainnya" <?php echo $report['kategori'] === 'Lainnya' ? 'selected' : ''; ?>>Lainnya</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="lokasi" class="form-label">
                                <i class="fas fa-map-marker-alt me-1"></i>Lokasi
                            </label>
                            <input type="text" class="form-control" id="lokasi" name="lokasi" 
                                   value="<?php echo htmlspecialchars($report['lokasi']); ?>" required>
                        </div>

                        <div class="mb-3">
                            <label for="deskripsi" class="form-label">
                                <i class="fas fa-align-left me-1"></i>Deskripsi Masalah
                            </label>
                            <textarea class="form-control" id="deskripsi" name="deskripsi" rows="5" required><?php echo htmlspecialchars($report['deskripsi']); ?></textarea>
                        </div>

                        <div class="mb-4">
                            <label for="foto" class="form-label">
                                <i class="fas fa-camera me-1"></i>Foto (Opsional)
                            </label>
                            
                            <?php if ($report['foto']): ?>
                            <div class="mb-3">
                                <p class="text-muted small mb-2">Foto saat ini:</p>
                                <img src="../uploads/<?php echo htmlspecialchars($report['foto']); ?>" 
                                     alt="Foto Laporan" class="current-photo">
                            </div>
                            <?php endif; ?>
                            
                            <input type="file" class="form-control" id="foto" name="foto" accept="image/*">
                            <div class="form-text">
                                Format: JPG, PNG, GIF. Maksimal 5MB. Kosongkan jika tidak ingin mengubah foto.
                            </div>
                        </div>

                        <div class="d-flex gap-3">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>Simpan Perubahan
                            </button>
                            <a href="detail_laporan.php?id=<?php echo $report_id; ?>" class="btn btn-outline-secondary">
                                <i class="fas fa-times me-2"></i>Batal
                            </a>
                        </div>
                    </form>
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