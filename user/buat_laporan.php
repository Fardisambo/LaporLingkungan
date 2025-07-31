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
    $judul = trim($_POST['judul']);
    $deskripsi = trim($_POST['deskripsi']);
    $lokasi = trim($_POST['lokasi']);
    $kategori = $_POST['kategori'];
    
    // Validate required fields
    if (empty($judul) || empty($deskripsi) || empty($lokasi)) {
        $error_message = 'Semua field wajib diisi!';
    } else {
        $foto = '';
        
        // Handle file upload
        if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
            $upload_dir = '../uploads/';
            $file_extension = strtolower(pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION));
            $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif'];
            
            if (in_array($file_extension, $allowed_extensions)) {
                $foto = uniqid() . '.' . $file_extension;
                $upload_path = $upload_dir . $foto;
                
                if (move_uploaded_file($_FILES['foto']['tmp_name'], $upload_path)) {
                    // File uploaded successfully
                } else {
                    $error_message = 'Gagal mengupload foto!';
                }
            } else {
                $error_message = 'Format file tidak didukung! Gunakan JPG, JPEG, PNG, atau GIF.';
            }
        }
        
        if (empty($error_message)) {
            // Insert report into database
            $stmt = $conn->prepare("INSERT INTO laporan (user_id, judul, deskripsi, lokasi, kategori, foto, status, created_at) VALUES (?, ?, ?, ?, ?, ?, 'pending', NOW())");
            $stmt->bind_param("isssss", $user_id, $judul, $deskripsi, $lokasi, $kategori, $foto);
            
            if ($stmt->execute()) {
                $success_message = 'Laporan berhasil dibuat! Tim kami akan segera memproses laporan Anda.';
                // Clear form data after successful submission
                $_POST = array();
            } else {
                $error_message = 'Gagal membuat laporan! Silakan coba lagi.';
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Buat Laporan - Laporin Lingkungan</title>
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
            border-radius: 20px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
            padding: 2rem;
            border: 1px solid rgba(0, 0, 0, 0.05);
        }
        
        .form-group {
            margin-bottom: 1.5rem;
        }
        
        .form-label {
            font-weight: 600;
            margin-bottom: 0.5rem;
            color: #333;
        }
        
        .form-control, .form-select {
            border-radius: 10px;
            border: 2px solid #e9ecef;
            padding: 0.75rem 1rem;
            transition: all 0.3s ease;
        }
        
        .form-control:focus, .form-select:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }
        
        .input-group-text {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            border: none;
            color: white;
            border-radius: 10px 0 0 10px;
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
        
        .file-upload {
            position: relative;
            display: inline-block;
            cursor: pointer;
            width: 100%;
        }
        
        .file-upload input[type=file] {
            position: absolute;
            opacity: 0;
            width: 100%;
            height: 100%;
            cursor: pointer;
        }
        
        .file-upload-label {
            display: block;
            padding: 1rem;
            border: 2px dashed #dee2e6;
            border-radius: 10px;
            text-align: center;
            transition: all 0.3s ease;
            background: #f8f9fa;
        }
        
        .file-upload:hover .file-upload-label {
            border-color: var(--primary-color);
            background: rgba(102, 126, 234, 0.05);
        }
        
        .preview-image {
            max-width: 100%;
            max-height: 200px;
            border-radius: 10px;
            margin-top: 1rem;
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
        
        .help-text {
            font-size: 0.875rem;
            color: #6c757d;
            margin-top: 0.25rem;
        }
        
        @media (max-width: 768px) {
            .page-header {
                padding: 1rem 0;
            }
            
            .form-card {
                padding: 1.5rem;
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
                        <a class="nav-link active" href="buat_laporan.php">
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
                        <i class="fas fa-plus me-2"></i>Buat Laporan Baru
                    </h1>
                    <p class="lead mb-0">Laporkan masalah lingkungan yang Anda temukan di sekitar Anda</p>
                </div>
                <div class="col-md-4 text-md-end">
                    <a href="laporan.php" class="btn btn-light btn-lg">
                        <i class="fas fa-list me-2"></i>Lihat Laporan Saya
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
                    
                    <form method="POST" enctype="multipart/form-data" id="reportForm">
                        <div class="form-group">
                            <label for="judul" class="form-label">
                                <i class="fas fa-heading me-1"></i>Judul Laporan
                            </label>
                            <input type="text" class="form-control" id="judul" name="judul" 
                                   value="<?php echo isset($_POST['judul']) ? htmlspecialchars($_POST['judul']) : ''; ?>" 
                                   placeholder="Masukkan judul laporan" required>
                            <div class="help-text">Berikan judul yang jelas dan deskriptif untuk laporan Anda</div>
                        </div>
                        
                        <div class="form-group">
                            <label for="kategori" class="form-label">
                                <i class="fas fa-tags me-1"></i>Kategori
                            </label>
                            <select class="form-select" id="kategori" name="kategori" required>
                                <option value="">Pilih kategori</option>
                                <option value="sampah" <?php echo (isset($_POST['kategori']) && $_POST['kategori'] === 'sampah') ? 'selected' : ''; ?>>Sampah</option>
                                <option value="polusi_udara" <?php echo (isset($_POST['kategori']) && $_POST['kategori'] === 'polusi_udara') ? 'selected' : ''; ?>>Polusi Udara</option>
                                <option value="polusi_air" <?php echo (isset($_POST['kategori']) && $_POST['kategori'] === 'polusi_air') ? 'selected' : ''; ?>>Polusi Air</option>
                                <option value="kerusakan_lingkungan" <?php echo (isset($_POST['kategori']) && $_POST['kategori'] === 'kerusakan_lingkungan') ? 'selected' : ''; ?>>Kerusakan Lingkungan</option>
                                <option value="pohon_tumbang" <?php echo (isset($_POST['kategori']) && $_POST['kategori'] === 'pohon_tumbang') ? 'selected' : ''; ?>>Pohon Tumbang</option>
                                <option value="lubang_jalan" <?php echo (isset($_POST['kategori']) && $_POST['kategori'] === 'lubang_jalan') ? 'selected' : ''; ?>>Lubang Jalan</option>
                                <option value="lainnya" <?php echo (isset($_POST['kategori']) && $_POST['kategori'] === 'lainnya') ? 'selected' : ''; ?>>Lainnya</option>
                            </select>
                            <div class="help-text">Pilih kategori yang paling sesuai dengan masalah yang Anda laporkan</div>
                        </div>
                        
                        <div class="form-group">
                            <label for="lokasi" class="form-label">
                                <i class="fas fa-map-marker-alt me-1"></i>Lokasi
                            </label>
                            <input type="text" class="form-control" id="lokasi" name="lokasi" 
                                   value="<?php echo isset($_POST['lokasi']) ? htmlspecialchars($_POST['lokasi']) : ''; ?>" 
                                   placeholder="Masukkan lokasi masalah (contoh: Jl. Sudirman No. 123)" required>
                            <div class="help-text">Berikan alamat atau lokasi yang spesifik agar tim kami dapat dengan mudah menemukan lokasi masalah</div>
                        </div>
                        
                        <div class="form-group">
                            <label for="deskripsi" class="form-label">
                                <i class="fas fa-align-left me-1"></i>Deskripsi Masalah
                            </label>
                            <textarea class="form-control" id="deskripsi" name="deskripsi" rows="5" 
                                      placeholder="Jelaskan detail masalah yang Anda temukan..." required><?php echo isset($_POST['deskripsi']) ? htmlspecialchars($_POST['deskripsi']) : ''; ?></textarea>
                            <div class="help-text">Jelaskan secara detail masalah yang Anda temukan, termasuk dampaknya terhadap lingkungan dan masyarakat sekitar</div>
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label">
                                <i class="fas fa-camera me-1"></i>Foto Bukti (Opsional)
                            </label>
                            <div class="file-upload">
                                <input type="file" id="foto" name="foto" accept="image/*" onchange="previewImage(this)">
                                <label for="foto" class="file-upload-label">
                                    <i class="fas fa-cloud-upload-alt me-2" style="font-size: 2rem; color: var(--primary-color);"></i>
                                    <div class="fw-bold">Upload Foto</div>
                                    <div class="text-muted">Klik untuk memilih foto atau drag & drop</div>
                                    <div class="text-muted small">Format: JPG, JPEG, PNG, GIF (Max: 5MB)</div>
                                </label>
                            </div>
                            <div id="imagePreview" class="mt-2" style="display: none;">
                                <img id="previewImg" class="preview-image" alt="Preview">
                            </div>
                            <div class="help-text">Foto akan membantu tim kami memahami masalah dengan lebih baik</div>
                        </div>
                        
                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <a href="laporan.php" class="btn btn-outline-secondary me-md-2">
                                <i class="fas fa-times me-2"></i>Batal
                            </a>
                            <button type="submit" class="btn btn-primary btn-submit">
                                <i class="fas fa-paper-plane me-2"></i>Kirim Laporan
                            </button>
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
    <script>
        function previewImage(input) {
            const preview = document.getElementById('imagePreview');
            const previewImg = document.getElementById('previewImg');
            
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                
                reader.onload = function(e) {
                    previewImg.src = e.target.result;
                    preview.style.display = 'block';
                }
                
                reader.readAsDataURL(input.files[0]);
            } else {
                preview.style.display = 'none';
            }
        }
        
        // Form validation
        document.getElementById('reportForm').addEventListener('submit', function(e) {
            const judul = document.getElementById('judul').value.trim();
            const deskripsi = document.getElementById('deskripsi').value.trim();
            const lokasi = document.getElementById('lokasi').value.trim();
            const kategori = document.getElementById('kategori').value;
            
            if (!judul || !deskripsi || !lokasi || !kategori) {
                e.preventDefault();
                alert('Semua field wajib diisi!');
                return false;
            }
            
            if (deskripsi.length < 20) {
                e.preventDefault();
                alert('Deskripsi minimal 20 karakter!');
                return false;
            }
        });
    </script>
</body>
</html> 