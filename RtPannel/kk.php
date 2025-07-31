<?php 
include '../db.php'; 
include '../adminpannel/auth.php';

// Fix: Make sure $user is an array, not a string
if (!isset($user) || !is_array($user)) {
    // Try to get user from session or database if not set
    if (isset($_SESSION['user_id'])) {
        $user_id = $_SESSION['user_id'];
        $user_query = $conn->query("SELECT * FROM users WHERE id = '$user_id' LIMIT 1");
        if ($user_query && $user_query->num_rows > 0) {
            $user = $user_query->fetch_assoc();
        } else {
            $user = [
                'username' => 'Admin'
            ];
        }
    } else {
        $user = [
            'username' => 'Admin'
        ];
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data KK - Admin Dashboard</title>
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
        .content-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
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
        .table {
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
        }
        .table thead th {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            font-weight: 600;
            padding: 1rem;
        }
        .table tbody tr {
            transition: all 0.3s ease;
        }
        .table tbody tr:hover {
            background-color: rgba(102, 126, 234, 0.05);
            transform: scale(1.01);
        }
        .btn-sm {
            border-radius: 8px;
            font-weight: 500;
            transition: all 0.3s ease;
        }
        .btn-sm:hover {
            transform: translateY(-1px);
        }
        .page-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 15px;
            padding: 1.5rem;
            margin-bottom: 2rem;
        }
        .stats-card {
            background: white;
            border-radius: 15px;
            padding: 1.5rem;
            margin-bottom: 2rem;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
        }
        .user-avatar {
            width: 35px;
            height: 35px;
            border-radius: 50%;
            background: linear-gradient(135deg, #667eea, #764ba2);
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
                        <a class="nav-link active" href="kk.php">
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
                                <?php 
                                // Defensive: check if username exists and is string
                                echo isset($user['username']) && is_string($user['username']) ? strtoupper(substr($user['username'], 0, 1)) : 'A'; 
                                ?>
                            </div>
                            <span>
                                <?php 
                                echo isset($user['username']) && is_string($user['username']) ? htmlspecialchars($user['username']) : 'Admin'; 
                                ?>
                            </span>
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

    <div class="main-content">
        <div class="container">
            <div class="page-header">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <h1 class="fw-bold mb-2">
                            <i class="fas fa-users me-3"></i>Data Kartu Keluarga
                        </h1>
                        <p class="mb-0 opacity-75">Kelola data kartu keluarga warga</p>
                    </div>
                    <div class="col-md-4 text-end">
                        <a href="tambah_kk.php" class="btn btn-light btn-custom">
                            <i class="fas fa-plus me-2"></i>Tambah KK
                        </a>
                    </div>
                </div>
            </div>

            <?php
            $total_kk_row = $conn->query("SELECT COUNT(*) as total FROM keluarga");
            $total_kk = 0;
            if ($total_kk_row && $total_kk_row instanceof mysqli_result) {
                $total_kk_assoc = $total_kk_row->fetch_assoc();
                $total_kk = isset($total_kk_assoc['total']) ? $total_kk_assoc['total'] : 0;
            }
            ?>
            
            <div class="row mb-4">
                <div class="col-12 col-md-4">
                    <div class="stats-card">
                        <div class="row align-items-center">
                            <div class="col-8">
                                <h3 class="fw-bold text-primary mb-1"><?php echo $total_kk; ?></h3>
                                <p class="mb-0 text-muted">Total KK</p>
                            </div>
                            <div class="col-4 text-end">
                                <i class="fas fa-users fa-2x text-primary opacity-25"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="content-card p-4">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th><i class="fas fa-id-card me-2"></i>No KK</th>
                                <th><i class="fas fa-user me-2"></i>Kepala Keluarga</th>
                                <th><i class="fas fa-map-marker-alt me-2"></i>Alamat</th>
                                <th><i class="fas fa-home me-2"></i>RT</th>
                                <th><i class="fas fa-building me-2"></i>RW</th>
                                <th><i class="fas fa-map me-2"></i>Kelurahan</th>
                                <th class="text-center"><i class="fas fa-cogs me-2"></i>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php
                        $result = $conn->query("SELECT * FROM keluarga ORDER BY kepala_keluarga ASC");
                        if ($result && $result instanceof mysqli_result && $result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()) {
                                echo "<tr>
                                    <td class='align-middle'>
                                        <div class='d-flex align-items-center'>
                                            <div class='avatar-sm bg-primary rounded-circle d-flex align-items-center justify-content-center me-3'>
                                                <i class='fas fa-id-card text-white'></i>
                                            </div>
                                            <div>
                                                <strong>" . htmlspecialchars($row['no_kk']) . "</strong>
                                            </div>
                                        </div>
                                    </td>
                                    <td class='align-middle'>" . htmlspecialchars($row['kepala_keluarga']) . "</td>
                                    <td class='align-middle'>" . htmlspecialchars($row['alamat']) . "</td>
                                    <td class='align-middle'>" . htmlspecialchars($row['rt']) . "</td>
                                    <td class='align-middle'>" . htmlspecialchars($row['rw']) . "</td>
                                    <td class='align-middle'>" . htmlspecialchars($row['kelurahan']) . "</td>
                                    <td class='align-middle text-center'>
                                        <div class='btn-group' role='group'>
                                            <a href='edit_kk.php?id=" . urlencode($row['id']) . "' class='btn btn-sm btn-warning me-1' title='Edit'>
                                                <i class='fas fa-edit'></i>
                                            </a>
                                            <a href='hapus_kk.php?id=" . urlencode($row['id']) . "' class='btn btn-sm btn-danger' 
                                               onclick='return confirm(\"Yakin ingin menghapus KK ini?\")' title='Hapus'>
                                                <i class='fas fa-trash'></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>";
                            }
                        } else {
                            echo "<tr>
                                <td colspan='7' class='text-center py-4'>
                                    <div class='text-muted'>
                                        <i class='fas fa-inbox fa-3x mb-3 opacity-25'></i>
                                        <p class='mb-0'>Belum ada data kartu keluarga</p>
                                    </div>
                                </td>
                            </tr>";
                        }
                        ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script src="../bootstrap-5.3.7-dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
