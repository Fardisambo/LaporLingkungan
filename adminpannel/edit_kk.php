<?php
include 'auth.php';
include '../db.php';

$id = $_GET['id'];
$result = $conn->query("SELECT * FROM keluarga WHERE id = $id");
$keluarga = $result->fetch_assoc();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $no_kk = $_POST['no_kk'];
    $kepala_keluarga = $_POST['kepala_keluarga'];
    $alamat = $_POST['alamat'];
    $rt = $_POST['rt'];
    $rw = $_POST['rw'];
    $kelurahan = $_POST['kelurahan'];

    $stmt = $conn->prepare("UPDATE keluarga SET no_kk=?, kepala_keluarga=?, alamat=?, rt=?, rw=?, kelurahan=? WHERE id=?");
    $stmt->bind_param("ssssssi", $no_kk, $kepala_keluarga, $alamat, $rt, $rw, $kelurahan, $id);
    
    if ($stmt->execute()) {
        header("Location: kk.php");
        exit;
    } else {
        $error = "Gagal mengupdate data KK";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit KK - Admin Dashboard</title>
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
        .form-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        .form-control {
            border-radius: 10px;
            border: 2px solid #e9ecef;
            padding: 12px 15px;
            transition: all 0.3s ease;
        }
        .form-control:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }
        .btn-custom {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            border-radius: 10px;
            padding: 12px 30px;
            color: white;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        .btn-custom:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
            color: white;
        }
        .btn-outline-secondary {
            border: 2px solid #6c757d;
            color: #6c757d;
            border-radius: 10px;
            padding: 12px 30px;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        .btn-outline-secondary:hover {
            background: #6c757d;
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(108, 117, 125, 0.4);
        }
        .page-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 15px;
            padding: 1.5rem;
            margin-bottom: 2rem;
        }
        .form-label {
            font-weight: 600;
            color: #495057;
            margin-bottom: 0.5rem;
        }
        .input-group-text {
            background: transparent;
            border: 2px solid #e9ecef;
            border-right: none;
            border-radius: 10px 0 0 10px;
        }
        .form-control {
            border-left: none;
            border-radius: 0 10px 10px 0;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-light">
        <div class="container">
            <a class="navbar-brand" href="home.php">
                <i class="fas fa-shield-alt me-2"></i>Admin Dashboard
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" 
                aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link active" href="kk.php">
                            <i class="fas fa-users me-1"></i>Data KK
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="warga.php">
                            <i class="fas fa-user me-1"></i>Data Warga
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="laporan.php">
                            <i class="fas fa-clipboard-list me-1"></i>Data Laporan
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="logout.php">
                            <i class="fas fa-sign-out-alt me-1"></i>Logout
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="main-content">
        <div class="container">
            <div class="page-header">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <h1 class="fw-bold mb-2">
                            <i class="fas fa-edit me-3"></i>Edit Kartu Keluarga
                        </h1>
                        <p class="mb-0 opacity-75">Edit data kartu keluarga yang sudah ada</p>
                    </div>
                    <div class="col-md-4 text-end">
                        <a href="kk.php" class="btn btn-light btn-outline-secondary">
                            <i class="fas fa-arrow-left me-2"></i>Kembali
                        </a>
                    </div>
                </div>
            </div>

            <div class="row justify-content-center">
                <div class="col-12 col-md-8 col-lg-6">
                    <div class="form-card p-4">
                        <?php if (isset($error)): ?>
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                <?php echo $error; ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        <?php endif; ?>

                        <form method="post">
                            <div class="mb-3">
                                <label for="no_kk" class="form-label">
                                    <i class="fas fa-id-card me-2"></i>Nomor Kartu Keluarga
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="fas fa-id-card"></i>
                                    </span>
                                    <input type="text" class="form-control" id="no_kk" name="no_kk" 
                                           value="<?= $keluarga['no_kk'] ?>" placeholder="Masukkan nomor KK" required>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="kepala_keluarga" class="form-label">
                                    <i class="fas fa-user me-2"></i>Nama Kepala Keluarga
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="fas fa-user"></i>
                                    </span>
                                    <input type="text" class="form-control" id="kepala_keluarga" name="kepala_keluarga" 
                                           value="<?= $keluarga['kepala_keluarga'] ?>" placeholder="Masukkan nama kepala keluarga" required>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="alamat" class="form-label">
                                    <i class="fas fa-map-marker-alt me-2"></i>Alamat
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="fas fa-map-marker-alt"></i>
                                    </span>
                                    <textarea class="form-control" id="alamat" name="alamat" rows="3" 
                                              placeholder="Masukkan alamat lengkap" required><?= $keluarga['alamat'] ?></textarea>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="rt" class="form-label">
                                            <i class="fas fa-home me-2"></i>RT
                                        </label>
                                        <div class="input-group">
                                            <span class="input-group-text">
                                                <i class="fas fa-home"></i>
                                            </span>
                                            <input type="text" class="form-control" id="rt" name="rt" 
                                                   value="<?= $keluarga['rt'] ?>" placeholder="Masukkan RT" required>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="rw" class="form-label">
                                            <i class="fas fa-building me-2"></i>RW
                                        </label>
                                        <div class="input-group">
                                            <span class="input-group-text">
                                                <i class="fas fa-building"></i>
                                            </span>
                                            <input type="text" class="form-control" id="rw" name="rw" 
                                                   value="<?= $keluarga['rw'] ?>" placeholder="Masukkan RW" required>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="mb-4">
                                <label for="kelurahan" class="form-label">
                                    <i class="fas fa-map me-2"></i>Kelurahan
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="fas fa-map"></i>
                                    </span>
                                    <input type="text" class="form-control" id="kelurahan" name="kelurahan" 
                                           value="<?= $keluarga['kelurahan'] ?>" placeholder="Masukkan kelurahan" required>
                                </div>
                            </div>

                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-custom">
                                    <i class="fas fa-save me-2"></i>Update Data
                                </button>
                                <a href="kk.php" class="btn btn-outline-secondary">
                                    <i class="fas fa-times me-2"></i>Batal
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="../bootstrap-5.3.7-dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
