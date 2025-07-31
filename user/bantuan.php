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
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bantuan - Laporin Lingkungan</title>
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
        
        .help-card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
            padding: 2rem;
            border: 1px solid rgba(0, 0, 0, 0.05);
            margin-bottom: 2rem;
        }
        
        .help-section {
            margin-bottom: 2rem;
        }
        
        .help-icon {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.5rem;
            margin-bottom: 1rem;
        }
        
        .step-item {
            display: flex;
            align-items: flex-start;
            margin-bottom: 1.5rem;
            padding: 1rem;
            background: #f8f9fa;
            border-radius: 10px;
            border-left: 4px solid var(--primary-color);
        }
        
        .step-number {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            background: var(--primary-color);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            margin-right: 1rem;
            flex-shrink: 0;
        }
        
        .step-content h6 {
            margin-bottom: 0.5rem;
            font-weight: 600;
        }
        
        .step-content p {
            margin-bottom: 0;
            color: #6c757d;
        }
        
        .faq-item {
            margin-bottom: 1rem;
        }
        
        .faq-question {
            background: #f8f9fa;
            border: none;
            border-radius: 10px;
            padding: 1rem;
            width: 100%;
            text-align: left;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        
        .faq-question:hover {
            background: #e9ecef;
        }
        
        .faq-answer {
            padding: 1rem;
            background: white;
            border-radius: 0 0 10px 10px;
            border: 1px solid #dee2e6;
            border-top: none;
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
        
        @media (max-width: 768px) {
            .page-header {
                padding: 1rem 0;
            }
            
            .help-card {
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
                        <a class="nav-link" href="buat_laporan.php">
                            <i class="fas fa-plus me-1"></i>Buat Laporan
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="bantuan.php">
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
                        <i class="fas fa-question-circle me-2"></i>Pusat Bantuan
                    </h1>
                    <p class="lead mb-0">Panduan lengkap penggunaan aplikasi Laporin Lingkungan</p>
                </div>
                <div class="col-md-4 text-md-end">
                    <a href="buat_laporan.php" class="btn btn-light btn-lg">
                        <i class="fas fa-plus me-2"></i>Buat Laporan
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="container">
        <!-- How to Create Report -->
        <div class="help-card">
            <div class="help-section">
                <div class="text-center mb-4">
                    <div class="help-icon mx-auto">
                        <i class="fas fa-plus"></i>
                    </div>
                    <h3 class="fw-bold">Cara Membuat Laporan</h3>
                    <p class="text-muted">Ikuti langkah-langkah berikut untuk membuat laporan lingkungan</p>
                </div>
                
                <div class="step-item">
                    <div class="step-number">1</div>
                    <div class="step-content">
                        <h6>Klik "Buat Laporan"</h6>
                        <p>Dari dashboard atau menu navigasi, klik tombol "Buat Laporan" untuk memulai proses pelaporan.</p>
                    </div>
                </div>
                
                <div class="step-item">
                    <div class="step-number">2</div>
                    <div class="step-content">
                        <h6>Isi Form Laporan</h6>
                        <p>Lengkapi semua field yang diperlukan: judul, kategori, lokasi, dan deskripsi masalah. Semua field wajib diisi.</p>
                    </div>
                </div>
                
                <div class="step-item">
                    <div class="step-number">3</div>
                    <div class="step-content">
                        <h6>Upload Foto (Opsional)</h6>
                        <p>Tambahkan foto bukti untuk memperjelas masalah yang Anda laporkan. Format yang didukung: JPG, JPEG, PNG, GIF.</p>
                    </div>
                </div>
                
                <div class="step-item">
                    <div class="step-number">4</div>
                    <div class="step-content">
                        <h6>Kirim Laporan</h6>
                        <p>Klik tombol "Kirim Laporan" untuk mengirim laporan Anda. Tim kami akan segera memproses laporan Anda.</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Categories -->
        <div class="help-card">
            <div class="help-section">
                <div class="text-center mb-4">
                    <div class="help-icon mx-auto">
                        <i class="fas fa-tags"></i>
                    </div>
                    <h3 class="fw-bold">Kategori Laporan</h3>
                    <p class="text-muted">Pilih kategori yang paling sesuai dengan masalah yang Anda temukan</p>
                </div>
                
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <div class="card h-100">
                            <div class="card-body">
                                <h6 class="fw-bold"><i class="fas fa-trash text-warning me-2"></i>Sampah</h6>
                                <p class="text-muted small">Tumpukan sampah, sampah berserakan, atau masalah pengelolaan sampah lainnya.</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <div class="card h-100">
                            <div class="card-body">
                                <h6 class="fw-bold"><i class="fas fa-smog text-secondary me-2"></i>Polusi Udara</h6>
                                <p class="text-muted small">Asap kendaraan, asap pabrik, atau polusi udara lainnya yang mengganggu.</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <div class="card h-100">
                            <div class="card-body">
                                <h6 class="fw-bold"><i class="fas fa-water text-info me-2"></i>Polusi Air</h6>
                                <p class="text-muted small">Pencemaran sungai, danau, atau sumber air lainnya.</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <div class="card h-100">
                            <div class="card-body">
                                <h6 class="fw-bold"><i class="fas fa-tree text-success me-2"></i>Kerusakan Lingkungan</h6>
                                <p class="text-muted small">Kerusakan taman, hutan, atau area hijau lainnya.</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <div class="card h-100">
                            <div class="card-body">
                                <h6 class="fw-bold"><i class="fas fa-tree text-danger me-2"></i>Pohon Tumbang</h6>
                                <p class="text-muted small">Pohon yang tumbang dan berpotensi membahayakan.</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <div class="card h-100">
                            <div class="card-body">
                                <h6 class="fw-bold"><i class="fas fa-road text-dark me-2"></i>Lubang Jalan</h6>
                                <p class="text-muted small">Lubang atau kerusakan jalan yang membahayakan pengguna jalan.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- FAQ -->
        <div class="help-card">
            <div class="help-section">
                <div class="text-center mb-4">
                    <div class="help-icon mx-auto">
                        <i class="fas fa-question"></i>
                    </div>
                    <h3 class="fw-bold">Pertanyaan Umum</h3>
                    <p class="text-muted">Temukan jawaban untuk pertanyaan yang sering diajukan</p>
                </div>
                
                <div class="accordion" id="faqAccordion">
                    <div class="faq-item">
                        <button class="faq-question" type="button" data-bs-toggle="collapse" data-bs-target="#faq1">
                            <i class="fas fa-chevron-down me-2"></i>Berapa lama waktu yang dibutuhkan untuk memproses laporan?
                        </button>
                        <div id="faq1" class="collapse faq-answer" data-bs-parent="#faqAccordion">
                            Tim kami akan memproses laporan Anda dalam waktu 1-3 hari kerja. Anda akan mendapat notifikasi ketika laporan selesai diproses.
                        </div>
                    </div>
                    
                    <div class="faq-item">
                        <button class="faq-question" type="button" data-bs-toggle="collapse" data-bs-target="#faq2">
                            <i class="fas fa-chevron-down me-2"></i>Apakah saya bisa mengedit laporan yang sudah dikirim?
                        </button>
                        <div id="faq2" class="collapse faq-answer" data-bs-parent="#faqAccordion">
                            Ya, Anda dapat mengedit laporan yang masih dalam status "Menunggu". Laporan yang sudah "Selesai" tidak dapat diedit.
                        </div>
                    </div>
                    
                    <div class="faq-item">
                        <button class="faq-question" type="button" data-bs-toggle="collapse" data-bs-target="#faq3">
                            <i class="fas fa-chevron-down me-2"></i>Apakah foto wajib diupload?
                        </button>
                        <div id="faq3" class="collapse faq-answer" data-bs-parent="#faqAccordion">
                            Tidak, foto bersifat opsional. Namun, foto akan membantu tim kami memahami masalah dengan lebih baik dan mempercepat proses penanganan.
                        </div>
                    </div>
                    
                    <div class="faq-item">
                        <button class="faq-question" type="button" data-bs-toggle="collapse" data-bs-target="#faq4">
                            <i class="fas fa-chevron-down me-2"></i>Bagaimana jika masalah tidak masuk dalam kategori yang tersedia?
                        </button>
                        <div id="faq4" class="collapse faq-answer" data-bs-parent="#faqAccordion">
                            Pilih kategori "Lainnya" dan jelaskan detail masalah dalam deskripsi. Tim kami akan mengkategorikan masalah sesuai dengan jenisnya.
                        </div>
                    </div>
                    
                    <div class="faq-item">
                        <button class="faq-question" type="button" data-bs-toggle="collapse" data-bs-target="#faq5">
                            <i class="fas fa-chevron-down me-2"></i>Apakah saya bisa melaporkan masalah di luar kota?
                        </button>
                        <div id="faq5" class="collapse faq-answer" data-bs-parent="#faqAccordion">
                            Saat ini aplikasi hanya melayani pelaporan masalah lingkungan dalam wilayah kota yang sama. Untuk masalah di luar kota, silakan hubungi pemerintah daerah setempat.
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Contact -->
        <div class="help-card">
            <div class="help-section">
                <div class="text-center mb-4">
                    <div class="help-icon mx-auto">
                        <i class="fas fa-phone"></i>
                    </div>
                    <h3 class="fw-bold">Hubungi Kami</h3>
                    <p class="text-muted">Jika Anda membutuhkan bantuan lebih lanjut</p>
                </div>
                
                <div class="row">
                    <div class="col-md-4 text-center mb-3">
                        <i class="fas fa-envelope text-primary" style="font-size: 2rem;"></i>
                        <h6 class="fw-bold mt-2">Email</h6>
                        <p class="text-muted">support@laporinlingkungan.com</p>
                    </div>
                    
                    <div class="col-md-4 text-center mb-3">
                        <i class="fas fa-phone text-primary" style="font-size: 2rem;"></i>
                        <h6 class="fw-bold mt-2">Telepon</h6>
                        <p class="text-muted">+62 21 1234 5678</p>
                    </div>
                    
                    <div class="col-md-4 text-center mb-3">
                        <i class="fas fa-clock text-primary" style="font-size: 2rem;"></i>
                        <h6 class="fw-bold mt-2">Jam Kerja</h6>
                        <p class="text-muted">Senin - Jumat<br>08:00 - 17:00 WIB</p>
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